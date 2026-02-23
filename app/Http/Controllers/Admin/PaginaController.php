<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class PaginaController extends Controller
{
    public function leiloes()
    {
        return Inertia::render('Admin/Construction', ['title' => 'Páginas - Leilões']);
    }

    public function quemSomos()
    {
        return Inertia::render('Admin/Construction', ['title' => 'Páginas - Quem Somos']);
    }

    public function patios()
    {
        return Inertia::render('Admin/Construction', ['title' => 'Páginas - Pátios']);
    }

    public function faleConosco()
    {
        return Inertia::render('Admin/Construction', ['title' => 'Páginas - Fale Conosco']);
    }

    public function faq()
    {
        return Inertia::render('Admin/Construction', ['title' => 'Páginas - Dúvidas Frequentes']);
    }

    public function privacidade()
    {
        return Inertia::render('Admin/Construction', ['title' => 'Páginas - Política de Privacidade']);
    }

    public function comoFunciona()
    {
        return Inertia::render('Admin/Construction', ['title' => 'Páginas - Como Funciona']);
    }

    public function termos()
    {
        return Inertia::render('Admin/Construction', ['title' => 'Páginas - Termos de Uso']);
    }
}
