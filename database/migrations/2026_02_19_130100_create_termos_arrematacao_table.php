<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('termos_arrematacao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lote_id')->constrained('lotes')->onDelete('cascade');
            $table->foreignId('leilao_id')->constrained('leiloes')->onDelete('cascade');
            $table->string('arrematante_nome');
            $table->string('arrematante_documento');
            $table->string('arrematante_email')->nullable();
            $table->string('arrematante_cidade')->nullable();
            $table->decimal('valor_arrematacao', 10, 2);
            $table->string('conta_bancaria_nome');
            $table->string('conta_bancaria_documento');
            $table->string('conta_bancaria_banco');
            $table->string('conta_bancaria_agencia');
            $table->string('conta_bancaria_conta');
            $table->string('conta_bancaria_pix')->nullable();
            $table->string('conta_bancaria_qr')->nullable();
            $table->longText('conteudo_html')->nullable();
            $table->string('pdf_path')->nullable();
            $table->boolean('enviado')->default(false);
            $table->timestamp('enviado_em')->nullable();
            $table->timestamps();
            $table->unique('lote_id');
            $table->index(['leilao_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('termos_arrematacao');
    }
};

