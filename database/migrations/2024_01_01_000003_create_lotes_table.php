<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('leilao_id')->constrained('leiloes')->onDelete('cascade');
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->decimal('valor_inicial', 10, 2);
            $table->decimal('valor_minimo_incremento', 10, 2)->default(10.00);
            $table->string('foto_capa')->nullable();
            $table->integer('ordem')->default(0);
            $table->enum('status', ['aberto', 'arrematado', 'sem_lances'])->default('aberto');
            $table->timestamps();
            
            $table->index('leilao_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lotes');
    }
};
