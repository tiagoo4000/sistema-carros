<?php

namespace App\Listeners;

use App\Events\EventoNovoCadastro;
use App\Models\EmailTemplate;
use App\Models\SystemConfig;
use App\Models\Usuario;
use App\Services\EmailTemplateRenderer;
use Illuminate\Support\Facades\Mail;

class EnviarEmailBoasVindas
{
    public function handle(EventoNovoCadastro $event): void
    {
        // 🔥 Busca o usuário pelo ID (não usa mais model serializado)
        $usuario = Usuario::findOrFail($event->usuario_id);

        $template = EmailTemplate::where('key', 'welcome')->first();

        if (!$template) {
            $template = EmailTemplate::create([
                'key' => 'welcome',
                'name' => 'Boas-vindas (Novo Cadastro)',
                'subject' => 'Bem-vindo(a), {{nome_cliente}}!',
                'html' => '<p>Olá {{nome_cliente}},</p><p>Seu cadastro em {{nome_site}} foi realizado com sucesso.</p><p>Fique à vontade para explorar nossos leilões.</p>',
                'text' => 'Olá {{nome_cliente}}, seu cadastro foi realizado em {{nome_site}}.',
                'enabled' => true,
            ]);
        }

        if (!$template || !$template->enabled) {
            return;
        }

        $store = SystemConfig::whereIn('key', [
            'mail_driver','mail_host','mail_port','mail_username','mail_password','mail_encryption',
            'mail_from_name','mail_from_address','mail_reply_to','mail_enabled',
        ])->pluck('value', 'key');

        if (($store['mail_enabled'] ?? '0') !== '1') {
            return;
        }

        $driver = $store['mail_driver'] ?? 'log';

        config(['mail.default' => $driver]);

        if ($driver === 'smtp') {
            config([
                'mail.mailers.smtp.host' => $store['mail_host'] ?? null,
                'mail.mailers.smtp.port' => (int)($store['mail_port'] ?? 587),
                'mail.mailers.smtp.encryption' => ($store['mail_encryption'] ?? null) === 'null'
                    ? null
                    : ($store['mail_encryption'] ?? 'tls'),
                'mail.mailers.smtp.username' => $store['mail_username'] ?? null,
                'mail.mailers.smtp.password' => $store['mail_password'] ?? null,
                'mail.from.address' => $store['mail_from_address'] ?? 'noreply@example.com',
                'mail.from.name' => $store['mail_from_name'] ?? 'Sistema',
            ]);
        } else {
            config([
                'mail.from.address' => $store['mail_from_address'] ?? 'noreply@example.com',
                'mail.from.name' => $store['mail_from_name'] ?? 'Sistema',
            ]);
        }

        $context = [
            'nome_cliente' => $usuario->nome,
            'email_cliente' => $usuario->email,
        ];

        $renderer = app(EmailTemplateRenderer::class);
        $rendered = $renderer->render($template, $context);

        Mail::html($rendered['html'], function ($message) use ($usuario, $rendered, $store) {
            $message->to($usuario->email)
                    ->subject($rendered['subject']);

            if (!empty($store['mail_reply_to'])) {
                $message->replyTo($store['mail_reply_to']);
            }
        });
    }
}
