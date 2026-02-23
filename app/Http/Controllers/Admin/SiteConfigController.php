<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SystemConfig;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class SiteConfigController extends Controller
{
    public function index()
    {
        $configs = SystemConfig::all()->pluck('value', 'key');
        
        return Inertia::render('Admin/Config/Site', [
            'configs' => $configs
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name' => 'nullable|string|max:255',
            'company_location' => 'nullable|string|max:255',
            'patio_location' => 'nullable|string|max:255',
            'cnpj' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'textmebot_enabled' => 'nullable|boolean',
            'textmebot_api_key' => 'nullable|string|max:255',
            'site_logo' => 'nullable|image|max:2048', // 2MB max
            'site_favicon' => 'nullable|image|mimes:ico,png,jpg,svg|max:1024', // 1MB max
        ]);

        $keys = [
            'site_name',
            'company_location',
            'patio_location',
            'cnpj',
            'whatsapp',
            'phone',
            'email',
            'textmebot_api_key',
        ];

        foreach ($keys as $key) {
            SystemConfig::updateOrCreate(
                ['key' => $key],
                ['value' => $request->input($key)]
            );
        }

        // Campo booleano tratado à parte para garantir '1'/'0'
        SystemConfig::updateOrCreate(
            ['key' => 'textmebot_enabled'],
            ['value' => $request->boolean('textmebot_enabled') ? '1' : '0']
        );

        if ($request->hasFile('site_logo')) {
            $path = $request->file('site_logo')->store('uploads/logos', 'public');
            SystemConfig::updateOrCreate(
                ['key' => 'site_logo'],
                ['value' => '/storage/' . $path]
            );
        }

        if ($request->hasFile('site_favicon')) {
            $path = $request->file('site_favicon')->store('uploads/favicons', 'public');
            SystemConfig::updateOrCreate(
                ['key' => 'site_favicon'],
                ['value' => '/storage/' . $path]
            );
        }

        return redirect()->back()->with('success', 'Configurações atualizadas com sucesso!');
    }
}
