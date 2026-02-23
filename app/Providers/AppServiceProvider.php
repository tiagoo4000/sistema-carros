<?php

namespace App\Providers;

use App\Models\Usuario;
use App\Models\Leilao;
use App\Models\Lote;
use App\Models\Banner;
use App\Models\Category;
use App\Models\SystemConfig;
use App\Observers\LeilaoObserver;
use App\Observers\LoteObserver;
use App\Observers\BannerObserver;
use App\Observers\CategoryObserver;
use App\Observers\SystemConfigObserver;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\EventoNovoCadastro;
use App\Listeners\EnviarEmailBoasVindas;
use App\Listeners\EnviarWhatsAppBoasVindas;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Eventos e Observers
        Event::listen(EventoNovoCadastro::class, [EnviarEmailBoasVindas::class, 'handle']);
        Event::listen(EventoNovoCadastro::class, [EnviarWhatsAppBoasVindas::class, 'handle']);
        Leilao::observe(LeilaoObserver::class);
        Lote::observe(LoteObserver::class);
        Banner::observe(BannerObserver::class);
        Category::observe(CategoryObserver::class);
        SystemConfig::observe(SystemConfigObserver::class);

        Gate::define('admin', function (Usuario $usuario) {
            return $usuario->admin === true;
        });

        // SMTP Dinâmico
        try {
            $smtp = Cache::remember('smtp_dynamic_config', 300, function () {
                $keys = [
                    'mail_host',
                    'mail_port',
                    'mail_encryption',
                    'mail_username',
                    'mail_password',
                    'mail_from_name',
                    'mail_from_address',
                ];
                return SystemConfig::whereIn('key', $keys)->pluck('value', 'key');
            });
            
            $host = $smtp['mail_host'] ?? null;
            if ($host) {
                Config::set('mail.default', 'smtp');
                Config::set('mail.mailers.smtp.transport', 'smtp');
                Config::set('mail.mailers.smtp.host', $host);
                if (isset($smtp['mail_port'])) {
                    Config::set('mail.mailers.smtp.port', (int) $smtp['mail_port']);
                }
                if (isset($smtp['mail_encryption'])) {
                    Config::set('mail.mailers.smtp.encryption', $smtp['mail_encryption'] ?: null);
                }
                if (isset($smtp['mail_username'])) {
                    Config::set('mail.mailers.smtp.username', $smtp['mail_username']);
                }
                if (isset($smtp['mail_password'])) {
                    Config::set('mail.mailers.smtp.password', $smtp['mail_password']);
                }
                if (isset($smtp['mail_from_name'])) {
                    Config::set('mail.from.name', $smtp['mail_from_name']);
                }
                if (isset($smtp['mail_from_address']) || isset($smtp['mail_username'])) {
                    Config::set('mail.from.address', $smtp['mail_from_address'] ?? $smtp['mail_username']);
                }
            }
        } catch (\Throwable $e) {
            // não quebra o boot caso a tabela não exista ou haja erro de conexão
        }

        // Share global config with all views
        if (!app()->runningInConsole()) {
            $siteConfig = Cache::remember('global_site_config', 3600, function () {
                return SystemConfig::all()->pluck('value', 'key');
            });
            
            // Set App Name
            if (isset($siteConfig['site_name'])) {
                config(['app.name' => $siteConfig['site_name']]);
            }

            // Share favicon with view
            View::share('site_favicon', $siteConfig['site_favicon'] ?? null);

            // Fallback para diretório compilado de views se storage/framework/views não for gravável
            try {
                $compiled = config('view.compiled');
                if (!$compiled || !is_dir($compiled) || !is_writable($compiled)) {
                    $fallback = storage_path('framework'.DIRECTORY_SEPARATOR.'views_fallback');
                    if (!is_dir($fallback)) {
                        @mkdir($fallback, 0775, true);
                    }
                    if (is_dir($fallback) && is_writable($fallback)) {
                        config(['view.compiled' => $fallback]);
                    } else {
                        $tmp = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'laravel-views';
                        if (!is_dir($tmp)) {
                            @mkdir($tmp, 0775, true);
                        }
                        if (is_dir($tmp) && is_writable($tmp)) {
                            config(['view.compiled' => $tmp]);
                        }
                    }
                }
            } catch (\Throwable $e) {
                // silencioso: preferimos não quebrar o boot por queda de permissão
            }
        }
    }
}
