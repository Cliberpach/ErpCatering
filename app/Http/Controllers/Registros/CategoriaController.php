<?php

namespace App\Http\Controllers\Registros;

use App\Http\Controllers\Controller;
use App\Http\Requests\Registros\Categoria\CategoriaStoreRequest;
use App\Http\Requests\Registros\Categoria\CategoriaUpdateRequest;
use App\Models\Registros\Categoria;
use Illuminate\Http\Request;
use Exception;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
class CategoriaController extends Controller
{
    public function index(){
        return view('registros.categorias.index');
    }

    public function getCategorias(Request $request){

        $categorias = Categoria::where('estado','ACTIVO')
                    ->select('id','descripcion as nombre','created_at as fecha_registro',
                    'updated_at as fecha_modificacion')
                    ->get();

        return DataTables::of($categorias)
                ->make(true);
    }

    public function create(){
        return view('registros.categorias.create');
    }


    public function store(CategoriaStoreRequest $request){
        
        DB::beginTransaction();
        try {

            $categoria                    =   new Categoria();
            $categoria->descripcion       =   Str::upper($request->get('descripcion'));
            $categoria->save();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'CATEGORÍA REGISTRADA']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function update(CategoriaUpdateRequest $request,$id){
        DB::beginTransaction();
        try {

            $categoria                  =   Categoria::find($id);
            $categoria->descripcion     =   Str::upper($request->get('descripcion_edit'));
            $categoria->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'CATEGORÍA ACTUALIZADA CON ÉXITO']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function destroy($id){
        DB::beginTransaction();
        try {
            $categoria                    =   Categoria::find($id);
            $categoria->estado            =   'ANULADO';
            $categoria->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'CATEGORÍA ELIMINADA']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }
}
