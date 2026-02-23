<?php

namespace Database\Factories;

use App\Models\Leilao;
use App\Models\Produto;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeilaoFactory extends Factory
{
    protected $model = Leilao::class;

    public function definition(): array
    {
        $valorInicial = fake()->randomFloat(2, 100, 1000);
        
        return [
            'produto_id' => Produto::factory(),
            'usuario_criador_id' => Usuario::factory(),
            'valor_inicial' => $valorInicial,
            'valor_minimo_incremento' => $valorInicial * 0.05,
            'data_inicio' => now(),
            'data_fim' => now()->addDays(3),
            'status' => 'ativo',
        ];
    }
}
