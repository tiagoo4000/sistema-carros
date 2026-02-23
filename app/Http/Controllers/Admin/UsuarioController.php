<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::where('admin', false)
            ->latest()
            ->paginate(10);
        return Inertia::render('Admin/Usuarios/Index', ['usuarios' => $usuarios]);
    }

    public function admins()
    {
        $usuarios = Usuario::where('admin', true)->latest()->paginate(10);
        return Inertia::render('Admin/Usuarios/Index', ['usuarios' => $usuarios]);
    }

    public function edit(Usuario $usuario)
    {
        return Inertia::render('Admin/Usuarios/Edit', [
            'usuario' => $usuario
        ]);
    }

    public function update(Request $request, Usuario $usuario)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('usuarios')->ignore($usuario->id)],
            'cpf' => ['nullable', 'string', Rule::unique('usuarios')->ignore($usuario->id)],
            'telefone' => 'nullable|string',
            'telefone_fixo' => 'nullable|string',
            'celular2' => 'nullable|string',
            'rg' => 'nullable|string',
            'orgao_expedidor' => 'nullable|string|max:255',
            'data_nascimento' => 'nullable|date',
            'renda_mensal' => 'nullable|numeric',
            // Endereço
            'cep' => 'nullable|string|max:9',
            'endereco' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'bairro' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:2',
            'complemento' => 'nullable|string|max:255',
            // Preferências
            'interesses' => 'nullable',
            'como_conheceu' => 'nullable|string|max:255',
            'bloqueado' => 'boolean',
            'documentos_validos' => 'boolean',
            'senha' => 'nullable|string|min:6|confirmed',
        ]);

        $data = $validated;
        if (array_key_exists('interesses', $data)) {
            if (is_string($data['interesses'])) {
                $data['interesses'] = collect(explode(',', $data['interesses']))
                    ->map(fn ($s) => trim($s))
                    ->filter()
                    ->values()
                    ->toArray();
            }
        }
        if (!empty($data['senha'])) {
            $data['senha'] = Hash::make($data['senha']);
        } else {
            unset($data['senha']);
        }

        $usuario->update($data);

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuário atualizado com sucesso.');
    }
}
