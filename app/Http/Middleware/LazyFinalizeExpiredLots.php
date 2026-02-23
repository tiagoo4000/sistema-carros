<?php

namespace App\Http\Middleware;

use App\Models\Lote;
use App\Services\FinalizarLoteService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class LazyFinalizeExpiredLots
{
    public function __construct(private FinalizarLoteService $service)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        // Só executa para rotas de leilão/lote/admin
        if (! $request->is('leiloes*') &&
            ! $request->is('lotes*') &&
            ! $request->is('admin*')) {
            return $next($request);
        }

        // Proteção contra concorrência e excesso (lock curto, não bloqueante)
        $lock = Cache::lock('lazy_finalize_lotes', 3);
        if ($lock->get()) {
            try {
                // Processar poucos por requisição para evitar carga (limit 3)
                $expirados = Lote::query()
                    ->whereIn('status', ['aberto', 'dou_lhe_1', 'dou_lhe_2'])
                    ->whereNotNull('ends_at')
                    ->where('ends_at', '<=', now())
                    ->orderBy('id')
                    ->limit(3)
                    ->get();

                foreach ($expirados as $lote) {
                    try {
                        $this->service->finalize($lote);
                    } catch (\Throwable $e) {
                        // Silenciar para não afetar a requisição do usuário
                        // Logs detalhados devem ser gerados dentro do service
                    }
                }
            } finally {
                optional($lock)->release();
            }
        }

        return $next($request);
    }
}

