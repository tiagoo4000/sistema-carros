<?php

namespace App\Services;

use App\Models\EmailTemplate;
use App\Models\SystemConfig;

class EmailTemplateRenderer
{
    public function render(EmailTemplate $template, array $context = []): array
    {
        $base = $this->baseLayout();
        $vars = $this->variables($context);

        $subject = $this->replaceVars($template->subject ?? '', $vars);
        $contentHtml = $this->replaceVars($template->html ?? '', $vars);
        $contentText = $this->replaceVars($template->text ?? strip_tags($contentHtml), $vars);

        $html = str_replace('{{content}}', $contentHtml, $base);
        $html = $this->replaceVars($html, $vars);

        return [
            'subject' => $subject,
            'html' => $html,
            'text' => $contentText,
        ];
    }

    public function variables(array $context = []): array
    {
        $store = SystemConfig::whereIn('key', [
            'site_name','site_email','site_phone','site_logo'
        ])->pluck('value', 'key');

        $defaults = [
            'nome_cliente' => $context['nome_cliente'] ?? 'Cliente',
            'email_cliente' => $context['email_cliente'] ?? 'cliente@example.com',
            'nome_lote' => $context['nome_lote'] ?? 'Lote Exemplo',
            'numero_lote' => $context['numero_lote'] ?? '000',
            'valor_lance' => $context['valor_lance'] ?? 'R$ 0,00',
            'valor_arremate' => $context['valor_arremate'] ?? 'R$ 0,00',
            'data_encerramento' => $context['data_encerramento'] ?? date('d/m/Y H:i'),
            'link_lote' => $context['link_lote'] ?? url('/'),
            'nome_site' => $context['nome_site'] ?? ($store['site_name'] ?? config('app.name')),
            'telefone_contato' => $context['telefone_contato'] ?? ($store['site_phone'] ?? ''),
            'email_contato' => $context['email_contato'] ?? ($store['site_email'] ?? ''),
            'logo_site' => $context['logo_site'] ?? ($store['site_logo'] ?? ''),
        ];

        return $defaults;
    }

    public function replaceVars(string $text, array $vars): string
    {
        $search = [];
        $replace = [];
        foreach ($vars as $k => $v) {
            $search[] = '{{'.$k.'}}';
            $replace[] = $v;
        }
        return str_replace($search, $replace, $text);
    }

    protected function baseLayout(): string
    {
        return <<<HTML
<!doctype html>
<html>
  <head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>{{nome_site}}</title>
  </head>
  <body style="margin:0;background:#f4f5f7;">
    <table role="presentation" style="width:100%;background:#f4f5f7;padding:24px 0;">
      <tr>
        <td>
          <table role="presentation" align="center" style="width:600px;max-width:95%;background:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #e5e7eb;">
            <tr>
              <td style="background:#111827;padding:24px;text-align:center;">
                <img src="{{logo_site}}" alt="{{nome_site}}" style="height:40px;max-width:200px;" onerror="this.style.display='none'"/>
                <div style="color:#fff;font-weight:700;font-size:18px;line-height:1.2;">{{nome_site}}</div>
              </td>
            </tr>
            <tr>
              <td style="padding:24px;font-size:14px;line-height:1.7;color:#111827;">
                {{content}}
              </td>
            </tr>
            <tr>
              <td style="background:#f9fafb;border-top:1px solid #e5e7eb;padding:16px 24px;text-align:center;color:#6b7280;font-size:12px;">
                Dúvidas? Fale com a gente: {{telefone_contato}} • {{email_contato}}<br/>
                © {{nome_site}}
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>
HTML;
    }
}

