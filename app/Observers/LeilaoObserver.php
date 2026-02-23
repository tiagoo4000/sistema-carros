<?php

namespace App\Observers;

use App\Models\Leilao;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\SystemConfig;
use App\Events\LoteStatusUpdated;

class LeilaoObserver
{
    private function clearCache()
    {
        Cache::forget('home_leiloes_data_v2');
        Cache::forget('home_leiloes_data_v3');
    }

    /**
     * Handle the Leilao "created" event.
     */
    public function created(Leilao $leilao): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Leilao "updated" event.
     */
    public function updated(Leilao $leilao): void
    {
        $this->clearCache();
        
        if ($leilao->isDirty('titulo')) {
            $leilao->lotes()->update(['subtitulo' => $leilao->titulo]);
        }

        if ($leilao->isDirty('status')) {
            $old = $leilao->getOriginal('status');
            $new = $leilao->status;
            if ($old === 'finalizado' && in_array($new, ['ativo'])) {
                DB::transaction(function () use ($leilao, $new) {
                    $lotes = $leilao->lotes()->get();
                    foreach ($lotes as $lote) {
                        // Não reabrir lotes definitivamente vendidos/reservados
                        if (in_array($lote->status, ['vendido', 'reservado', 'arrematado']) || $lote->comprador_id) {
                            continue;
                        }
                        // Reativar lote
                        $lote->status = 'aberto';
                        $lote->comprador_id = null;
                        $lote->valor_compra = null;
                        $lote->comprado_em = null;
                        // Recalcular ends_at: usa data_fim do leilão se futura; senão remove para evitar reencerramento imediato
                        if ($leilao->data_fim && $leilao->data_fim->gt(now())) {
                            $lote->ends_at = $leilao->data_fim;
                        } else {
                            $lote->ends_at = null;
                        }
                        $lote->saveQuietly();
                        broadcast(new LoteStatusUpdated($lote));
                        \Log::info('lote_reset_reabertura', [
                            'leilao_id' => $leilao->id,
                            'lote_id' => $lote->id,
                            'novo_status' => $lote->status,
                        ]);
                    }
                });
            }
        }

        if ($leilao->isDirty('data_fim')) {
            DB::transaction(function () use ($leilao) {
                $lotes = $leilao->lotes()->get();
                foreach ($lotes as $lote) {
                    if (in_array($lote->status, ['vendido', 'reservado', 'arrematado'])) {
                        continue;
                    }
                    if ($leilao->data_fim && $leilao->data_fim->gt(now())) {
                        $lote->ends_at = $leilao->data_fim;
                    } else {
                        $lote->ends_at = null;
                    }
                    $lote->saveQuietly();
                    broadcast(new LoteStatusUpdated($lote));
                }
            });
        }
    }

    /**
     * Handle the Leilao "deleted" event.
     */
    public function deleted(Leilao $leilao): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Leilao "restored" event.
     */
    public function restored(Leilao $leilao): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Leilao "force deleted" event.
     */
    public function forceDeleted(Leilao $leilao): void
    {
        //
    }
}
