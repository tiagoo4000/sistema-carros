<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Usuario;

Broadcast::channel('App.Models.Usuario.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('leilao.{leilaoId}', function ($user, $leilaoId) {
    return true; // Canal público para leitura, mas requer auth para conectar no socket geralmente
});
