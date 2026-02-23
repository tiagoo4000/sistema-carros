<?php

namespace App\Http\Controllers;

use App\Models\Leilao;
use App\Models\Produto;
use App\Models\Lance;
use App\Models\Lote;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function dashboard()
    {
        $today = Carbon::today();

        $todayUsers = Usuario::whereDate('created_at', $today)->count();

        $trueBidLots = Lance::where('is_fake', false)
            ->join('lotes', 'lances.lote_id', '=', 'lotes.id')
            ->join('leiloes', 'lotes.leilao_id', '=', 'leiloes.id')
            ->selectRaw('lotes.id as lote_id, leiloes.id as leilao_id, leiloes.titulo as leilao_titulo, lotes.titulo as lote_titulo, lotes.ordem as lote_ordem, COUNT(lances.id) as total_true_bids, MAX(lances.valor) as maior_valor')
            ->groupBy('lotes.id', 'leiloes.id', 'leiloes.titulo', 'lotes.titulo', 'lotes.ordem')
            ->orderByDesc('maior_valor')
            ->limit(12)
            ->get()
            ->map(function ($row) {
                return [
                    'leilao_id' => (int) $row->leilao_id,
                    'leilao_titulo' => $row->leilao_titulo,
                    'lote_id' => (int) $row->lote_id,
                    'lote_titulo' => $row->lote_titulo,
                    'lote_ordem' => (int) $row->lote_ordem,
                    'total_true_bids' => (int) $row->total_true_bids,
                    'maior_valor' => (float) $row->maior_valor,
                ];
            });

        return Inertia::render('Admin/Dashboard', [
            'leiloes' => Leilao::with(['produto', 'usuarioCriador', 'lances'])
                ->latest()
                ->paginate(10),
            'stats' => [
                'total_leiloes' => Leilao::count(),
                'leiloes_ativos' => Leilao::where('status', 'ativo')->count(),
                'total_usuarios' => Usuario::count(),
                'today_users' => $todayUsers,
                'true_bid_lots' => $trueBidLots,
            ]
        ]);
    }

    public function createLeilao()
    {
        return Inertia::render('Admin/Leiloes/Create');
    }

    public function storeLeilao(Request $request)
    {
        $request->validate([
            'produto_nome' => 'required|string|max:255',
            'produto_descricao' => 'required|string',
            'produto_imagem_url' => 'required|url',
            'valor_inicial' => 'required|numeric|min:1',
            'valor_minimo_incremento' => 'required|numeric|min:0.5',
            'data_inicio' => 'required|date|after_or_equal:now',
            'data_fim' => 'required|date|after:data_inicio',
        ]);

        $produto = Produto::create([
            'nome' => $request->produto_nome,
            'descricao' => $request->produto_descricao,
            'imagem_url' => $request->produto_imagem_url,
        ]);

        $leilao = Leilao::create([
            'produto_id' => $produto->id,
            'usuario_criador_id' => $request->user()->id,
            'valor_inicial' => $request->valor_inicial,
            'valor_minimo_incremento' => $request->valor_minimo_incremento,
            'data_inicio' => $request->data_inicio,
            'data_fim' => $request->data_fim,
            'status' => 'ativo',
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Leilão criado com sucesso!');
    }
}
