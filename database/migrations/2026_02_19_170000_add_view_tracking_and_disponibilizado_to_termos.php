<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('termos_arrematacao')) {
            Schema::table('termos_arrematacao', function (Blueprint $table) {
                if (!Schema::hasColumn('termos_arrematacao', 'visualizado_user_agent')) {
                    $table->string('visualizado_user_agent', 255)->nullable()->after('visualizado_ip');
                }
                if (!Schema::hasColumn('termos_arrematacao', 'visualizado_device')) {
                    $table->string('visualizado_device', 50)->nullable()->after('visualizado_user_agent');
                }
                if (!Schema::hasColumn('termos_arrematacao', 'disponibilizado_em')) {
                    $table->timestamp('disponibilizado_em')->nullable()->after('status');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('termos_arrematacao')) {
            Schema::table('termos_arrematacao', function (Blueprint $table) {
                foreach (['visualizado_user_agent','visualizado_device','disponibilizado_em'] as $col) {
                    if (Schema::hasColumn('termos_arrematacao', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};

