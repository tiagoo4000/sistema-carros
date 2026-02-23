<?php

namespace App\Observers;

use App\Models\Lote;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Services\FinalizarLoteService;
use App\Events\LoteStatusUpdated;

class LoteObserver
{
    private function clearCache()
    {
        Cache::forget('home_leiloes_data_v2');
        Cache::forget('home_leiloes_data_v3');
        Cache::forget('home_categories_v2');
    }

    public function created(Lote $lote): void
    {
        $this->clearCache();
    }

    public function updated(Lote $lote): void
    {
        $this->clearCache();
        try {
            if ($lote->wasChanged('status')) {
                broadcast(new LoteStatusUpdated($lote));
                $finalizar = app(FinalizarLoteService::class);
                $finalizar->finalizeLeilaoIfCompleted($lote->leilao);
            } elseif ($lote->wasChanged('ends_at')) {
                broadcast(new LoteStatusUpdated($lote));
            }
        } catch (\Throwable $e) {
            Log::warning('lote_observer_update_fail', ['lote_id' => $lote->id, 'erro' => $e->getMessage()]);
        }
    }

    public function deleted(Lote $lote): void
    {
        $this->clearCache();
    }
}
