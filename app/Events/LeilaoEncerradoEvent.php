<?php

namespace App\Events;

use App\Models\Leilao;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeilaoEncerradoEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $leilao;

    public function __construct(Leilao $leilao)
    {
        $this->leilao = $leilao;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('leilao.' . $this->leilao->id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'leilao.encerrado';
    }

    public function broadcastWith(): array
    {
        $vencedor = null;
        $maiorLance = $this->leilao->lances()
            ->where(function ($q) {
                $q->where('is_fake', false)->orWhereNull('is_fake');
            })
            ->orderBy('valor', 'desc')
            ->first();
        
        if ($maiorLance) {
            $vencedor = [
                'id' => $maiorLance->usuario->id,
                'nome' => $maiorLance->usuario->nome,
            ];
        }

        return [
            'leilao_id' => $this->leilao->id,
            'valor_final' => $maiorLance ? $maiorLance->valor : $this->leilao->valor_inicial,
            'vencedor' => $vencedor,
            'encerrado_em' => now()->toIso8601String(),
        ];
    }
}
