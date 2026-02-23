<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leiloes', function (Blueprint $table) {
            $table->enum('tipo', ['leilao', 'venda_direta'])->default('leilao')->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('leiloes', function (Blueprint $table) {
            $table->dropColumn('tipo');
        });
    }
};
