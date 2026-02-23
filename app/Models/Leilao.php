<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leilao extends Model
{
    use HasFactory;

    protected $table = 'leiloes';

    protected $fillable = [
        'titulo',
        'descricao',
        'local',
        'data_inicio',
        'data_fim',
        'status',
        'tipo',
        'comitente_id',
    ];

    protected $casts = [
        'data_inicio' => 'datetime',
        'data_fim' => 'datetime',
    ];
    
    protected $appends = ['status_tempo'];
    
    public function lotes()
    {
        return $this->hasMany(Lote::class, 'leilao_id');
    }

    public function comitente()
    {
        return $this->belongsTo(Comitente::class);
    }

    public function lances()
    {
        return $this->hasManyThrough(
            Lance::class,   // Destino: lances
            Lote::class,    // Intermediário: lotes
            'leilao_id',    // Chave em Lote que referencia Leilao
            'lote_id',      // Chave em Lance que referencia Lote
            'id',           // Chave local em Leilao
            'id'            // Chave local em Lote
        );
    }
    
    public function getStatusTempoAttribute(): string
    {
        $now = now();
        $start = $this->data_inicio;
        $end = $this->data_fim;
        if ($start && $now->lt($start)) {
            return 'loteando';
        }
        if ($end && $now->lt($end)) {
            return 'aberto';
        }
        return 'encerrado';
    }
}
