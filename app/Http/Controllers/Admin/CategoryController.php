<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::latest()->get();
        return Inertia::render('Admin/Categories/Index', [
            'categories' => $categories
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Categories/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,webp|max:2048', // Validação de imagem
        ]);

        $path = $request->file('image')->store('categories', 'public');

        Category::create([
            'name' => $request->name,
            'image_path' => $path,
            'active' => true,
        ]);

        return redirect()->route('admin.categorias.index')->with('success', 'Categoria criada com sucesso!');
    }

    public function edit(Category $categoria)
    {
        return Inertia::render('Admin/Categories/Edit', [
            'category' => $categoria
        ]);
    }

    public function update(Request $request, Category $categoria)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,webp|max:2048',
        ]);

        $data = [
            'name' => $request->name,
        ];

        if ($request->hasFile('image')) {
            // Deletar imagem antiga se existir
            if ($categoria->image_path && Storage::disk('public')->exists($categoria->image_path)) {
                Storage::disk('public')->delete($categoria->image_path);
            }
            
            $path = $request->file('image')->store('categories', 'public');
            $data['image_path'] = $path;
        }

        $categoria->update($data);

        return redirect()->route('admin.categorias.index')->with('success', 'Categoria atualizada com sucesso!');
    }

    public function destroy(Category $categoria)
    {
        if ($categoria->image_path && Storage::disk('public')->exists($categoria->image_path)) {
            Storage::disk('public')->delete($categoria->image_path);
        }

        $categoria->delete();

        return redirect()->route('admin.categorias.index')->with('success', 'Categoria removida com sucesso!');
    }
}
