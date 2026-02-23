<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\TermoArrematacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class TermoClienteController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $termos = TermoArrematacao::with('lote.leilao')
            ->whereHas('lote', function ($q) use ($user) {
                $q->where('comprador_id', $user->id);
            })
            ->whereIn('status', ['disponibilizado','aceito'])
            ->latest()
            ->paginate(10);
        return Inertia::render('Site/Termos/Index', [
            'termos' => $termos,
        ]);
    }

    public function show(Request $request, TermoArrematacao $termo)
    {
        $this->authorizeAccess($termo);
        return Inertia::render('Site/Termos/Show', [
            'termo' => $termo->load('lote.leilao'),
        ]);
    }

    public function pdf(Request $request, TermoArrematacao $termo)
    {
        $this->authorizeAccess($termo);
        if (!$termo->pdf_path || !Storage::exists($termo->pdf_path)) {
            abort(404, 'PDF não encontrado');
        }
        return response()->file(Storage::path($termo->pdf_path));
    }

    public function download(Request $request, TermoArrematacao $termo)
    {
        $this->authorizeAccess($termo);
        if ($termo->status !== 'aceito') {
            abort(403, 'Download liberado apenas após aceite.');
        }
        if (!$termo->pdf_path || !Storage::exists($termo->pdf_path)) {
            abort(404, 'PDF não encontrado');
        }
        return response()->download(Storage::path($termo->pdf_path));
    }

    public function accept(Request $request, TermoArrematacao $termo)
    {
        $this->authorizeAccess($termo);
        if ($termo->status !== 'disponibilizado') {
            abort(403, 'Termo não está disponível para aceite.');
        }
        $request->validate([
            'aceite' => 'accepted',
        ], [
            'aceite.accepted' => 'É necessário aceitar os termos para prosseguir.',
        ]);
        if ($termo->status === 'aceito') {
            return back()->with('success', 'Termo já aceito anteriormente.');
        }
        $fileHash = null;
        if ($termo->pdf_path && Storage::exists($termo->pdf_path)) {
            $fileHash = hash_file('sha256', Storage::path($termo->pdf_path));
        }
        $payload = [
            'termo_id' => $termo->id,
            'numero' => $termo->numero,
            'lote_id' => $termo->lote_id,
            'leilao_id' => $termo->leilao_id,
            'arrematante_documento' => $termo->arrematante_documento,
            'valor_arrematacao' => (string) $termo->valor_arrematacao,
            'pdf_sha256' => $fileHash,
            'conta' => [
                'nome' => $termo->conta_bancaria_nome,
                'doc' => $termo->conta_bancaria_documento,
                'banco' => $termo->conta_bancaria_banco,
                'agencia' => $termo->conta_bancaria_agencia,
                'conta' => $termo->conta_bancaria_conta,
                'pix' => $termo->conta_bancaria_pix,
            ],
        ];
        $hash = hash('sha256', json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        $termo->aceito_em = now();
        $termo->aceito_ip = $request->ip();
        $termo->aceito_user_agent = substr($request->userAgent() ?? '', 0, 255);
        $termo->hash_integridade = $hash;
        $termo->status = 'aceito';
        $termo->save();

        Log::info('termo_aceito', [
            'termo_id' => $termo->id,
            'lote_id' => $termo->lote_id,
            'leilao_id' => $termo->leilao_id,
            'numero' => $termo->numero,
            'comprador_id' => $termo->lote?->comprador_id,
            'aceito_em' => $termo->aceito_em?->toIso8601String(),
            'aceito_ip' => $termo->aceito_ip,
        ]);

        return back()->with('success', 'Termo aceito com sucesso. Download liberado.');
    }

    public function markViewed(Request $request, TermoArrematacao $termo)
    {
        $this->authorizeAccess($termo);
        if ($termo->status !== 'disponibilizado') {
            return response()->json(['ok' => false], 403);
        }
        if (!$termo->visualizado_em) {
            $ua = $request->userAgent() ?? '';
            $device = str_contains(strtolower($ua), 'mobile') ? 'mobile' : 'web';
            $termo->visualizado_em = now();
            $termo->visualizado_ip = $request->ip();
            $termo->visualizado_user_agent = substr($ua, 0, 255);
            $termo->visualizado_device = $device;
            $termo->save();
            \Log::info('termo_visualizado', [
                'termo_id' => $termo->id,
                'lote_id' => $termo->lote_id,
                'visualizado_em' => $termo->visualizado_em?->toIso8601String(),
                'visualizado_ip' => $termo->visualizado_ip,
                'visualizado_device' => $termo->visualizado_device,
            ]);
        }
        return response()->json(['ok' => true]);
    }

    private function authorizeAccess(TermoArrematacao $termo): void
    {
        $userId = Auth::id();
        $termo->loadMissing('lote');
        if (!$termo->lote || $termo->lote->comprador_id !== $userId) {
            abort(403);
        }
    }
}
