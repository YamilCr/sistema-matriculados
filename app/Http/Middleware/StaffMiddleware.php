<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class StaffMiddleware
{
    /**
     * Maneja una solicitud entrante.
     */
   public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Permitir si es Admin (1) O Staff (3)
        // Ajusta los IDs según tu base de datos
        if (Auth::check() && in_array($user->role_id, [1, 3])) {
            return $next($request);
        }

        return redirect()->route('dashboard')->with('error', 'No tienes permisos suficientes.');
    }
}