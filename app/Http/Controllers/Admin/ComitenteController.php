<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comitente;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class ComitenteController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Comitentes/Index', [
            'comitentes' => Comitente::latest()->get()
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Comitentes/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'imagem' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        if ($request->hasFile('imagem')) {
            $path = $request->file('imagem')->store('comitentes', 'public');
            $validated['imagem'] = $path;
        }

        Comitente::create($validated);

        return redirect()->route('admin.comitentes.index')->with('success', 'Comitente criado com sucesso.');
    }

    public function edit(Comitente $comitente)
    {
        return Inertia::render('Admin/Comitentes/Edit', [
            'comitente' => $comitente
        ]);
    }

    public function update(Request $request, Comitente $comitente)
    {
        // dd($request->all()); // Debug se necessário

        $rules = [
            'nome' => 'required|string|max:255',
            'imagem' => 'nullable', // Permitir string (sem alteração) ou arquivo
        ];

        // Se for arquivo, valida
        if ($request->hasFile('imagem')) {
            $rules['imagem'] = 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048';
        }

        $validated = $request->validate($rules);

        if ($request->hasFile('imagem')) {
            if ($comitente->imagem) {
                Storage::disk('public')->delete($comitente->imagem);
            }
            $path = $request->file('imagem')->store('comitentes', 'public');
            $validated['imagem'] = $path;
        } else {
            // Remove do array se não for arquivo, para não sobrescrever com null ou string antiga se não necessário,
            // mas o update só atualiza o que está no array. Se não enviou imagem nova, mantém a antiga.
            // O frontend pode enviar null ou undefined se não mudou.
            unset($validated['imagem']);
        }

        $comitente->update($validated);

        return redirect()->route('admin.comitentes.index')->with('success', 'Comitente atualizado com sucesso.');
    }

    public function destroy(Comitente $comitente)
    {
        if ($comitente->imagem) {
            Storage::disk('public')->delete($comitente->imagem);
        }
        $comitente->delete();

        return redirect()->route('admin.comitentes.index')->with('success', 'Comitente excluído com sucesso.');
    }
}
