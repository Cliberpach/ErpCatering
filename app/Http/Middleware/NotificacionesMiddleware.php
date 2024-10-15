<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;

class NotificacionesMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->getRoleNames()[0] === "LOGISTICA") {
            $colaboradorId = Auth::user()->colaborador_id;

            $notificaciones = DB::select('SELECT 
                                n.id as notificacion_id,
                                r.id  as requerimiento_id,
                                co.nombre AS supervisor_nombre,
                                pr.nombre AS proyecto_nombre,
                                r.created_at AS fecha_registro,
                                r.estado as requerimiento_estado,
                                n.estado as notificacion_estado
                            FROM 
                                notificaciones AS n
                            INNER JOIN requerimientos AS r ON r.id = n.requerimiento_id
                            INNER JOIN proyectos AS pr ON pr.id = r.proyecto_id
                            INNER JOIN colaboradores AS co ON co.id = r.supervisor_id
                            WHERE r.estado != "ANULADO"
                            AND n.colaborador_notificado_id = ?
                            ORDER BY r.id DESC', [$colaboradorId]);

            View::share('notificaciones', $notificaciones);
        }
        return $next($request);
    }
}
