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
        Schema::table('leiloes', function (Blueprint $table) {
            $table->foreignId('comitente_id')->nullable()->constrained('comitentes')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leiloes', function (Blueprint $table) {
            $table->dropForeign(['comitente_id']);
            $table->dropColumn('comitente_id');
        });
    }
};
