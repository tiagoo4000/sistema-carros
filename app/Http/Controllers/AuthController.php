<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use App\Events\EventoNovoCadastro;

class AuthController extends Controller
{
    public function create()
    {
        return Inertia::render('Auth/Register');
    }

    public function store(Request $request)
    {
        $tipo = $request->input('tipo_pessoa', 'pf');

        $baseRules = [
            'tipo_pessoa' => 'required|in:pf,pj',
            'email' => 'required|string|email|max:255|unique:usuarios',
            'senha' => ['required', 'confirmed', Rules\Password::defaults()],
            // Endere���o
            'cep' => 'nullable|string|max:9',
            'endereco' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'bairro' => 'nullable|string|max:120',
            'estado' => 'nullable|string|size:2',
            'cidade' => 'nullable|string|max:120',
            'complemento' => 'nullable|string|max:120',
            // Prefer���ncias
            'interesses' => 'nullable|array',
            'interesses.*' => 'string|in:veiculos,motos,onibus,caminhoes,imoveis,equipamentos',
            'como_conheceu' => 'nullable|string|in:google,facebook,instagram,radio,internet,indicacao,outros',
            'aceite_termos' => 'accepted',
        ];

        if ($tipo === 'pf') {
            $rules = array_merge($baseRules, [
                'nome' => 'required|string|max:255',
                'apelido' => 'nullable|string|max:60',
                'cpf' => 'required|string|regex:/^\\d{3}\\.\\d{3}\\.\\d{3}-\\d{2}$/|unique:usuarios,cpf',
                'rg' => 'nullable|string|max:20',
                'orgao_expedidor' => 'nullable|string|max:20',
                'data_nascimento' => 'nullable|date',
                'renda_mensal' => 'nullable|numeric|min:0',
                'telefone' => 'required|string|max:20',
                'celular2' => 'nullable|string|max:20',
                'telefone_fixo' => 'nullable|string|max:20',
                'ocupacao' => 'nullable|string|max:120',
            ]);
        } else {
            $rules = array_merge($baseRules, [
                'razao_social' => 'required|string|max:255',
                'nome_fantasia' => 'nullable|string|max:255',
                'cnpj' => 'required|string|regex:/^\\d{2}\\.\\d{3}\\.\\d{3}\\/\\d{4}-\\d{2}$/|unique:usuarios,cnpj',
                'inscricao_estadual' => 'nullable|string|max:30',
                'data_abertura' => 'nullable|date',
                'faturamento_mensal' => 'nullable|numeric|min:0',
                'telefone_comercial' => 'required|string|max:20',
                'responsavel_nome' => 'required|string|max:255',
                'responsavel_cpf' => 'required|string|regex:/^\\d{3}\\.\\d{3}\\.\\d{3}-\\d{2}$/',
            ]);
        }

        $validated = $request->validate($rules);

        $payload = [
            'email' => $validated['email'],
            'senha' => Hash::make($validated['senha']),
            'admin' => false,
            'bloqueado' => false,
            'documentos_validos' => false,
            'status_cadastro' => 'aguardando_documentacao',
            'tipo_pessoa' => $tipo,
            // endere���o e prefer���ncias
            'cep' => $validated['cep'] ?? null,
            'endereco' => $validated['endereco'] ?? null,
            'numero' => $validated['numero'] ?? null,
            'bairro' => $validated['bairro'] ?? null,
            'estado' => $validated['estado'] ?? null,
            'cidade' => $validated['cidade'] ?? null,
            'complemento' => $validated['complemento'] ?? null,
            'interesses' => $validated['interesses'] ?? [],
            'como_conheceu' => $validated['como_conheceu'] ?? null,
        ];
        if ($tipo === 'pf') {
            $payload = array_merge($payload, [
                'nome' => $validated['nome'],
                'apelido' => $validated['apelido'] ?? null,
                'cpf' => $validated['cpf'],
                'rg' => $validated['rg'] ?? null,
                'orgao_expedidor' => $validated['orgao_expedidor'] ?? null,
                'data_nascimento' => $validated['data_nascimento'] ?? null,
                'renda_mensal' => $validated['renda_mensal'] ?? null,
                'telefone' => $validated['telefone'],
                'celular2' => $validated['celular2'] ?? null,
                'telefone_fixo' => $validated['telefone_fixo'] ?? null,
                'ocupacao' => $validated['ocupacao'] ?? null,
            ]);
        } else {
            $payload = array_merge($payload, [
                'nome' => $validated['razao_social'],
                'razao_social' => $validated['razao_social'],
                'nome_fantasia' => $validated['nome_fantasia'] ?? null,
                'cnpj' => $validated['cnpj'],
                'inscricao_estadual' => $validated['inscricao_estadual'] ?? null,
                'data_abertura' => $validated['data_abertura'] ?? null,
                'faturamento_mensal' => $validated['faturamento_mensal'] ?? null,
                'telefone_comercial' => $validated['telefone_comercial'],
                'responsavel_nome' => $validated['responsavel_nome'],
                'responsavel_cpf' => $validated['responsavel_cpf'],
            ]);
        }

        $usuario = Usuario::create($payload);

        event(new EventoNovoCadastro($usuario->id));

        Auth::login($usuario);

        return redirect()->route('minha-conta.documentos')
            ->with('success', 'Cadastro realizado com sucesso. Envie seus documentos para concluir a valida������o.');
    }

    public function login()
    {
        return Inertia::render('Auth/Login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->boolean('remember'))) {
            $request->session()->regenerate();

            if (Auth::user()->admin) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'As credenciais fornecidas n���o correspondem aos nossos registros.',
        ])->onlyInput('email');
    }

    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
