<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AutoLogout
{
    /**
     * Handle an incoming request.
     * Cierra la sesión si no hay actividad en los últimos 5 minutos (300 segundos).
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $last = $request->session()->get('last_activity');
            $now = time();

            if ($last && ($now - $last) > 900) { // 15 minutos
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('status', 'Sesión cerrada por inactividad.');
            }

            $request->session()->put('last_activity', $now);
        }

        return $next($request);
    }
}
