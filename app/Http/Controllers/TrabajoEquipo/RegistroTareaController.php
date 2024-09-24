<?php

namespace App\Http\Controllers\TrabajoEquipo;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrabajoEquipos\RegistroTarea\RegistroTareaCreateRequest;
use App\Http\Requests\TrabajoEquipos\RegistroTarea\RegistroTareaEditRequest;
use App\Http\Requests\TrabajoEquipos\RegistroTarea\RegistroTareaStoreRequest;
use App\Http\Requests\TrabajoEquipos\RegistroTarea\RegistroTareaUpdateRequest;
use App\Models\Herramientas\Departamento;
use App\Models\Herramientas\Distrito;
use App\Models\Herramientas\Provincia;
use App\Models\Registros\Maquinaria;
use App\Models\Registros\Proyecto;
use App\Models\TrabajoEquipo\RegistroTarea;
use Illuminate\Http\Request;
use Exception;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Registros\Almacen;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
class RegistroTareaController extends Controller
{   
    public function index(){
        return view('trabajo_equipos.registro_tarea.index');
    }

    public function create(RegistroTareaCreateRequest $request){

        //========= OBTENIENDO PROYECTO DEL SUPERVISOR ======
        $proyecto   =   DB::select('select  pr.id,pr.nombre
                        from proyectos as pr
                        where pr.supervisor_id = ?',[Auth::user()->colaborador_id])[0];

        $maquinarias =   DB::select('select pm.maquinaria_id,m.nombre as maquinaria_nombre
                        from proyecto_maquinaria as pm
                        inner join maquinarias as m on m.id = pm.maquinaria_id
                        where pm.proyecto_id = ?',[$proyecto->id]);

        
        return view('trabajo_equipos.registro_tarea.create',
        compact('proyecto','maquinarias'));
    }

    public function getRegistrosTarea(Request $request){

        $registros_tarea =  DB::table('registros_tarea as rt')
                            ->join('proyectos as pr', 'pr.id', '=', 'rt.proyecto_id')
                            ->join('maquinarias as m', 'm.id', '=', 'rt.maquinaria_id')
                            ->join('colaboradores as c', 'c.id', '=', 'rt.supervisor_id')
                            ->select(
                                'rt.id', 
                                'pr.nombre as proyecto_nombre',
                                'm.nombre as maquinaria_nombre',
                                'c.nombre as supervisor_nombre',
                                'rt.observacion',
                                'rt.created_at as fecha_registro',
                                'rt.cantidad_horas_viajes',
                                'rt.supervisor_id'
                            )
                            ->where('rt.estado','ACTIVO')
                            ->get();

        return DataTables::of($registros_tarea)
                ->make(true);
    }

    public function store(RegistroTareaStoreRequest $request){
        DB::beginTransaction();
        try {

            $maquinaria                             =   Maquinaria::find($request->get('maquinaria'));

            $registro_tarea                         =   new RegistroTarea(); 
            $registro_tarea->proyecto_id            =   $request->get('proyecto_id');
            $registro_tarea->maquinaria_id          =   $request->get('maquinaria');
            $registro_tarea->supervisor_id          =   Auth::user()->colaborador_id;
            $registro_tarea->cantidad_horas_viajes  =   $request->get('cant_horas_viajes');
            $registro_tarea->observacion            =   $request->get('observacion');
            $registro_tarea->costo                  =   $maquinaria->costo_gasto;
            $registro_tarea->importe                =   $maquinaria->costo_gasto * $request->get('cant_horas_viajes');
            $registro_tarea->save();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'SE HA REGISTRADO LA TAREA']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function edit(RegistroTareaEditRequest $request,$id){
        
        $registro_tarea =   RegistroTarea::find($id);
        $proyecto       =   Proyecto::find($registro_tarea->proyecto_id);

        $maquinarias    =   DB::select('select pm.maquinaria_id,m.nombre as maquinaria_nombre
                            from proyecto_maquinaria as pm
                            inner join maquinarias as m on m.id = pm.maquinaria_id
                            where pm.proyecto_id = ?',[$proyecto->id]);

    
        return view('trabajo_equipos.registro_tarea.edit',
        compact('registro_tarea','proyecto','maquinarias'));

    }

    public function update(RegistroTareaUpdateRequest $request,$id){
        DB::beginTransaction();
        try {
            $maquinaria                             =   Maquinaria::find($request->get('maquinaria'));

            $registro_tarea                         =   RegistroTarea::find($id); 
            //$registro_tarea->proyecto_id          =   $request->get('proyecto_id');
            $registro_tarea->maquinaria_id          =   $request->get('maquinaria');
            //$registro_tarea->supervisor_id        =   Auth::user()->colaborador_id;
            $registro_tarea->cantidad_horas_viajes  =   $request->get('cant_horas_viajes');
            $registro_tarea->observacion            =   $request->get('observacion');
            $registro_tarea->costo                  =   $maquinaria->costo_gasto;
            $registro_tarea->importe                =   $maquinaria->costo_gasto * $request->get('cant_horas_viajes');
            $registro_tarea->save();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'SE HA ACTUALIZADO LA TAREA']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function destroy($id){
        DB::beginTransaction();
        try {
            $registro_tarea                    =   RegistroTarea::find($id);
            $registro_tarea->estado            =   'ANULADO';
            $registro_tarea->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'TAREA ELIMINADA']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }


}
