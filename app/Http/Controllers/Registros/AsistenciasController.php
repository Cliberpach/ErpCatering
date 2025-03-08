<?php

namespace App\Http\Controllers\Registros;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Registros\Asistencias;
use App\Models\Registros\Colaborador;
use App\Models\Registros\Proyecto;
use Illuminate\Support\Facades\DB;

class AsistenciasController extends Controller
{
    public function index()
    {
        $proyecto = Proyecto::where('estado', '<>', 'ANULADO')->first();
    
        if (!$proyecto) {
            return redirect()->back()->with('error', 'No hay proyectos activos.');
        }
    
        $proyectoNombre = $proyecto->nombre;
    
        return view('registros.asistencias.index', compact('proyectoNombre'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nro_documento' => 'required|exists:colaboradores,nro_documento',
            'tipo_registro' => 'required|in:entrada,salida',
        ]);

        // Obtener el colaborador por número de documento
        $colaborador = Colaborador::where('nro_documento', $request->nro_documento)->first();

        if (!$colaborador) {
            return redirect()->back()->with('error', 'El DNI ingresado no existe.');
        }

        // Obtener el proyecto activo
        $proyecto = Proyecto::where('estado', '<>', 'ANULADO')->first();

        if (!$proyecto) {
            return redirect()->back()->with('error', 'No hay proyectos activos.');
        }

        $fechaActual = now()->toDateString();
        $horaActual = now()->toTimeString();

        // Verificar si ya existe un registro de entrada
        $asistencia = Asistencias::where('colaborador_id', $colaborador->id)
            ->where('proyecto_id', $proyecto->id)
            ->where('fecha', $fechaActual)
            ->first();

        if ($request->tipo_registro === 'entrada') {
            if ($asistencia) {
                return redirect()->back()->with('error', 'Ya registró su entrada hoy.');
            }

            Asistencias::create([
                'colaborador_id' => $colaborador->id,
                'proyecto_id' => $proyecto->id,
                'fecha' => $fechaActual,
                'hora_entrada' => $horaActual,
                'estado' => 'ASISTIO',
            ]);
        } else {
            if (!$asistencia) {
                return redirect()->back()->with('error', 'Debe marcar su entrada antes de la salida.');
            }

            $asistencia->update(['hora_salida' => $horaActual]);
        }

        return redirect()->back()->with('success', 'Asistencia registrada correctamente.');
    }
}
