<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Lote;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use App\Services\FinalizarLoteService;

class LoteController extends Controller
{
    protected function maskDisplay(string $name): string
    {
        $s = trim($name);
        if ($s === '') return $s;
        $plain = preg_replace('/\s+/u', '', $s);
        $len = mb_strlen($plain);
        if ($len <= 1) return mb_strtoupper($plain);
        $first = mb_strtoupper(mb_substr($plain, 0, 1));
        $last = mb_strtoupper(mb_substr($plain, -1));
        $stars = str_repeat('*', max(1, $len - 2));
        return $first . $stars . $last;
    }

    public function show(Lote $lote, FinalizarLoteService $finalizar)
    {
        $lote->load(['leilao', 'maiorLance.usuario', 'fotos']);
        $finalizar->checkAndFinalize($lote);
        $finalizar->finalizeLeilaoIfCompleted($lote->leilao);
        $lote->refresh()->load(['leilao', 'maiorLance.usuario', 'fotos']);
        $userVerification = null;
        if (auth()->check()) {
            $user = auth()->user();
            if ($user->verificacao_forcada || $user->documentos_validos) {
                $userVerification = ['status' => 'approved', 'reasons' => []];
            } else {
                $docs = $user->documentos()->get()->groupBy('tipo');
                $docFotoValid = ($docs->has('rg') && $docs['rg']->firstWhere('validado', true))
                    || ($docs->has('cnh') && $docs['cnh']->firstWhere('validado', true));
                $selfieValid = $docs->has('selfie') && $docs['selfie']->firstWhere('validado', true);
                $compValid = $docs->has('comprovante_residencia') && $docs['comprovante_residencia']->firstWhere('validado', true);

                $hasRejected = false;
                $reasons = [];
                foreach (['rg','cnh','selfie','comprovante_residencia'] as $t) {
                    if ($docs->has($t)) {
                        foreach ($docs[$t] as $d) {
                            if ($d->validado === false && $d->observacoes) {
                                $hasRejected = true;
                                $reasons[] = ['tipo' => $t, 'motivo' => $d->observacoes];
                            }
                        }
                    }
                }

                if ($docFotoValid && $selfieValid && $compValid) {
                    $userVerification = ['status' => 'approved', 'reasons' => []];
                } elseif ($hasRejected) {
                    $userVerification = ['status' => 'rejected', 'reasons' => $reasons];
                } else {
                    $userVerification = ['status' => 'pending', 'reasons' => []];
                }
            }
        }

        // Navigation
        $previous = Lote::where('leilao_id', $lote->leilao_id)
            ->where('ordem', '<', $lote->ordem)
            ->orderBy('ordem', 'desc')
            ->select('id', 'ordem', 'titulo', 'foto_capa') 
            ->first();

        $next = Lote::where('leilao_id', $lote->leilao_id)
            ->where('ordem', '>', $lote->ordem)
            ->orderBy('ordem', 'asc')
            ->select('id', 'ordem', 'titulo', 'foto_capa') 
            ->first();

        // Definir nome do vencedor mascarado (fake ou real)
        $vencedorNome = null;
        if ($lote->maiorLance) {
            $maior = $lote->maiorLance;
            $baseNome = $maior->is_fake
                ? ($maior->nome_exibicao ?: 'Participante')
                : ($maior->usuario?->nome ?: 'Participante');
            $vencedorNome = $this->maskDisplay($baseNome);
            if ($maior->usuario) {
                $lote->maiorLance->usuario->nome = $this->maskDisplay($maior->usuario->nome);
            }
        }

        return Inertia::render('Site/Lotes/Show', [
            'lote' => $lote,
            'leilao' => $lote->leilao,
            'previousLote' => $previous,
            'nextLote' => $next,
            'userVerification' => $userVerification,
            'serverNow' => now()->toIso8601String(),
            'vencedorNome' => $vencedorNome,
        ]);
    }

    public function history(Lote $lote)
    {
        $lote->load('leilao');
        if ($lote->leilao && $lote->leilao->tipo === 'venda_direta') {
            return response()->json([
                'total' => 0,
                'lances' => []
            ]);
        }
        $lances = $lote->lances()
            ->with('usuario')
            ->orderBy('valor', 'desc')
            ->get()
            ->map(function ($lance) {
                if ($lance->is_fake) {
                    $nome = $this->maskDisplay($lance->nome_exibicao ?: 'Participante');
                    $cidade = 'Localização não informada';
                    $uf = '';
                    if (!empty($lance->cidade_exibicao) && str_contains($lance->cidade_exibicao, '/')) {
                        [$cidade, $uf] = explode('/', $lance->cidade_exibicao, 2);
                        $cidade = trim($cidade);
                        $uf = trim($uf);
                    } elseif (!empty($lance->cidade_exibicao)) {
                        $cidade = $lance->cidade_exibicao;
                    }
                    
                    return [
                        'id' => $lance->id,
                        'valor' => $lance->valor,
                        'usuario' => $nome,
                        'cidade' => $cidade,
                        'uf' => $uf,
                        'data' => $lance->created_at->format('d/m/Y H:i'),
                        'tipo' => 'Automático',
                    ];
                } else {
                    $user = $lance->usuario;
                    $display = 'Usuário';
                    if ($user && !empty($user->apelido)) {
                        $display = $user->apelido;
                    } elseif ($user && !empty($user->nome)) {
                        $display = $this->maskDisplay($user->nome);
                    }
    
                    $cidade = null;
                    $uf = null;
                    if ($user) {
                        $cidade = $user->cidade ? trim($user->cidade) : null;
                        $uf = $user->estado ? trim($user->estado) : null;
                    }
                    if (empty($cidade) || empty($uf)) {
                        $cidade = 'Localização não informada';
                        $uf = '';
                    }
    
                    return [
                        'id' => $lance->id,
                        'valor' => $lance->valor,
                        'usuario' => $display,
                        'cidade' => $cidade,
                        'uf' => $uf,
                        'data' => $lance->created_at->format('d/m/Y H:i'),
                        'tipo' => 'Manual'
                    ];
                }

            });

        return response()->json([
            'total' => $lances->count(),
            'lances' => $lances
        ]);
    }

    public function purchase(Request $request, Lote $lote)
    {
        $lote->load(['leilao', 'maiorLance', 'maiorLance.usuario']);
        if (!$lote->leilao || $lote->leilao->tipo !== 'venda_direta') {
            return response()->json(['errors' => ['compra' => 'Operação indisponível para este lote.']], 422);
        }
        if (in_array($lote->status, ['vendido', 'reservado'])) {
            return response()->json(['errors' => ['compra' => 'Lote indisponível.']], 422);
        }
        if ($lote->leilao->status !== 'ativo') {
            return response()->json(['errors' => ['compra' => 'Leilão inativo para compra.']], 422);
        }

        return DB::transaction(function () use ($request, $lote) {
            $locked = Lote::whereKey($lote->id)->lockForUpdate()->first();
            if (in_array($locked->status, ['vendido', 'reservado'])) {
                return response()->json(['errors' => ['compra' => 'Lote já indisponível.']], 422);
            }

            $locked->status = 'vendido';
            $locked->comprador_id = $request->user()->id;
            $locked->valor_compra = $locked->valor_inicial ?? 0;
            $locked->comprado_em = now();
            $locked->save();

            broadcast(new \App\Events\LoteStatusUpdated($locked));

            return response()->noContent();
        });
    }
}
