<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image_path',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function lotes()
    {
        return $this->hasMany(Lote::class);
    }
}
