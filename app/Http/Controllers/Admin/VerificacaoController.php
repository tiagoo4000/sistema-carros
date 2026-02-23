<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\Documento;
use App\Models\LogAuditoria;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VerificacaoController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');
        $nome = $request->get('nome');
        $cpf = $request->get('cpf');
        $apenasPendentes = $request->boolean('apenas_pendentes');
        $dataDe = $request->get('de');
        $dataAte = $request->get('ate');

        $query = Usuario::query()
            ->where('admin', false)
            ->where('email', 'not like', '%@example.%')
            ->with(['documentos' => function ($q) use ($dataDe, $dataAte) {
                if ($dataDe) $q->whereDate('created_at', '>=', $dataDe);
                if ($dataAte) $q->whereDate('created_at', '<=', $dataAte);
            }]);

        if ($nome) {
            $query->where('nome', 'like', "%{$nome}%");
        }
        if ($cpf) {
            $query->where('cpf', 'like', "%{$cpf}%");
        }

        $lista = $query->get()->map(function ($u) {
            $docs = $u->documentos->groupBy('tipo');
            $docFotoValid = ($docs->has('rg') && $docs['rg']->firstWhere('validado', true))
                || ($docs->has('cnh') && $docs['cnh']->firstWhere('validado', true));
            $selfieValid = $docs->has('selfie') && $docs['selfie']->firstWhere('validado', true);
            $compValid = $docs->has('comprovante_residencia') && $docs['comprovante_residencia']->firstWhere('validado', true);
            $hasRejected = false;
            foreach (['rg','cnh','selfie','comprovante_residencia'] as $t) {
                if ($docs->has($t)) {
                    foreach ($docs[$t] as $d) {
                        if ($d->validado === false && $d->observacoes) {
                            $hasRejected = true;
                            break 2;
                        }
                    }
                }
            }
            if ($u->verificacao_forcada || $u->documentos_validos || ($docFotoValid && $selfieValid && $compValid)) $st = 'approved';
            elseif ($hasRejected) $st = 'rejected';
            else $st = 'pending';
            $last = $u->documentos->max('created_at');
            $priority = $st === 'pending' ? 'Alta' : ($st === 'rejected' ? 'Média' : 'Baixa');
            return [
                'id' => $u->id,
                'nome' => $u->nome,
                'cpf' => $u->cpf,
                'email' => $u->email,
                'status' => $st,
                'forcado' => (bool) $u->verificacao_forcada,
                'prioridade' => $priority,
                'ultimo_envio' => $last,
            ];
        });

        if ($status) {
            $lista = $lista->filter(fn($r) => $r['status'] === $status)->values();
        }
        if ($apenasPendentes) {
            $lista = $lista->filter(fn($r) => $r['status'] === 'pending')->values();
        }

        $page = (int) $request->get('page', 1);
        $perPage = 10;
        $total = $lista->count();
        $slice = $lista->slice(($page - 1) * $perPage, $perPage)->values();
        $usuarios = new \Illuminate\Pagination\LengthAwarePaginator(
            $slice,
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return Inertia::render('Admin/Verificacoes/Index', [
            'registros' => $usuarios,
            'filters' => [
                'status' => $status,
                'nome' => $nome,
                'cpf' => $cpf,
                'apenas_pendentes' => $apenasPendentes,
                'de' => $dataDe,
                'ate' => $dataAte,
            ],
        ]);
    }

    public function show(Usuario $usuario)
    {
        $usuario->load('documentos');
        $docs = $usuario->documentos->groupBy('tipo');
        return Inertia::render('Admin/Verificacoes/Show', [
            'usuario' => [
                'id' => $usuario->id,
                'nome' => $usuario->nome,
                'cpf' => $usuario->cpf,
                'email' => $usuario->email,
                'telefone' => $usuario->telefone,
                'created_at' => $usuario->created_at,
                'documentos_validos' => (bool) $usuario->documentos_validos,
                'verificacao_forcada' => (bool) $usuario->verificacao_forcada,
            ],
            'documentos' => $usuario->documentos->map(function ($d) {
                return [
                    'id' => $d->id,
                    'tipo' => $d->tipo,
                    'validado' => (bool) $d->validado,
                    'observacoes' => $d->observacoes,
                    'caminho' => $d->caminho_arquivo,
                    'created_at' => $d->created_at,
                ];
            }),
        ]);
    }

    public function aprovar(Request $request, Usuario $usuario)
    {
        $required = ['rg','cnh','selfie','comprovante_residencia'];
        $docs = $usuario->documentos()->whereIn('tipo', $required)->get()->groupBy('tipo');
        foreach (['rg','cnh','selfie','comprovante_residencia'] as $t) {
            if ($docs->has($t)) {
                $docs[$t]->each(function ($d) {
                    $d->validado = true;
                    $d->observacoes = null;
                    $d->save();
                });
            }
        }
        $this->recalcularStatusUsuario($usuario);
        LogAuditoria::create([
            'usuario_id' => auth()->id(),
            'acao' => 'verificacao_aprovada',
            'dados' => ['usuario_verificado_id' => $usuario->id],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
        return back()->with('success', 'Verificação aprovada.');
    }

    public function forcarAprovacao(Request $request, Usuario $usuario)
    {
        // Marca todos os documentos enviados como validados (se existirem)
        $usuario->documentos()->update(['validado' => true, 'observacoes' => null]);
        // Ativa flags de aprovação
        $usuario->update([
            'documentos_validos' => true,
            'verificacao_forcada' => true,
        ]);
        LogAuditoria::create([
            'usuario_id' => auth()->id(),
            'acao' => 'verificacao_forcada',
            'dados' => ['usuario_verificado_id' => $usuario->id],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
        return back()->with('success', 'Aprovação forçada aplicada. O usuário está liberado e o envio de novos documentos será bloqueado.');
    }

    public function rejeitar(Request $request, Usuario $usuario)
    {
        $data = $request->validate(['motivo' => 'required|string|max:500']);
        $required = ['rg','cnh','selfie','comprovante_residencia'];
        $docs = $usuario->documentos()->whereIn('tipo', $required)->get();
        foreach ($docs as $d) {
            $d->validado = false;
            $d->observacoes = $data['motivo'];
            $d->save();
        }
        $this->recalcularStatusUsuario($usuario);
        LogAuditoria::create([
            'usuario_id' => auth()->id(),
            'acao' => 'verificacao_rejeitada',
            'dados' => ['usuario_verificado_id' => $usuario->id, 'motivo' => $data['motivo']],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
        return back()->with('success', 'Verificação rejeitada.');
    }

    protected function recalcularStatusUsuario(Usuario $usuario): void
    {
        $docsAll = $usuario->documentos()->get()->groupBy('tipo');
        $docFotoValid = ($docsAll->has('rg') && $docsAll['rg']->firstWhere('validado', true))
            || ($docsAll->has('cnh') && $docsAll['cnh']->firstWhere('validado', true));
        $selfieValid = $docsAll->has('selfie') && $docsAll['selfie']->firstWhere('validado', true);
        $compValid = $docsAll->has('comprovante_residencia') && $docsAll['comprovante_residencia']->firstWhere('validado', true);
        // Se já foi forçada, mantém aprovados; caso contrário, calcula estritamente
        if ($usuario->verificacao_forcada) {
            $usuario->update(['documentos_validos' => true]);
        } else {
            $usuario->update(['documentos_validos' => $docFotoValid && $selfieValid && $compValid]);
        }
    }
}
