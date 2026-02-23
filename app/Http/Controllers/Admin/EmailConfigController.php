<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemConfig;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Mail;

class EmailConfigController extends Controller
{
    public function index()
    {
        $config = SystemConfig::whereIn('key', [
            'mail_driver',
            'mail_host',
            'mail_port',
            'mail_username',
            'mail_password',
            'mail_encryption',
            'mail_from_name',
            'mail_from_address',
            'mail_reply_to',
            'mail_enabled',
        ])->pluck('value', 'key');

        return Inertia::render('Admin/Config/Email', [
            'config' => $config,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'mail_driver' => 'nullable|string|in:smtp,sendmail,log,mailgun,ses,postmark',
            'mail_host' => 'nullable|string|max:255',
            'mail_port' => 'nullable|numeric',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string|max:255',
            'mail_encryption' => 'nullable|string|in:tls,ssl,null',
            'mail_from_name' => 'nullable|string|max:255',
            'mail_from_address' => 'nullable|email|max:255',
            'mail_reply_to' => 'nullable|email|max:255',
            'mail_enabled' => 'nullable|boolean',
        ]);

        foreach ($validated as $key => $value) {
            if ($key === 'mail_password' && ($value === null || $value === '')) {
                continue;
            }
            SystemConfig::updateOrCreate(
                ['key' => $key],
                ['value' => is_bool($value) ? ($value ? '1' : '0') : (string) $value]
            );
        }

        return back()->with('success', 'Configurações de e-mail salvas com sucesso.');
    }

    public function test(Request $request)
    {
        $data = $request->validate([
            'test_to' => 'required|email',
        ]);

        $store = SystemConfig::whereIn('key', [
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

        try {
            Mail::raw('Este é um e-mail de teste do sistema.', function ($message) use ($data, $store) {
                $message->to($data['test_to'])
                    ->subject('Teste de E-mail');
                if (!empty($store['mail_reply_to'])) {
                    $message->replyTo($store['mail_reply_to']);
                }
            });
            return back()->with('success', 'E-mail de teste enviado. ' . ($driver === 'log' ? 'Como driver=log, verifique storage/logs/laravel.log.' : 'Verifique sua caixa de entrada.'));
        } catch (\Throwable $e) {
            return back()->with('error', 'Falha ao enviar e-mail de teste: ' . $e->getMessage());
        }
    }
}
