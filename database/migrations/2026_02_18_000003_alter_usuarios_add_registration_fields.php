<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            if (!Schema::hasColumn('usuarios', 'tipo_pessoa')) {
                $table->enum('tipo_pessoa', ['pf', 'pj'])->default('pf')->after('nome');
            }
            // Pessoa Física
            if (!Schema::hasColumn('usuarios', 'rg')) {
                $table->string('rg')->nullable()->after('cpf');
            }
            if (!Schema::hasColumn('usuarios', 'orgao_expedidor')) {
                $table->string('orgao_expedidor')->nullable()->after('rg');
            }
            if (!Schema::hasColumn('usuarios', 'data_nascimento')) {
                $table->date('data_nascimento')->nullable()->after('orgao_expedidor');
            }
            if (!Schema::hasColumn('usuarios', 'renda_mensal')) {
                $table->decimal('renda_mensal', 12, 2)->nullable()->after('data_nascimento');
            }
            if (!Schema::hasColumn('usuarios', 'apelido')) {
                $table->string('apelido')->nullable()->after('nome');
            }
            if (!Schema::hasColumn('usuarios', 'telefone_fixo')) {
                $table->string('telefone_fixo')->nullable()->after('telefone');
            }
            if (!Schema::hasColumn('usuarios', 'celular2')) {
                $table->string('celular2')->nullable()->after('telefone');
            }
            if (!Schema::hasColumn('usuarios', 'ocupacao')) {
                $table->string('ocupacao')->nullable()->after('renda_mensal');
            }
            // Pessoa Jurídica
            if (!Schema::hasColumn('usuarios', 'cnpj')) {
                $table->string('cnpj')->nullable()->unique()->after('cpf');
            }
            if (!Schema::hasColumn('usuarios', 'razao_social')) {
                $table->string('razao_social')->nullable()->after('cnpj');
            }
            if (!Schema::hasColumn('usuarios', 'nome_fantasia')) {
                $table->string('nome_fantasia')->nullable()->after('razao_social');
            }
            if (!Schema::hasColumn('usuarios', 'inscricao_estadual')) {
                $table->string('inscricao_estadual')->nullable()->after('nome_fantasia');
            }
            if (!Schema::hasColumn('usuarios', 'data_abertura')) {
                $table->date('data_abertura')->nullable()->after('inscricao_estadual');
            }
            if (!Schema::hasColumn('usuarios', 'faturamento_mensal')) {
                $table->decimal('faturamento_mensal', 12, 2)->nullable()->after('data_abertura');
            }
            if (!Schema::hasColumn('usuarios', 'telefone_comercial')) {
                $table->string('telefone_comercial')->nullable()->after('telefone');
            }
            if (!Schema::hasColumn('usuarios', 'responsavel_nome')) {
                $table->string('responsavel_nome')->nullable()->after('telefone_comercial');
            }
            if (!Schema::hasColumn('usuarios', 'responsavel_cpf')) {
                $table->string('responsavel_cpf')->nullable()->after('responsavel_nome');
            }
            // Endereço
            if (!Schema::hasColumn('usuarios', 'cep')) {
                $table->string('cep', 9)->nullable()->after('documentos_validos');
            }
            if (!Schema::hasColumn('usuarios', 'endereco')) {
                $table->string('endereco')->nullable()->after('cep');
            }
            if (!Schema::hasColumn('usuarios', 'numero')) {
                $table->string('numero')->nullable()->after('endereco');
            }
            if (!Schema::hasColumn('usuarios', 'bairro')) {
                $table->string('bairro')->nullable()->after('numero');
            }
            if (!Schema::hasColumn('usuarios', 'estado')) {
                $table->string('estado', 2)->nullable()->change();
            }
            if (!Schema::hasColumn('usuarios', 'cidade')) {
                $table->string('cidade')->nullable()->change();
            }
            if (!Schema::hasColumn('usuarios', 'complemento')) {
                $table->string('complemento')->nullable()->after('bairro');
            }
            // Preferências
            if (!Schema::hasColumn('usuarios', 'interesses')) {
                $table->json('interesses')->nullable()->after('complemento');
            }
            if (!Schema::hasColumn('usuarios', 'como_conheceu')) {
                $table->string('como_conheceu')->nullable()->after('interesses');
            }
            if (!Schema::hasColumn('usuarios', 'status_cadastro')) {
                $table->string('status_cadastro')->default('aguardando_documentacao')->after('como_conheceu');
            }
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $drops = [
                'tipo_pessoa','rg','orgao_expedidor','data_nascimento','renda_mensal','apelido','telefone_fixo','celular2','ocupacao',
                'cnpj','razao_social','nome_fantasia','inscricao_estadual','data_abertura','faturamento_mensal','telefone_comercial','responsavel_nome','responsavel_cpf',
                'cep','endereco','numero','bairro','complemento','interesses','como_conheceu','status_cadastro'
            ];
            foreach ($drops as $col) {
                if (Schema::hasColumn('usuarios', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

