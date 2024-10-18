<?php

namespace App\Http\Controllers\Jornales;

use App\Http\Controllers\Controller;
use App\Http\Requests\Jornales\RegistroLabor\MarcarEntradaRequest;
use App\Http\Requests\Jornales\RegistroLabor\MarcarSalidaRequest;
use App\Http\Requests\Jornales\RegistroLabor\RegistroLaborStoreRequest;
use App\Models\Jornales\RegistroLabor;
use App\Models\Jornales\RegistroLaborDetalle;
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

class RegistroLaborController extends Controller
{
    public function index(){
        return view('jornales.registro_labor.index');
    }

    public function getRegistrosLabor(Request $request){

        $registros_labor = DB::table('registros_labor as rl')
                            ->join('colaboradores as c', 'c.id', '=', 'rl.supervisor_id')
                            ->select(
                                'rl.id', 
                                'c.nombre as supervisor_nombre',
                                'rl.cant_trabajadores',
                                'rl.observacion',
                                'rl.created_at as fecha_registro',
                                'rl.observacion as observacion',
                                'rl.estado'
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
                            where pr.supervisor_id = ?',[Auth::user()->id]);

            if(count($proyecto) === 0){
                throw new Exception("Error, Necesitas supervisar algún proyecto para poder iniciar la asistencia");
            }
            
            //========== REGISTRAR MAESTRO ASISTENCIA =======
            $registro_labor                     =   new RegistroLabor();
            $registro_labor->supervisor_id      =   $colaborador[0]->id;
            $registro_labor->proyecto_id        =   $proyecto[0]->id;
            $registro_labor->fecha_asistencia   =   Carbon::today();
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
                $registro_labor_detalle->save();
            }
          
            DB::commit();
            return response()->json(['success'=>true,'message'=>'SE HA INICIADO LA ASISTENCIA!!']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function asistenciasCreate($id){

        //======= OBTENIENDO EL REGISTRO LABOR ======
        $registro_labor_maestro =   RegistroLabor::find($id);
          
        //======== OBTENIENDO LOS COLABORADORES ENLAZADOS A ESE PROYECTO =====
        $colaboradores  =   DB::select('select 
                                rld.colaborador_id as colaborador_id,
                                co.nombre as colaborador_nombre,
                                ca.descripcion as cargo_nombre,
                                co.nro_documento as colaborador_nro_documento,
                                td.descripcion as colaborador_tipo_documento,
                                rld.hora_entrada,
                                rld.hora_salida,
                                rld.img_ruta,
                                rld.img_nombre
                            from registros_labor_detalle as rld
                            inner join proyecto_personal as pp on (pp.proyecto_id =  rld.proyecto_id and pp.colaborador_id =  rld.colaborador_id)
                            inner join colaboradores as co on co.id = rld.colaborador_id
                            inner join cargos as ca on ca.id = co.cargo_id
                            inner join tipos_documento as td on td.id = co.tipo_documento_id
                            where pp.proyecto_id = ? 
                            and pp.estado = "ACTIVO" 
                            and (rld.registro_labor_id = ? or rld.registro_labor_id is null)',
                            [$registro_labor_maestro->proyecto_id,$id]);

        //======== OBTENIENDO COLABORADOR ACTUAL =======
        $colaborador_actual_id  =   DB::select('select co.id
                                    from users as u
                                    inner join colaboradores as co on co.id = u.colaborador_id
                                    where u.id = ?',[Auth::user()->id])[0]->id;

        return view('jornales.registro_labor.asistencias',
        compact('colaboradores','registro_labor_maestro','colaborador_actual_id'));
    }

    public function marcarEntrada(MarcarEntradaRequest $request){
        DB::beginTransaction();
        try {
            $tipo_asistencia    =   $request->get('tipo_asistencia',null);
            $hora_entrada       =   $request->get('hora_entrada',null);
            $registro_labor_id  =   $request->get('registro_labor_id',null);
            $colaborador_id     =   $request->get('colaborador_id',null);

            $registro_labor     =   RegistroLabor::find($registro_labor_id);

            //======= VALIDACIÓN COMPLEJA =======
            RegistroLaborController::validacionMarcarAsistencia($registro_labor,$colaborador_id);
            
            if ($tipo_asistencia === 'AUTOMATICO') {
                $hora_entrada = Carbon::now()->format('H:i');
            }

            //========= MARCAR ASISTENCIA HORA ENTRADA =======
            DB::update('
                update registros_labor_detalle
                set hora_entrada = ?
                where proyecto_id = ? and colaborador_id = ? and registro_labor_id = ?',
                [$hora_entrada, 
                $registro_labor->proyecto_id,
                $colaborador_id, 
                $registro_labor->id]
            );

            //====== INCREMENTANDO CANT_TRABAJADORES EN EL MAESTRO =======
            DB::update('
                update registros_labor
                set cant_trabajadores = cant_trabajadores + 1
                where  id = ?',
                [$registro_labor->id]
            );

            //======== GUARDANDO IMAGEN DE ASISTENCIA ENTRADA ======
            if ($request->hasFile('img_asistencia_entrada')) {

                $destinationPath = public_path('img/asistencia_entrada');
            
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true);
                }
            
                $file           =   $request->file('img_asistencia_entrada');

                $extension      =   $file->getClientOriginalExtension();
            
                $fileName       =   $registro_labor->id.'_'.$registro_labor->proyecto_id.'_'.$request->get('colaborador_id'). '.' . $extension;
            
                $file->move($destinationPath, $fileName);

                //========= GUARDANDO LA RUTA DE LA IMAGEN Y EL NOMBRE ======
                DB::update('
                    update registros_labor_detalle
                    set img_ruta = ? , img_nombre = ?
                    where proyecto_id = ? and colaborador_id = ? and registro_labor_id = ?',
                    ['img/asistencia_entrada/'.$fileName, 
                    $fileName,
                    $registro_labor->proyecto_id,
                    $request->get('colaborador_id'), 
                    $registro_labor->id]
                );
            
            }
           
            $colaboradores    =   $this->getColaboradoresAsistencia($registro_labor->proyecto_id,$registro_labor->id);

            DB::commit();
            return response()->json(['success'=>true,'message'=>'ASISTENCIA REGISTRADA','colaboradores'=>$colaboradores]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
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

    public function getColaboradoresAsistencia($proyecto_id,$registro_labor_id){

        $colaboradores  =   DB::select('select 
                                rld.colaborador_id as colaborador_id,
                                co.nombre as colaborador_nombre,
                                ca.descripcion as cargo_nombre,
                                co.nro_documento as colaborador_nro_documento,
                                td.descripcion as colaborador_tipo_documento,
                                rld.hora_entrada,
                                rld.hora_salida,
                                rld.img_ruta,
                                rld.img_nombre
                            from registros_labor_detalle as rld
                            inner join proyecto_personal as pp on (pp.proyecto_id =  rld.proyecto_id and pp.colaborador_id =  rld.colaborador_id)
                            inner join colaboradores as co on co.id = rld.colaborador_id
                            inner join cargos as ca on ca.id = co.cargo_id
                            inner join tipos_documento as td on td.id = co.tipo_documento_id
                            where 
                            pp.proyecto_id = ? 
                            and pp.estado = "ACTIVO" 
                            and (rld.registro_labor_id = ? or rld.registro_labor_id is null)',
                            [$proyecto_id,$registro_labor_id]);

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
                    tiempo_trabajado = TIMEDIFF(?, hora_entrada)
                WHERE proyecto_id = ? 
                AND colaborador_id = ? 
                AND registro_labor_id = ?
                AND supervisor_id = ?',
                [
                    $hora_salida, 
                    $hora_salida, 
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
