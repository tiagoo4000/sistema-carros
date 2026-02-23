<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Leilao;
use App\Models\Usuario;
use App\Models\Lance;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $todayUsers = Usuario::whereDate('created_at', Carbon::today())->count();

        $trueBidAuctions = Lance::where('is_fake', false)
            ->whereNotNull('usuario_id')
            ->join('lotes', 'lances.lote_id', '=', 'lotes.id')
            ->join('leiloes', 'lotes.leilao_id', '=', 'leiloes.id')
            ->selectRaw('leiloes.id as leilao_id, leiloes.titulo as leilao_titulo, COUNT(DISTINCT lotes.id) as lotes_com_lances, COUNT(lances.id) as total_true_bids, MAX(lances.valor) as maior_valor')
            ->groupBy('leiloes.id', 'leiloes.titulo')
            ->orderByDesc('maior_valor')
            ->limit(12)
            ->get()
            ->map(function ($row) {
                return [
                    'leilao_id' => (int) $row->leilao_id,
                    'leilao_titulo' => $row->leilao_titulo,
                    'lotes_com_lances' => (int) $row->lotes_com_lances,
                    'total_true_bids' => (int) $row->total_true_bids,
                    'maior_valor' => (float) $row->maior_valor,
                ];
            });

        $pendingBase = Usuario::where('admin', false)
            ->where('documentos_validos', false)
            ->where(function ($qb) {
            $qb->where(function ($q) {
                $q->whereDoesntHave('documentos', function ($d) {
                    $d->whereIn('tipo', ['rg', 'cnh']);
                });
            })
            ->orWhereDoesntHave('documentos', function ($d) {
                $d->where('tipo', 'selfie');
            })
            ->orWhereDoesntHave('documentos', function ($d) {
                $d->where('tipo', 'comprovante_residencia');
            });
        });
        $pendingCount = (clone $pendingBase)->count();
        $pendingDocs = (clone $pendingBase)
            ->with(['documentos' => function ($q) {
                $q->whereIn('tipo', ['rg', 'cnh', 'selfie', 'comprovante_residencia']);
            }])
            ->orderBy('nome')
            ->limit(100)
            ->get(['id', 'nome', 'telefone', 'email'])
            ->map(function ($u) {
                $docs = $u->documentos->groupBy('tipo');
                $sentDocFoto = ($docs->has('rg') && $docs['rg']->count() > 0) || ($docs->has('cnh') && $docs['cnh']->count() > 0);
                $sentSelfie = $docs->has('selfie') && $docs['selfie']->count() > 0;
                $sentComp = $docs->has('comprovante_residencia') && $docs['comprovante_residencia']->count() > 0;
                $missing = [];
                if (!$sentDocFoto) { $missing[] = 'RG ou CNH'; }
                if (!$sentSelfie) { $missing[] = 'Selfie'; }
                if (!$sentComp) { $missing[] = 'Comprovante de residência'; }
                return [
                    'id' => (int) $u->id,
                    'nome' => $u->nome,
                    'telefone' => $u->telefone,
                    'email' => $u->email,
                    'faltantes' => $missing,
                ];
            })
            ->values();

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'leiloes_ativos' => Leilao::where('status', 'ativo')->count(),
                'total_usuarios' => Usuario::count(),
                'today_users' => $todayUsers,
                // Agrupado por Leilão, não por Lote
                'true_bid_auctions' => $trueBidAuctions,
                'pending_docs_count' => $pendingCount,
                'pending_docs' => $pendingDocs,
            ]
        ]);
    }
}
