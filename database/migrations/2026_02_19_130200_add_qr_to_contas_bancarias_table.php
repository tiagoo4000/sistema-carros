<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contas_bancarias', function (Blueprint $table) {
            if (!Schema::hasColumn('contas_bancarias', 'qr_code_pix')) {
                $table->string('qr_code_pix')->nullable()->after('chave_pix');
            }
        });
    }

    public function down(): void
    {
        Schema::table('contas_bancarias', function (Blueprint $table) {
            if (Schema::hasColumn('contas_bancarias', 'qr_code_pix')) {
                $table->dropColumn('qr_code_pix');
            }
        });
    }
};

