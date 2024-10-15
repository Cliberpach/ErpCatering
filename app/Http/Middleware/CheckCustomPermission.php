<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckCustomPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next,string $permission): Response
    {
        if (Auth::check()) {
            if (Auth::user()->can($permission)) {
                return $next($request);
            }
        }

        // Redirigir si no tiene el permiso
        return redirect('/acceso_denegado')->with('error', 'No tienes permiso para acceder a esta página.');
    }
}
