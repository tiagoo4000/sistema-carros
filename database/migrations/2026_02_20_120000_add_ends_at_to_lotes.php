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
            if (!Schema::hasColumn('lotes', 'ends_at')) {
                $table->dateTime('ends_at')->nullable()->after('status');
            }
        });

        // Backfill ends_at from leilao.data_fim where null
        // Assumes MySQL
        DB::statement('
            UPDATE lotes l
            JOIN leiloes le ON le.id = l.leilao_id
               SET l.ends_at = le.data_fim
             WHERE l.ends_at IS NULL
        ');
    }

    public function down(): void
    {
        Schema::table('lotes', function (Blueprint $table) {
            if (Schema::hasColumn('lotes', 'ends_at')) {
                $table->dropColumn('ends_at');
            }
        });
    }
};

