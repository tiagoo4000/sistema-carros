<?php

namespace App\Services;

use App\Models\EmailTemplate;

class WhatsAppTemplateRenderer
{
    protected EmailTemplateRenderer $emailRenderer;

    public function __construct(EmailTemplateRenderer $emailRenderer)
    {
        $this->emailRenderer = $emailRenderer;
    }

    public function render(EmailTemplate $template, array $context = []): array
    {
        $vars = $this->emailRenderer->variables($context);
        $text = $this->emailRenderer->replaceVars($template->text ?? '', $vars);
        return [
            'text' => $text,
        ];
    }
}
