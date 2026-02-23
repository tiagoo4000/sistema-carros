<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            if (!Schema::hasColumn('usuarios', 'verificacao_forcada')) {
                $table->boolean('verificacao_forcada')->default(false)->after('documentos_validos');
            }
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            if (Schema::hasColumn('usuarios', 'verificacao_forcada')) {
                $table->dropColumn('verificacao_forcada');
            }
        });
    }
};
