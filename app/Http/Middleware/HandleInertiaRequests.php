<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $siteConfig = \App\Models\SystemConfig::all()->pluck('value', 'key');

        $user = $request->user();
        $userPayload = null;
        if ($user) {
            $userPayload = $this->ensureUtf8($user->toArray());
        }

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $userPayload,
            ],
            'site_config' => [
                'site_name' => $siteConfig['site_name'] ?? config('app.name'),
                'site_logo' => $siteConfig['site_logo'] ?? null,
                'site_favicon' => $siteConfig['site_favicon'] ?? null,
                'company_location' => $siteConfig['company_location'] ?? null,
                'patio_location' => $siteConfig['patio_location'] ?? null,
                'cnpj' => $siteConfig['cnpj'] ?? null,
                'whatsapp' => $siteConfig['whatsapp'] ?? null,
                'phone' => $siteConfig['phone'] ?? null,
                'email' => $siteConfig['email'] ?? null,
            ],
            'ziggy' => function () use ($request) {
                return array_merge((new Ziggy)->toArray(), [
                    'location' => $request->url(),
                ]);
            },
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ]);
    }

    protected function ensureUtf8($data)
    {
        if (is_array($data)) {
            $res = [];
            foreach ($data as $k => $v) {
                $res[$this->ensureUtf8($k)] = $this->ensureUtf8($v);
            }
            return $res;
        }
        if (is_string($data)) {
            if (function_exists('mb_detect_encoding') && function_exists('mb_convert_encoding')) {
                if (!mb_detect_encoding($data, 'UTF-8', true)) {
                    return mb_convert_encoding($data, 'UTF-8', 'auto');
                }
            } else {
                return utf8_encode($data);
            }
            return $data;
        }
        return $data;
    }
}
