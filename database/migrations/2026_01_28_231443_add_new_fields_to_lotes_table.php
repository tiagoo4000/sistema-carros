<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('lotes', function (Blueprint $table) {
            $table->string('subtitulo')->nullable()->after('titulo');
            $table->string('ano')->nullable()->after('subtitulo');
            $table->integer('quilometragem')->nullable()->after('ano');
            $table->decimal('valor_mercado', 10, 2)->nullable()->after('valor_inicial');
            $table->string('combustivel')->nullable()->after('quilometragem');
            $table->string('cor')->nullable()->after('combustivel');
            $table->boolean('possui_chave')->default(false)->after('cor');
            $table->string('tipo')->nullable()->after('possui_chave');
            $table->string('tipo_retomada')->nullable()->after('tipo');
            $table->string('localizacao')->nullable()->after('tipo_retomada');
            $table->string('prazo_documentacao')->nullable()->after('localizacao');
            $table->text('descricao_detalhada')->nullable()->after('descricao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lotes', function (Blueprint $table) {
            $table->dropColumn([
                'subtitulo',
                'ano',
                'quilometragem',
                'valor_mercado',
                'combustivel',
                'cor',
                'possui_chave',
                'tipo',
                'tipo_retomada',
                'localizacao',
                'prazo_documentacao',
                'descricao_detalhada',
            ]);
        });
    }
};
