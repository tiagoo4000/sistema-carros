<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;

class EventoNovoCadastro
{
    use Dispatchable;

    public int $usuario_id;

    public function __construct(int $usuario_id)
    {
        $this->usuario_id = $usuario_id;
    }
}
