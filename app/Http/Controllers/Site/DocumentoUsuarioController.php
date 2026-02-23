<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use App\Models\Documento;
use App\Models\LogAuditoria;

class DocumentoUsuarioController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user()->load('documentos');
        $required = ['rg', 'cnh', 'selfie', 'comprovante_residencia'];
        $docs = $user->documentos()->whereIn('tipo', $required)->get()->groupBy('tipo');
        $status = [];
        foreach ($required as $t) {
            $status[$t] = $docs->has($t) ? $docs[$t]->first() : null;
        }

        $sentDocFoto = ($docs->has('rg') && $docs['rg']->count() > 0) || ($docs->has('cnh') && $docs['cnh']->count() > 0);
        $sentSelfie = $docs->has('selfie') && $docs['selfie']->count() > 0;
        $sentComp = $docs->has('comprovante_residencia') && $docs['comprovante_residencia']->count() > 0;
        $allSent = $sentDocFoto && $sentSelfie && $sentComp;

        $docFotoValid = ($docs->has('rg') && $docs['rg']->firstWhere('validado', true))
            || ($docs->has('cnh') && $docs['cnh']->firstWhere('validado', true));
        $selfieValid = $docs->has('selfie') && $docs['selfie']->firstWhere('validado', true);
        $compValid = $docs->has('comprovante_residencia') && $docs['comprovante_residencia']->firstWhere('validado', true);

        $hasRejected = false;
        $rejectionReason = null;
        foreach (['rg','cnh','selfie','comprovante_residencia'] as $t) {
            if ($docs->has($t)) {
                foreach ($docs[$t] as $d) {
                    if ($d->validado === false && $d->observacoes) {
                        $hasRejected = true;
                        $rejectionReason = $d->observacoes;
                        break 2;
                    }
                }
            }
        }

        if ($user->verificacao_forcada || $user->documentos_validos || ($docFotoValid && $selfieValid && $compValid)) {
            $overall = 'approved';
        } elseif ($hasRejected) {
            $overall = 'rejected';
        } elseif ($allSent) {
            $overall = 'pending';
        } else {
            $overall = 'none';
        }

        $approvalLog = LogAuditoria::whereIn('acao', ['verificacao_aprovada', 'verificacao_forcada'])
            ->where('dados->usuario_verificado_id', $user->id)
            ->with('usuario')
            ->latest()
            ->first();

        return Inertia::render('Site/MinhaConta/Verificacao', [
            'status' => $status,
            'overallStatus' => $overall,
            'rejection_reason' => $rejectionReason,
            'approval' => $approvalLog ? [
                'approved_at' => $approvalLog->created_at,
                'approved_by' => optional($approvalLog->usuario)->nome,
            ] : null,
        ]);
    }

    public function meus(Request $request)
    {
        $user = $request->user();
        $docs = $user->documentos()->latest()->paginate(10)->through(function ($d) {
            return [
                'id' => $d->id,
                'tipo' => $d->tipo,
                'validado' => (bool) $d->validado,
                'observacoes' => $d->observacoes,
                'created_at' => $d->created_at,
            ];
        });
        return Inertia::render('Site/MinhaConta/MeusDocumentos', [
            'documentos' => $docs
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user()->load('documentos');
        if ($user->verificacao_forcada) {
            return back()->withErrors(['status' => 'Envio bloqueado: sua conta foi aprovada por decisão administrativa. Envio de novos documentos pelo site está desativado.']);
        }
        if ($user->documentos_validos) {
            return back()->withErrors(['status' => 'Envio bloqueado: sua verificação já está aprovada.']);
        }
        $docs = $user->documentos->groupBy('tipo');
        $sentDocFoto = ($docs->has('rg') && $docs['rg']->count() > 0) || ($docs->has('cnh') && $docs['cnh']->count() > 0);
        $sentSelfie = $docs->has('selfie') && $docs['selfie']->count() > 0;
        $sentComp = $docs->has('comprovante_residencia') && $docs['comprovante_residencia']->count() > 0;
        $allSent = $sentDocFoto && $sentSelfie && $sentComp;
        $docFotoValid = ($docs->has('rg') && $docs['rg']->firstWhere('validado', true))
            || ($docs->has('cnh') && $docs['cnh']->firstWhere('validado', true));
        $selfieValid = $docs->has('selfie') && $docs['selfie']->firstWhere('validado', true);
        $compValid = $docs->has('comprovante_residencia') && $docs['comprovante_residencia']->firstWhere('validado', true);
        $hasRejected = false;
        foreach (['rg','cnh','selfie','comprovante_residencia'] as $t) {
            if ($docs->has($t)) {
                foreach ($docs[$t] as $d) {
                    if ($d->validado === false && $d->observacoes) { $hasRejected = true; break 2; }
                }
            }
        }
        $overall = $docFotoValid && $selfieValid && $compValid ? 'approved' : ($hasRejected ? 'rejected' : ($allSent ? 'pending' : 'none'));
        if (in_array($overall, ['approved', 'pending'])) {
            return back()->withErrors(['status' => 'Envio bloqueado: sua verificação está '.$overall.'. Aguarde a análise do administrador.']);
        }

        $request->validate([
            'tipo' => 'required|string|in:rg,cnh,selfie,comprovante_residencia',
            'arquivo' => [
                'required',
                'file',
                'max:8192',
                'mimetypes:image/jpeg,image/png,image/webp,image/heic,image/heif,application/pdf'
            ],
        ]);
        $path = $request->file('arquivo')->store('documentos', 'local');
        Documento::create([
            'usuario_id' => $user->id,
            'tipo' => $request->tipo,
            'caminho_arquivo' => $path,
            'validado' => false,
            'observacoes' => null,
        ]);
        return back()->with('success', 'Documento enviado com sucesso. Aguarde a validação.');
    }

    public function download(Request $request, Documento $documento)
    {
        abort_unless($documento->usuario_id === $request->user()->id, 403);
        $pathLocal = storage_path('app/' . $documento->caminho_arquivo);
        if (file_exists($pathLocal)) {
            return response()->download($pathLocal);
        }
        $pathPublic = storage_path('app/public/' . $documento->caminho_arquivo);
        if (file_exists($pathPublic)) {
            return response()->download($pathPublic);
        }
        abort(404);
    }
}
