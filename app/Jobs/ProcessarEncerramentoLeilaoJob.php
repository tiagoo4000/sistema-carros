<?php

namespace App\Jobs;

use App\Events\LeilaoEncerradoEvent;
use App\Models\Leilao;
use App\Models\SystemConfig;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\WhatsAppTemplateRenderer;
use App\Services\TextMeBotService;

class ProcessarEncerramentoLeilaoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $leilao;

    public function __construct(Leilao $leilao)
    {
        $this->leilao = $leilao;
    }

    public function handle(): void
    {
        // Re-check conditions to avoid race conditions
        $this->leilao->refresh();

        if ($this->leilao->status !== 'ativo') {
            return;
        }

        if ($this->leilao->data_fim->isFuture()) {
            // Se por algum motivo a data foi estendida e o job executou antes
            return;
        }

        DB::transaction(function () {
            // Lock for update to prevent concurrent processing
            $leilao = Leilao::where('id', $this->leilao->id)->lockForUpdate()->first();

            if ($leilao->status !== 'ativo') {
                return;
            }

            $leilao->status = 'finalizado';
            $leilao->save();

            $percentConfig = optional(SystemConfig::where('key', 'comissao_percentual')->first())->value;
            $prazoDiasConfig = optional(SystemConfig::where('key', 'prazo_pagamento_dias')->first())->value;
            $percentual = is_null($percentConfig) ? 5.0 : (float)$percentConfig;
            $prazoDias = is_null($prazoDiasConfig) ? 2 : (int)$prazoDiasConfig;

            $lotes = $leilao->lotes()->with(['lances' => function ($q) {
                $q->where(function ($qq) {
                    $qq->where('is_fake', false)->orWhereNull('is_fake');
                })->orderByDesc('valor');
            }])->get();

            foreach ($lotes as $lote) {
                $maiorLance = $lote->lances->first();
                if (!$maiorLance) {
                \Log::info('lote_sem_lances', [
                    'leilao_id' => $leilao->id,
                    'lote_id' => $lote->id,
                    'motivo' => 'sem_lances_validos'
                ]);
                continue;
                }

                $usuario = $maiorLance->usuario()->first();
                if (!$usuario) {
                \Log::info('lote_sem_arrematante', [
                    'leilao_id' => $leilao->id,
                    'lote_id' => $lote->id,
                    'lance_vencedor_id' => $maiorLance->id,
                    'motivo' => 'usuario_nao_encontrado'
                ]);
                continue;
                }

                $valorArremate = (float)$maiorLance->valor;
                $comissaoValor = round($valorArremate * ($percentual / 100), 2);
                $valorTotal = $valorArremate + $comissaoValor;
                $dataLimite = now()->addDays($prazoDias)->startOfDay();

                $lote->status = 'arrematado';
                $lote->comprador_id = $usuario->id;
                $lote->valor_compra = $valorArremate;
                $lote->comprado_em = now();
                $lote->save();

                \Log::info('lote_arrematado_job_processar', [
                    'leilao_id' => $leilao->id,
                    'lote_id' => $lote->id,
                    'valor_arremate' => $valorArremate,
                    'lance_vencedor_id' => $maiorLance->id,
                    'usuario_vencedor_id' => $usuario->id
                ]);

                try {
                    $waEnabled = SystemConfig::where('key', 'textmebot_enabled')->value('value') === '1';
                    $phone = $usuario->telefone ?? null;
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
                            $text = $renderer->render($tpl, [
                                'nome_cliente' => $usuario->nome ?? 'Cliente',
                                'email_cliente' => $usuario->email ?? null,
                                'nome_lote' => $lote->titulo,
                                'numero_lote' => $lote->ordem ?? (string)$lote->id,
                                'valor_arremate' => 'R$ ' . number_format($valorArremate, 2, ',', '.'),
                                'link_lote' => route('lotes.show', $lote->id),
                            ])['text'];
                            $svc = app(TextMeBotService::class);
                            $svc->send($phone, $text);
                        }
                    }
                } catch (\Throwable $e) {
                    Log::warning('wa_lot_won_job_error', ['lote_id' => $lote->id, 'erro' => $e->getMessage()]);
                }
            }
            
            // Dispara evento de encerramento
            event(new LeilaoEncerradoEvent($leilao));
            
            Log::info("Leilão {$leilao->id} encerrado com sucesso.");
        });
    }
}
