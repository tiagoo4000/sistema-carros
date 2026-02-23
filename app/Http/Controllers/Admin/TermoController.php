<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TermoArrematacao;
use App\Models\Lote;
use App\Models\ContaBancaria;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class TermoController extends Controller
{
    public function index(Request $request)
    {
        $termos = TermoArrematacao::with(['lote', 'leilao'])->latest()->paginate(20);
        $contas = \App\Models\ContaBancaria::where('ativa', true)
            ->orderByDesc('padrao')
            ->get(['id','banco','tipo_documento','nome_completo','razao_social','cpf','cnpj','chave_pix']);
        return Inertia::render('Admin/Termos/Index', [
            'termos' => $termos,
            'contas' => $contas,
        ]);
    }

    public function generateToday(Request $request)
    {
        $date = now()->toDateString();
        $conta = ContaBancaria::where('ativa', true)->orderByDesc('padrao')->first();
        if (!$conta) {
            Log::warning('sem_conta_bancaria_ativa');
            return back()->with('error', 'Nenhuma conta bancária ativa encontrada.');
        }
        Log::info('conta_bancaria_utilizada', ['conta_id' => $conta->id]);

        $lotes = Lote::with(['leilao', 'comprador'])
            ->whereNotNull('comprador_id')
            ->whereNotNull('valor_compra')
            ->whereDate('comprado_em', $date)
            ->whereDoesntHave('termo')
            ->get();

        if ($lotes->isEmpty()) {
            return back()->with('error', 'Nenhum lote elegível encontrado para hoje (com comprador, valor_compra e sem termo).');
        }

        $gerados = 0;
        foreach ($lotes as $lote) {
            $ok = $this->gerarParaLote($lote, $conta);
            if ($ok) $gerados++;
        }

        if ($gerados === 0) {
            return back()->with('error', '0 termos gerados. Verifique permissões de escrita em storage e logs de PDF no servidor.');
        }
        return back()->with('success', "Termos gerados: {$gerados}");
    }

    public function generatePending(Request $request)
    {
        $conta = ContaBancaria::where('ativa', true)->orderByDesc('padrao')->first();
        if (!$conta) {
            Log::warning('sem_conta_bancaria_ativa');
            return back()->with('error', 'Nenhuma conta bancária ativa encontrada.');
        }
        Log::info('conta_bancaria_utilizada', ['conta_id' => $conta->id]);

        $lotes = Lote::with(['leilao', 'comprador'])
            ->whereNotNull('comprador_id')
            ->whereNotNull('valor_compra')
            ->whereDoesntHave('termo')
            ->get();

        if ($lotes->isEmpty()) {
            return back()->with('error', 'Nenhum lote pendente elegível (com comprador, valor_compra e sem termo).');
        }

        $gerados = 0;
        foreach ($lotes as $lote) {
            $ok = $this->gerarParaLote($lote, $conta);
            if ($ok) $gerados++;
        }
        if ($gerados === 0) {
            return back()->with('error', '0 termos gerados. Verifique permissões de escrita em storage e logs de PDF no servidor.');
        }
        return back()->with('success', "Termos pendentes gerados: {$gerados}");
    }

    private function gerarParaLote($lote, $conta): bool
    {
        if (!$lote->comprador) {
            Log::info('termo_lote_sem_comprador', ['lote_id' => $lote->id]);
            return false;
        }
        if ($lote->termo) {
            Log::info('termo_ignorado_existente', ['lote_id' => $lote->id]);
            return false;
        }
        $user = $lote->comprador;
        $doc = $user->cpf ?: $user->cnpj ?: '';
        $nome = $user->nome ?: $user->razao_social ?: '';
        $cidade = $user->cidade ?: '';
        $email = $user->email ?: null;
        $contaNome = $conta->tipo_documento === 'cpf' ? ($conta->nome_completo ?: '') : ($conta->razao_social ?: '');
        $contaDoc = $conta->tipo_documento === 'cpf' ? ($conta->cpf ?: '') : ($conta->cnpj ?: '');

        $termo = new TermoArrematacao();
        $termo->lote_id = $lote->id;
        $termo->leilao_id = $lote->leilao_id;
        $termo->arrematante_nome = $nome;
        $termo->arrematante_documento = $doc;
        $termo->arrematante_email = $email;
        $termo->arrematante_cidade = $cidade;
        $termo->valor_arrematacao = $lote->valor_compra;
        $termo->conta_bancaria_nome = $contaNome;
        $termo->conta_bancaria_documento = $contaDoc;
        $termo->conta_bancaria_banco = $conta->banco;
        $termo->conta_bancaria_agencia = $conta->agencia;
        $termo->conta_bancaria_conta = $conta->conta;
        $termo->conta_bancaria_pix = $conta->chave_pix;
        $termo->conta_bancaria_qr = $conta->qr_code_pix;
        $termo->save();

        $termo->numero = now()->format('Ymd') . '-' . str_pad((string)$termo->id, 5, '0', STR_PAD_LEFT);
        $html = view('termos.doc', ['termo' => $termo, 'lote' => $lote])->render();
        $termo->conteudo_html = $html;

        $dateFolder = now()->toDateString();
        $folder = 'termos/' . $dateFolder;
        $timestamp = now()->format('YmdHis');
        $filename = 'termo-lote-' . $lote->id . '-' . $timestamp . '.pdf';
        $path = $folder . '/' . $filename;
        try {
            Storage::makeDirectory($folder);
            $pdf = Pdf::loadHTML($html);
            Storage::put($path, $pdf->output());
            $termo->pdf_path = $path;
            $termo->status = 'pendente';
            $termo->save();
            Log::info('termo_pdf_gerado', ['termo_id' => $termo->id, 'path' => $path]);
            Log::info('termo_gerado', ['termo_id' => $termo->id, 'lote_id' => $lote->id]);
            return true;
        } catch (\Throwable $e) {
            Log::warning('termo_geracao_falhou', [
                'lote_id' => $lote->id,
                'erro' => $e->getMessage(),
                'disk' => config('filesystems.default'),
                'path' => $path,
            ]);
            return false;
        }
    }
    public function pdf(TermoArrematacao $termo)
    {
        if (!$termo->pdf_path || !Storage::exists($termo->pdf_path)) {
            return response('PDF não encontrado.', 404);
        }
        return response()->file(Storage::path($termo->pdf_path));
    }

    public function resend(Request $request, TermoArrematacao $termo)
    {
        if (!$termo->arrematante_email) {
            return back()->with('error', 'O termo não possui e-mail do arrematante.');
        }
        if (!$termo->pdf_path || !Storage::exists($termo->pdf_path)) {
            return back()->with('error', 'PDF não encontrado para envio.');
        }
        $to = $termo->arrematante_email;
        try {
            Mail::send([], [], function ($message) use ($to, $termo) {
                $message->to($to)
                    ->subject('Termo de Arrematação')
                    ->text('Segue em anexo seu termo de arrematação.')
                    ->attach(Storage::path($termo->pdf_path));
            });
            $termo->enviado = true;
            $termo->enviado_em = now();
            $termo->save();
            return back()->with('success', 'E-mail reenviado.');
        } catch (\Throwable $e) {
            Log::warning('email_envio_falhou', [
                'termo_id' => $termo->id,
                'erro' => $e->getMessage(),
            ]);
            return back()->with('error', 'Falha ao enviar e-mail. Verifique configuração de e-mail do ambiente.');
        }
    }

    public function destroy(TermoArrematacao $termo)
    {
        if ($termo->pdf_path && Storage::exists($termo->pdf_path)) {
            Storage::delete($termo->pdf_path);
        }
        $termo->delete();
        return back()->with('success', 'Termo removido.');
    }

    public function disponibilizar(Request $request, TermoArrematacao $termo)
    {
        $request->validate([
            'conta_id' => 'required|integer|exists:contas_bancarias,id',
        ]);
        $conta = \App\Models\ContaBancaria::where('id', $request->conta_id)->where('ativa', true)->first();
        if (!$conta) {
            return back()->with('error', 'Conta bancária inválida ou inativa.');
        }
        $lote = $termo->lote ?: Lote::find($termo->lote_id);
        if (!$lote || !$lote->comprador_id) {
            return back()->with('error', 'Lote sem comprador. Não é possível disponibilizar.');
        }
        $termo->conta_bancaria_nome = $conta->tipo_documento === 'cpf' ? ($conta->nome_completo ?: '') : ($conta->razao_social ?: '');
        $termo->conta_bancaria_documento = $conta->tipo_documento === 'cpf' ? ($conta->cpf ?: '') : ($conta->cnpj ?: '');
        $termo->conta_bancaria_banco = $conta->banco;
        $termo->conta_bancaria_agencia = $conta->agencia;
        $termo->conta_bancaria_conta = $conta->conta;
        $termo->conta_bancaria_pix = $conta->chave_pix;
        $termo->conta_bancaria_qr = $conta->qr_code_pix;
        $termo->status = 'disponibilizado';
        $termo->disponibilizado_em = now();
        $termo->save();
        \Log::info('termo_disponibilizado', [
            'termo_id' => $termo->id,
            'conta_id' => $conta->id,
            'lote_id' => $termo->lote_id,
        ]);
        return back()->with('success', 'Termo disponibilizado ao cliente.');
    }
}
