<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Lote;
use App\Services\FinalizarLoteService;

class FinalizarLotesExpirados extends Command
{
    protected $signature = 'lotes:finalizar-expirados {--chunk=200} {--dry-run}';
    protected $description = 'Finaliza lotes expirados e atualiza leilões';

    public function handle(FinalizarLoteService $service): int
    {
        $chunk = (int) $this->option('chunk');
        $dry = (bool) $this->option('dry-run');
        $total = 0;
        $ok = 0;

        Lote::query()
            ->whereIn('status', ['aberto', 'dou_lhe_1', 'dou_lhe_2'])
            ->whereNotNull('ends_at')
            ->where('ends_at', '<=', now())
            ->orderBy('id')
            ->chunk($chunk, function ($lotes) use ($service, $dry, &$total, &$ok) {
                foreach ($lotes as $lote) {
                    $total++;
                    if ($dry) {
                        $this->line("DRY: Lote {$lote->id} elegível");
                        continue;
                    }
                    if ($service->finalize($lote)) {
                        $ok++;
                    }
                }
            });

        $this->info("Total elegíveis: {$total}; Finalizados: {$ok}");
        return self::SUCCESS;
    }
}

