<?php

namespace App\Http\Controllers\Consultas;

use App\Http\Controllers\Controller;
use App\Models\Registros\Proyecto;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Exports\Consultas\PersonalExport;
use App\Models\Registros\AsistenciaDetalle;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CPersonalController extends Controller
{
    public function index()
    {
        $rol        =   Auth::user()->getRoleNames()[0];
        $proyectos  =   null;

        //======= OBTENIENDO ALMACENES ======
        if ($rol === 'SUPERVISOR') {
            $proyectos  =   DB::select('select pr.*  
                            from proyectos as pr 
                            where pr.supervisor_id = ? 
                            and pr.estado <> "ANULADO"', [Auth::user()->colaborador_id]);
        } else {
            $proyectos  =   Proyecto::where('estado', '<>', 'ANULADO')->get();
        }

        return view('consultas.personal.index', compact('proyectos'));
    }

    public function getConsultaPersonal(Request $request)
    {
        $fecha_inicio = $request->get('fecha_inicio', null);
        $fecha_fin = $request->get('fecha_fin', null);
        $proyecto_id = $request->get('proyecto_id', null);

        // Aquí cambiamos la tabla a 'asistencia_detalles' y sus relaciones
        $consulta = DB::table('asistencia_detalles as ad')
            ->join('asistencias as a', 'a.id', '=', 'ad.asistencia_id') // Relación con la tabla 'asistencias'
            ->join('colaboradores as c', 'c.id', '=', 'a.colaborador_id') // Relación con los colaboradores
            ->join('cargos as ca', 'ca.id', '=', 'c.cargo_id') // Relación con los cargos
            ->join('tipos_documento as td', 'td.id', '=', 'c.tipo_documento_id') // Relación con los tipos de documentos
            ->select(
                'td.descripcion as tipo_documento',
                'c.nro_documento',
                'c.nombre as colaborador_nombre',
                'ca.descripcion as cargo',
                'c.pago_mensual',
                'c.id as colaborador_id',
                DB::raw('FORMAT(c.pago_dia, 2) as pago_dia'),
                // Calculamos los días trabajados sin feriado
                DB::raw('COUNT(CASE WHEN a.feriado = 0 AND a.estado = "ASISTIO" THEN 1 END) as no_feriados_trabajados'),
                // Calculamos los días feriados trabajados
                DB::raw('COUNT(CASE WHEN a.feriado = 1 AND a.estado = "ASISTIO" THEN 1 END) as feriados_trabajados'),
                // Contamos los días totales trabajados (sin tener en cuenta si es feriado)
                DB::raw('COUNT(CASE WHEN a.estado = "ASISTIO" THEN 1 END) as total_dias_trabajados')
            )
            ->where('c.estado', 'ACTIVO'); // Solo considerar colaboradores activos

        // Filtrar por proyecto si se ha seleccionado
        if ($proyecto_id) {
            $consulta->where('a.proyecto_id', $proyecto_id);
        }

        // Filtrar por fecha de inicio
        if ($fecha_inicio) {
            $consulta->where('a.fecha_asistencia', '>=', $fecha_inicio . ' 00:00:00');
        }

        // Filtrar por fecha de fin
        if ($fecha_fin) {
            $consulta->where('a.fecha_asistencia', '<=', $fecha_fin . ' 23:59:59');
        }

        // Agrupar por los campos relevantes
        $consulta->groupBy('c.id', 'c.nombre', 'c.pago_mensual', 'c.pago_dia', 'td.descripcion', 'c.nro_documento', 'ca.descripcion');

        // Retornar el DataTables con la consulta
        return DataTables::of($consulta->get())
            ->make(true);
    }


    public function excel(Request $request)
    {
        $fecha_inicio   =   $request->query('fecha_inicio');
        $fecha_fin      =   $request->query('fecha_fin');
        $proyecto_id    =   $request->query('proyecto_id');
        $fecha_actual   =   Carbon::now();

        return Excel::download(new PersonalExport($fecha_inicio, $fecha_fin, $proyecto_id), 'reporte_personal_' . $fecha_actual . '.xlsx');
    }

    public function pdf(Request $request)
    {

        $fecha_inicio   =   $request->get('fecha_inicio', null);
        $fecha_fin      =   $request->get('fecha_fin', null);
        $proyecto_id    =   $request->get('proyecto_id', null);

        $proyecto   =   Proyecto::find($proyecto_id);
        if (!$proyecto_id) {
            dd('EL PROYECTO NO EXISTE EN LA BD');
        }
        $consulta = DB::table('asistencia_detalles as ad')
            ->join('asistencias as a', 'a.id', '=', 'ad.asistencia_id') // Relación con la tabla 'asistencias'
            ->join('colaboradores as c', 'c.id', '=', 'a.colaborador_id') // Relación con los colaboradores
            ->join('cargos as ca', 'ca.id', '=', 'c.cargo_id') // Relación con los cargos
            ->join('tipos_documento as td', 'td.id', '=', 'c.tipo_documento_id') // Relación con los tipos de documentos
            ->select(
                'td.descripcion as tipo_documento',
                'c.nro_documento',
                'c.nombre as colaborador_nombre',
                'ca.descripcion as cargo',
                'c.pago_mensual',
                DB::raw('FORMAT(c.pago_dia, 2) as pago_dia'),
                DB::raw('COUNT(CASE WHEN ad.feriado = 0 AND a.estado = "ASISTIO" THEN 1 END) as no_feriados_trabajados'),
                DB::raw('COUNT(CASE WHEN ad.feriado = 1 AND a.estado = "ASISTIO" THEN 1 END) as feriados_trabajados'),
                DB::raw('COUNT(CASE WHEN a.estado = "ASISTIO" THEN 1 END) as total_dias_trabajados')
            )
            ->where('c.estado', 'ACTIVO');

        // Filtrar por proyecto
        if ($proyecto_id) {
            $consulta->where('a.proyecto_id', $proyecto_id);
        }

        // Filtrar por fecha de inicio
        if ($fecha_inicio) {
            $consulta->where('a.fecha_asistencia', '>=', $fecha_inicio . ' 00:00:00');
        }

        // Filtrar por fecha de fin
        if ($fecha_fin) {
            $consulta->where('a.fecha_asistencia', '<=', $fecha_fin . ' 23:59:59');
        }

        // Agrupar por los campos relevantes
        $consulta->groupBy('c.id', 'c.nombre', 'c.pago_mensual', 'c.pago_dia', 'td.descripcion', 'c.nro_documento', 'ca.descripcion');
        $consulta   =   $consulta->get();

        Carbon::setLocale('es');
        $fecha_impresion = Carbon::now();
        $fecha_impresion = $fecha_impresion->translatedFormat('l, d \d\e F \d\e\l Y');
        $fecha_impresion = strtoupper($fecha_impresion);


        $empresa    =   DB::select('select * from empresas as e
                        where e.id = 1')[0];

        // Configurar las opciones de DOMPDF si es necesario
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');

        // Instanciar el objeto DOMPDF
        $dompdf = new Dompdf($options);

        // Definir el contenido del PDF (HTML)
        $html = view(
            'consultas.personal.pdf.pdf',
            compact('empresa', 'consulta', 'fecha_impresion', 'proyecto', 'fecha_inicio', 'fecha_fin')
        )
            ->render();

        // Cargar el HTML en DOMPDF
        $dompdf->loadHtml($html);

        // Opcional: Configurar el tamaño de papel y la orientación
        $dompdf->setPaper('A4', 'portrait'); // O 'landscape'

        // Renderizar el PDF
        $dompdf->render();

        // Visualizar el PDF en una nueva ventana en lugar de descargarlo
        return $dompdf->stream('reporte_personal.pdf', ['Attachment' => false]);
    }

    //===============================0    INICIO DETALLES     0================================

    public function detalles($colaborador_id)
    {
        // Obtener el colaborador y su horario
        $colaboradorProyecto = DB::table('colaborador_proyecto as cp')
            ->join('horarios as h', 'h.id', '=', 'cp.horario_id')
            ->where('cp.colaborador_id', $colaborador_id)
            ->select('h.hora_inicio', 'h.hora_final')
            ->first();

        // Obtener los detalles de asistencia, incluyendo el motivo de permiso
        $asistenciasDetalles = DB::table('asistencia_detalles as ad')
            ->join('asistencias as a', 'a.id', '=', 'ad.asistencia_id')
            ->leftJoin('motivo_descanso as md', 'md.id', '=', 'ad.motivo_id') // Relacionar con la tabla motivo_descanso para obtener el motivo
            ->where('a.colaborador_id', $colaborador_id)
            ->select(
                'ad.id',
                'ad.hora_entrada',
                'ad.hora_salida',
                'ad.hora_entrada_break',
                'ad.hora_salida_break',
                'ad.tiempo_trabajado',
                'ad.retraso',
                'ad.adelanto',
                'ad.horas_extra',
                'ad.horas_no_trabajadas',
                'ad.estado',
                'ad.created_at',
                'ad.updated_at',
                'a.fecha_asistencia',
                'md.descripcion as motivo_permiso', // Obtenemos el motivo de permiso
                'a.feriado as feriado_asistencia'
            )
            ->get();

        return view('consultas.personal.detalles', compact('asistenciasDetalles', 'colaborador_id', 'colaboradorProyecto'));
    }


    public function getDetalles($colaborador_id, Request $request)
    {
        $fecha_inicio = $request->get('fecha_inicio');
        $fecha_fin = $request->get('fecha_fin');

        // Obtener los detalles de asistencia, incluyendo el horario y el motivo de permiso en un solo query
        $query = DB::table('asistencia_detalles as ad')
            ->join('asistencias as a', 'a.id', '=', 'ad.asistencia_id')
            ->join('colaborador_proyecto as cp', 'cp.colaborador_id', '=', 'a.colaborador_id')
            ->join('horarios as h', 'h.id', '=', 'cp.horario_id') // Relacionamos el horario
            ->leftJoin('motivo_descanso as md', 'md.id', '=', 'ad.motivo_id') // Relacionamos con la tabla motivo_descanso para obtener el motivo
            ->select(
                'ad.id',
                'ad.hora_entrada',
                'ad.hora_salida',
                'ad.hora_entrada_break',
                DB::raw('COALESCE(ad.hora_salida_break, "00:00") as hora_salida_break'),
                DB::raw('COALESCE(ad.tiempo_trabajado, "00:00") as tiempo_trabajado'),
                DB::raw('COALESCE(ad.retraso, "00:00") as retraso'),
                DB::raw('COALESCE(ad.adelanto, "00:00") as adelanto'),
                DB::raw('COALESCE(ad.horas_extra, "00:00") as horas_extra'),
                DB::raw('COALESCE(ad.horas_no_trabajadas, "00:00") as horas_no_trabajadas'),
                'ad.estado',
                'a.fecha_asistencia',
                DB::raw('COALESCE(md.descripcion, "Ninguno") as motivo_permiso'),
                'a.feriado as feriado_asistencia',
                DB::raw("CONCAT(h.hora_inicio, ' - ', h.hora_final) as turno") // Concatenamos hora_inicio y hora_final para el turno
            )
            ->where('a.colaborador_id', $colaborador_id); // Filtrar por colaborador_id

        // Filtros de fecha
        if ($fecha_inicio) {
            $query->where('a.fecha_asistencia', '>=', $fecha_inicio);
        }

        if ($fecha_fin) {
            $query->where('a.fecha_asistencia', '<=', $fecha_fin);
        }

        // Ejecutar la consulta y retornar los datos para DataTables
        return DataTables::of($query)
            ->make(true); // Se genera el formato para DataTables
    }




    public function getMotivosDescanso()
    {
        $motivos = DB::table('motivo_descanso')->get(['id', 'descripcion']);
        return response()->json(['motivos' => $motivos]);
    }

    public function guardarMotivoPermiso(Request $request)
    {
        $asistenciaDetalle = AsistenciaDetalle::find($request->asistencia_detalle_id);
        if ($asistenciaDetalle) {
            $asistenciaDetalle->motivo_id = $request->motivo_id;
            $asistenciaDetalle->motivo_permiso = $request->motivo_permiso;
            $asistenciaDetalle->save();
            return response()->json(['success' => "Guardado con éxito!"]);
        }
        return response()->json(['success' => false]);
    }

    public function verificarContraseña(Request $request)
    {
        // Recibimos la contraseña enviada desde el frontend
        $password = $request->input('password');

        // Obtén el usuario administrador (asumimos que el usuario con ID 1 es el administrador)
        $admin = User::find(1); // O usa la lógica para obtener el administrador de tu base de datos

        // Verificamos si la contraseña proporcionada coincide con la almacenada en la base de datos (usamos Hash::check)
        if ($admin && Hash::check($password, $admin->password)) {
            // Si la contraseña es correcta
            return response()->json(['success' => true]);
        } else {
            // Si la contraseña es incorrecta
            return response()->json(['success' => false]);
        }
    }

    public function getHorasExtra($id)
    {
        $asistenciaDetalle = AsistenciaDetalle::find($id);

        if ($asistenciaDetalle) {
            return response()->json(['success' => true, 'horas_extra' => $asistenciaDetalle->horas_extra]);
        }

        return response()->json(['success' => false, 'message' => 'Detalle de asistencia no encontrado.']);
    }



    public function asignarHorasExtra(Request $request)
    {
        // Buscar el detalle de la asistencia por ID
        $asistenciaDetalle = AsistenciaDetalle::find($request->asistencia_detalle_id);

        if ($asistenciaDetalle) {
            // Asignar las horas extra
            $asistenciaDetalle->horas_extra = $request->horas_extra;  // Guardamos las horas extra

            // Guardar los cambios en la base de datos
            $asistenciaDetalle->save();

            // Retornar éxito
            return response()->json(['success' => "Horas extra asignadas con éxito!"]);
        }

        // Si no se encuentra el detalle de la asistencia, retornamos error
        return response()->json(['success' => false, 'message' => 'Detalle de asistencia no encontrado.']);
    }
}
