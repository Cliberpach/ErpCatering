<?php

namespace App\Http\Controllers\Registros;

use App\Http\Controllers\Controller;
use App\Http\Requests\Registros\Marca\MarcaStoreRequest;
use App\Http\Requests\Registros\Marca\MarcaUpdateRequest;
use App\Models\Registros\Marca;
use Illuminate\Http\Request;
use Exception;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class MarcaController extends Controller
{
    public function index(){
        return view('registros.marcas.index');
    }

    public function getMarcas(Request $request){

        $marcas = Marca::where('estado','ACTIVO')
                    ->select('id','descripcion as nombre','created_at as fecha_registro',
                    'updated_at as fecha_modificacion')
                    ->get();

        return DataTables::of($marcas)
                ->make(true);
    }

    public function create(){
        return view('registros.marcas.create');
    }


    public function store(MarcaStoreRequest $request){
        
        DB::beginTransaction();
        try {

            $marca                    =   new Marca();
            $marca->descripcion       =   Str::upper($request->get('descripcion'));
            $marca->save();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'MARCA REGISTRADA']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function update(MarcaUpdateRequest $request,$id){
        DB::beginTransaction();
        try {

            $marca                  =   Marca::find($id);
            $marca->descripcion     =   Str::upper($request->get('descripcion_edit'));
            $marca->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'MARCA ACTUALIZADA CON ÉXITO']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function destroy($id){
        DB::beginTransaction();
        try {
            $marca                    =   Marca::find($id);
            $marca->estado            =   'ANULADO';
            $marca->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'MARCA ELIMINADA']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function getListMarcas(){
        try {
            $marcas =   Marca::where('estado','ACTIVO')->get();
            return response()->json(['success'=>true,'lstMarcas'=>$marcas]);
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }
}
