<?php

namespace Database\Factories;

use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsuarioFactory extends Factory
{
    protected $model = Usuario::class;

    public function definition(): array
    {
        return [
            'nome' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'telefone' => fake()->phoneNumber(),
            'cidade' => fake('pt_BR')->city(),
            'estado' => fake('pt_BR')->stateAbbr(),
            'email_verificado_em' => now(),
            'senha' => Hash::make('password'),
            'remember_token' => Str::random(10),
            'admin' => false,
        ];
    }
}
