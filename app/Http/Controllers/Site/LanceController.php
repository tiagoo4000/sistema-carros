<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Lote;
use App\Actions\RegistrarLanceAction;
use Illuminate\Http\Request;
use Exception;
use App\Services\FinalizarLoteService;

class LanceController extends Controller
{
    public function store(Request $request, Lote $lote, RegistrarLanceAction $action, FinalizarLoteService $finalizar)
    {
        $lote->load('leilao');
        if ($lote->leilao && $lote->leilao->tipo === 'venda_direta') {
            return back()->withErrors(['valor' => 'Lotes de venda direta não aceitam lances.']);
        }

        if ($request->user()->isAdmin()) {
            return back()->withErrors(['valor' => 'Administradores não podem dar lances.']);
        }

        $user = $request->user();
        if (!($user->documentos_validos || $user->verificacao_forcada)) {
            $required = ['rg', 'selfie', 'comprovante_residencia'];
            $docs = $user->documentos()->whereIn('tipo', $required)->get()->groupBy('tipo');
            foreach ($required as $t) {
                if (!$docs->has($t) || !$docs[$t]->firstWhere('validado', true)) {
                    return back()->withErrors([
                        'valor' => 'Para participar dos leilões é necessário enviar e aguardar aprovação dos seus documentos.'
                    ]);
                }
            }
        }

        $request->validate([
            'valor' => 'required|numeric|min:0',
        ]);

        try {
            // Validação de expiração antes de aceitar lance
            if ($finalizar->checkAndFinalize($lote)) {
                return back()->withErrors(['valor' => 'Leilão encerrado para este lote.']);
            }
            $action->execute($request->user(), $lote, $request->valor);
            return back()->with('success', 'Lance registrado com sucesso!');
        } catch (Exception $e) {
            return back()->withErrors(['valor' => $e->getMessage()]);
        }
    }
}
