<?php

namespace App\Events;

use App\Models\Lote;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LoteStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $lote;
    public $status;
    public $vencedor;
    public $valor_final;
    public $ends_at;
    public $server_now;

    protected function maskDisplay(string $name): string
    {
        $s = trim($name);
        if ($s === '') return $s;
        $plain = preg_replace('/\s+/u', '', $s);
        $len = mb_strlen($plain);
        if ($len <= 1) return mb_strtoupper($plain);
        $first = mb_strtoupper(mb_substr($plain, 0, 1));
        $last = mb_strtoupper(mb_substr($plain, -1));
        $stars = str_repeat('*', max(1, $len - 2));
        return $first . $stars . $last;
    }

    public function __construct(Lote $lote)
    {
        $this->lote = $lote;
        $this->status = $lote->status;
        $this->ends_at = optional($lote->ends_at)->toIso8601String();
        $this->server_now = now()->toIso8601String();
        
        if ($this->status === 'vendido') {
            $maiorLance = $lote->lances()->orderBy('valor', 'desc')->first();
            $this->valor_final = $maiorLance ? $maiorLance->valor : $lote->valor_inicial;
            if ($maiorLance) {
                $isFake = (bool) $maiorLance->getAttribute('is_fake');
                if ($isFake) {
                    $nomeExib = $maiorLance->getAttribute('nome_exibicao');
                    $this->vencedor = $this->maskDisplay($nomeExib ?: 'Participante');
                } elseif ($maiorLance->usuario) {
                    $this->vencedor = $this->maskDisplay($maiorLance->usuario->nome);
                }
            }
        }
    }
    public function broadcastOn(): array
    {
        return [
            new Channel('lote.' . $this->lote->id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'lote.status.updated';
    }
}
