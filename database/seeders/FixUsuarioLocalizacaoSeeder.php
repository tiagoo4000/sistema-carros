<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use Faker\Factory as FakerFactory;

class FixUsuarioLocalizacaoSeeder extends Seeder
{
    public function run(): void
    {
        $ufs = ['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'];
        $faker = FakerFactory::create('pt_BR');

        // Garante localização do usuário demo
        Usuario::where('email', 'usuario@leilao.com')->update([
            'cidade' => 'São Paulo',
            'estado' => 'SP',
        ]);

        // Atualiza usuários sem UF válida para dados brasileiros
        $usuarios = Usuario::where(function($q) use ($ufs) {
                $q->whereNull('estado')->orWhereNotIn('estado', $ufs);
            })
            ->get();

        foreach ($usuarios as $usuario) {
            $usuario->cidade = $faker->city();
            $usuario->estado = $faker->stateAbbr();
            $usuario->save();
        }
    }
}

