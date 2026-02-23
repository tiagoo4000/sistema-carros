<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lote extends Model
{
    use HasFactory;

    protected $table = 'lotes';

    protected $fillable = [
        'leilao_id',
        'category_id',
        'titulo',
        'subtitulo',
        'descricao',
        'descricao_detalhada',
        'valor_inicial',
        'valor_mercado',
        'valor_minimo_incremento',
        'foto_capa',
        'ordem',
        'status',
        'ends_at',
        'views',
        'comprador_id',
        'valor_compra',
        'comprado_em',
        'ano',
        'quilometragem',
        'combustivel',
        'cor',
        'possui_chave',
        'tipo',
        'tipo_retomada',
        'localizacao',
        'prazo_documentacao',
    ];

    protected $casts = [
        'ends_at' => 'datetime',
        'comprado_em' => 'datetime',
        'valor_inicial' => 'decimal:2',
        'valor_mercado' => 'decimal:2',
        'valor_minimo_incremento' => 'decimal:2',
        'valor_compra' => 'decimal:2',
    ];
    
    protected $appends = ['status_tempo'];

    public function leilao()
    {
        return $this->belongsTo(Leilao::class, 'leilao_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function comprador()
    {
        return $this->belongsTo(Usuario::class, 'comprador_id');
    }

    public function fotos()
    {
        return $this->hasMany(FotoLote::class, 'lote_id');
    }

    public function lances()
    {
        return $this->hasMany(Lance::class, 'lote_id');
    }
    
    public function maiorLance()
    {
        return $this->hasOne(Lance::class, 'lote_id')->latest('valor');
    }

    public function termo()
    {
        return $this->hasOne(TermoArrematacao::class, 'lote_id');
    }
    
    public function getStatusTempoAttribute(): string
    {
        $this->loadMissing('leilao');
        $now = now();
        $start = optional($this->leilao)->data_inicio;
        $end = $this->ends_at ?: optional($this->leilao)->data_fim;
        if ($start && $now->lt($start)) {
            return 'loteando';
        }
        if ($end && $now->lt($end)) {
            return 'aberto';
        }
        return 'encerrado';
    }
}
