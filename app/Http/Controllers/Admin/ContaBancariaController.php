<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContaBancaria;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class ContaBancariaController extends Controller
{
    public function index()
    {
        if (!Schema::hasTable('contas_bancarias')) {
            return Inertia::render('Admin/ContasBancarias/Index', [
                'contas' => [
                    'data' => [],
                    'current_page' => 1,
                    'last_page' => 1,
                ],
                'missing' => ['contas_bancarias'],
            ]);
        }
        return Inertia::render('Admin/ContasBancarias/Index', [
            'contas' => ContaBancaria::orderByDesc('padrao')->orderBy('banco')->paginate(20),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/ContasBancarias/Create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tipo_documento' => 'required|in:cpf,cnpj',
            'nome_completo' => 'nullable|string|max:255',
            'cpf' => 'nullable|string|max:20',
            'razao_social' => 'nullable|string|max:255',
            'cnpj' => 'nullable|string|max:30',
            'banco' => 'required|string|max:255',
            'agencia' => 'required|string|max:50',
            'conta' => 'required|string|max:50',
            'tipo_conta' => 'required|in:corrente,poupanca',
            'chave_pix' => 'nullable|string|max:255',
            'qr_code_pix' => 'nullable|image|max:2048',
            'ativa' => 'required|boolean',
            'padrao' => 'required|boolean',
        ]);

        if ($data['tipo_documento'] === 'cpf') {
            $data['razao_social'] = null;
            $data['cnpj'] = null;
        } else {
            $data['nome_completo'] = null;
            $data['cpf'] = null;
        }

        if ($data['padrao']) {
            ContaBancaria::query()->update(['padrao' => false]);
        }

        if ($request->hasFile('qr_code_pix')) {
            $data['qr_code_pix'] = $request->file('qr_code_pix')->store('contas/qr');
        }

        ContaBancaria::create($data);

        return redirect()->route('admin.contas.index')->with('success', 'Conta bancária cadastrada.');
    }

    public function edit(ContaBancaria $conta)
    {
        return Inertia::render('Admin/ContasBancarias/Edit', [
            'conta' => $conta,
        ]);
    }

    public function update(Request $request, ContaBancaria $conta)
    {
        $data = $request->validate([
            'tipo_documento' => 'required|in:cpf,cnpj',
            'nome_completo' => 'nullable|string|max:255',
            'cpf' => 'nullable|string|max:20',
            'razao_social' => 'nullable|string|max:255',
            'cnpj' => 'nullable|string|max:30',
            'banco' => 'required|string|max:255',
            'agencia' => 'required|string|max:50',
            'conta' => 'required|string|max:50',
            'tipo_conta' => 'required|in:corrente,poupanca',
            'chave_pix' => 'nullable|string|max:255',
            'qr_code_pix' => 'nullable|image|max:2048',
            'ativa' => 'required|boolean',
            'padrao' => 'required|boolean',
        ]);

        if ($data['tipo_documento'] === 'cpf') {
            $data['razao_social'] = null;
            $data['cnpj'] = null;
        } else {
            $data['nome_completo'] = null;
            $data['cpf'] = null;
        }

        if ($data['padrao']) {
            ContaBancaria::where('id', '!=', $conta->id)->update(['padrao' => false]);
        }

        if ($request->hasFile('qr_code_pix')) {
            if ($conta->qr_code_pix && Storage::exists($conta->qr_code_pix)) {
                Storage::delete($conta->qr_code_pix);
            }
            $data['qr_code_pix'] = $request->file('qr_code_pix')->store('contas/qr');
        }

        $conta->update($data);

        return redirect()->route('admin.contas.index')->with('success', 'Conta bancária atualizada.');
    }

    public function destroy(ContaBancaria $conta)
    {
        $conta->delete();
        return back()->with('success', 'Conta removida.');
    }

    public function definirPadrao(ContaBancaria $conta)
    {
        ContaBancaria::query()->update(['padrao' => false]);
        $conta->update(['padrao' => true]);
        return back()->with('success', 'Conta definida como padrão.');
    }
}
