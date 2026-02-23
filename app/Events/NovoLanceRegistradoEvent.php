<?php

namespace App\Events;

use App\Models\Lance;
use App\Models\Leilao;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NovoLanceRegistradoEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $leilao;
    public $lance;

    /**
     * Create a new event instance.
     */
    public function __construct(Leilao $leilao, Lance $lance)
    {
        $this->leilao = $leilao;
        $this->lance = $lance;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('leilao.' . $this->leilao->id),
        ];
    }
    
    public function broadcastAs()
    {
        return 'lance.registrado';
    }
    
    public function broadcastWith()
    {
        return [
            'lance' => [
                'id' => $this->lance->id,
                'valor' => $this->lance->valor,
                'created_at' => $this->lance->created_at->toIso8601String(),
                'usuario' => [
                    'id' => $this->lance->usuario->id,
                    'nome' => $this->lance->usuario->nome,
                ]
            ],
            'leilao' => [
                'id' => $this->leilao->id,
                'maior_lance' => $this->lance->valor
            ]
        ];
    }
}
