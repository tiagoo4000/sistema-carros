<?php

namespace App\Services;

use App\Models\SystemConfig;
use Illuminate\Support\Facades\Http;

class TextMeBotService
{
    public function send(string $recipient, string $text): array
    {
        $enabled = SystemConfig::where('key', 'textmebot_enabled')->value('value') ?? '0';
        if ($enabled !== '1') {
            return ['ok' => false, 'error' => 'disabled'];
        }
        $apiKey = SystemConfig::where('key', 'textmebot_api_key')->value('value') ?? env('TEXTMEBOT_API_KEY');
        if (!$apiKey) {
            return ['ok' => false, 'error' => 'missing_key'];
        }
        $digits = preg_replace('/\D+/', '', $recipient);
        if (strpos($digits, '55') !== 0) {
            if (strlen($digits) === 11) {
                $digits = '55' . $digits;
            }
        }
        try {
            $resp = Http::timeout(10)->get('https://api.textmebot.com/send.php', [
                'recipient' => $digits,
                'apikey' => $apiKey,
                'text' => $text,
            ]);
            $ok = $resp->successful();
            return [
                'ok' => $ok,
                'status' => $resp->status(),
                'body' => $resp->body(),
            ];
        } catch (\Throwable $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }
}
