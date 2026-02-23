<?php

namespace App\Console\Commands;

use App\Jobs\ProcessarEncerramentoLeilaoJob;
use App\Models\Leilao;
use Illuminate\Console\Command;

class VerificarLeiloesEncerrados extends Command
{
    protected $signature = 'leiloes:verificar-encerrados';
    protected $description = 'Verifica leilões ativos que expiraram e dispara job de encerramento';

    public function handle()
    {
        $leiloesExpirados = Leilao::where('status', 'ativo')
            ->where('data_fim', '<=', now())
            ->get();

        $count = $leiloesExpirados->count();

        if ($count > 0) {
            $this->info("Encontrados {$count} leilões expirados. Disparando jobs...");
            
            foreach ($leiloesExpirados as $leilao) {
                ProcessarEncerramentoLeilaoJob::dispatch($leilao);
            }
        } else {
            $this->info("Nenhum leilão expirado encontrado.");
        }
    }
}
