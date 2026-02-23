<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'nome',
        'apelido',
        'tipo_pessoa',
        'email',
        'cpf',
        'rg',
        'orgao_expedidor',
        'data_nascimento',
        'renda_mensal',
        'telefone',
        'telefone_fixo',
        'celular2',
        'ocupacao',
        // PJ
        'cnpj',
        'razao_social',
        'nome_fantasia',
        'inscricao_estadual',
        'data_abertura',
        'faturamento_mensal',
        'telefone_comercial',
        'responsavel_nome',
        'responsavel_cpf',
        // Endereço
        'cep',
        'endereco',
        'numero',
        'bairro',
        'cidade',
        'estado',
        'complemento',
        // Preferências
        'interesses',
        'como_conheceu',
        'status_cadastro',
        'senha',
        'admin',
        'bloqueado',
        'documentos_validos',
        'verificacao_forcada',
    ];

    protected $hidden = [
        'senha',
        'remember_token',
    ];

    protected $casts = [
        'email_verificado_em' => 'datetime',
        'senha' => 'hashed',
        'admin' => 'boolean',
        'bloqueado' => 'boolean',
        'documentos_validos' => 'boolean',
        'verificacao_forcada' => 'boolean',
        'interesses' => 'array',
        'data_nascimento' => 'date',
        'data_abertura' => 'date',
    ];
    
    // Relações
    public function lances()
    {
        return $this->hasMany(Lance::class, 'usuario_id');
    }
    
    public function documentos()
    {
        return $this->hasMany(Documento::class, 'usuario_id');
    }
    
    // Auth password fix for 'senha' column
    public function getAuthPassword()
    {
        return $this->senha;
    }

    public function isAdmin(): bool
    {
        return $this->admin === true;
    }
}
