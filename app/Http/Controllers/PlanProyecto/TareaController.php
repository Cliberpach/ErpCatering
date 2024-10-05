<?php

namespace App\Http\Controllers\PlanProyecto;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlanProyecto\Tarea\TareaStoreRequest;
use App\Http\Requests\PlanProyecto\Tarea\TareaUpdateRequest;
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
        $proyectos  =   Proyecto::where('estado','<>','ANULADO')->get();
        return view('plan_proyecto.tareas.index',compact('proyectos'));
    }

    public function getTareas(Request $request){
        $proyecto_id    =   $request->get('proyecto_id',null);

        $tareas  = DB::table('proyecto_tareas as pt')
                    ->select(
                        'pt.id', 
                        'pt.proyecto_id', 
                        'pt.nombre', 
                        'pt.fecha_inicio', 
                        'pt.fecha_fin', 
                        DB::raw('CONCAT(pt.avance, "%") AS avance'),                   
                        'pt.dias_faltantes', 
                        'pt.observacion', 
                        'pt.estado'
                    )
                    ->where('pt.estado','<>','ANULADO');

        if($proyecto_id){
            $tareas->where('pt.proyecto_id',$proyecto_id);
        }

        return DataTables::of($tareas->get())
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
            $tarea->nombre          =   mb_strtoupper($request->get('tarea_nombre'));

            $tarea->fecha_inicio    =   $request->get('tarea_fecha_inicio');
            $tarea->fecha_fin       =   $request->get('tarea_fecha_fin');
            $fecha_inicio           =   Carbon::parse($tarea->fecha_inicio);
            $fecha_fin              =   Carbon::parse($tarea->fecha_fin);

            $dias_faltantes         =   $fecha_inicio->diffInDays($fecha_fin, false);

            $tarea->avance          =   0.0;
            $tarea->dias_faltantes  =   $dias_faltantes;
            $tarea->observacion     =   $request->get('tarea_observacion');
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

    public function edit($id){
        $proyecto_tarea     =   DB::select('select * from proyecto_tareas as pt
                                where pt.id = ?',[$id])[0];

        $subtareas          =   DB::select('select * from proyecto_tarea_detalles as ptd
                                where ptd.proyecto_tarea_id = ?',[$id]);

        $proyecto   =   Proyecto::find($proyecto_tarea->proyecto_id);

        return view('plan_proyecto.tareas.edit',
        compact('proyecto_tarea','proyecto','subtareas'));
    }

    public function update($id,TareaUpdateRequest $request){
        DB::beginTransaction();
        try {

            $lstSubtareas           =   json_decode($request->get('lstSubtareas'));
            TareaController::validacionSubtareas($lstSubtareas);

            $tarea                  =   Tarea::find($id);
            $tarea->proyecto_id     =   $request->get('proyecto_id');
            $tarea->nombre          =   mb_strtoupper($request->get('tarea_nombre'));

            $tarea->fecha_inicio    =   $request->get('tarea_fecha_inicio');
            $tarea->fecha_fin       =   $request->get('tarea_fecha_fin');
            $fecha_inicio           =   Carbon::parse($tarea->fecha_inicio);
            $fecha_fin              =   Carbon::parse($tarea->fecha_fin);

            $dias_faltantes         =   $fecha_inicio->diffInDays($fecha_fin, false);

            $tarea->avance          =   0.0;
            $tarea->dias_faltantes  =   $dias_faltantes;
            $tarea->observacion     =   $request->get('tarea_observacion');
            $tarea->update();

            //======= ELIMINANDO TAREAS ======
            DB::table('proyecto_tarea_detalles')
            ->where('proyecto_tarea_id', $tarea->id)
            ->delete();


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

            return response()->json(['success'=>true,'message'=>'TAREA ACTUALIZADA CON ÉXITO']);

        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }


    public function show($id){
        try {
            $tarea  =   DB::select('select
                        pt.nombre,
                        pt.fecha_inicio,
                        pt.fecha_fin,
                        pt.avance,
                        pt.dias_faltantes,
                        pt.observacion,
                        pr.nombre as proyecto_nombre
                        from proyecto_tareas as pt
                        left join proyectos as pr on pr.id = pt.proyecto_id
                        where pt.id = ?',[$id]);

            $subtareas  =   DB::select('select *
                            from proyecto_tarea_detalles as ptd
                            where ptd.proyecto_tarea_id = ?
                            order by ptd.id ASC',[$id]);

            if(count($tarea) === 0){
                throw new Exception("NO SE ENCONTRÓ LA TAREA EN LA BD");
            }

            return response()->json(['success'=>true,'tarea'=>$tarea[0],'subtareas'=>$subtareas]);
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }


    public function avance($id,Request $request){
        DB::beginTransaction();
        try {

            $lstSubtareasAvance     =   json_decode($request->get('lstSubtareasAvance'));

            foreach ($lstSubtareasAvance as $subtarea) {

                DB::update('UPDATE proyecto_tarea_detalles 
                SET estado = ?
                WHERE proyecto_tarea_id = ? and id = ?', 
                [$subtarea->estado,$id, $subtarea->id]);

            }

            //========= CALCULANDO NUEVO PORCENTAJE Y ESTADO DE LA TAREA ========
            $tarea  =   Tarea::find($id);

            $subtareas_actualizadas_pendientes  =   DB::select('select count(*) as cant_subtareas_pendientes  
                                                    from proyecto_tarea_detalles as ptd
                                                    where ptd.proyecto_tarea_id = ? 
                                                    and ptd.estado = "PENDIENTE"',[$id])[0];
                                        
            $subtareas_actualizadas_finalizadas =   DB::select('select count(*) as cant_subtareas_finalizadas 
                                                    from proyecto_tarea_detalles as ptd
                                                    where ptd.proyecto_tarea_id = ? 
                                                    and ptd.estado = "FINALIZADO"',[$id])[0];

            $subtareas_total                    =   DB::select('select count(*) as cant_subtareas_total
                                                    from proyecto_tarea_detalles as ptd
                                                    where ptd.proyecto_tarea_id = ? 
                                                    and ptd.estado != "ANULADO"',[$id])[0];

            if($subtareas_total->cant_subtareas_total == $subtareas_actualizadas_finalizadas->cant_subtareas_finalizadas){
                $tarea->estado  =   'FINALIZADO';
            }else{
                if($subtareas_actualizadas_finalizadas->cant_subtareas_finalizadas > 0){
                    $tarea->estado  =   'EN PROCESO';
                }
                if($subtareas_actualizadas_finalizadas->cant_subtareas_finalizadas === 0){
                    $tarea->estado  =   'PENDIENTE';
                }
            }
            

            $tarea->avance  =   100*($subtareas_actualizadas_finalizadas->cant_subtareas_finalizadas / $subtareas_total->cant_subtareas_total);
            $tarea->update();


            //======= CALCULANDO NUEVO PORCENTAJE Y ESTADO DEL PROYECTO =======
            $proyecto   =   Proyecto::find($tarea->proyecto_id);

            if(!$proyecto){
                throw new Exception("NO SE ENCONTRÓ EL PROYECTO EN LA BD");  
            }

            //======= OTBIENDO LAS TAREAS DEL PROYECTO =====
            $tareas_proyecto_pendientes    =   DB::select('select count(*) as cant 
                                                from proyecto_tareas as pt
                                                where pt.proyecto_id = ? 
                                                and pt.estado = "PENDIENTE"',[$proyecto->id])[0];

            $tareas_proyecto_finalizadas    =   DB::select('select count(*) as cant
                                                from proyecto_tareas as pt
                                                where pt.proyecto_id = ? 
                                                and pt.estado = "FINALIZADO"',[$proyecto->id])[0];

            $tareas_proyecto_proceso        =   DB::select('select count(*)  as cant
                                                from proyecto_tareas as pt
                                                where pt.proyecto_id = ? 
                                                and pt.estado = "EN PROCESO"',[$proyecto->id])[0];

            $tareas_proyecto_total          =   DB::select('select count(*) as cant
                                                from proyecto_tareas as pt
                                                where pt.proyecto_id = ? 
                                                and pt.estado <> "ANULADO"',[$proyecto->id])[0];
            
            if($tareas_proyecto_total->cant === $tareas_proyecto_finalizadas->cant){
                $proyecto->estado   =   'FINALIZADO';
            }else{
                if($tareas_proyecto_finalizadas->cant > 0){
                    $proyecto->estado   =   'EN PROCESO';
                }
                if($tareas_proyecto_finalizadas->cant === 0){
                    $proyecto->estado   =   'PENDIENTE';
                }
            }

            $proyecto->avance   =   100*($tareas_proyecto_finalizadas->cant/$tareas_proyecto_total->cant);
            $proyecto->update();


            DB::commit();
            return response()->json(['success'=>true,'message'=>'AVANCE DE LA TAREA REGISTRADO CON ÉXITO']);


        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }

    }
    
}
