<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('order')->orderBy('created_at', 'desc')->get();
        return Inertia::render('Admin/Banners/Index', [
            'banners' => $banners
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Banners/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120', // 5MB
            'type' => 'required|in:hero,middle,footer',
            'status' => 'required|in:ativo,inativo',
            'title' => 'nullable|string|max:255',
            'link' => 'nullable|url|max:255',
            'order' => 'nullable|integer'
        ]);

        $path = $request->file('image')->store('uploads/banners', 'public');

        Banner::create([
            'image_path' => '/storage/' . $path,
            'type' => $request->type,
            'status' => $request->status,
            'title' => $request->title,
            'link' => $request->link,
            'order' => $request->order ?? 0,
        ]);

        return redirect()->route('admin.layout.banners.index')->with('success', 'Banner criado com sucesso!');
    }

    public function edit(Banner $banner)
    {
        return Inertia::render('Admin/Banners/Edit', [
            'banner' => $banner
        ]);
    }

    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'image' => 'nullable|image|max:5120',
            'type' => 'required|in:hero,middle,footer',
            'status' => 'required|in:ativo,inativo',
            'title' => 'nullable|string|max:255',
            'link' => 'nullable|url|max:255',
            'order' => 'nullable|integer'
        ]);

        $data = [
            'type' => $request->type,
            'status' => $request->status,
            'title' => $request->title,
            'link' => $request->link,
            'order' => $request->order ?? 0,
        ];

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($banner->image_path) {
                $oldPath = str_replace('/storage/', '', $banner->image_path);
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('image')->store('uploads/banners', 'public');
            $data['image_path'] = '/storage/' . $path;
        }

        $banner->update($data);

        return redirect()->route('admin.layout.banners.index')->with('success', 'Banner atualizado com sucesso!');
    }

    public function destroy(Banner $banner)
    {
        if ($banner->image_path) {
            $oldPath = str_replace('/storage/', '', $banner->image_path);
            Storage::disk('public')->delete($oldPath);
        }
        
        $banner->delete();

        return redirect()->route('admin.layout.banners.index')->with('success', 'Banner excluído com sucesso!');
    }
}
