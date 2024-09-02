<?php

namespace App\Http\Controllers\Registros;

use App\Http\Controllers\Controller;
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
        return view('registros.proyectos.index');
    }

    public function create(){
        
        return view('registros.proyectos.create');
    }

    public function getProyectos(Request $request){

        $proyectos = Proyecto::where('estado','ACTIVO')
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

}
