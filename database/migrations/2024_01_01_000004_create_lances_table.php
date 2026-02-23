<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lote_id')->constrained('lotes')->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->decimal('valor', 10, 2);
            $table->timestamps();
            
            $table->index(['lote_id', 'valor']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lances');
    }
};
