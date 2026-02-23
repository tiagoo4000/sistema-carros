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
                if (!Schema::hasColumn('termos_arrematacao', 'status')) {
                    $table->string('status', 30)->default('pendente')->after('pdf_path');
                }
                if (!Schema::hasColumn('termos_arrematacao', 'visualizado_em')) {
                    $table->timestamp('visualizado_em')->nullable()->after('status');
                }
                if (!Schema::hasColumn('termos_arrematacao', 'visualizado_ip')) {
                    $table->string('visualizado_ip', 45)->nullable()->after('visualizado_em');
                }
                if (!Schema::hasColumn('termos_arrematacao', 'aceito_em')) {
                    $table->timestamp('aceito_em')->nullable()->after('visualizado_ip');
                }
                if (!Schema::hasColumn('termos_arrematacao', 'aceito_ip')) {
                    $table->string('aceito_ip', 45)->nullable()->after('aceito_em');
                }
                if (!Schema::hasColumn('termos_arrematacao', 'aceito_user_agent')) {
                    $table->string('aceito_user_agent', 255)->nullable()->after('aceito_ip');
                }
                if (!Schema::hasColumn('termos_arrematacao', 'hash_integridade')) {
                    $table->string('hash_integridade', 128)->nullable()->after('aceito_user_agent');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('termos_arrematacao')) {
            Schema::table('termos_arrematacao', function (Blueprint $table) {
                foreach (['status','visualizado_em','visualizado_ip','aceito_em','aceito_ip','aceito_user_agent','hash_integridade'] as $col) {
                    if (Schema::hasColumn('termos_arrematacao', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};

