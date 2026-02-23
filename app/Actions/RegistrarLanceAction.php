<?php

namespace App\Actions;

use App\Models\Lance;
use App\Models\Lote;
use App\Models\Usuario;
use App\Models\EmailTemplate;
use App\Models\SystemConfig;
use App\Services\EmailTemplateRenderer;
use App\Services\WhatsAppTemplateRenderer;
use App\Services\TextMeBotService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Exception;

class RegistrarLanceAction
{
    public function execute(Usuario $usuario, Lote $lote, float $valor)
    {
        $leilao = $lote->leilao()->firstOrFail();
        
        // 1. Validar janelas de tempo (não depende de status manual)
        $now = now();
        $start = $leilao->data_inicio;
        $end = $lote->ends_at ?: $leilao->data_fim;
        if ($start && $now->lt($start)) {
            throw new Exception('Este lote ainda não está aberto para lances.');
        }
        if ($end && $now->gte($end)) {
            throw new Exception('Este lote foi encerrado.');
        }
        // 2. Bloquear se já vendido/encerrado logicamente
        if (in_array($lote->status, ['vendido', 'sem_lances', 'reservado', 'arrematado'])) {
            throw new Exception('Este lote não aceita mais lances.');
        }

        // 4. Lock atômico via Redis para evitar Race Conditions
        $lock = Cache::lock('lote_lance_' . $lote->id, 5); // 5 segundos de lock

        try {
            return $lock->block(3, function () use ($usuario, $lote, $leilao, $valor) {
                // Refresh no model para garantir dados atualizados
                $lote->refresh();
                $leilao->refresh();
                
                // Revalidar janela de tempo dentro do lock
                $now = now();
                $start = $leilao->data_inicio;
                $end = $lote->ends_at ?: $leilao->data_fim;
                if (($start && $now->lt($start)) || ($end && $now->gte($end))) {
                    throw new Exception('A janela de lances para este lote não está ativa.');
                }
                
                $maiorLance = $lote->maiorLance;
                $valorAtual = $maiorLance ? $maiorLance->valor : $lote->valor_inicial;
                
                // 5. Validar valor do lance
                if ($valor <= $valorAtual) {
                    throw new Exception("O lance deve ser maior que o valor atual: R$ " . number_format($valorAtual, 2, ',', '.'));
                }
                
                $incrementoMinimo = $lote->valor_minimo_incremento;
                if ($maiorLance && ($valor < $valorAtual + $incrementoMinimo)) {
                    throw new Exception("O lance deve superar o atual em pelo menos R$ " . number_format($incrementoMinimo, 2, ',', '.'));
                }

                $prevUsuarioId = $maiorLance?->usuario_id;
                return DB::transaction(function () use ($usuario, $lote, $leilao, $valor, $prevUsuarioId) {
                    $lance = Lance::create([
                        'lote_id' => $lote->id,
                        'usuario_id' => $usuario->id,
                        'valor' => $valor,
                    ]);

                    // Reset status if it was in warning state
                    if (in_array($lote->status, ['dou_lhe_1', 'dou_lhe_2'])) {
                        $lote->status = 'aberto';
                        $lote->save();
                        broadcast(new \App\Events\LoteStatusUpdated($lote));
                    } else {
                        // Even if status didn't change, we should broadcast the new bid via the status update event 
                        // so the card updates the price and potential visual cues
                        broadcast(new \App\Events\LoteStatusUpdated($lote));
                    }

                    // Removido: extensão automática global do leilão (anti-sniper).
                    // Regra: não recalcular/estender end_time sem regra explícita configurada por lote.

                    // Notificar por e-mail o usuário real ultrapassado (se houver)
                    if ($prevUsuarioId && $prevUsuarioId !== $usuario->id) {
                        Log::info('outbid:detected', ['lote_id' => $lote->id, 'prev_usuario_id' => $prevUsuarioId, 'current_usuario_id' => $usuario->id, 'valor' => $valor]);
                        try {
                            $dest = Usuario::find($prevUsuarioId);
                            if ($dest && $dest->email) {
                                $mailEnabled = SystemConfig::where('key', 'mail_enabled')->value('value');
                                if ($mailEnabled === '1') {
                                    $template = EmailTemplate::where('key', 'bid_outbid')->first();
                                    if (!$template) {
                                        $template = EmailTemplate::create([
                                            'key' => 'bid_outbid',
                                            'name' => 'Lance Superado',
                                            'subject' => 'Seu lance foi superado no lote {{numero_lote}}',
                                            'html' => '<p>Olá {{nome_cliente}},</p><p>Seu lance no lote {{numero_lote}} ({{nome_lote}}) foi superado.</p><p>Novo valor atual: {{valor_lance}}. <a href="{{link_lote}}">Dar um novo lance</a>.</p>',
                                            'text' => 'Olá {{nome_cliente}}, seu lance no lote {{numero_lote}} foi superado.',
                                            'enabled' => true,
                                        ]);
                                    }
                                    if ($template->enabled) {
                                        $renderer = app(EmailTemplateRenderer::class);
                                        $context = [
                                            'nome_cliente' => $dest->nome,
                                            'email_cliente' => $dest->email,
                                            'nome_lote' => $lote->titulo,
                                            'numero_lote' => $lote->ordem ?? (string)$lote->id,
                                            'valor_lance' => 'R$ ' . number_format($valor, 2, ',', '.'),
                                            'link_lote' => route('lotes.show', $lote->id),
                                        ];
                                        $rendered = $renderer->render($template, $context);
                                        $replyTo = SystemConfig::where('key', 'mail_reply_to')->value('value');
                                        Log::info('outbid:mail:send', ['to' => $dest->email, 'driver' => config('mail.default'), 'host' => config('mail.mailers.smtp.host')]);
                                        Mail::html($rendered['html'], function ($message) use ($dest, $rendered, $replyTo) {
                                            $message->to($dest->email)
                                                    ->subject($rendered['subject']);
                                            if (!empty($replyTo)) {
                                                $message->replyTo($replyTo);
                                            }
                                        });
                                        Log::info('outbid:mail:sent', ['to' => $dest->email]);
                                    }
                                } else {
                                    Log::info('outbid:mail:disabled');
                                }
                            }
                        } catch (\Throwable $e) {
                            Log::warning('outbid:mail:error', ['error' => $e->getMessage()]);
                        }

                        // Notificar por WhatsApp o usuário ultrapassado (se houver telefone)
                        try {
                            $dest = $dest ?? Usuario::find($prevUsuarioId);
                            $phone = $dest?->telefone;
                            $waEnabled = SystemConfig::where('key', 'textmebot_enabled')->value('value') === '1';
                            if ($waEnabled && $phone) {
                                $waTemplate = EmailTemplate::firstOrCreate(
                                    ['key' => 'wa_bid_outbid'],
                                    [
                                        'name' => 'Lance Superado (WhatsApp)',
                                        'text' => 'Olá {{nome_cliente}}, seu lance no lote {{numero_lote}} ({{nome_lote}}) foi superado. Novo valor: {{valor_lance}}. Acesse: {{link_lote}}',
                                        'enabled' => true,
                                    ]
                                );
                                if ($waTemplate->enabled) {
                                    $waRenderer = app(WhatsAppTemplateRenderer::class);
                                    $context = [
                                        'nome_cliente' => $dest->nome ?? 'Cliente',
                                        'email_cliente' => $dest->email ?? null,
                                        'nome_lote' => $lote->titulo,
                                        'numero_lote' => $lote->ordem ?? (string)$lote->id,
                                        'valor_lance' => 'R$ ' . number_format($valor, 2, ',', '.'),
                                        'link_lote' => route('lotes.show', $lote->id),
                                    ];
                                    $rendered = $waRenderer->render($waTemplate, $context);
                                    $wa = app(TextMeBotService::class);
                                    Log::info('outbid:wa:send', ['to' => $phone]);
                                    $resp = $wa->send($phone, $rendered['text']);
                                    if (!($resp['ok'] ?? false)) {
                                        Log::warning('outbid:wa:failed', ['to' => $phone, 'response' => $resp]);
                                    } else {
                                        Log::info('outbid:wa:sent', ['to' => $phone]);
                                    }
                                } else {
                                    Log::info('outbid:wa:disabled');
                                }
                            } else {
                                Log::info('outbid:wa:skip', ['enabled' => $waEnabled, 'has_phone' => (bool) $phone]);
                            }
                        } catch (\Throwable $e) {
                            Log::warning('outbid:wa:error', ['error' => $e->getMessage()]);
                        }
                    }

                    // Confirmar por WhatsApp o lance registrado para o usuário atual
                    try {
                        $phone = $usuario->telefone;
                        $waEnabled = SystemConfig::where('key', 'textmebot_enabled')->value('value') === '1';
                        if ($waEnabled && $phone) {
                            $waTemplate = EmailTemplate::firstOrCreate(
                                ['key' => 'wa_bid_confirmed'],
                                [
                                    'name' => 'Lance Registrado (WhatsApp)',
                                    'text' => 'Confirmamos seu lance de {{valor_lance}} no lote {{numero_lote}} ({{nome_lote}}).',
                                    'enabled' => true,
                                ]
                            );
                            if ($waTemplate->enabled) {
                                $waRenderer = app(WhatsAppTemplateRenderer::class);
                                $context = [
                                    'nome_cliente' => $usuario->nome ?? 'Cliente',
                                    'email_cliente' => $usuario->email ?? null,
                                    'nome_lote' => $lote->titulo,
                                    'numero_lote' => $lote->ordem ?? (string)$lote->id,
                                    'valor_lance' => 'R$ ' . number_format($valor, 2, ',', '.'),
                                    'link_lote' => route('lotes.show', $lote->id),
                                ];
                                $rendered = $waRenderer->render($waTemplate, $context);
                                $wa = app(TextMeBotService::class);
                                Log::info('bid:wa:send', ['to' => $phone]);
                                $resp = $wa->send($phone, $rendered['text']);
                                if (!($resp['ok'] ?? false)) {
                                    Log::warning('bid:wa:failed', ['to' => $phone, 'response' => $resp]);
                                } else {
                                    Log::info('bid:wa:sent', ['to' => $phone]);
                                }
                            }
                        } else {
                            Log::info('bid:wa:skip', ['enabled' => $waEnabled, 'has_phone' => (bool) $phone]);
                        }
                    } catch (\Throwable $e) {
                        Log::warning('bid:wa:error', ['error' => $e->getMessage()]);
                    }

                    return $lance;
                });
            });
        } catch (\Illuminate\Contracts\Cache\LockTimeoutException $e) {
            throw new Exception('Não foi possível processar seu lance devido à alta demanda. Tente novamente.');
        }
    }
}
