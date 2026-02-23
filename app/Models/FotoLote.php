<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FotoLote extends Model
{
    use HasFactory;

    protected $table = 'foto_lotes';

    protected $fillable = [
        'lote_id',
        'caminho',
    ];

    public function lote()
    {
        return $this->belongsTo(Lote::class);
    }
}
