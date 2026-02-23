<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Expand enum de status dos lotes para contemplar todos os estados usados no código.
     */
    public function up(): void
    {
        // Estados utilizados no frontend/backend:
        // aberto, dou_lhe_1, dou_lhe_2, vendido, finalizado, sem_lances, reservado, arrematado
        DB::statement("
            ALTER TABLE `lotes`
            MODIFY `status` ENUM(
                'aberto',
                'dou_lhe_1',
                'dou_lhe_2',
                'vendido',
                'finalizado',
                'sem_lances',
                'reservado',
                'arrematado'
            ) NOT NULL DEFAULT 'aberto'
        ");
    }

    /**
     * Reverte para o conjunto mínimo original para compatibilidade antiga.
     * Atenção: valores fora do conjunto serão convertidos para o primeiro enum ('aberto').
     */
    public function down(): void
    {
        DB::statement("
            ALTER TABLE `lotes`
            MODIFY `status` ENUM(
                'aberto',
                'arrematado',
                'sem_lances'
            ) NOT NULL DEFAULT 'aberto'
        ");
    }
};

