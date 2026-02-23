<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contas_bancarias', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo_documento', ['cpf', 'cnpj']);
            $table->string('nome_completo')->nullable();
            $table->string('cpf', 14)->nullable();
            $table->string('razao_social')->nullable();
            $table->string('cnpj', 18)->nullable();
            $table->string('banco');
            $table->string('agencia');
            $table->string('conta');
            $table->enum('tipo_conta', ['corrente', 'poupanca']);
            $table->string('chave_pix')->nullable();
            $table->boolean('ativa')->default(true);
            $table->boolean('padrao')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contas_bancarias');
    }
};

