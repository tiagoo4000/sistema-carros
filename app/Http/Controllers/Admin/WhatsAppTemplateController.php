<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Services\WhatsAppTemplateRenderer;
use App\Services\TextMeBotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class WhatsAppTemplateController extends Controller
{
    protected array $defaults = [
        'wa_welcome' => [
            'name' => 'Boas-vindas (WhatsApp)',
            'text' => 'Olá {{nome_cliente}}, seu cadastro em {{nome_site}} foi realizado com sucesso. Qualquer dúvida, fale conosco: {{telefone_contato}}.',
        ],
        'wa_bid_outbid' => [
            'name' => 'Lance Superado (WhatsApp)',
            'text' => 'Olá {{nome_cliente}}, seu lance no lote {{numero_lote}} ({{nome_lote}}) foi superado. Novo valor: {{valor_lance}}. Acesse: {{link_lote}}',
        ],
        'wa_lot_won' => [
            'name' => 'Lote Arrematado (WhatsApp)',
            'text' => 'Parabéns {{nome_cliente}}! Você arrematou o lote {{numero_lote}} ({{nome_lote}}) por {{valor_arremate}}.',
        ],
        'wa_bid_confirmed' => [
            'name' => 'Lance Registrado (WhatsApp)',
            'text' => 'Confirmamos seu lance de {{valor_lance}} no lote {{numero_lote}} ({{nome_lote}}).',
        ],
    ];

    public function index()
    {
        if (!Schema::hasTable('email_templates')) {
            return Inertia::render('Admin/WhatsApp/Templates/Index', [
                'templates' => [],
                'missing' => true,
            ]);
        }

        foreach ($this->defaults as $key => $def) {
            EmailTemplate::firstOrCreate(
                ['key' => $key],
                [
                    'name' => $def['name'],
                    'subject' => null,
                    'html' => null,
                    'text' => $def['text'],
                    'enabled' => true,
                ]
            );
        }

        $templates = EmailTemplate::whereIn('key', array_keys($this->defaults))
            ->orderBy('name')
            ->get(['id','key','name','enabled','updated_at']);

        return Inertia::render('Admin/WhatsApp/Templates/Index', [
            'templates' => $templates,
            'missing' => false,
        ]);
    }

    public function edit(string $key)
    {
        if (!Schema::hasTable('email_templates')) {
            return redirect()->route('admin.layout.whatsapp.templates.index')->with('error', 'A tabela email_templates não existe. Execute as migrations.');
        }
        $template = EmailTemplate::where('key', $key)->firstOrFail();
        $variables = [
            'Cliente' => ['{{nome_cliente}}','{{email_cliente}}'],
            'Lote' => ['{{nome_lote}}','{{numero_lote}}','{{valor_lance}}','{{valor_arremate}}','{{data_encerramento}}','{{link_lote}}'],
            'Sistema' => ['{{nome_site}}','{{telefone_contato}}','{{email_contato}}'],
        ];
        return Inertia::render('Admin/WhatsApp/Templates/Edit', [
            'template' => $template,
            'variables' => $variables,
        ]);
    }

    public function update(Request $request, string $key)
    {
        $request->validate([
            'enabled' => 'required|boolean',
            'text' => 'nullable|string',
        ]);
        $template = EmailTemplate::where('key', $key)->firstOrFail();
        $template->enabled = (bool) $request->enabled;
        $template->text = $request->text;
        $template->save();
        return back()->with('success', 'Template atualizado.');
    }

    public function preview(WhatsAppTemplateRenderer $renderer, string $key)
    {
        if (!Schema::hasTable('email_templates')) {
            return response('A tabela email_templates não existe. Execute as migrations.', 503);
        }
        $template = EmailTemplate::where('key', $key)->firstOrFail();
        $rendered = $renderer->render($template, []);
        return response('<pre style="white-space:pre-wrap;word-break:break-word;padding:16px;">'.e($rendered['text']).'</pre>');
    }

    public function test(Request $request, WhatsAppTemplateRenderer $renderer, TextMeBotService $service, string $key)
    {
        if (!Schema::hasTable('email_templates')) {
            return redirect()->route('admin.layout.whatsapp.templates.index')->with('error', 'A tabela email_templates não existe. Execute as migrations.');
        }
        $template = EmailTemplate::where('key', $key)->firstOrFail();
        $data = $request->validate([
            'test_to' => 'required|string',
        ]);

        if (!($template->enabled)) {
            return back()->with('error', 'Template desabilitado.');
        }

        $rendered = $renderer->render($template, []);
        $resp = $service->send($data['test_to'], $rendered['text']);
        if (!($resp['ok'] ?? false)) {
            return back()->with('error', 'Falha ao enviar teste: '.($resp['error'] ?? $resp['body'] ?? 'erro desconhecido'));
        }
        return back()->with('success', 'Mensagem de teste enviada.');
    }
}
