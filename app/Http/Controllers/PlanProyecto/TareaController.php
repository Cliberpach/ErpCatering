<?php

namespace App\Http\Controllers\PlanProyecto;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlanProyecto\Tarea\TareaStoreRequest;
use App\Models\PlanProyecto\Tarea;
use App\Models\PlanProyecto\TareaDetalle;
use App\Models\Registros\Proyecto;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Exception;
use Yajra\DataTables\Facades\DataTables;

class TareaController extends Controller
{
    public function index(){
        $proyectos  =   Proyecto::where('estado','ACTIVO')->get();
        return view('plan_proyecto.tareas.index',compact('proyectos'));
    }

    public function getTareas(Request $request){

        $almacenes = DB::table('proyecto_tareas as pt')
                    ->select(
                        'pt.id', 
                        'pt.proyecto_id', 
                        'pt.nombre', 
                        'pt.fecha_inicio', 
                        'pt.fecha_fin', 
                        DB::raw('CONCAT(pt.avance * 100, "%") AS avance'),                   
                        'pt.dias_faltantes', 
                        'pt.observacion', 
                    )
                    ->where('pt.estado','<>','ANULADO')
                    ->get();

        return DataTables::of($almacenes)
                ->make(true);
    }

    public function create($id){
        $proyecto   =   Proyecto::find($id);

        return view('plan_proyecto.tareas.create',compact('proyecto'));
    }

    public function store(TareaStoreRequest $request){
        DB::beginTransaction();
        try {
            $lstSubtareas           =   json_decode($request->get('lstSubtareas'));

            TareaController::validacionSubtareas($lstSubtareas);

            $tarea                  =   new Tarea();
            $tarea->proyecto_id     =   $request->get('proyecto_id');
            $tarea->nombre          = mb_strtoupper($request->get('tarea_nombre'));

            $tarea->fecha_inicio    =   $request->get('tarea_fecha_inicio');
            $tarea->fecha_fin       =   $request->get('tarea_fecha_fin');
            $fecha_inicio           =   Carbon::parse($tarea->fecha_inicio);
            $fecha_fin              =   Carbon::parse($tarea->fecha_fin);

            $dias_faltantes         =   $fecha_inicio->diffInDays($fecha_fin, false);

            $tarea->avance          =   0.0;
            $tarea->dias_faltantes  =   $dias_faltantes;
            $tarea->observacion     =   $request->get('observacion');
            $tarea->save();

            //====== GRABANDO SUBTAREAS ======
            foreach ($lstSubtareas as $subtarea) {
                $tarea_detalle                       =   new TareaDetalle();
                $tarea_detalle->proyecto_tarea_id    =   $tarea->id;
                $tarea_detalle->nombre               =   mb_strtoupper($subtarea->nombre);
                $tarea_detalle->fecha_inicio         =   $subtarea->fecha_inicio;
                $tarea_detalle->fecha_fin            =   $subtarea->fecha_fin;
                $tarea_detalle->observacion          =   $subtarea->observacion;
                $tarea_detalle->save();
            }
            
            DB::commit();

            return response()->json(['success'=>true,'message'=>'TAREA REGISTRADA CON ÉXITO']);

        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

 
    
    public static function validacionSubtareas($lstSubtareas)
    {
        $nombresSubtareas = []; 

        if(count($lstSubtareas) === 0){
            throw new Exception("EL LISTADO DE SUBTAREAS ESTA VACÍO!!!");
        }

        foreach ($lstSubtareas as $subtarea) {
            // Validar el nombre: obligatorio y máximo de 150 caracteres
            if (empty($subtarea->nombre)) {
                throw new Exception("El nombre de la subtarea es obligatorio.");
            }
            if (strlen($subtarea->nombre) > 150) {
                throw new Exception("El nombre de la subtarea no puede tener más de 150 caracteres.");
            }

            // Validar que el nombre de la subtarea sea único en el listado
            if (in_array($subtarea->nombre, $nombresSubtareas)) {
                throw new Exception("El nombre de la subtarea '{$subtarea->nombre}' ya existe en el listado.");
            }
            $nombresSubtareas[] = $subtarea->nombre;

            // Validar la fecha de inicio: obligatoria y debe ser menor a la fecha fin
            if (empty($subtarea->fecha_inicio)) {
                throw new Exception("La fecha de inicio es obligatoria.");
            }

            // Validar la fecha de fin: obligatoria
            if (empty($subtarea->fecha_fin)) {
                throw new Exception("La fecha de fin es obligatoria.");
            }

            // Convertir las fechas a instancias de Carbon para hacer las comparaciones
            $fecha_inicio   =   Carbon::parse($subtarea->fecha_inicio);
            $fecha_fin      =   Carbon::parse($subtarea->fecha_fin);

            // Verificar que la fecha de inicio sea menor a la fecha de fin
            if ($fecha_inicio->greaterThanOrEqualTo($fecha_fin)) {
                throw new Exception("La fecha de inicio debe ser menor a la fecha de fin.");
            }

            // Validar la observación: máximo de 300 caracteres
            if (!empty($subtarea->observacion) && strlen($subtarea->observacion) > 300) {
                throw new Exception("La observación no puede tener más de 300 caracteres.");
            }
        }
    }
    
}
