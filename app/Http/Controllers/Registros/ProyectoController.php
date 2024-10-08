<?php

namespace App\Http\Controllers\Registros;

use App\Http\Controllers\Controller;
use App\Http\Requests\Registros\Proyecto\ProyectoAsignarSupervisorRequest;
use App\Http\Requests\Registros\Proyecto\ProyectoStoreRequest;
use App\Http\Requests\Registros\Proyecto\ProyectoUpdateRequest;
use App\Models\Registros\Almacen;
use App\Models\Registros\Proyecto;
use App\Models\Registros\ProyectoMaquinaria;
use App\Models\Registros\ProyectoPersonal;
use Illuminate\Http\Request;
use Exception;
use Throwable;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
class ProyectoController extends Controller
{
    public function index(){

        

        return view('registros.proyectos.index');
    }

    public function create(){
        
        return view('registros.proyectos.create');
    }

    public function getProyectos(Request $request){

        $proyectos = Proyecto::where('proyectos.estado','<>', 'ANULADO')
                    ->leftJoin('colaboradores', 'proyectos.supervisor_id', '=', 'colaboradores.id')
                    ->select('proyectos.id', 
                    'proyectos.nombre', 
                    'colaboradores.nombre as supervisor_nombre',
                    'proyectos.costo',
                    'proyectos.avance_costo',
                    'proyectos.diferencia',
                    DB::raw('CONCAT(proyectos.avance, "%") AS avance'),                   
                    'proyectos.estado') 
                    ->get();

        return DataTables::of($proyectos)
                ->make(true);
    }

    public function store(ProyectoStoreRequest $request){
        
        DB::beginTransaction();
        try {

            $proyecto                   =   new Proyecto();
            $proyecto->nombre           =   Str::upper($request->get('nombre'));
            $proyecto->costo            =   $request->get('costo');
            $proyecto->avance_costo     =   $request->get('avance_costo');
            $proyecto->diferencia       =   $request->get('diferencia');
            $proyecto->direccion        =   mb_strtoupper($request->get('direccion'), 'UTF-8');
            $proyecto->save();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'PROYECTO REGISTRADO']);

        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function edit($id){
      
        $proyecto           =   Proyecto::find($id);

        return view('registros.proyectos.edit',compact('proyecto'));
    }

    public function update(ProyectoUpdateRequest $request, $id){
        DB::beginTransaction();
        try {

            $proyecto                   =   Proyecto::find($id);
            $proyecto->nombre           =   Str::upper($request->get('nombre'));
            $proyecto->costo            =   $request->get('costo');
            $proyecto->avance_costo     =   $request->get('avance_costo');
            $proyecto->diferencia       =   $request->get('diferencia');
            $proyecto->direccion        =   mb_strtoupper($request->get('direccion'), 'UTF-8');
            $proyecto->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'PROYECTO ACTUALIZADO']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function show($id){
        try {
            $proyecto   =   DB::select('select 
                            pr.id,
                            pr.nombre,
                            pr.costo,
                            pr.avance_costo,
                            pr.diferencia,
                            pr.direccion,
                            c.nombre as supervisor_nombre,
                            c.id as supervisor_id
                            from proyectos as pr
                            left join colaboradores as c on c.id = pr.supervisor_id
                            where pr.id = ?',[$id]);

            if(count($proyecto) === 0){
                throw new Exception("NO SE ENONCTRÓ EL PROYECTO EN LA BD!!!");
            }

            $proyecto   =   $proyecto[0];

            $personal   =   DB::select('select 
                            c.nombre,
                            td.descripcion as tipo_documento_descripcion,
                            c.nro_documento
                            from proyecto_personal as pp
                            left join colaboradores as c on c.id = pp.colaborador_id
                            left join tipos_documento as td on td.id = c.tipo_documento_id
                            where pp.proyecto_id = ?',[$id]);

            $maquinaria =   DB::select('select 
                            m.nombre,
                            m.costo_gasto,
                            tgd.descripcion as tipo_gasto_descripcion,
                            m.observacion
                            from proyecto_maquinaria as pm
                            left join maquinarias as m on m.id = pm.maquinaria_id
                            left join tablas_generales_detalles as tgd on tgd.id = m.tipo_gasto_id
                            where pm.proyecto_id = ? 
                            and tgd.tabla_general_id = 2',
                            [$id]);

            return response()->json(['success'=>true,
                                        'proyecto'=>$proyecto,
                                        'personal'=>$personal,
                                        'maquinaria'=>$maquinaria]);
        } catch (Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }        
    }


    public function destroy($id){
        DB::beginTransaction();
        try {
            $proyecto                    =   Proyecto::find($id);
            $proyecto->estado            =   'ANULADO';
            $proyecto->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'PROYECTO ELIMINADO']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function finalizarProyecto(Request $request,$id){
        DB::beginTransaction();
        try {
            $proyecto                    =   Proyecto::find($id);
            $proyecto->estado            =   'FINALIZADO';
            $proyecto->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'PROYECTO FINALIZADO']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function asignarSupervisor(ProyectoAsignarSupervisorRequest $request,$id){
        DB::beginTransaction();
        try {

            $proyecto                   =   Proyecto::find($id);
            $proyecto->supervisor_id    =   $request->get('supervisor');
            $proyecto->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'SUPERVISOR ASIGNADO CON ÉXITO']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function asignarPersonalCreate($id){
        
        //=========== OBTENER TODOS LOS USUARIOS LIBRES QUE NO ESTÉN ASIGNADOS A PROYECTOS DIFERENTES A ESTE ACTUALMENTE =======
        //========== ADEMÁS QUE NO SEAN SUPERVISORES ======
        $colaboradores_libres   =   DB::select('SELECT 
                                            co.id as colaborador_id,
                                            co.nombre as colaborador_nombre,
                                            co.nro_documento as colaborador_nro_documento,
                                            ca.descripcion as cargo_nombre
                                        FROM colaboradores AS co
                                        inner join cargos as ca on ca.id = co.cargo_id 
                                        WHERE co.id NOT IN (
                                            SELECT pp.colaborador_id
                                            FROM proyecto_personal AS pp
                                            WHERE pp.estado = "ACTIVO" AND pp.proyecto_id != ?
                                        ) AND ca.descripcion != "ADMIN" && ca.descripcion != "SUPERVISOR"
                                    ',[$id]);

        $colaboradores_asignados    =   DB::select('select 
                                        pp.colaborador_id 
                                        from proyecto_personal as pp
                                        where pp.proyecto_id = ?',[$id]);  

        $idsAsignados = array_column($colaboradores_asignados, 'colaborador_id');

                    
        $proyecto_id    =   $id;
        $proyecto       =   Proyecto::find($id);

       
        return view('registros.proyectos.asignar_personal',
        compact('colaboradores_libres','proyecto_id','idsAsignados','proyecto'));

    }

    public function asignarPersonalStore(Request $request){
        DB::beginTransaction();
        try {
            $lstUsuariosAsignados   =   json_decode($request->get('lstUsuariosAsignados'));
            $proyecto_id            =   $request->get('proyecto_id');

            DB::table('proyecto_personal')
            ->where('proyecto_id', $proyecto_id)
            ->delete();

            foreach ($lstUsuariosAsignados as  $usuario_asignado) {
                $proyecto_personal                  =   new ProyectoPersonal();
                $proyecto_personal->proyecto_id     =   $proyecto_id;
                $proyecto_personal->colaborador_id  =   $usuario_asignado;
                $proyecto_personal->save();
            }

            DB::commit();
            return response()->json(['success'=>true,'message'=>'PERSONAL ASIGNADO CON ÉXITO']);
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function asignarMaquinariaCreate($id){
        
        //=========== OBTENER TODAS LAS MAQUINARIAS LIBRES QUE NO ESTÉN ASIGNADOS A PROYECTOS DIFERENTES A ESTE ACTUALMENTE =======
        //========== ADEMÁS QUE NO SEAN SUPERVISORES ======
        $maquinarias_libres   =   DB::select('SELECT 
                                            m.id as maquinaria_id,
                                            m.nombre as maquinaria_nombre
                                        FROM maquinarias AS m
                                        WHERE m.id NOT IN (
                                            SELECT pm.maquinaria_id
                                            FROM proyecto_maquinaria AS pm
                                            WHERE pm.estado = "ACTIVO" AND pm.proyecto_id != ?
                                        )
                                    ',[$id]);

        $maquinarias_asignadas    =   DB::select('select 
                                        pm.maquinaria_id 
                                        from proyecto_maquinaria as pm
                                        where pm.proyecto_id = ?',[$id]);  

        $idsAsignados = array_column($maquinarias_asignadas, 'maquinaria_id');

                    
        $proyecto_id    =   $id;
        $proyecto       =   Proyecto::find($id);

       
        return view('registros.proyectos.asignar_maquinaria',
        compact('maquinarias_libres','proyecto_id','idsAsignados','proyecto'));

    }

    public function asignarMaquinariaStore(Request $request){
        DB::beginTransaction();
        try {
            $lstMaquinariasAsignadas    =   json_decode($request->get('lstMaquinariasAsignadas'));
            $proyecto_id                =   $request->get('proyecto_id');

            DB::table('proyecto_maquinaria')
            ->where('proyecto_id', $proyecto_id)
            ->delete();

            foreach ($lstMaquinariasAsignadas as  $maquinaria_asignada) {
                $proyecto_maquinaria                    =   new ProyectoMaquinaria();
                $proyecto_maquinaria->proyecto_id       =   $proyecto_id;
                $proyecto_maquinaria->maquinaria_id     =   $maquinaria_asignada;
                $proyecto_maquinaria->save();
            }

            DB::commit();
            return response()->json(['success'=>true,'message'=>'MAQUINARIA ASIGNADA CON ÉXITO']);
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

}
