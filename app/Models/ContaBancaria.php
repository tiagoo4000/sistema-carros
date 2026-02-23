<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContaBancaria extends Model
{
    protected $table = 'contas_bancarias';

    protected $fillable = [
        'tipo_documento',
        'nome_completo',
        'cpf',
        'razao_social',
        'cnpj',
        'banco',
        'agencia',
        'conta',
        'tipo_conta',
        'chave_pix',
        'qr_code_pix',
        'ativa',
        'padrao',
    ];

    protected $casts = [
        'ativa' => 'boolean',
        'padrao' => 'boolean',
    ];
}
