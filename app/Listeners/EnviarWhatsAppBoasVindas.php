<?php

namespace App\Listeners;

use App\Events\EventoNovoCadastro;
use App\Models\EmailTemplate;
use App\Models\Usuario;
use App\Models\SystemConfig;
use App\Services\WhatsAppTemplateRenderer;
use App\Services\TextMeBotService;

class EnviarWhatsAppBoasVindas
{
    public function handle(EventoNovoCadastro $event): void
    {
        if (!class_exists(\App\Services\WhatsAppTemplateRenderer::class)) {
            return;
        }
        $usuario = Usuario::find($event->usuario_id);
        if (!$usuario) {
            return;
        }
        $enabled = SystemConfig::where('key', 'textmebot_enabled')->value('value') === '1';
        if (!$enabled) {
            return;
        }
        $phone = $usuario->telefone;
        if (!$phone) {
            return;
        }
        $template = EmailTemplate::firstOrCreate(
            ['key' => 'wa_welcome'],
            [
                'name' => 'Boas-vindas (WhatsApp)',
                'text' => 'Olá {{nome_cliente}}, seu cadastro em {{nome_site}} foi realizado com sucesso. Qualquer dúvida, fale conosco: {{telefone_contato}}.',
                'enabled' => true,
            ]
        );
        if (!$template->enabled) {
            return;
        }
        $renderer = app(WhatsAppTemplateRenderer::class);
        $text = $renderer->render($template, [
            'nome_cliente' => $usuario->nome,
            'email_cliente' => $usuario->email,
        ])['text'];
        $svc = app(TextMeBotService::class);
        $svc->send($phone, $text);
    }
}
