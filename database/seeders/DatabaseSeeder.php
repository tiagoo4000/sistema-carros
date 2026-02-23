<?php

namespace Database\Seeders;

use App\Models\Leilao;
use App\Models\Lote;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin
        Usuario::updateOrCreate(
            ['email' => 'admin@leilao.com'],
            [
                'nome' => 'Admin',
                'senha' => Hash::make('password'),
                'admin' => true,
                'bloqueado' => false,
                'documentos_validos' => true,
                'cidade' => 'São Paulo',
                'estado' => 'SP',
            ]
        );

        // Usuário Comum
        Usuario::updateOrCreate(
            ['email' => 'usuario@leilao.com'],
            [
                'nome' => 'Usuário Demo',
                'senha' => Hash::make('password'),
                'admin' => false,
                'bloqueado' => false,
                'documentos_validos' => true,
                'cidade' => 'São Paulo',
                'estado' => 'SP',
            ]
        );

        // Leilão Ativo
        $leilaoAtivo = Leilao::create([
            'titulo' => 'Leilão de Imóveis - Centro',
            'descricao' => 'Grande oportunidade de imóveis no centro da cidade.',
            'local' => 'Online',
            'data_inicio' => now()->subHour(),
            'data_fim' => now()->addHours(2),
            'status' => 'ativo',
        ]);

        // Lotes do Leilão Ativo
        Lote::create([
            'leilao_id' => $leilaoAtivo->id,
            'titulo' => 'Apartamento 2 Quartos',
            'descricao' => 'Apartamento reformado, 2 quartos, 1 vaga.',
            'valor_inicial' => 150000.00,
            'valor_minimo_incremento' => 1000.00,
            'ordem' => 1,
            'status' => 'aberto',
        ]);

        Lote::create([
            'leilao_id' => $leilaoAtivo->id,
            'titulo' => 'Sala Comercial',
            'descricao' => 'Sala comercial 40m2.',
            'valor_inicial' => 80000.00,
            'valor_minimo_incremento' => 500.00,
            'ordem' => 2,
            'status' => 'aberto',
        ]);

        // Leilão Futuro (pré-início)
        $leilaoAgendado = Leilao::create([
            'titulo' => 'Leilão de Veículos',
            'descricao' => 'Veículos recuperados de financiamento.',
            'local' => 'Pátio Central',
            'data_inicio' => now()->addDays(2),
            'data_fim' => now()->addDays(3),
            'status' => 'ativo',
        ]);

        Lote::create([
            'leilao_id' => $leilaoAgendado->id,
            'titulo' => 'Honda Civic 2020',
            'descricao' => 'Completo, banco de couro.',
            'valor_inicial' => 60000.00,
            'valor_minimo_incremento' => 500.00,
            'ordem' => 1,
            'status' => 'aberto',
        ]);

        // Generate realistic bids
        $this->call([
            FixUsuarioLocalizacaoSeeder::class,
            LancesSeeder::class,
        ]);
    }
}
