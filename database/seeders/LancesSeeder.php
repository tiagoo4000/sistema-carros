<?php

namespace Database\Seeders;

use App\Models\Lance;
use App\Models\Lote;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class LancesSeeder extends Seeder
{
    public function run(): void
    {
        // Lances automáticos fake sem criar usuários
        $cities = config('br_cities');
        if (!$cities || count($cities) === 0) {
            $this->command?->warn('Base de cidades brasileiras não disponível. Abortando LancesSeeder.');
            return;
        }

        // 2. Get all open lots or create some if none exist
        $lotes = Lote::where('status', 'aberto')->get();
        
        if ($lotes->isEmpty()) {
            $this->command->info('Nenhum lote aberto encontrado. Pulando seed de lances.');
            return;
        }

        foreach ($lotes as $lote) {
            $this->command->info("Gerando lances para o lote: {$lote->titulo} (ID: {$lote->id})");

            // Número aleatório de lances (5 a 50)
            $numBids = rand(5, 50);
            
            // Valor inicial (do lote)
            $currentValue = $lote->valor_inicial;
            $incrementoMinimo = $lote->valor_minimo_incremento;
            
            // Tempo inicial (~7 dias atrás)
            $currentTime = Carbon::now()->subDays(7);

            for ($i = 0; $i < $numBids; $i++) {
                // Incremento de tempo
                $currentTime->addMinutes(rand(10, 300));
                
                // Evita futuro
                if ($currentTime->isFuture()) {
                    $currentTime = Carbon::now()->subMinutes(rand(1, 60));
                }

                // Incremento de valor (1x a 3x)
                $incremento = $incrementoMinimo * (rand(10, 30) / 10);
                $currentValue += $incremento;

                // Cidade e nome de exibição
                $city = $cities[array_rand($cities)];
                $cidadeUF = $city['nome'] . '/' . $city['uf'];
                $nomeExibicao = 'Participante ' . strtoupper($city['uf']) . '-' . rand(100, 999);

                Lance::create([
                    'lote_id' => $lote->id,
                    'valor' => $currentValue,
                    'is_fake' => true,
                    'nome_exibicao' => $nomeExibicao,
                    'cidade_exibicao' => $cidadeUF,
                    'created_at' => $currentTime,
                    'updated_at' => $currentTime,
                ]);
            }
        }
    }
}
