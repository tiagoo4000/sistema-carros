<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ClientDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Lances para a listagem (Paginado)
        $lances = $user->lances()
            ->with(['lote.leilao', 'lote.maiorLance'])
            ->latest()
            ->paginate(10);

        // Compras (Venda Direta) do usuário
        $compras = \App\Models\Lote::with('leilao')
            ->where('comprador_id', $user->id)
            ->latest('comprado_em')
            ->paginate(10, ['*'], 'compras_page');

        // Calcular estatísticas
        $activeBidsCount = $user->lances()
            ->whereHas('lote.leilao', function ($query) {
                $query->where('status', 'ativo');
            })
            ->distinct('lote_id')
            ->count('lote_id');

        $wonLotsCount = $user->lances()
            ->whereHas('lote', function ($query) {
                $query->where('status', 'vendido');
            })
            ->count();

        $pendingTerms = \App\Models\TermoArrematacao::whereHas('lote', function ($q) use ($user) {
                $q->where('comprador_id', $user->id);
            })
            ->whereIn('status', ['disponibilizado'])
            ->count();

        return Inertia::render('Site/MinhaConta/Index', [
            'user' => $user,
            'lances' => $lances,
            'compras' => $compras,
            'stats' => [
                'active_bids' => $activeBidsCount,
                'won_lots' => $wonLotsCount,
                'total_bids' => $user->lances()->count()
            ],
            'pending_terms' => $pendingTerms,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'email' => 'required|email|unique:usuarios,email,' . $user->id,
            'apelido' => 'nullable|string|max:255',
            'rg' => 'nullable|string',
            'orgao_expedidor' => 'nullable|string|max:255',
            'data_nascimento' => 'nullable|date',
            'renda_mensal' => 'nullable|numeric',
            'telefone' => 'nullable|string|max:20',
            'telefone_fixo' => 'nullable|string|max:20',
            'celular2' => 'nullable|string|max:20',
            'ocupacao' => 'nullable|string|max:255',
            'cep' => 'nullable|string|max:9',
            'endereco' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'bairro' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:2',
            'complemento' => 'nullable|string|max:255',
            'interesses' => 'nullable',
            'como_conheceu' => 'nullable|string|max:255',
        ]);
        if (array_key_exists('interesses', $data)) {
            if (is_string($data['interesses'])) {
                $data['interesses'] = collect(explode(',', $data['interesses']))
                    ->map(fn ($s) => trim($s))
                    ->filter()
                    ->values()
                    ->toArray();
            }
        }
        $user->update($data);
        return back()->with('success', 'Dados atualizados com sucesso.');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'current_password' => 'required|string|min:6',
            'new_password' => 'required|string|min:6|confirmed',
        ]);
        if (!Hash::check($data['current_password'], $user->senha)) {
            throw ValidationException::withMessages([
                'current_password' => 'Senha atual incorreta.',
            ]);
        }
        $user->update(['senha' => Hash::make($data['new_password'])]);
        return back()->with('success', 'Senha atualizada com sucesso.');
    }
}
