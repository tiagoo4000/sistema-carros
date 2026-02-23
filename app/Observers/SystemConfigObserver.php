<?php

namespace App\Observers;

use App\Models\SystemConfig;
use Illuminate\Support\Facades\Cache;

class SystemConfigObserver
{
    public function saved(SystemConfig $config): void
    {
        Cache::forget('global_site_config');
        Cache::forget('smtp_dynamic_config');
    }

    public function deleted(SystemConfig $config): void
    {
        Cache::forget('global_site_config');
        Cache::forget('smtp_dynamic_config');
    }
}
