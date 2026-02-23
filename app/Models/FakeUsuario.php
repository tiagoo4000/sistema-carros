<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FakeUsuario extends Model
{
    protected $table = 'fake_usuarios';

    protected $fillable = [
        'apelido',
        'estado',
        'tag',
    ];
}

