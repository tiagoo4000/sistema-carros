<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comitente extends Model
{
    use HasFactory;

    protected $fillable = ['nome', 'imagem'];

    public function leiloes()
    {
        return $this->hasMany(Leilao::class);
    }
}
