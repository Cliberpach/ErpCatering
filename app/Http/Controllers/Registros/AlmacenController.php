<?php

namespace App\Http\Controllers\Registros;

use App\Http\Controllers\Controller;
use App\Http\Requests\Registros\Almacen\AlmacenAsignarProyecto;
use App\Http\Requests\Registros\Almacen\AlmacenStoreRequest;
use App\Http\Requests\Registros\Almacen\AlmacenUpdateRequest;
use App\Models\Registros\Proyecto;
use Illuminate\Http\Request;
use Exception;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Registros\Almacen;

class AlmacenController extends Controller
{
    public function index(){
        $proyectos  =   Proyecto::where('estado','ACTIVO')->get();
       
        return view('registros.almacenes.index',compact('proyectos'));
    }

    public function getAlmacenes(Request $request){

        $almacenes = DB::table('almacenes as a')
                    ->leftJoin('proyectos as pr', 'pr.id', '=', 'a.proyecto_id')
                    ->select(
                        'a.id', 
                        'a.descripcion as nombre',
                        'a.created_at as fecha_registro',
                        'a.updated_at as fecha_modificacion',
                        'pr.nombre as proyecto_nombre'
                    )
                    ->where('a.estado','ACTIVO')
                    ->get();

        return DataTables::of($almacenes)
                ->make(true);
    }

    public function create(){
        return view('registros.almacenes.create');
    }


    public function store(AlmacenStoreRequest $request){
        
        DB::beginTransaction();
        try {

            $almacen                    =   new Almacen();
            $almacen->descripcion       =   Str::upper($request->get('descripcion'));
            $almacen->save();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'ALMACÉN REGISTRADO']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function update(AlmacenUpdateRequest $request,$id){
        DB::beginTransaction();
        try {

            $almacen                  =   Almacen::find($id);
            $almacen->descripcion     =   Str::upper($request->get('descripcion_edit'));
            $almacen->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'ALMACÉN ACTUALIZADO CON ÉXITO']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function destroy($id){
        DB::beginTransaction();
        try {
            $almacen                    =   Almacen::find($id);
            $almacen->estado            =   'ANULADO';
            $almacen->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'ALMACÉN ELIMINADO']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function getListAlmacenes(){
        try {
            $almacenes =   Almacen::where('estado','ACTIVO')->get();
            return response()->json(['success'=>true,'lstAlmacenes'=>$almacenes]);
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function asignarProyecto(AlmacenAsignarProyecto $request,$id){
        DB::beginTransaction();
        try {

            $almacen                  =   Almacen::find($id);
            $almacen->proyecto_id     =   $request->get('proyecto');
            $almacen->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'PROYECTO ASIGNADO CON ÉXITO']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }
}
