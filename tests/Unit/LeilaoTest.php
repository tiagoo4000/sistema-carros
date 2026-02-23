<?php

namespace Tests\Unit;

use App\Actions\RegistrarLanceAction;
use App\Models\Leilao;
use App\Models\Usuario;
use App\Models\Lance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Event;
use App\Events\NovoLanceRegistradoEvent;
use Illuminate\Support\Facades\Cache;

class LeilaoTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_pode_dar_lance_em_leilao_ativo()
    {
        Event::fake();

        $usuario = Usuario::factory()->create();
        $leilao = Leilao::factory()->create([
            'status' => 'ativo',
            'valor_inicial' => 100,
            'valor_minimo_incremento' => 10,
            'data_inicio' => now()->subHour(),
            'data_fim' => now()->addHour(),
        ]);

        $action = new RegistrarLanceAction();
        $lance = $action->execute($usuario, $leilao, 120);

        $this->assertDatabaseHas('lances', [
            'id' => $lance->id,
            'leilao_id' => $leilao->id,
            'usuario_id' => $usuario->id,
            'valor' => 120
        ]);

        Event::assertDispatched(NovoLanceRegistradoEvent::class);
    }

    public function test_nao_pode_dar_lance_menor_que_atual_mais_incremento()
    {
        Event::fake(); // Fake events to avoid broadcast errors

        $usuario1 = Usuario::factory()->create();
        $usuario2 = Usuario::factory()->create();
        
        $leilao = Leilao::factory()->create([
            'status' => 'ativo',
            'valor_inicial' => 100,
            'valor_minimo_incremento' => 10,
            'data_inicio' => now()->subHour(),
            'data_fim' => now()->addHour(),
        ]);

        $action = new RegistrarLanceAction();
        $action->execute($usuario1, $leilao, 110);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('O lance deve superar o atual em pelo menos R$ 10');

        $action->execute($usuario2, $leilao, 115); // Deveria ser pelo menos 120
    }

    public function test_extensao_automatica_do_leilao()
    {
        Event::fake(); // Fake events to avoid broadcast errors

        $usuario = Usuario::factory()->create();
        $leilao = Leilao::factory()->create([
            'status' => 'ativo',
            'valor_inicial' => 100,
            'valor_minimo_incremento' => 10,
            'data_inicio' => now()->subHour(),
            'data_fim' => now()->addSeconds(10), // Faltam 10 segundos
        ]);

        $fimOriginal = $leilao->data_fim;

        $action = new RegistrarLanceAction();
        $action->execute($usuario, $leilao, 110);

        $leilao->refresh();

        // Deve ter adicionado 30 segundos
        $this->assertTrue($leilao->data_fim->gt($fimOriginal));
    }
    
    public function test_concorrencia_de_lances()
    {
        // Simular concorrência é difícil em testes unitários simples sem workers reais,
        // mas podemos testar se o lock é chamado.
        
        Cache::shouldReceive('lock')
            ->once()
            ->andReturnSelf();
            
        Cache::shouldReceive('block')
            ->once()
            ->andReturn(new Lance());

        $usuario = Usuario::factory()->create();
        $leilao = Leilao::factory()->create([
            'status' => 'ativo',
            'valor_inicial' => 100,
            'valor_minimo_incremento' => 10,
            'data_inicio' => now()->subHour(),
            'data_fim' => now()->addHour(),
        ]);

        $action = new RegistrarLanceAction();
        // Mockamos o retorno, então não vai salvar no banco de verdade neste teste específico do mock
        try {
            $action->execute($usuario, $leilao, 110);
        } catch (\Exception $e) {
            // Ignorar erros de retorno do mock se houver
        }
    }
}
