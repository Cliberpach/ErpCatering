<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {

        

        if (auth()->check() && Auth::user()->getRoleNames()[0] == $role) {
            return $next($request); // Si tiene el rol, deja pasar
        }

        abort(403, 'Solo los supervisores pueden registrar requerimientos.');
    }
}
