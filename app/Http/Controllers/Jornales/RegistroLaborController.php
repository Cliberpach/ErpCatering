<?php

namespace App\Http\Controllers\Jornales;

use App\Http\Controllers\Controller;
use App\Http\Requests\Jornales\RegistroLabor\MarcarEntradaRequest;
use App\Http\Requests\Jornales\RegistroLabor\MarcarSalidaRequest;
use App\Http\Requests\Jornales\RegistroLabor\RegistroLaborStoreRequest;
use App\Models\Jornales\RegistroLabor;
use App\Models\Jornales\RegistroLaborDetalle;
use App\Models\Registros\Cargo;
use App\Models\Registros\Proyecto;
use App\Models\Registros\ProyectoPersonal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Exception;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class RegistroLaborController extends Controller
{
    public function index(){
        return view('jornales.registro_labor.index');
    }

    public function getRegistrosLabor(Request $request){

        $registros_labor = DB::table('registros_labor as rl')
                            ->join('colaboradores as c', 'c.id', '=', 'rl.supervisor_id')
                            ->leftJoin('feriados as f','f.id','rl.feriado_id')
                            ->select(
                                'rl.id', 
                                'c.nombre as supervisor_nombre',
                                'rl.cant_trabajadores',
                                'rl.observacion',
                                'rl.created_at as fecha_registro',
                                'rl.observacion as observacion',
                                'rl.estado',
                                DB::raw('IF(rl.feriado = 1, "FERIADO", "DIA NORMAL") as feriado_estado')
                            )
                            ->where('rl.estado','!=','ANULADO')
                            ->where('rl.supervisor_id',Auth::user()->colaborador_id)
                            ->get();

        return DataTables::of($registros_labor)
                ->make(true);
    }

    public function store(RegistroLaborStoreRequest $request){
        DB::beginTransaction();
        try {

            //========= OBTENIENDO COLABORADOR DEL USUARIO =======
            $colaborador   =   DB::select('select c.id from colaboradores as c
                                where c.id = ?',[Auth::user()->colaborador_id]);
             
            if(count($colaborador) === 0){
                throw new Exception("Error, No se encontró el colaborador asociado al usuario!!");
            }                   
       
            //========== BUSCANDO EL PROYECTO QUE SUPERVISA EL USUARIO AUTENTICADO ========
            $proyecto   =   DB::select('select pr.id from proyectos as pr
                            where pr.supervisor_id = ?',[Auth::user()->colaborador_id]);

            if(count($proyecto) === 0){
                throw new Exception("Error, Necesitas supervisar algún proyecto para poder iniciar la asistencia");
            }

            //======= REVIZANDO SI EL DÍA ACTUAL ES FERIADO =====
            $fecha_actual = Carbon::now()->format('Y-m-d');

            $feriado    =   DB::select('select f.* 
                            from feriados as f
                            where f.fecha = ?',[$fecha_actual]);

            
            //========== REGISTRAR MAESTRO ASISTENCIA =======
            $registro_labor                     =   new RegistroLabor();
            $registro_labor->supervisor_id      =   $colaborador[0]->id;
            $registro_labor->proyecto_id        =   $proyecto[0]->id;
            $registro_labor->fecha_asistencia   =   Carbon::today();

            if(count($feriado) === 1){
                $registro_labor->feriado    =   true;
                $registro_labor->feriado_id =   $feriado[0]->id;
            }

            $registro_labor->save();

            //===== OBTENER TODOS LOS USUARIOS ASOCIADOS A ESE PROYECTO ======
            $proyecto_colaboradores              =   ProyectoPersonal::where('proyecto_id',$proyecto[0]->id)->get();
        
            //======= REGISTRAR DETALLE ========
            //======= REGISTRANDO AL SUPERVISOR EN LA ASISTENCIA TMB ======
            $registro_labor_detalle                     =   new RegistroLaborDetalle();
            $registro_labor_detalle->proyecto_id        =   $proyecto[0]->id;
            $registro_labor_detalle->supervisor_id      =   $colaborador[0]->id;
            $registro_labor_detalle->colaborador_id     =   $colaborador[0]->id;
            $registro_labor_detalle->registro_labor_id  =   $registro_labor->id;
            $registro_labor_detalle->save();
            //====== REGISTRANDO EQUIPO DE TRABAJO EN LA ASISTENCIA =======
            foreach ($proyecto_colaboradores as $proyecto_colaborador) {
                $registro_labor_detalle                     =   new RegistroLaborDetalle();
                $registro_labor_detalle->proyecto_id        =   $proyecto[0]->id;
                $registro_labor_detalle->supervisor_id      =   $colaborador[0]->id;
                $registro_labor_detalle->colaborador_id     =   $proyecto_colaborador->colaborador_id;
                $registro_labor_detalle->registro_labor_id  =   $registro_labor->id;
                if(count($feriado) === 1){
                    $registro_labor_detalle->feriado    =   true;
                    $registro_labor_detalle->feriado_id =   $feriado[0]->id;
                }
                $registro_labor_detalle->save();
            }
          
            DB::commit();
            return response()->json(['success'=>true,'message'=>'SE HA INICIADO LA ASISTENCIA!!']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }


    public function asistenciasCreate($id)
    {
        //======= OBTENIENDO EL REGISTRO LABOR ======
        $registro_labor_maestro = RegistroLabor::find($id);

        $colaboradores    =   $this->getColaboradoresAsistencia($registro_labor_maestro->proyecto_id,$registro_labor_maestro->id);

          
        //======== OBTENIENDO LOS COLABORADORES ENLAZADOS A ESE PROYECTO =====
        /*$colaboradores = DB::select('SELECT 
                                        rld.colaborador_id AS colaborador_id,
                                        co.nombre AS colaborador_nombre,
                                        ca.descripcion AS cargo_nombre,
                                        co.nro_documento AS colaborador_nro_documento,
                                        h.nombre_proyecto AS horario_descripcion,
                                        r.nombre AS regimen_nombre,
                                        rld.hora_entrada,
                                        rld.hora_salida,
                                        CASE 
                                            WHEN rld.hora_entrada > h.hora_inicio 
                                            THEN TIMESTAMPDIFF(MINUTE, h.hora_inicio, rld.hora_entrada) 
                                            ELSE 0 
                                        END AS tardanza
                                    FROM registros_labor_detalle AS rld
                                    INNER JOIN proyecto_personal AS pp 
                                        ON pp.proyecto_id = rld.proyecto_id 
                                        AND pp.colaborador_id = rld.colaborador_id
                                    INNER JOIN colaboradores AS co 
                                        ON co.id = rld.colaborador_id
                                    INNER JOIN cargos AS ca 
                                        ON ca.id = co.cargo_id
                                    INNER JOIN colaborador_proyecto AS cp 
                                        ON cp.colaborador_id = co.id
                                    INNER JOIN horarios AS h 
                                        ON h.id = cp.horario_id
                                    INNER JOIN regimens AS r 
                                        ON r.id = cp.regimen_id
                                    WHERE 
                                        pp.proyecto_id = ? 
                                        AND pp.estado = "ACTIVO" 
                                        AND (rld.registro_labor_id = ? OR rld.registro_labor_id IS NULL)', 
                                    [$registro_labor_maestro->proyecto_id, $id]);*/
    
        //======== OBTENIENDO COLABORADOR ACTUAL =======
        $colaborador_actual_id = DB::select('SELECT co.id
                                             FROM users AS u
                                             INNER JOIN colaboradores AS co ON co.id = u.colaborador_id
                                             WHERE u.id = ?', [Auth::user()->id])[0]->id;
    
        return view('jornales.registro_labor.asistencias',
            compact('colaboradores', 'registro_labor_maestro', 'colaborador_actual_id'));
    }



/*
//========= MANUAL =====
array:4 [ // app\Http\Controllers\Jornales\RegistroLaborController.php:144
  "_token"              => "c451CXaK9qFsjM4WZpJh8kqJgCtqW5RE0q0piaon"
  "hora_entrada"        => "12:45"
  "registro_labor_id"   => "1"
  "colaborador_id"      => "4"
]

//======= AUTOMÁTICA ======
array:5 [ // app\Http\Controllers\Jornales\RegistroLaborController.php:157
  "_token"              => "c451CXaK9qFsjM4WZpJh8kqJgCtqW5RE0q0piaon"
  "tipo_asistencia"     => "AUTOMATICO"
  "hora_entrada"        => "12:45"
  "registro_labor_id"   => "1"
  "colaborador_id"      => "4"
]
*/ 
    public function marcarEntrada(MarcarEntradaRequest $request){
     
        DB::beginTransaction();
        try {
            $tipo_asistencia    = $request->get('tipo_asistencia', null);
            $hora_entrada       = $request->get('hora_entrada', null);
            $registro_labor_id  = $request->get('registro_labor_id', null);
            $colaborador_id     = $request->get('colaborador_id', null);
    
            $registro_labor     = RegistroLabor::find($registro_labor_id);
            
            // Obtener datos del colaborador en colaborador_proyecto
            $colaborador_proyecto = DB::table('colaborador_proyecto')
                ->where('colaborador_id', $colaborador_id)
                ->first();
    
            if (!$colaborador_proyecto || !$colaborador_proyecto->horario_id || !$colaborador_proyecto->regimen_id) {
                return response()->json(['success' => false, 'message' => 'Debe asignarse un horario y régimen antes de registrar asistencia.']);
            }
    
            // Obtener horario del colaborador
            $horario = DB::table('horarios')->where('id', $colaborador_proyecto->horario_id)->first();
            
            if (!$horario) {
                return response()->json(['success' => false, 'message' => 'No se encontró el horario asignado.']);
            }
    
            $minutos_tolerancia = $horario->minutos_tolerancia;

            
            $hora_inicio = Carbon::parse($horario->hora_inicio);
            $hora_entrada = Carbon::parse($hora_entrada);
    
            // Validación de hora de entrada
            if ($hora_entrada->greaterThan($hora_inicio->copy()->addMinutes($minutos_tolerancia))) {
                $tardanza = $hora_entrada->diffInMinutes($hora_inicio);
               
                $mensaje_tardanza = "Advertencia: La asistencia está fuera del horario. Tardanza: {$tardanza} minutos.";
                Log::warning("Intento de asistencia tardía para colaborador {$colaborador_id} en el proyecto {$registro_labor->proyecto_id}.");
            } else {
                $mensaje_tardanza = "Asistencia registrada correctamente.";
            }
    
            // Registrar la asistencia
            DB::update('UPDATE registros_labor_detalle SET hora_entrada = ? WHERE proyecto_id = ? AND colaborador_id = ? AND registro_labor_id = ?',
                [$hora_entrada, $registro_labor->proyecto_id, $colaborador_id, $registro_labor->id]
            );
    
            // Incrementar cantidad de trabajadores en el maestro
            DB::update('UPDATE registros_labor SET cant_trabajadores = cant_trabajadores + 1 WHERE id = ?', [$registro_labor->id]);
            
            /*$colaboradores = DB::table('registros_labor_detalle as rld')
            ->join('colaboradores as c', 'rld.colaborador_id', '=', 'c.id')
            ->where('rld.proyecto_id', $registro_labor->proyecto_id)
            ->where('rld.registro_labor_id', $registro_labor->id)
            ->select('c.id', 'c.nombre', 'rld.hora_entrada')
            ->get();*/


            $colaboradores    =   $this->getColaboradoresAsistencia($registro_labor->proyecto_id,$registro_labor->id);


            DB::commit();
            return response()->json([
                'success' => true,
                'message' => $mensaje_tardanza,
                'colaboradores' => $colaboradores // Enviar colaboradores actualizados
            ]);
    
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }
    

    public static function validacionMarcarAsistencia($registro_labor,$colaborador_id){
        
        //=========== PROYECTO DIFERENTE A NULL =======
        if(!$registro_labor->proyecto_id){
            throw new Exception("EL REGISTRO DE LABOR NO ESTÁS ASOCIADO A NINGÚN PROYECTO");
        }

        //========== ASISTENCIA ACTIVA =========
        if($registro_labor->estado === 'FINALIZADO'){
            throw new Exception("ERROR, EL REGISTRO DE LABOR ESTÁ FINALIZADO");
        }
        if($registro_labor->estado === 'ANULADO'){
            throw new Exception("ERROR, EL REGISTRO DE LABOR ESTÁ ANULADO");
        }

        //======== VERIFICANDO QUE EL SUPERVISOR ESTÉ ASOCIADO A ESE PROYECTO =========
        $asociado   =   DB::select('select p.id
                        from proyectos as p
                        where p.id = ? 
                        and p.supervisor_id = ?',[$registro_labor->proyecto_id,Auth::user()->colaborador_id]);
    
        if(count($asociado) === 0){
            throw new Exception("EL SUPERVISOR NO ESTÁ ASOCIADO AL PROYECTO");
        }

        //======== VERIFICANDO QUE EL USUARIO HAYA CREADO ESTA ASISTENCIA =========
        if($registro_labor->supervisor_id !== Auth::user()->colaborador_id){
            throw new Exception("USTED NO HA CREADO ESTE REGISTRO DE ASISTENCIA!!");
        }

    } 


    public function getColaboradoresAsistencia($proyecto_id, $registro_labor_id) 
    {
        $colaboradores = DB::select('
            SELECT 
                rld.colaborador_id AS colaborador_id,
                co.nombre AS colaborador_nombre,
                co.nro_documento AS colaborador_nro_documento,
                h.nombre_proyecto AS horario_descripcion, 
                r.nombre AS regimen_nombre, 
                ca.descripcion AS cargo_nombre,
                rld.hora_entrada,
                rld.hora_salida,
                COALESCE( 
                    CASE 
                        WHEN rld.hora_entrada IS NOT NULL 
                            AND h.hora_inicio IS NOT NULL 
                            AND h.minutos_tolerancia IS NOT NULL
                            AND rld.hora_entrada > DATE_ADD(h.hora_inicio, INTERVAL h.minutos_tolerancia MINUTE)
                        THEN TIMESTAMPDIFF(MINUTE, DATE_ADD(h.hora_inicio, INTERVAL h.minutos_tolerancia MINUTE), rld.hora_entrada)
                        ELSE 0
                    END, 0) AS tardanza
            FROM registros_labor_detalle AS rld
            INNER JOIN proyecto_personal AS pp 
                ON pp.proyecto_id = rld.proyecto_id 
                AND pp.colaborador_id = rld.colaborador_id
            INNER JOIN colaboradores AS co ON co.id = rld.colaborador_id
            INNER JOIN cargos AS ca ON ca.id = co.cargo_id
            LEFT JOIN colaborador_proyecto AS cp 
                ON  cp.colaborador_id = co.id 
            LEFT JOIN horarios AS h ON h.id = cp.horario_id
            LEFT JOIN regimens AS r ON r.id = cp.regimen_id
            WHERE pp.proyecto_id = ? 
                AND pp.estado = "ACTIVO" 
                AND (rld.registro_labor_id = ? OR rld.registro_labor_id IS NULL)',
            [$proyecto_id, $registro_labor_id]
        );
        return $colaboradores;
        
    }
    
    

    
    public static function validacionMarcarSalida($registro_labor,$hora_salida,$colaborador_id){
        //========= VALIDANDO HORA DE SALIDA ======
        if(!$hora_salida){
            throw new Exception("La hora de salida es nula");
        }

        $registro_labor_detalle =   DB::select('select 
                                    rld.hora_entrada
                                    from registros_labor_detalle as rld
                                    where rld.registro_labor_id = ? 
                                    and rld.colaborador_id = ? 
                                    and rld.proyecto_id = ? 
                                    and rld.supervisor_id = ?',
                                    [$registro_labor->id, 
                                    $colaborador_id, 
                                    $registro_labor->proyecto_id,
                                    Auth::user()->colaborador_id]);

        if (count($registro_labor_detalle) === 0) {
            throw new Exception("NO EXISTE EL REGISTRO DE ASISTENCIA DEL COLABORADOR EN LA BD");
        }

        $hora_salida    = Carbon::parse($hora_salida); 
        $hora_entrada   = Carbon::parse($registro_labor_detalle[0]->hora_entrada); 

        if ($hora_salida->lt($hora_entrada)) { 
            throw new Exception("La hora de salida: " . $hora_salida->format('H:i:s') . " es menor que la hora de entrada: " . $hora_entrada->format('H:i:s'));
        }
    }

    public function marcarSalida(MarcarSalidaRequest $request){
        DB::beginTransaction();
        try {
           
            $tipo_asistencia    =   $request->get('tipo_asistencia_salida',null);
            $hora_salida        =   $request->get('hora_salida',null);
            $registro_labor_id  =   $request->get('registro_labor_id',null);
            $colaborador_id     =   $request->get('colaborador_id',null);

            $registro_labor     =   RegistroLabor::find($registro_labor_id);

            //======= VALIDACIÓN COMPLEJA =======
            RegistroLaborController::validacionMarcarAsistencia($registro_labor,$colaborador_id);

            if ($tipo_asistencia === 'AUTOMATICO') {
                $hora_salida = Carbon::now()->format('H:i');
            }

            //========= VALIDACIÓN MARCAR SALIDA =======
            RegistroLaborController::validacionMarcarSalida($registro_labor,$hora_salida,$colaborador_id);

            DB::update('
                UPDATE registros_labor_detalle
                SET hora_salida = ?, 
                    tiempo_trabajado = TIMEDIFF(?, hora_entrada),
                    updated_at = ?,
                    estado = "ASISTIO"
                WHERE proyecto_id = ? 
                AND colaborador_id = ? 
                AND registro_labor_id = ?
                AND supervisor_id = ?',
                [
                    $hora_salida, 
                    $hora_salida, 
                    Carbon::now(),
                    $registro_labor->proyecto_id, 
                    $request->get('colaborador_id'), 
                    $registro_labor->id,
                    Auth::user()->colaborador_id
                ]
            );
        
            $colaboradores    =   $this->getColaboradoresAsistencia($registro_labor->proyecto_id,$registro_labor->id);


            DB::commit();
            return response()->json(['success'=>true,'message'=>'SALIDA REGISTRADA','colaboradores'=>$colaboradores]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage(),'line'=>$th->getLine()]);
        }
    }

    public function destroy($id){
        DB::beginTransaction();
        try {
            $registro_labor                    =   RegistroLabor::find($id);
            $registro_labor->estado            =   'ANULADO';
            $registro_labor->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'ASISTENCIA ELIMINADA']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function finalizar($id){
        DB::beginTransaction();
        try {
            $registro_labor                    =   RegistroLabor::find($id);
            $registro_labor->estado            =   'FINALIZADO';
            $registro_labor->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'ASISTENCIA FINALIZADA']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

}
