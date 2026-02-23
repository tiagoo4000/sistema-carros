<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\LeilaoController as AdminLeilaoController;
use App\Http\Controllers\Admin\LoteController;
use App\Http\Controllers\Admin\DocumentoController;
use App\Http\Controllers\Admin\VerificacaoController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ComitenteController;
use App\Http\Controllers\Admin\PaginaController;
use App\Http\Controllers\Site\HomeController;
use App\Http\Controllers\Site\LeilaoController as SiteLeilaoController;
use App\Http\Controllers\Site\LoteController as SiteLoteController;
use App\Http\Controllers\Admin\WhatsAppController;
use App\Http\Controllers\Site\LanceController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Admin\SystemConfigController;
use App\Http\Controllers\Admin\SiteConfigController;
use App\Http\Controllers\Admin\BannerController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- ÁREA PÚBLICA / SITE ---

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/leiloes', [SiteLeilaoController::class, 'index'])->name('leiloes.index');
Route::get('/leiloes/{leilao}', [SiteLeilaoController::class, 'show'])->name('leiloes.show');
Route::get('/lotes/{lote}', [SiteLoteController::class, 'show'])->name('lotes.show');
Route::get('/lotes/{lote}/historico', [SiteLoteController::class, 'history'])->name('lotes.history');

// Search API
Route::get('/api/search/autocomplete', [HomeController::class, 'autocomplete'])->name('api.search.autocomplete');
Route::get('/api/search/count', [HomeController::class, 'searchCount'])->name('api.search.count');
Route::get('/api/home/data', [HomeController::class, 'getHomeData'])->name('api.home.data');

// Autenticação
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'login'])->name('login');
    Route::post('login', [AuthController::class, 'authenticate']);
    Route::get('registrar', [AuthController::class, 'create'])->name('register');
    Route::post('registrar', [AuthController::class, 'store']);
});

// Área do Usuário Autenticado
Route::middleware(['auth'])->group(function () {
    Route::post('logout', [AuthController::class, 'destroy'])->name('logout');

    Route::get('/minha-conta', [\App\Http\Controllers\Site\ClientDashboardController::class, 'index'])->name('minha-conta');
    Route::get('/minha-conta/documentos', [\App\Http\Controllers\Site\DocumentoUsuarioController::class, 'index'])->name('minha-conta.documentos');
    Route::post('/minha-conta/documentos', [\App\Http\Controllers\Site\DocumentoUsuarioController::class, 'store'])->name('minha-conta.documentos.store');
    Route::get('/minha-conta/meus-documentos', [\App\Http\Controllers\Site\DocumentoUsuarioController::class, 'meus'])->name('minha-conta.meus-documentos');
    Route::put('/minha-conta/perfil', [\App\Http\Controllers\Site\ClientDashboardController::class, 'updateProfile'])->name('minha-conta.perfil.update');
    Route::put('/minha-conta/senha', [\App\Http\Controllers\Site\ClientDashboardController::class, 'updatePassword'])->name('minha-conta.senha.update');

    // Termos do Cliente
    Route::get('/minha-conta/termos', [\App\Http\Controllers\Site\TermoClienteController::class, 'index'])->name('cliente.termos.index');
    Route::get('/minha-conta/termos/{termo}', [\App\Http\Controllers\Site\TermoClienteController::class, 'show'])->name('cliente.termos.show');
    Route::get('/minha-conta/termos/{termo}/pdf', [\App\Http\Controllers\Site\TermoClienteController::class, 'pdf'])->name('cliente.termos.pdf');
    Route::get('/minha-conta/termos/{termo}/download', [\App\Http\Controllers\Site\TermoClienteController::class, 'download'])->name('cliente.termos.download');
    Route::post('/minha-conta/termos/{termo}/aceitar', [\App\Http\Controllers\Site\TermoClienteController::class, 'accept'])->name('cliente.termos.accept');
    Route::post('/minha-conta/termos/{termo}/visualizado', [\App\Http\Controllers\Site\TermoClienteController::class, 'markViewed'])->name('cliente.termos.viewed');

    // Lances
    Route::post('/lotes/{lote}/lance', [LanceController::class, 'store'])
        ->middleware(\App\Http\Middleware\EnsureUserIsVerified::class)
        ->name('lotes.lance');

    // Compra direta
    Route::post('/lotes/{lote}/comprar', [SiteLoteController::class, 'purchase'])->name('lotes.comprar');
});

// --- ÁREA ADMINISTRATIVA ---

Route::middleware(['auth', 'can:admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Configurações do Sistema
    Route::get('configuracoes/layout', [SystemConfigController::class, 'index'])->name('config.layout.index');
    Route::post('configuracoes/layout', [SystemConfigController::class, 'update'])->name('config.layout.update');

    // Layout do Site
    Route::prefix('layout')->name('layout.')->group(function () {
        // Configuração Geral (Nome, Logo, etc)
        Route::get('config', [SiteConfigController::class, 'index'])->name('config.index');
        Route::post('config', [SiteConfigController::class, 'update'])->name('config.update');

        // Banners
        Route::resource('banners', BannerController::class);

        // E-mail: Templates
        Route::prefix('email/templates')->name('email.templates.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'index'])->name('index');
            Route::get('/{key}/edit', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'edit'])->name('edit');
            Route::post('/{key}', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'update'])->name('update');
            Route::get('/{key}/preview', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'preview'])->name('preview');
            Route::post('/{key}/test', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'test'])->name('test');
        });
        // WhatsApp: Templates
        Route::prefix('whatsapp/templates')->name('whatsapp.templates.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\WhatsAppTemplateController::class, 'index'])->name('index');
            Route::get('/{key}/edit', [\App\Http\Controllers\Admin\WhatsAppTemplateController::class, 'edit'])->name('edit');
            Route::post('/{key}/update', [\App\Http\Controllers\Admin\WhatsAppTemplateController::class, 'update'])->name('update');
            Route::get('/{key}/preview', [\App\Http\Controllers\Admin\WhatsAppTemplateController::class, 'preview'])->name('preview');
            Route::post('/{key}/test', [\App\Http\Controllers\Admin\WhatsAppTemplateController::class, 'test'])->name('test');
        });
    });

    // Configuração de E-mail
    Route::prefix('email')->name('email.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\EmailConfigController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Admin\EmailConfigController::class, 'update'])->name('update');
        Route::post('/test', [\App\Http\Controllers\Admin\EmailConfigController::class, 'test'])->name('test');
    });

    // Gestão de Categorias
    Route::resource('categorias', CategoryController::class)->parameters([
        'categorias' => 'categoria'
    ]);

    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Gestão de Usuários
    Route::get('usuarios/administradores', [UsuarioController::class, 'admins'])->name('usuarios.admins');
    Route::resource('usuarios', UsuarioController::class)->only(['index', 'edit', 'update']);

    // Gestão de Documentos
    Route::get('documentos', [DocumentoController::class, 'index'])->name('documentos.index');
    Route::get('documentos/novo', [DocumentoController::class, 'create'])->name('documentos.create');
    Route::post('documentos', [DocumentoController::class, 'store'])->name('documentos.store');
    Route::post('documentos/{documento}/validar', [DocumentoController::class, 'validar'])->name('documentos.validar');
    Route::post('documentos/{documento}/rejeitar', [DocumentoController::class, 'rejeitar'])->name('documentos.rejeitar');
    Route::get('documentos/{documento}/download', [DocumentoController::class, 'download'])->name('documentos.download');

    // Verificações (nova área)
    Route::prefix('verificacoes')->name('verificacoes.')->group(function () {
        Route::get('/', [VerificacaoController::class, 'index'])->name('index');
        Route::get('/{usuario}', [VerificacaoController::class, 'show'])->name('show');
        Route::post('/{usuario}/aprovar', [VerificacaoController::class, 'aprovar'])->name('aprovar');
        Route::post('/{usuario}/rejeitar', [VerificacaoController::class, 'rejeitar'])->name('rejeitar');
        Route::post('/{usuario}/forcar-aprovacao', [VerificacaoController::class, 'forcarAprovacao'])->name('forcar_aprovacao');
    });

    // Termos e Contas
    Route::resource('contas', \App\Http\Controllers\Admin\ContaBancariaController::class)
        ->parameters(['contas' => 'conta']);
    Route::patch('contas/{conta}/definir-padrao', [\App\Http\Controllers\Admin\ContaBancariaController::class, 'definirPadrao'])->name('contas.definir_padrao');
    Route::get('termos', [\App\Http\Controllers\Admin\TermoController::class, 'index'])->name('termos.index');
    Route::post('termos/gerar-hoje', [\App\Http\Controllers\Admin\TermoController::class, 'generateToday'])->name('termos.generate_today');
    Route::post('termos/gerar-pendentes', [\App\Http\Controllers\Admin\TermoController::class, 'generatePending'])->name('termos.generate_pending');
    Route::get('termos/{termo}/pdf', [\App\Http\Controllers\Admin\TermoController::class, 'pdf'])->name('termos.pdf');
    Route::post('termos/{termo}/disponibilizar', [\App\Http\Controllers\Admin\TermoController::class, 'disponibilizar'])->name('termos.disponibilizar');
    Route::post('termos/{termo}/reenviar', [\App\Http\Controllers\Admin\TermoController::class, 'resend'])->name('termos.resend');
    Route::delete('termos/{termo}', [\App\Http\Controllers\Admin\TermoController::class, 'destroy'])->name('termos.destroy');

    // Gestão de Leilões
    Route::resource('leiloes', AdminLeilaoController::class)
        ->parameters(['leiloes' => 'leilao']);
    
    // Ações Administrativas Leilões
    Route::put('leiloes/{leilao}/toggle-status', [AdminLeilaoController::class, 'toggleStatus'])->name('leiloes.toggle-status');
    Route::post('leiloes/{leilao}/gerar-lances', [AdminLeilaoController::class, 'gerarLances'])->name('leiloes.gerar-lances');
    Route::post('leiloes/{leilao}/limpar-lances', [AdminLeilaoController::class, 'limparLances'])->name('leiloes.limpar-lances');
    Route::post('leiloes/{leilao}/gerar-views', [AdminLeilaoController::class, 'gerarVisualizacoes'])->name('leiloes.gerar-views');

    // Gestão de Lotes (Nested Resource)
    Route::resource('leiloes.lotes', LoteController::class)
        ->parameters(['leiloes' => 'leilao'])
        ->except(['index', 'show']);
    
    // Lances por Lote (Admin)
    Route::get('leiloes/{leilao}/lotes/{lote}/lances', [LoteController::class, 'lances'])->name('leiloes.lotes.lances.index');
    Route::delete('leiloes/{leilao}/lotes/{lote}/lances/{lance}', [LoteController::class, 'destroyLance'])->name('leiloes.lotes.lances.destroy');
    
    // Listagem de Lotes (Geral ou Filtrada)
    Route::get('lotes', [LoteController::class, 'index'])->name('lotes.index');

    // Gestão de Comitentes
    Route::resource('comitentes', ComitenteController::class);

    // Páginas
    Route::prefix('paginas')->name('paginas.')->group(function () {
        Route::get('leiloes', [PaginaController::class, 'leiloes'])->name('leiloes');
        Route::get('quem-somos', [PaginaController::class, 'quemSomos'])->name('quem_somos');
        Route::get('patios', [PaginaController::class, 'patios'])->name('patios');
        Route::get('fale-conosco', [PaginaController::class, 'faleConosco'])->name('fale_conosco');
        
        // Rodapé
        Route::get('duvidas-frequentes', [PaginaController::class, 'faq'])->name('duvidas_frequentes');
        Route::get('politica-privacidade', [PaginaController::class, 'privacidade'])->name('politica_privacidade');
        Route::get('como-funciona', [PaginaController::class, 'comoFunciona'])->name('como_funciona');
        Route::get('termos-uso', [PaginaController::class, 'termos'])->name('termos_uso');
    });

    // API: Lances recentes para notificações do header (admin)
    Route::get('api/lances-recentes', function () {
        $clearedAt = session('admin_notifications_cleared_at');
        $lances = \App\Models\Lance::with([
            'usuario:id,nome,documentos_validos,admin',
            'lote:id,titulo'
        ])
        ->whereHas('usuario', function ($q) {
            $q->where('admin', false)->where('documentos_validos', true);
        })
        ->when($clearedAt, function ($q) use ($clearedAt) {
            $q->where('created_at', '>', $clearedAt);
        })
        ->latest()
        ->limit(20)
        ->get();

        return response()->json($lances->map(function ($l) {
            return [
                'id' => $l->id,
                'usuario' => [
                    'nome' => $l->usuario?->nome ?? 'Cliente',
                ],
                'lote' => [
                    'id' => $l->lote?->id,
                    'titulo' => $l->lote?->titulo,
                    'url' => $l->lote ? route('lotes.show', $l->lote) : null,
                ],
                'valor' => (float) $l->valor,
                'criado_em' => $l->created_at?->toIso8601String(),
            ];
        }));
    })->name('admin.api.lances_recentes');

    // API: Limpar notificações (marca o momento da limpeza na sessão)
    Route::post('api/notificacoes/limpar', function () {
        session(['admin_notifications_cleared_at' => now()]);
        return response()->json(['ok' => true]);
    })->name('admin.api.notificacoes_limpar');

    Route::post('api/whatsapp/send', [WhatsAppController::class, 'send'])->name('api.whatsapp.send');
});

// Test Route for Status Update
Route::post('/api/test/lotes/{lote}/status', function (\Illuminate\Http\Request $request, \App\Models\Lote $lote) {
    $status = $request->input('status');
    $lote->status = $status;
    $lote->save();
    
    event(new \App\Events\LoteStatusUpdated($lote));
    
    return response()->json(['success' => true, 'status' => $status]);
});
