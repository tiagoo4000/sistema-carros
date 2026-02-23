<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TextMeBotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppController extends Controller
{
    public function send(Request $request, TextMeBotService $service)
    {
        try {
            $data = $request->validate([
                'recipient' => 'required|string',
                'text' => 'required|string',
            ]);
            $res = $service->send($data['recipient'], $data['text']);
            if (!($res['ok'] ?? false)) {
                Log::warning('textmebot:send_failed', ['recipient' => $data['recipient'], 'response' => $res]);
                return response()->json($res, 422);
            }
            return response()->json($res);
        } catch (\Throwable $e) {
            Log::error('textmebot:exception', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['ok' => false, 'error' => 'internal_error', 'message' => $e->getMessage()], 500);
        }
    }
}
