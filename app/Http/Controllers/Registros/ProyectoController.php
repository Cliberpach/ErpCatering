<?php

namespace App\Http\Controllers\Registros;

use App\Http\Controllers\Controller;
use App\Http\Requests\Registros\Proyecto\ProyectoAsignarSupervisorRequest;
use App\Http\Requests\Registros\Proyecto\ProyectoStoreRequest;
use App\Http\Requests\Registros\Proyecto\ProyectoUpdateRequest;
use App\Models\Registros\Almacen;
use App\Models\Registros\Proyecto;
use Illuminate\Http\Request;
use Exception;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
class ProyectoController extends Controller
{
    public function index(){

        $supervisores   =   DB::select('select u.* from users as u 
                            inner join model_has_roles as mhr on mhr.model_id = u.id
                            inner join roles as r on r.id = mhr.role_id
                            left join proyectos as pr on pr.supervisor_id = u.id 
                            where r.name = "SUPERVISOR" and u.estado =  "ACTIVO" and pr.supervisor_id is null');

        return view('registros.proyectos.index',compact('supervisores'));
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

}
