<?php

namespace App\Services;

use App\Models\Lote;
use App\Models\Leilao;
use App\Models\EmailTemplate;
use App\Models\ContaBancaria;
use App\Models\TermoArrematacao;
use App\Models\SystemConfig;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\WhatsAppTemplateRenderer;
use App\Services\TextMeBotService;

class FinalizarLoteService
{
    public function checkAndFinalize(Lote $lote): bool
    {
        $lote->loadMissing(['leilao', 'maiorLance.usuario']);
        if (!in_array($lote->status, ['aberto', 'dou_lhe_1', 'dou_lhe_2'])) {
            return false; // já finalizado/vendido
        }
        $endsAt = $lote->ends_at;
        if (!$endsAt) {
            return false;
        }
        if (now()->lt($endsAt)) {
            return false;
        }
        return $this->finalize($lote);
    }

    public function finalize(Lote $lote): bool
    {
        return DB::transaction(function () use ($lote) {
            $locked = Lote::whereKey($lote->id)->lockForUpdate()->first();
            if (!in_array($locked->status, ['aberto', 'dou_lhe_1', 'dou_lhe_2'])) {
                return false; // idempotência
            }

            $maiorLance = $locked->lances()->orderBy('valor', 'desc')->first();
            $podeVender = false;
            if ($maiorLance) {
                $valorReserva = $locked->getAttribute('valor_reserva');
                if (!is_null($valorReserva)) {
                    $podeVender = $maiorLance->valor >= $valorReserva;
                } else {
                    $podeVender = true;
                }
            }

            if ($maiorLance && $podeVender) {
                $locked->status = 'vendido';
                $locked->comprador_id = $maiorLance->usuario_id;
                $locked->valor_compra = $maiorLance->valor;
                $locked->comprado_em = now();
            } else {
                // Sem lances válidos (ou reserva não atingida)
                $locked->status = 'sem_lances';
                $locked->comprador_id = null;
                $locked->valor_compra = null;
                $locked->comprado_em = null;
            }
            $locked->save();

            event(new \App\Events\LoteStatusUpdated($locked));

            if ($maiorLance && $podeVender) {
                try {
                    $waEnabled = SystemConfig::where('key', 'textmebot_enabled')->value('value') === '1';
                    $comprador = $locked->comprador()->first();
                    $phone = $comprador?->telefone;
                    if ($waEnabled && $phone) {
                        $tpl = EmailTemplate::firstOrCreate(
                            ['key' => 'wa_lot_won'],
                            [
                                'name' => 'Lote Arrematado (WhatsApp)',
                                'text' => 'Parabéns {{nome_cliente}}! Você arrematou o lote {{numero_lote}} ({{nome_lote}}) por {{valor_arremate}}.',
                                'enabled' => true,
                            ]
                        );
                        if ($tpl->enabled) {
                            $renderer = app(WhatsAppTemplateRenderer::class);
                            $context = [
                                'nome_cliente' => $comprador->nome ?? 'Cliente',
                                'email_cliente' => $comprador->email ?? null,
                                'nome_lote' => $locked->titulo,
                                'numero_lote' => $locked->ordem ?? (string)$locked->id,
                                'valor_arremate' => 'R$ ' . number_format((float)$locked->valor_compra, 2, ',', '.'),
                                'link_lote' => route('lotes.show', $locked->id),
                            ];
                            $text = $renderer->render($tpl, $context)['text'];
                            $svc = app(TextMeBotService::class);
                            $svc->send($phone, $text);
                        }
                    }
                } catch (\Throwable $e) {
                    Log::warning('wa_lot_won_error', ['lote_id' => $locked->id, 'erro' => $e->getMessage()]);
                }
                $this->gerarTermo($locked);
            }

            // Atualizar Leilão se não houver mais lotes abertos e data_fim já passou
            $leilao = $locked->leilao()->lockForUpdate()->first();
            $this->finalizeLeilaoIfCompleted($leilao);
            return true;
        });
    }

    public function finalizeLeilaoIfCompleted(?Leilao $leilao): bool
    {
        if (!$leilao) return false;
        return DB::transaction(function () use ($leilao) {
            $lockedLeilao = Leilao::whereKey($leilao->id)->lockForUpdate()->first();
            if (!$lockedLeilao || $lockedLeilao->status === 'finalizado') {
                return false;
            }
            $open = $lockedLeilao->lotes()
                ->whereIn('status', ['aberto', 'dou_lhe_1', 'dou_lhe_2'])
                ->count();
            if ($open === 0 && ($lockedLeilao->data_fim ? now()->gte($lockedLeilao->data_fim) : true)) {
                $lockedLeilao->status = 'finalizado';
                $lockedLeilao->save();
                event(new \App\Events\LeilaoEncerradoEvent($lockedLeilao));
                return true;
            }
            return false;
        });
    }

    protected function gerarTermo(Lote $lote): bool
    {
        try {
            if ($lote->termo) {
                Log::info('termo_ignorado_existente', ['lote_id' => $lote->id]);
                return false;
            }
            $conta = ContaBancaria::where('ativa', true)->orderByDesc('padrao')->first();
            if (!$conta) {
                Log::warning('sem_conta_bancaria_ativa');
                return false;
            }
            $user = $lote->comprador()->first();
            if (!$user) {
                Log::info('termo_lote_sem_comprador', ['lote_id' => $lote->id]);
                return false;
            }

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
            Log::warning('termo_geracao_falhou', ['lote_id' => $lote->id, 'erro' => $e->getMessage()]);
            return false;
        }
    }
}
