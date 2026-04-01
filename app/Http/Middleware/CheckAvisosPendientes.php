<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <--- ESTA ES LA LÍNEA QUE FALTA

class CheckAvisosPendientes
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            // Buscamos si el usuario tiene algún aviso urgente sin leer
            $avisoUrgente = Auth::user()->avisos()
                ->where('prioridad', 'Urgente')
                ->wherePivot('leido_at', null)
                ->first();

            // Si existe, lo compartimos con todas las vistas para que el modal lo muestre
            if ($avisoUrgente) {
                view()->share('avisoUrgente', $avisoUrgente);
            }
        }

        return $next($request);
    }
}