<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Leilao;
use App\Models\Lote;
use App\Models\Lance;
use App\Models\FotoLote;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class LoteController extends Controller
{
    public function index(Request $request)
    {
        $query = Lote::with('leilao');

        $leilao = null;
        if ($request->filled('leilao_id')) {
            $leilao = Leilao::find($request->leilao_id);
            if ($leilao) {
                $query->where('leilao_id', $leilao->id);
            }
        }

        $lotes = $query->orderBy('ordem')->paginate(10)->withQueryString();
        
        return Inertia::render('Admin/Lotes/Index', [
            'lotes' => $lotes,
            'leilao' => $leilao,
            'filters' => $request->only(['leilao_id'])
        ]);
    }

    public function create(Leilao $leilao)
    {
        $categories = Category::where('active', true)->orderBy('name')->get();
        $nextOrder = $leilao->lotes()->max('ordem') + 1;

        return Inertia::render('Admin/Lotes/Create', [
            'leilao' => $leilao,
            'categories' => $categories,
            'nextOrder' => $nextOrder
        ]);
    }

    public function store(Request $request, Leilao $leilao)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'valor_inicial' => 'required|numeric|min:0',
            'valor_minimo_incremento' => 'required|numeric|min:0',
            'ordem' => 'required|integer',
            'status' => 'required|in:aberto,arrematado,sem_lances',
            'ano' => 'required|string|max:20',
            'quilometragem' => 'required|integer|min:0',
            'valor_mercado' => 'required|numeric|min:0',
            'combustivel' => 'required|string|max:50',
            'cor' => 'required|string|max:50',
            'possui_chave' => 'required|boolean',
            'category_id' => 'required|exists:categories,id',
            'tipo_retomada' => 'required|string|max:100',
            'localizacao' => 'required|string|max:255',
            'prazo_documentacao' => 'required|string|max:100',
            'descricao_detalhada' => 'nullable|string',
            'fotos' => 'nullable|array',
            'fotos.*' => 'image|mimes:jpeg,jpg,webp|max:10240', // 10MB max, NO PNG
            'capa_index' => 'nullable|integer',
        ], [
            'fotos.*.mimes' => 'Apenas imagens JPG, JPEG e WEBP são permitidas. PNG não é permitido.',
        ]);

        $data = $validated;
        $data['subtitulo'] = $leilao->titulo;
        
        // Obter nome da categoria para preencher o campo 'tipo' (legado/compatibilidade)
        $category = Category::find($request->category_id);
        $data['tipo'] = $category->name;

        $lote = $leilao->lotes()->create($data);

        // Upload de Fotos
        if ($request->hasFile('fotos')) {
            $capaIndex = $request->input('capa_index', 0); // Default to first image
            
            foreach ($request->file('fotos') as $index => $file) {
                $path = $file->store("lotes/{$lote->id}", 'public');
                
                // Salvar na tabela de fotos
                $foto = $lote->fotos()->create([
                    'caminho' => $path
                ]);

                // Definir capa se for o índice escolhido
                if ($index == $capaIndex) {
                    $lote->foto_capa = $path;
                    $lote->save();
                }
            }
            
            // Fallback: se nenhuma capa foi definida (ex: capa_index inválido), pegar a primeira
            if (!$lote->foto_capa && $lote->fotos()->exists()) {
                $firstFoto = $lote->fotos()->first();
                $lote->foto_capa = $firstFoto->caminho;
                $lote->save();
            }
        }

        return redirect()->route('admin.lotes.index', ['leilao_id' => $leilao->id])->with('success', 'Lote criado com sucesso.');
    }

    public function edit(Leilao $leilao, Lote $lote)
    {
        $lote->load('fotos');
        $categories = Category::where('active', true)->orderBy('name')->get();
        
        return Inertia::render('Admin/Lotes/Edit', [
            'leilao' => $leilao,
            'lote' => $lote,
            'categories' => $categories
        ]);
    }

    public function update(Request $request, Leilao $leilao, Lote $lote)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'valor_inicial' => 'required|numeric|min:0',
            'valor_minimo_incremento' => 'required|numeric|min:0',
            'ordem' => 'required|integer',
            'status' => 'required|in:aberto,arrematado,sem_lances',
            'ano' => 'required|string|max:20',
            'quilometragem' => 'required|integer|min:0',
            'valor_mercado' => 'required|numeric|min:0',
            'combustivel' => 'required|string|max:50',
            'cor' => 'required|string|max:50',
            'possui_chave' => 'required|boolean',
            'category_id' => 'required|exists:categories,id',
            'tipo_retomada' => 'required|string|max:100',
            'localizacao' => 'required|string|max:255',
            'prazo_documentacao' => 'required|string|max:100',
            'descricao_detalhada' => 'nullable|string',
            'fotos' => 'nullable|array',
            'fotos.*' => 'image|mimes:jpeg,jpg,webp|max:10240',
            'deleted_fotos' => 'nullable|array',
            'deleted_fotos.*' => 'integer|exists:foto_lotes,id',
            'capa_tipo' => 'nullable|string|in:existing,new', // 'existing' (use id) or 'new' (use index)
            'capa_valor' => 'nullable', // id or index
        ], [
            'fotos.*.mimes' => 'Apenas imagens JPG, JPEG e WEBP são permitidas. PNG não é permitido.',
        ]);

        $data = $validated;
        $data['subtitulo'] = $leilao->titulo;

        // Obter nome da categoria para preencher o campo 'tipo' (legado/compatibilidade)
        $category = Category::find($request->category_id);
        $data['tipo'] = $category->name;

        $lote->update($data);

        // Deletar fotos removidas
        if ($request->filled('deleted_fotos')) {
            $fotosToDelete = FotoLote::whereIn('id', $request->deleted_fotos)->where('lote_id', $lote->id)->get();
            foreach ($fotosToDelete as $foto) {
                Storage::disk('public')->delete($foto->caminho);
                $foto->delete();
            }
        }

        // Upload de Novas Fotos
        $newPhotosPaths = [];
        if ($request->hasFile('fotos')) {
            foreach ($request->file('fotos') as $index => $file) {
                $path = $file->store("lotes/{$lote->id}", 'public');
                $lote->fotos()->create(['caminho' => $path]);
                $newPhotosPaths[$index] = $path;
            }
        }

        // Atualizar Capa
        if ($request->filled('capa_tipo')) {
            if ($request->capa_tipo === 'existing') {
                $foto = FotoLote::find($request->capa_valor);
                if ($foto && $foto->lote_id === $lote->id) {
                    $lote->foto_capa = $foto->caminho;
                    $lote->save();
                }
            } elseif ($request->capa_tipo === 'new' && isset($newPhotosPaths[$request->capa_valor])) {
                $lote->foto_capa = $newPhotosPaths[$request->capa_valor];
                $lote->save();
            }
        } 
        // Se a capa atual foi deletada e nenhuma nova foi selecionada, tentar pegar a primeira disponível
        elseif ($request->filled('deleted_fotos')) {
             // Verificar se a capa atual ainda existe no disco/banco. 
             // Como deletamos via ID, podemos checar se o caminho atual ainda está na lista de fotos do lote.
             // Mas mais fácil é: se o lote->foto_capa aponta para um arquivo deletado...
             // Melhor: Se não temos capa definida ou a capa atual sumiu, redefinir.
             $lote->refresh();
             $exists = $lote->fotos()->where('caminho', $lote->foto_capa)->exists();
             if (!$exists) {
                 $first = $lote->fotos()->first();
                 if ($first) {
                     $lote->foto_capa = $first->caminho;
                     $lote->save();
                 } else {
                     $lote->foto_capa = null;
                     $lote->save();
                 }
             }
        }

        return redirect()->route('admin.lotes.index', ['leilao_id' => $leilao->id])->with('success', 'Lote atualizado com sucesso.');
    }

    public function destroy(Leilao $leilao, Lote $lote)
    {
        // Deletar fotos do disco
        foreach ($lote->fotos as $foto) {
            Storage::disk('public')->delete($foto->caminho);
        }
        $lote->fotos()->delete();
        $lote->delete();
        
        return redirect()->route('admin.leiloes.show', $leilao->id)->with('success', 'Lote excluído com sucesso.');
    }

    public function lances(Leilao $leilao, Lote $lote)
    {
        abort_unless($lote->leilao_id === $leilao->id, 404);
        $lances = $lote->lances()
            ->with(['usuario:id,nome,apelido'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($l) {
                return [
                    'id' => $l->id,
                    'valor' => (float) $l->valor,
                    'is_fake' => (bool) $l->is_fake,
                    'usuario' => $l->is_fake
                        ? ($l->nome_exibicao ?: 'Automático')
                        : ($l->usuario?->apelido ?: $l->usuario?->nome ?: 'Usuário'),
                    'cidade' => $l->is_fake ? ($l->cidade_exibicao ?: null) : null,
                    'created_at' => optional($l->created_at)->toIso8601String(),
                ];
            });
        return response()->json([
            'total' => $lances->count(),
            'lances' => $lances,
        ]);
    }

    public function destroyLance(Leilao $leilao, Lote $lote, Lance $lance)
    {
        abort_unless($lote->leilao_id === $leilao->id && $lance->lote_id === $lote->id, 404);
        $lance->delete();
        return response()->json(['ok' => true]);
    }
}
