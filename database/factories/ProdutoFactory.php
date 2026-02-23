<?php

namespace Database\Factories;

use App\Models\Produto;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProdutoFactory extends Factory
{
    protected $model = Produto::class;

    public function definition(): array
    {
        return [
            'nome' => fake()->word(),
            'descricao' => fake()->sentence(),
            'imagem_url' => 'img1.jpg',
        ];
    }
}
