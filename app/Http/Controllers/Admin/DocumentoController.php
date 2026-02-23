<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Documento;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class DocumentoController extends Controller
{
    public function index()
    {
        $documentos = Documento::with('usuario')->latest()->paginate(20);
        return Inertia::render('Admin/Documentos/Index', [
            'documentos' => $documentos
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Documentos/Create', [
            'usuarios' => Usuario::orderBy('nome')->get(['id', 'nome', 'email'])
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'usuario_id' => 'required|exists:usuarios,id',
            'tipo' => 'required|string|in:rg,cnh,selfie,comprovante_residencia,outros',
            'arquivo' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'observacoes' => 'nullable|string'
        ]);

        $path = $request->file('arquivo')->store('documentos', 'local');

        $doc = Documento::create([
            'usuario_id' => $request->usuario_id,
            'tipo' => $request->tipo,
            'caminho_arquivo' => $path,
            'validado' => true, // Upload pelo admin já considera validado
            'observacoes' => $request->observacoes
        ]);

        $this->recalcularStatusUsuario($doc->usuario);

        return redirect()->route('admin.documentos.index')->with('success', 'Documento enviado com sucesso.');
    }

    public function validar(Request $request, Documento $documento)
    {
        $documento->update(['validado' => true, 'observacoes' => null]);
        $this->recalcularStatusUsuario($documento->usuario);

        return back()->with('success', 'Documento validado com sucesso.');
    }

    public function rejeitar(Request $request, Documento $documento)
    {
        $request->validate([
            'motivo' => 'nullable|string|max:500'
        ]);
        $documento->update([
            'validado' => false,
            'observacoes' => $request->motivo
        ]);
        $this->recalcularStatusUsuario($documento->usuario);
        return back()->with('success', 'Documento rejeitado com sucesso.');
    }

    protected function recalcularStatusUsuario(\App\Models\Usuario $usuario): void
    {
        $docsAll = $usuario->documentos()->get()->groupBy('tipo');
        $docFotoValid = ($docsAll->has('rg') && $docsAll['rg']->firstWhere('validado', true))
            || ($docsAll->has('cnh') && $docsAll['cnh']->firstWhere('validado', true));
        $selfieValid = $docsAll->has('selfie') && $docsAll['selfie']->firstWhere('validado', true);
        $compValid = $docsAll->has('comprovante_residencia') && $docsAll['comprovante_residencia']->firstWhere('validado', true);
        $ok = $docFotoValid && $selfieValid && $compValid;
        $usuario->update(['documentos_validos' => $ok]);
    }

    public function download(\App\Models\Documento $documento)
    {
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
