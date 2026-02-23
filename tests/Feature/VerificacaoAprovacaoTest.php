<?php

namespace Tests\Feature;

use App\Models\Documento;
use App\Models\Leilao;
use App\Models\Lote;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerificacaoAprovacaoTest extends TestCase
{
    use RefreshDatabase;

    public function test_forcar_aprovacao_define_flags_e_bloqueia_envio_documentos(): void
    {
        $admin = Usuario::factory()->create(['admin' => true]);
        $user = Usuario::factory()->create(['admin' => false, 'documentos_validos' => false]);

        $this->actingAs($admin)
            ->post(route('admin.verificacoes.forcar_aprovacao', $user->id))
            ->assertSessionHas('success');

        $user->refresh();
        $this->assertTrue($user->documentos_validos);
        $this->assertTrue($user->verificacao_forcada);

        // Usuário não consegue mais enviar documentos
        $this->actingAs($user)
            ->post(route('minha-conta.documentos.store'), [
                'tipo' => 'selfie',
                'arquivo' => \Illuminate\Http\UploadedFile::fake()->image('selfie.jpg'),
            ])
            ->assertSessionHasErrors();
    }

    public function test_aprovar_stricto_requer_todos_os_documentos(): void
    {
        $admin = Usuario::factory()->create(['admin' => true]);
        $user = Usuario::factory()->create(['admin' => false, 'documentos_validos' => false]);
        Documento::create(['usuario_id' => $user->id, 'tipo' => 'selfie', 'caminho_arquivo' => 'x/selfie.jpg', 'validado' => false]);

        $this->actingAs($admin)
            ->post(route('admin.verificacoes.aprovar', $user->id))
            ->assertSessionHas('success');

        $user->refresh();
        $this->assertFalse($user->documentos_validos, 'A aprovação estrita não deve aprovar sem todos os documentos.');
    }

    public function test_lance_permitido_quando_verificacao_forcada(): void
    {
        $user = Usuario::factory()->create(['admin' => false, 'documentos_validos' => true, 'verificacao_forcada' => true]);
        $leilao = Leilao::factory()->create(['status' => 'ativo', 'tipo' => 'leilao']);
        $lote = \App\Models\Lote::create([
            'leilao_id' => $leilao->id,
            'titulo' => 'Item',
            'valor_inicial' => 1000,
            'ordem' => 1,
            'status' => 'aberto',
        ]);

        $this->actingAs($user)
            ->post(route('lotes.lance', $lote->id), ['valor' => 1200])
            ->assertSessionHasNoErrors();
    }
}
