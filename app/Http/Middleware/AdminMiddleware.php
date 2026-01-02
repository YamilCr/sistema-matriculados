<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Maneja una solicitud entrante.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Verificamos si el usuario está autenticado 
        // 2. Verificamos si su role_id es 1 (Admin según tu Seeder) 
        if (Auth::check() && Auth::user()->role_id === 1) {
            return $next($request);
        }

        // Si no es admin, redirigimos al dashboard con un mensaje de error
        return redirect()->route('dashboard')->with('error', 'No tienes permisos de Super Usuario.');
    }
}