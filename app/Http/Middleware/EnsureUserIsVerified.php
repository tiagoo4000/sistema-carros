<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }
        if ($user->isAdmin()) {
            return $next($request);
        }
        if (!($user->documentos_validos || $user->verificacao_forcada)) {
            return back()->withErrors([
                'valor' => 'Para participar dos leilões é necessário enviar e aguardar aprovação dos seus documentos.'
            ]);
        }
        return $next($request);
    }
}
