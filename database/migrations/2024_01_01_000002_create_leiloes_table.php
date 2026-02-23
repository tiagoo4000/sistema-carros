<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leiloes', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->string('local')->nullable(); // Para leilão presencial/online
            $table->dateTime('data_inicio');
            $table->dateTime('data_fim');
            $table->enum('status', ['agendado', 'ativo', 'finalizado', 'cancelado'])->default('agendado');
            $table->timestamps();
            
            $table->index('status');
            $table->index('data_fim');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leiloes');
    }
};
