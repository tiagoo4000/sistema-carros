<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Leilao;
use App\Models\Lote;
use App\Models\Banner;
use App\Models\Category;
use App\Models\SystemConfig;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use App\Services\FinalizarLoteService;

use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        try {
            // Layout fixo
            $view = 'Site/HomeCarro';

            // Dados rápidos (Banners e Categorias) podem vir direto ou via cache
            $banners = Cache::remember('home_banners_v2', 600, function () {
                return Banner::where('status', 'ativo')
                    ->orderBy('order')
                    ->orderBy('created_at', 'desc')
                    ->get();
            });

            $categories = Cache::remember('home_categories_v2', 600, function () {
                return Category::where('active', true)
                    ->withCount('lotes')
                    ->get();
            });

            // Retorna a view com dados leves. 
            // Os dados pesados (leiloesAtivos, proximosLeiloes) virão via API (Skeleton Loader).
            return Inertia::render($view, [
                'banners' => $banners,
                'categories' => $categories,
                'leiloesAtivos' => null, // Frontend vai buscar via API
                'proximosLeiloes' => null, // Frontend vai buscar via API
                'serverNow' => now()->toIso8601String()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in HomeController', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function getHomeData()
    {
        try {
            $data = Cache::remember('home_leiloes_data_v3', 30, function () {
                // 1. Leilões Ativos
                $leiloesAtivos = Leilao::with('comitente')
                    ->withCount('lotes')
                    ->whereIn(DB::raw('LOWER(TRIM(status))'), ['ativo'])
                    ->where(function($q) {
                        $q->whereNull('data_fim')->orWhere('data_fim', '>', now());
                    })
                    ->where(function($q){
                        $q->whereNull('data_inicio')->orWhere('data_inicio', '<=', now());
                    })
                    ->orderByRaw("COALESCE(data_fim, '2099-12-31 23:59:59')")
                    ->take(5)
                    ->get();

                foreach ($leiloesAtivos as $leilao) {
                    // Load preview lots (limit 4) - Otimizado: select apenas colunas necessárias
                    $visibleStatuses = ['aberto', 'dou_lhe_1', 'dou_lhe_2'];
                    if ($leilao->tipo === 'venda_direta' && (is_null($leilao->data_fim) || $leilao->data_fim > now())) {
                        $visibleStatuses[] = 'vendido';
                    }

                    $lots = $leilao->lotes()
                        ->select('id', 'leilao_id', 'titulo', 'subtitulo', 'descricao', 'valor_inicial', 'foto_capa', 'ordem', 'status', 'ends_at', 'ano', 'quilometragem', 'localizacao', 'tipo_retomada')
                        ->whereIn('status', $visibleStatuses)
                        ->orderBy('ordem')
                        ->take(4)
                        ->with(['maiorLance' => function($q) {
                            $q->select('id', 'lote_id', 'valor');
                        }])
                        ->withCount('fotos')
                        ->get();

                    $leilao->setRelation('lotes', $lots);
                    $leilao->lotes_count = $lots->count();
                }
                
                // 2. Próximos Leilões
                $proximosLeiloes = Leilao::with(['lotes' => function($q) {
                        $q->select('id', 'leilao_id', 'titulo', 'valor_inicial', 'foto_capa', 'ordem', 'status')
                          ->orderBy('ordem')->limit(1);
                    }, 'comitente'])
                    ->withCount('lotes')
                    ->where('data_inicio', '>', now())
                    ->orderBy('data_inicio')
                    ->take(6)
                    ->get();

                return [
                    'leiloesAtivos' => $leiloesAtivos,
                    'proximosLeiloes' => $proximosLeiloes
                ];
            });
            
            // Fora do cache: garantir atualização de status do leilão sem depender do cache
            try {
                $finalizar = app(FinalizarLoteService::class);
                $ids = collect($data['leiloesAtivos'] ?? [])->pluck('id')->filter()->unique()->values()->all();
                if ($ids) {
                    $leiloes = Leilao::whereIn('id', $ids)->get();
                    foreach ($leiloes as $le) {
                        $finalizar->finalizeLeilaoIfCompleted($le);
                    }
                }
            } catch (\Throwable $e) {
                \Log::warning('home_finalize_leilao_falha', ['erro' => $e->getMessage()]);
            }

            return response()->json($data);
        } catch (\Exception $e) {
            \Log::error('Error fetching home data', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    public function autocomplete(Request $request)
    {
        $query = $request->input('query');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $suggestions = [];

        // 1. Categories matching name
        $categories = Category::where('active', true)
            ->where('name', 'like', "%{$query}%")
            ->take(3)
            ->get();
        
        foreach ($categories as $cat) {
            $suggestions[] = [
                'type' => 'category',
                'value' => $cat->name,
                'label' => $cat->name . ' - Categoria',
                'category_id' => $cat->id
            ];
        }

        // 2. Lotes matching title (Model simulation)
        $lotes = Lote::select('titulo', 'category_id')
            ->where('titulo', 'like', "%{$query}%")
            ->where('status', 'aberto') // Only open lots
            ->with('category')
            ->distinct('titulo')
            ->take(5)
            ->get();

        foreach ($lotes as $lote) {
            $catName = $lote->category ? $lote->category->name : 'Item';
            $suggestions[] = [
                'type' => 'model',
                'value' => $lote->titulo,
                'label' => $lote->titulo . ' - ' . $catName
            ];
        }

        return response()->json($suggestions);
    }

    public function searchCount(Request $request)
    {
        $query = $request->input('query');
        $uf = $request->input('estado');
        $condicao = $request->input('condicao');

        $lotesQuery = Lote::query()
            ->where('status', 'aberto'); // Only available items

        if ($query) {
            $lotesQuery->where(function($q) use ($query) {
                $q->where('titulo', 'like', "%{$query}%")
                  ->orWhereHas('category', function($catQ) use ($query) {
                      $catQ->where('name', 'like', "%{$query}%");
                  });
            });
        }

        if ($uf && $uf !== 'Todos') {
            $lotesQuery->where(function($q) use ($uf) {
                $q->where('localizacao', 'like', "%{$uf}%")
                  ->orWhereHas('leilao', function($leilaoQ) use ($uf) {
                      $leilaoQ->where('local', 'like', "%{$uf}%");
                  });
            });
        }

        if ($condicao && $condicao !== 'Todos') {
            // Map UI "Finalidade/Condição" to DB "condicao"
            // Assuming UI sends 'Novo' or 'Usado' or 'Venda Direta' (mapped to DB enum?)
            // If user meant "Venda Direta" vs "Leilão", that's usually Leilao Type.
            // But if user meant Condition (Novo/Usado), we use 'condicao'.
            // Let's assume strict mapping to 'condicao' column for now.
            $lotesQuery->where('condicao', strtolower($condicao));
        }

        $count = $lotesQuery->count();

        return response()->json(['count' => $count]);
    }

    // Debug: resumo de leilões (somente em local)
    public function debugLeiloesResumo()
    {
        if (config('app.env') !== 'local') {
            return response()->json(['error' => 'forbidden'], 403);
        }
        $all = Leilao::select('id','titulo','status','data_inicio','data_fim','updated_at')
            ->orderBy('id','desc')
            ->get()
            ->map(function($l){
                return [
                    'id' => $l->id,
                    'titulo' => $l->titulo,
                    'status' => $l->status,
                    'data_inicio' => $l->data_inicio,
                    'data_fim' => $l->data_fim,
                    'is_window_now' => (is_null($l->data_inicio) || $l->data_inicio <= now()) && (is_null($l->data_fim) || $l->data_fim > now())
                ];
            });
        return response()->json($all);
    }
}
