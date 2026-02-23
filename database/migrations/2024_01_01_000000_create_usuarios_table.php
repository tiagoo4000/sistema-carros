<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('email')->unique();
            $table->string('cpf')->nullable()->unique();
            $table->string('telefone')->nullable();
            $table->timestamp('email_verificado_em')->nullable();
            $table->string('senha');
            $table->boolean('admin')->default(false);
            $table->boolean('bloqueado')->default(false);
            $table->boolean('documentos_validos')->default(false);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
