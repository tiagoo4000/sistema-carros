<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lance extends Model
{
    use HasFactory;

    protected $table = 'lances';

    protected $fillable = [
        'lote_id',
        'usuario_id',
        'valor',
        'is_fake',
        'nome_exibicao',
        'cidade_exibicao',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'is_fake' => 'boolean',
    ];
    
    public function lote()
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }
    
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
