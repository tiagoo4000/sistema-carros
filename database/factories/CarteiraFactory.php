<?php

namespace Database\Factories;

use App\Models\Carteira;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

class CarteiraFactory extends Factory
{
    protected $model = Carteira::class;

    public function definition(): array
    {
        return [
            'usuario_id' => Usuario::factory(),
            'saldo' => fake()->randomFloat(2, 0, 10000),
        ];
    }
}
