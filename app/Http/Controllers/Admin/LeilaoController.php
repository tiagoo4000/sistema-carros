<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Leilao;
use App\Models\Comitente;
use App\Models\Usuario;
use App\Models\Lance;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class LeilaoController extends Controller
{
    public function index()
    {
        $leiloes = Leilao::withCount('lotes')->latest()->paginate(10);
        return Inertia::render('Admin/Leiloes/Index', [
            'leiloes' => $leiloes
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Leiloes/Create', [
            'comitentes' => Comitente::orderBy('nome')->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'local' => 'required|string|max:255',
            'data_inicio' => 'required|date',
            'data_fim' => 'required|date|after:data_inicio',
            'status' => 'required|in:ativo,finalizado,cancelado',
            'tipo' => 'required|in:leilao,venda_direta',
            'comitente_id' => 'nullable|exists:comitentes,id',
        ]);

        Leilao::create($validated);

        return redirect()->route('admin.leiloes.index')->with('success', 'Leilão criado com sucesso.');
    }

    public function show(Leilao $leilao)
    {
        $leilao->load(['lotes' => function($query) {
            $query->orderBy('ordem');
        }]);
        
        return Inertia::render('Admin/Leiloes/Show', [
            'leilao' => $leilao
        ]);
    }

    public function edit(Leilao $leilao)
    {
        return Inertia::render('Admin/Leiloes/Edit', [
            'leilao' => $leilao,
            'comitentes' => Comitente::orderBy('nome')->get()
        ]);
    }

    public function update(Request $request, Leilao $leilao)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'local' => 'required|string|max:255',
            'data_inicio' => 'required|date',
            'data_fim' => 'required|date|after:data_inicio',
            'status' => 'required|in:ativo,finalizado,cancelado',
            'tipo' => 'required|in:leilao,venda_direta',
            'comitente_id' => 'nullable|exists:comitentes,id',
        ]);

        $leilao->update($validated);

        return redirect()->route('admin.leiloes.index')->with('success', 'Leilão atualizado com sucesso.');
    }

    public function destroy(Leilao $leilao)
    {
        $leilao->delete();
        return redirect()->route('admin.leiloes.index')->with('success', 'Leilão excluído com sucesso.');
    }

    public function toggleStatus(Leilao $leilao)
    {
        // Se estiver ativo, desativa (muda para cancelado/inativo)
        // Se estiver em qualquer outro status, ativa

        if ($leilao->status === 'ativo') {
            // Usamos 'cancelado' como status de "Desativado" para remover do site
            $leilao->update(['status' => 'cancelado']);
            $msg = 'Leilão desativado com sucesso.';
        } else {
            $leilao->update(['status' => 'ativo']);
            $msg = 'Leilão ativado com sucesso.';
        }

        return back()->with('success', $msg);
    }

    public function gerarLances(Request $request, Leilao $leilao, \App\Services\FakeUserService $fakeUsers)
    {
        if ($leilao->tipo === 'venda_direta') {
            return back()->with('error', 'Venda direta não aceita geração de lances.');
        }
        $request->validate([
            'quantidade' => 'required|integer|min:1|max:50',
        ]);

        $lotes = $leilao->lotes;
        
        if ($lotes->isEmpty()) {
            return back()->with('error', 'Este leilão não possui lotes.');
        }

        $count = 0;
        $qty = $request->quantidade;

        $cities = config('br_cities');
        if (!$cities || count($cities) === 0) {
            return back()->with('error', 'Base de cidades brasileiras não disponível.');
        }

        $usedByLote = [];
        // Distribui lances fake aleatoriamente entre os lotes
        for ($i = 0; $i < $qty; $i++) {
            $lote = $lotes->random();
            $city = $cities[array_rand($cities)];
            $cidadeUF = $city['nome'] . '/' . $city['uf'];

            $nomeExibicao = $fakeUsers->generateNameForLance($lote->id, 15, 3);
            $usedByLote[$lote->id][] = $nomeExibicao;
            if (count($usedByLote[$lote->id]) > 5) {
                array_shift($usedByLote[$lote->id]);
            }

            // Pega o maior lance atual ou valor inicial
            $ultimoLance = $lote->lances()->latest('valor')->first();
            $valorBase = $ultimoLance ? $ultimoLance->valor : $lote->valor_inicial;
            
            // Adiciona incremento
            $novoValor = $valorBase + $lote->valor_minimo_incremento + (rand(0, 5) * 100);

            Lance::create([
                'lote_id' => $lote->id,
                'valor' => $novoValor,
                'is_fake' => true,
                'nome_exibicao' => $nomeExibicao,
                'cidade_exibicao' => $cidadeUF,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Atualiza status se necessário
            if ($lote->status === 'sem_lances' || $lote->status === 'aberto') {
                $lote->update(['status' => 'aberto']); // Garante que está aberto se recebeu lance
            }
            
            $count++;
        }

        return back()->with('success', "$count lances gerados com sucesso.");
    }

    public function limparLances(Leilao $leilao)
    {
        $lotes = $leilao->lotes;
        
        if ($lotes->isEmpty()) {
            return back()->with('error', 'Este leilão não possui lotes.');
        }

        foreach ($lotes as $lote) {
            $lote->lances()->delete();
            $lote->update([
                'status' => 'aberto',
                'comprador_id' => null,
                'valor_compra' => null,
                'comprado_em' => null,
            ]);
        }

        return back()->with('success', 'Todos os lances foram removidos.');
    }

    public function gerarVisualizacoes(Request $request, Leilao $leilao)
    {
        $request->validate([
            'quantidade' => 'required|integer|min:1|max:1000',
            'distribuicao' => 'required|in:igual,aleatoria',
        ]);

        $lotes = $leilao->lotes;
        
        if ($lotes->isEmpty()) {
            return back()->with('error', 'Este leilão não possui lotes.');
        }

        $totalViews = $request->quantidade;
        
        if ($request->distribuicao === 'igual') {
            $viewsPerLote = intdiv($totalViews, $lotes->count());
            foreach ($lotes as $lote) {
                $lote->increment('views', $viewsPerLote);
            }
        } else {
            // Aleatória
            // Simplesmente incrementa um lote aleatório N vezes (pode ser lento para muitos)
            // Melhor: dividir o total em partes aleatórias
            $remaining = $totalViews;
            foreach ($lotes as $index => $lote) {
                if ($index === $lotes->count() - 1) {
                    $add = $remaining;
                } else {
                    $add = rand(0, $remaining);
                    $remaining -= $add;
                }
                $lote->increment('views', $add);
            }
        }

        return back()->with('success', 'Visualizações geradas com sucesso.');
    }
}
