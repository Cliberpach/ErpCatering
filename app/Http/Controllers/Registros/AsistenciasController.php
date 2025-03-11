<?php

namespace App\Http\Controllers\Registros;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Registros\Asistencias;
use App\Models\Registros\AsistenciaDetalle;
use App\Models\Registros\Colaborador;
use App\Models\Registros\Proyecto;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AsistenciasController extends Controller
{
    public function index()
    {
        // Obtener el proyecto activo
        $proyecto = Proyecto::where('estado', '<>', 'ANULADO')->first();

        if (!$proyecto) {
            return redirect()->back()->with('error', 'No hay proyectos activos.');
        }

        $proyectoNombre = $proyecto->nombre;

        return view('registros.asistencias.index', compact('proyectoNombre'));
    }

    public function store(Request $request)
{
    // Validación de los campos
    $request->validate([
        'nro_documento' => 'required|exists:colaboradores,nro_documento', // Verifica que el DNI esté registrado
        'tipo_registro' => 'required|in:entrada,entrada_break,salida_break,salida', // Verifica las 4 opciones
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

    // Obtener el horario del colaborador
    $colaboradorProyecto = DB::table('colaborador_proyecto as cp')
        ->join('horarios as h', 'h.id', '=', 'cp.horario_id')
        ->where('cp.colaborador_id', $colaborador->id)
        ->select('h.hora_inicio', 'h.hora_final')
        ->first();

    if (!$colaboradorProyecto) {
        return redirect()->back()->with('error', 'No se ha encontrado el horario para este colaborador.');
    }

    // Verificar si ya existe un registro de asistencia para el colaborador en el día
    $asistencia = Asistencias::where('colaborador_id', $colaborador->id)
        ->where('proyecto_id', $proyecto->id)
        ->where('fecha_asistencia', $fechaActual)
        ->first();

    if (!$asistencia) {
        // Si no existe asistencia para el día, crearla
        $asistencia = Asistencias::create([
            'colaborador_id' => $colaborador->id,
            'proyecto_id' => $proyecto->id,
            'fecha_asistencia' => $fechaActual,
            'estado' => 'ASISTIO',
            'observacion' => $request->observacion,
        ]);
    }

    // Validaciones según el tipo de registro solicitado
    $asistenciaDetalle = $asistencia->detalles()->first();

    if ($request->tipo_registro === 'entrada') {
        // Si ya hay una entrada registrada, no se puede registrar otra
        if ($asistenciaDetalle && $asistenciaDetalle->hora_entrada) {
            return redirect()->back()->with('error', 'Ya has registrado tu hora de entrada hoy.');
        }

        // Calcular el retraso si lo hay
        $retraso = null;
        $horaInicioTurno = $colaboradorProyecto->hora_inicio; // Hora de inicio del turno
        $horaEntrada = $horaActual; // Hora actual (hora de entrada del colaborador)

        if ($horaEntrada > $horaInicioTurno) {
            // Convertir las horas a timestamps para calcular la diferencia
            $horaInicioTurnoTimestamp = strtotime($horaInicioTurno);
            $horaEntradaTimestamp = strtotime($horaEntrada);

            // Calcular el retraso en minutos
            $retrasoEnMinutos = ($horaEntradaTimestamp - $horaInicioTurnoTimestamp) / 60;

            // Convertir el retraso en minutos a horas y minutos
            $horas = floor($retrasoEnMinutos / 60);
            $minutos = $retrasoEnMinutos % 60;

            // Formatear el retraso como HH:MM:SS
            $retraso = sprintf("%02d:%02d:00", $horas, $minutos); // Guardamos solo horas y minutos
        }

        // Registrar la hora de entrada
        $detalle = AsistenciaDetalle::create([
            'asistencia_id' => $asistencia->id,
            'hora_entrada' => $horaActual,
            'retraso' => $retraso, // Guardamos el retraso calculado
            'estado' => 'ACTIVO',
        ]);
    } elseif ($request->tipo_registro === 'entrada_break') {
        // Si no hay hora de entrada registrada, no se puede registrar entrada al break
        if (!$asistenciaDetalle || !$asistenciaDetalle->hora_entrada) {
            return redirect()->back()->with('error', 'Debe registrar primero la hora de entrada.');
        }
        // Registrar la hora de entrada al break
        if ($asistenciaDetalle->hora_entrada_break) {
            return redirect()->back()->with('error', 'Ya has registrado la entrada al break.');
        }
        AsistenciaDetalle::where('asistencia_id', $asistencia->id)
            ->update(['hora_entrada_break' => $horaActual]);
    } elseif ($request->tipo_registro === 'salida_break') {
        // Si no hay entrada al break registrada, no se puede registrar salida del break
        if (!$asistenciaDetalle || !$asistenciaDetalle->hora_entrada_break) {
            return redirect()->back()->with('error', 'Debe registrar primero la entrada al break.');
        }
        // Si no se ha registrado la salida del break, no se puede registrar la salida
        if ($asistenciaDetalle->hora_salida_break) {
            return redirect()->back()->with('error', 'Ya has registrado la salida del break.');
        }
        // Registrar la hora de salida del break
        AsistenciaDetalle::where('asistencia_id', $asistencia->id)
            ->update(['hora_salida_break' => $horaActual]);
    } elseif ($request->tipo_registro === 'salida') {
        // Si no hay hora de entrada registrada, no se puede registrar salida
        if (!$asistenciaDetalle || !$asistenciaDetalle->hora_entrada) {
            return redirect()->back()->with('error', 'Debe registrar primero la hora de entrada.');
        }
        // Registrar la hora de salida
        if ($asistenciaDetalle->hora_salida) {
            return redirect()->back()->with('error', 'Ya has registrado la salida.');
        }

// Calcular horas trabajadas y no trabajadas
$horasTrabajadas = 0;
$minutosTrabajados = 0;
$horasNoTrabajadas = 0;

// Obtener hora de salida y hora de entrada
$horaSalida = $horaActual; // Hora de salida (actual)
$horaEntrada = $asistenciaDetalle->hora_entrada; // Hora de entrada

// Verificar que ambas horas están disponibles para hacer el cálculo
if ($horaEntrada && $horaSalida) {
    // Convertir las horas a timestamps para calcular la diferencia en segundos
    $horaEntradaTimestamp = strtotime($horaEntrada);
    $horaSalidaTimestamp = strtotime($horaSalida);

    // Calcular la diferencia en segundos
    $diferenciaEnSegundos = $horaSalidaTimestamp - $horaEntradaTimestamp;

    // Convertir segundos a horas y minutos
    $horasTrabajadas = floor($diferenciaEnSegundos / 3600); // Horas trabajadas
    $minutosTrabajados = floor(($diferenciaEnSegundos % 3600) / 60); // Minutos trabajados

    // Calcular las horas no trabajadas basadas en el turno
    $horaInicioTurno = $colaboradorProyecto->hora_inicio; // Hora inicio del turno
    $horaFinalTurno = $colaboradorProyecto->hora_final; // Hora final del turno

    $horaInicioTurnoTimestamp = strtotime($horaInicioTurno);
    $horaFinalTurnoTimestamp = strtotime($horaFinalTurno);

    // Calcular la duración total del turno (en segundos)
    $duracionTurnoEnSegundos = $horaFinalTurnoTimestamp - $horaInicioTurnoTimestamp;

    // Calcular las horas no trabajadas: la diferencia entre el tiempo total del turno y el tiempo trabajado
    $diferenciaEnSegundosNoTrabajados = $duracionTurnoEnSegundos - $diferenciaEnSegundos;

    // Convertir la diferencia en segundos a horas y minutos
    $horasNoTrabajadas = floor($diferenciaEnSegundosNoTrabajados / 3600); // Horas no trabajadas
    $minutosNoTrabajados = floor(($diferenciaEnSegundosNoTrabajados % 3600) / 60); // Minutos no trabajados
}

// Actualizar la hora de salida y guardar las horas trabajadas y no trabajadas como cadenas (strings)
    AsistenciaDetalle::where('asistencia_id', $asistencia->id)
        ->update([
            'hora_salida' => $horaActual, // Hora de salida
            'tiempo_trabajado' => sprintf("%02d:%02d:00", $horasTrabajadas, $minutosTrabajados), // Horas trabajadas en formato HH:mm:ss
            'horas_no_trabajadas' => sprintf("%02d:%02d:00", $horasNoTrabajadas, $minutosNoTrabajados), // Horas no trabajadas en formato HH:mm:ss
        ]);



    }

    return redirect()->back()->with('success', 'Asistencia registrada correctamente.');
}


}
