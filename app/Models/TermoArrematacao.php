<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TermoArrematacao extends Model
{
    protected $table = 'termos_arrematacao';

    protected $fillable = [
        'lote_id',
        'leilao_id',
        'numero',
        'arrematante_nome',
        'arrematante_documento',
        'arrematante_email',
        'arrematante_cidade',
        'valor_arrematacao',
        'conta_bancaria_nome',
        'conta_bancaria_documento',
        'conta_bancaria_banco',
        'conta_bancaria_agencia',
        'conta_bancaria_conta',
        'conta_bancaria_pix',
        'conta_bancaria_qr',
        'conteudo_html',
        'pdf_path',
        'status',
        'disponibilizado_em',
        'visualizado_em',
        'visualizado_ip',
        'visualizado_user_agent',
        'visualizado_device',
        'aceito_em',
        'aceito_ip',
        'aceito_user_agent',
        'hash_integridade',
        'enviado',
        'enviado_em',
    ];

    protected $casts = [
        'valor_arrematacao' => 'decimal:2',
        'disponibilizado_em' => 'datetime',
        'visualizado_em' => 'datetime',
        'aceito_em' => 'datetime',
        'enviado' => 'boolean',
        'enviado_em' => 'datetime',
    ];

    public function lote()
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }

    public function leilao()
    {
        return $this->belongsTo(Leilao::class, 'leilao_id');
    }
}
