<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Leilao;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\FinalizarLoteService;

class LeilaoController extends Controller
{
    public function index(Request $request)
    {
        $query = Leilao::withCount('lotes')
            ->where(function($q) {
                $q->whereNull('data_fim')->orWhere('data_fim', '>', now());
            });

        // Filter by Search Query (Title, Category of Lotes)
        if ($request->filled('q')) {
            $term = $request->input('q');
            $query->whereHas('lotes', function($q) use ($term) {
                $q->where('titulo', 'like', "%{$term}%")
                  ->orWhereHas('category', function($catQ) use ($term) {
                      $catQ->where('name', 'like', "%{$term}%");
                  });
            });
        }

        // Filter by Estado (Location)
        if ($request->filled('estado') && $request->input('estado') !== 'Todos') {
            $uf = $request->input('estado');
            // Check Leilao location OR Lote location
            $query->where(function($q) use ($uf) {
                $q->where('local', 'like', "%{$uf}%")
                  ->orWhereHas('lotes', function($loteQ) use ($uf) {
                      $loteQ->where('localizacao', 'like', "%{$uf}%");
                  });
            });
        }

        // Filter by Condicao
        if ($request->filled('condicao') && $request->input('condicao') !== 'Todos') {
            $condicao = strtolower($request->input('condicao'));
            $query->whereHas('lotes', function($q) use ($condicao) {
                $q->where('condicao', $condicao);
            });
        }

        $leiloes = $query->orderBy('data_inicio')
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Site/Leiloes/Index', [
            'leiloes' => $leiloes
        ]);
    }

    public function show(Leilao $leilao, FinalizarLoteService $finalizar)
    {
        $leilao->load(['lotes' => function($query) {
            $query->orderBy('ordem')->with('maiorLance');
        }]);
        foreach ($leilao->lotes as $l) {
            $finalizar->checkAndFinalize($l);
        }
        // Atualiza status do leilão caso todos os lotes tenham sido encerrados
        $finalizar->finalizeLeilaoIfCompleted($leilao);
        $leilao->load(['lotes' => function($query) {
            $query->orderBy('ordem')->with('maiorLance');
        }]);

        return Inertia::render('Site/Leiloes/Show', [
            'leilao' => $leilao
        ]);
    }
}
