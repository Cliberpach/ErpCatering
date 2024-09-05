<?php

namespace App\Http\Controllers\Registros;

use App\Http\Controllers\Controller;
use App\Http\Requests\Registros\Proyecto\ProyectoAsignarSupervisorRequest;
use App\Http\Requests\Registros\Proyecto\ProyectoStoreRequest;
use App\Http\Requests\Registros\Proyecto\ProyectoUpdateRequest;
use App\Models\Registros\Almacen;
use App\Models\Registros\Proyecto;
use App\Models\Registros\ProyectoPersonal;
use Illuminate\Http\Request;
use Exception;
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

        $proyectos = Proyecto::where('proyectos.estado', 'ACTIVO')
                    ->leftJoin('users', 'proyectos.supervisor_id', '=', 'users.id')
                    ->select('proyectos.*', 'users.name as supervisor_nombre') 
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
            $proyecto->save();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'PROYECTO REGISTRADO']);

        } catch (\Throwable $th) {
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
            $proyecto->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'PROYECTO ACTUALIZADO']);

        } catch (\Throwable $th) {
            DB::rollBack();
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

}
