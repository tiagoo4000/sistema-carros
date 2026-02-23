<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SystemConfig;
use Inertia\Inertia;

class SystemConfigController extends Controller
{
    public function index()
    {
        $layout = SystemConfig::where('key', 'layout_active')->first()->value ?? 'casa';
        
        return Inertia::render('Admin/Config/Layout', [
            'currentLayout' => $layout
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'layout' => 'required|in:casa,carro'
        ]);

        SystemConfig::updateOrCreate(
            ['key' => 'layout_active'],
            ['value' => $request->layout]
        );

        return redirect()->back()->with('success', 'Layout atualizado com sucesso!');
    }
}
