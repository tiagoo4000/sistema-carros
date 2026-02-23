<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Services\EmailTemplateRenderer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Illuminate\Support\Facades\Schema;

class EmailTemplateController extends Controller
{
    protected array $defaults = [
        'welcome' => [
            'name' => 'Boas-vindas (Novo Cadastro)',
            'subject' => 'Bem-vindo(a), {{nome_cliente}}!',
            'html' => '<p>Olá {{nome_cliente}},</p><p>Seu cadastro em {{nome_site}} foi realizado com sucesso.</p><p>Fique à vontade para explorar nossos leilões.</p>',
        ],
        'bid_outbid' => [
            'name' => 'Lance Superado',
            'subject' => 'Seu lance foi superado no lote {{numero_lote}}',
            'html' => '<p>Olá {{nome_cliente}},</p><p>Seu lance no lote {{numero_lote}} ({{nome_lote}}) foi superado.</p><p>Novo valor atual: {{valor_lance}}. <a href="{{link_lote}}">Dar um novo lance</a>.</p>',
        ],
        'lot_won' => [
            'name' => 'Lote Arrematado',
            'subject' => 'Parabéns! Você arrematou o lote {{numero_lote}}',
            'html' => '<p>Olá {{nome_cliente}},</p><p>Você venceu o lote {{numero_lote}} ({{nome_lote}}) por {{valor_arremate}}.</p>',
        ],
        'bid_confirmed' => [
            'name' => 'Confirmação de Lance',
            'subject' => 'Lance registrado no lote {{numero_lote}}',
            'html' => '<p>Olá {{nome_cliente}},</p><p>Confirmamos seu lance de {{valor_lance}} no lote {{numero_lote}} ({{nome_lote}}).</p>',
        ],
        'password_reset' => [
            'name' => 'Recuperação de Senha',
            'subject' => 'Instruções para redefinir sua senha',
            'html' => '<p>Olá {{nome_cliente}},</p><p>Recebemos um pedido para redefinir sua senha. Se foi você, siga as instruções enviadas.</p>',
        ],
    ];

    public function index()
    {
        if (!Schema::hasTable('email_templates')) {
            return Inertia::render('Admin/Email/Templates/Index', [
                'templates' => [],
                'missing' => true,
            ]);
        }
        foreach ($this->defaults as $key => $def) {
            EmailTemplate::firstOrCreate(
                ['key' => $key],
                [
                    'name' => $def['name'],
                    'subject' => $def['subject'],
                    'html' => $def['html'],
                    'text' => strip_tags($def['html']),
                    'enabled' => true,
                ]
            );
        }

        $templates = EmailTemplate::orderBy('name')->get(['id','key','name','enabled','updated_at']);
        return Inertia::render('Admin/Email/Templates/Index', [
            'templates' => $templates,
        ]);
    }

    public function edit(string $key)
    {
        if (!Schema::hasTable('email_templates')) {
            return redirect()->route('admin.layout.email.templates.index')->with('error', 'A tabela email_templates não existe. Execute as migrations.');
        }
        $template = EmailTemplate::where('key', $key)->firstOrFail();
        $variables = [
            'Cliente' => ['{{nome_cliente}}','{{email_cliente}}'],
            'Lote' => ['{{nome_lote}}','{{numero_lote}}','{{valor_lance}}','{{valor_arremate}}','{{data_encerramento}}','{{link_lote}}'],
            'Sistema' => ['{{nome_site}}','{{telefone_contato}}','{{email_contato}}'],
        ];
        return Inertia::render('Admin/Email/Templates/Edit', [
            'template' => $template,
            'variables' => $variables,
        ]);
    }

    public function update(Request $request, string $key)
    {
        if (!Schema::hasTable('email_templates')) {
            return redirect()->route('admin.layout.email.templates.index')->with('error', 'A tabela email_templates não existe. Execute as migrations.');
        }
        $template = EmailTemplate::where('key', $key)->firstOrFail();
        $data = $request->validate([
            'enabled' => 'required|boolean',
            'subject' => 'nullable|string|max:255',
            'html' => 'nullable|string',
            'text' => 'nullable|string',
        ]);
        if (array_key_exists('html', $data) && is_string($data['html']) && str_contains($data['html'], '&lt;')) {
            $data['html'] = html_entity_decode($data['html']);
        }
        $template->fill($data);
        $template->updated_by = Auth::id();
        $template->save();
        return back()->with('success', 'Template atualizado com sucesso.');
    }

    public function preview(EmailTemplateRenderer $renderer, string $key)
    {
        if (!Schema::hasTable('email_templates')) {
            return response('A tabela email_templates não existe. Execute as migrations.', 503);
        }
        $template = EmailTemplate::where('key', $key)->firstOrFail();
        $rendered = $renderer->render($template, []);
        return response($rendered['html']);
    }

    public function test(Request $request, EmailTemplateRenderer $renderer, string $key)
    {
        if (!Schema::hasTable('email_templates')) {
            return redirect()->route('admin.layout.email.templates.index')->with('error', 'A tabela email_templates não existe. Execute as migrations.');
        }
        $template = EmailTemplate::where('key', $key)->firstOrFail();
        $data = $request->validate([
            'test_to' => 'required|email',
        ]);

        $store = \App\Models\SystemConfig::whereIn('key', [
            'mail_driver','mail_host','mail_port','mail_username','mail_password','mail_encryption',
            'mail_from_name','mail_from_address','mail_reply_to','mail_enabled',
        ])->pluck('value', 'key');
        if (($store['mail_enabled'] ?? '0') !== '1') {
            return back()->with('error', 'Envio de e-mails está desativado nas configurações.');
        }

        $driver = $store['mail_driver'] ?? 'log';
        config(['mail.default' => $driver]);
        if ($driver === 'smtp') {
            config([
                'mail.mailers.smtp.host' => $store['mail_host'] ?? null,
                'mail.mailers.smtp.port' => (int)($store['mail_port'] ?? 587),
                'mail.mailers.smtp.encryption' => ($store['mail_encryption'] ?? null) === 'null' ? null : ($store['mail_encryption'] ?? 'tls'),
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

        $rendered = $renderer->render($template, []);
        try {
            Mail::html($rendered['html'], function ($message) use ($data, $rendered, $store) {
                $message->to($data['test_to'])->subject($rendered['subject']);
                if (!empty($store['mail_reply_to'])) {
                    $message->replyTo($store['mail_reply_to']);
                }
            });
            $msg = 'E-mail de teste enviado.';
            if ($driver === 'log') {
                $msg .= ' Como driver=log, verifique storage/logs/laravel.log.';
            }
            return back()->with('success', $msg);
        } catch (\Throwable $e) {
            return back()->with('error', 'Falha ao enviar teste: '.$e->getMessage());
        }
    }
}
