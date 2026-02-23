<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogAuditoria extends Model
{
    use HasFactory;

    protected $table = 'logs_auditoria';

    protected $fillable = [
        'usuario_id',
        'acao',
        'dados',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'dados' => 'array',
    ];
    
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
