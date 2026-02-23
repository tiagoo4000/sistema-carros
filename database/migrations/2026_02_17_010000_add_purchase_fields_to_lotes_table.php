<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lotes', function (Blueprint $table) {
            $table->foreignId('comprador_id')->nullable()->constrained('usuarios')->nullOnDelete()->after('views');
            $table->decimal('valor_compra', 10, 2)->nullable()->after('comprador_id');
            $table->timestamp('comprado_em')->nullable()->after('valor_compra');
        });

        // Ajustar enum de status para incluir 'vendido' e 'reservado'
        // MySQL específico: modificar a coluna mantendo valores existentes
        DB::statement("ALTER TABLE lotes MODIFY status ENUM('aberto','arrematado','sem_lances','reservado','vendido') DEFAULT 'aberto'");
    }

    public function down(): void
    {
        // Reverter enum (remover 'vendido' e 'reservado')
        DB::statement("ALTER TABLE lotes MODIFY status ENUM('aberto','arrematado','sem_lances') DEFAULT 'aberto'");

        Schema::table('lotes', function (Blueprint $table) {
            $table->dropForeign(['comprador_id']);
            $table->dropColumn(['comprador_id', 'valor_compra', 'comprado_em']);
        });
    }
};
