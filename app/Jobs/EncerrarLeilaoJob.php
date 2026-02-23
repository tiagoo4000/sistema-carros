<?php

namespace App\Jobs;

use App\Models\Leilao;
use App\Models\SystemConfig;
use App\Models\EmailTemplate;
use App\Events\LeilaoEncerradoEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use App\Services\WhatsAppTemplateRenderer;
use App\Services\TextMeBotService;

class EncerrarLeilaoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $leilaoId;

    /**
     * Create a new job instance.
     */
    public function __construct($leilaoId)
    {
        $this->leilaoId = $leilaoId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $leilao = Leilao::find($this->leilaoId);

        if (!$leilao || $leilao->status !== 'ativo') {
            return;
        }

        // Verifica se a data fim já passou
        if (now()->lt($leilao->data_fim)) {
            // Se ainda não passou (ex: foi estendido por anti-sniper), reagenda o job
            self::dispatch($this->leilaoId)->delay($leilao->data_fim);
            return;
        }

        DB::transaction(function () use ($leilao) {
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
                    continue;
                }

                $usuario = $maiorLance->usuario()->first();
                if (!$usuario) {
                    continue;
                }

                $valorArremate = (float)$maiorLance->valor;
                $comissaoValor = round($valorArremate * ($percentual / 100), 2);
                $valorTotal = $valorArremate + $comissaoValor;
                $dataLimite = now()->addDays($prazoDias)->startOfDay();

                // Marca o lote como arrematado e registra comprador
                $lote->status = 'arrematado';
                $lote->comprador_id = $usuario->id;
                $lote->valor_compra = $valorArremate;
                $lote->comprado_em = now();
                $lote->save();

                \Log::info('lote_arrematado_job_encerrar', [
                    'leilao_id' => $leilao->id,
                    'lote_id' => $lote->id,
                    'valor_arremate' => $valorArremate
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
                    \Log::warning('wa_lot_won_job_error', ['lote_id' => $lote->id, 'erro' => $e->getMessage()]);
                }
            }
        });

        broadcast(new LeilaoEncerradoEvent($leilao));
        
        // Notificação ao vivo já emitida; processamento adicional ocorre via painel admin.
    }
}
