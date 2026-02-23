<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lances', function (Blueprint $table) {
            if (!Schema::hasColumn('lances', 'is_fake')) {
                $table->boolean('is_fake')->default(false)->after('usuario_id');
            }
            if (!Schema::hasColumn('lances', 'nome_exibicao')) {
                $table->string('nome_exibicao')->nullable()->after('is_fake');
            }
            if (!Schema::hasColumn('lances', 'cidade_exibicao')) {
                $table->string('cidade_exibicao')->nullable()->after('nome_exibicao');
            }
        });
        
        // Tornar usuario_id opcional para suportar lances fake
        // Drop FK, alterar coluna para nullable, recriar FK
        try {
            Schema::table('lances', function (Blueprint $table) {
                $table->dropForeign(['usuario_id']);
            });
        } catch (\Throwable $e) {
            // Ignora se já não existir
        }
        
        // Alterar tipo da coluna via SQL para evitar necessidade de doctrine/dbal
        DB::statement('ALTER TABLE lances MODIFY usuario_id BIGINT UNSIGNED NULL');
        
        Schema::table('lances', function (Blueprint $table) {
            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade');
            try {
                $table->index(['is_fake']);
            } catch (\Throwable $e) {
                //
            }
        });
    }

    public function down(): void
    {
        // Reverter campos adicionados
        Schema::table('lances', function (Blueprint $table) {
            if (Schema::hasColumn('lances', 'cidade_exibicao')) {
                $table->dropColumn('cidade_exibicao');
            }
            if (Schema::hasColumn('lances', 'nome_exibicao')) {
                $table->dropColumn('nome_exibicao');
            }
            if (Schema::hasColumn('lances', 'is_fake')) {
                $table->dropColumn('is_fake');
            }
        });

        // Voltar usuario_id para NOT NULL
        try {
            Schema::table('lances', function (Blueprint $table) {
                $table->dropForeign(['usuario_id']);
            });
        } catch (\Throwable $e) {
        }
        DB::statement('ALTER TABLE lances MODIFY usuario_id BIGINT UNSIGNED NOT NULL');
        Schema::table('lances', function (Blueprint $table) {
            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade');
        });
    }
};
