<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('participantes_leilao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('leilao_id')->constrained('leiloes')->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['leilao_id', 'usuario_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('participantes_leilao');
    }
};
