<?php

namespace Database\Factories;

use App\Models\Lance;
use App\Models\Lote;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

class LanceFactory extends Factory
{
    protected $model = Lance::class;

    public function definition(): array
    {
        return [
            'lote_id' => Lote::factory(),
            'usuario_id' => Usuario::factory(),
            'valor' => fake()->randomFloat(2, 1000, 50000),
            'created_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'updated_at' => now(),
        ];
    }
}
