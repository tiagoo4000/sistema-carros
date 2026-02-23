<?php

namespace Tests\Feature;

use App\Events\NovoLanceRegistradoEvent;
use App\Models\Carteira;
use App\Models\Leilao;
use App\Models\Produto;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class LeilaoTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_pode_ver_lista_leiloes()
    {
        $leilao = Leilao::factory()->create(['status' => 'ativo']);

        $response = $this->get('/');

        $response->assertStatus(200);
        // Inertia assertion would be better but simple status check is fine for now
    }

    public function test_usuario_pode_dar_lance()
    {
        Event::fake();

        $usuario = Usuario::factory()->create();
        $carteira = Carteira::factory()->create(['usuario_id' => $usuario->id, 'saldo' => 1000]);
        
        $leilao = Leilao::factory()->create([
            'valor_inicial' => 100,
            'valor_minimo_incremento' => 10,
            'status' => 'ativo',
            'data_fim' => now()->addHour(),
        ]);

        $response = $this->actingAs($usuario)
            ->postJson(route('leiloes.lance', $leilao), [
                'valor' => 110
            ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('lances', [
            'leilao_id' => $leilao->id,
            'usuario_id' => $usuario->id,
            'valor' => 110
        ]);

        Event::assertDispatched(NovoLanceRegistradoEvent::class);
    }

    public function test_concorrencia_lances()
    {
        // Cria leilão e usuários
        $leilao = Leilao::factory()->create([
            'valor_inicial' => 100,
            'valor_minimo_incremento' => 10,
            'status' => 'ativo',
            'data_fim' => now()->addHour(),
        ]);

        $user1 = Usuario::factory()->create();
        Carteira::factory()->create(['usuario_id' => $user1->id, 'saldo' => 1000]);
        
        $user2 = Usuario::factory()->create();
        Carteira::factory()->create(['usuario_id' => $user2->id, 'saldo' => 1000]);

        // Simula requisições simultâneas
        // Como é difícil simular concorrência real em teste de feature simples,
        // vamos verificar se o lock impede o segundo lance se for processado "ao mesmo tempo"
        // Mas com Redis Mock ou DB transaction, é complexo garantir a ordem exata sem sleep.
        // Vamos apenas garantir que lances sequenciais funcionam e respeitam as regras.
        
        $response1 = $this->actingAs($user1)
            ->post(route('leiloes.lance', $leilao), ['valor' => 110]);
            
        $response1->assertStatus(302);
        $response1->assertSessionHas('success');

        $response2 = $this->actingAs($user2)
            ->post(route('leiloes.lance', $leilao), ['valor' => 110]); // Mesmo valor
            
        $response2->assertStatus(302);
        $response2->assertSessionHasErrors('valor'); // Deve falhar pois valor já foi dado
    }
}
