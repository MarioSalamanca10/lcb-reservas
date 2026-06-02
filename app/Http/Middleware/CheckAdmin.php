<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Si el usuario inició sesión y su rol es 'admin', lo dejamos pasar
        if (auth()->check() && auth()->user()->rol === 'admin') {
            return $next($request);
        }

        // Si es un profe intentando entrar a la zona de admin, lo devolvemos a sus reservas
        return redirect()->route('reservas.create')->with('error', 'Acceso denegado. Esta zona es solo para administradores.');
    }
}
