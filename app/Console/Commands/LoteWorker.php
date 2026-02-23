<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Lote;
use App\Services\FinalizarLoteService;

class LoteWorker extends Command
{
    protected $signature = 'lotes:worker {--batch=20} {--sleep=1}';
    protected $description = 'Worker contínuo que finaliza lotes expirados sem depender de requisições HTTP';

    public function handle(FinalizarLoteService $service): int
    {
        $batch = (int) max(1, $this->option('batch'));
        $sleep = (int) max(1, $this->option('sleep'));

        $this->info("LoteWorker iniciado (batch={$batch}, sleep={$sleep}s). Pressione Ctrl+C para sair.");

        while (true) {
            try {
                $now = now();
                $lotes = Lote::query()
                    ->select(['id']) // leve para montar a lista
                    ->whereIn('status', ['aberto', 'dou_lhe_1', 'dou_lhe_2'])
                    ->whereNotNull('ends_at')
                    ->where('ends_at', '<=', $now)
                    ->orderBy('ends_at')
                    ->orderBy('id')
                    ->limit($batch)
                    ->get();

                if ($lotes->isEmpty()) {
                    sleep($sleep);
                    continue;
                }

                $processed = 0;
                foreach ($lotes as $loteRef) {
                    try {
                        // Carrega o modelo completo só quando necessário
                        $lote = Lote::with(['leilao', 'maiorLance.usuario'])->find($loteRef->id);
                        if (!$lote) {
                            continue;
                        }
                        $ok = $service->checkAndFinalize($lote);
                        if ($ok) {
                            $processed++;
                        }
                    } catch (\Throwable $e) {
                        Log::error('lote_worker_item_fail', [
                            'lote_id' => $loteRef->id ?? null,
                            'erro' => $e->getMessage(),
                        ]);
                        // Pequeno backoff local para evitar loop quente em item problemático
                        usleep(150_000); // 150ms
                    }
                }

                // Log discreto para acompanhamento
                if ($processed > 0) {
                    Log::info('lote_worker_processed', [
                        'count' => $processed,
                        'timestamp' => now()->toIso8601String(),
                    ]);
                }

                // Respeita intervalo entre ciclos
                sleep($sleep);
            } catch (\Throwable $e) {
                Log::error('lote_worker_loop_fail', ['erro' => $e->getMessage()]);
                // Backoff curto para evitar consumir CPU em caso de falha recorrente
                sleep(max($sleep, 2));
            }
        }
    }
}

