<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `leiloes` MODIFY `status` ENUM('ativo','finalizado','cancelado') NOT NULL DEFAULT 'ativo'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `leiloes` MODIFY `status` ENUM('agendado','ativo','finalizado','cancelado') NOT NULL DEFAULT 'ativo'");
    }
};
