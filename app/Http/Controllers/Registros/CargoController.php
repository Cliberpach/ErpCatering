<?php

namespace App\Http\Controllers\Registros;

use App\Http\Controllers\Controller;
use App\Http\Requests\Registros\Cargo\CargoStoreRequest;
use App\Http\Requests\Registros\Cargo\CargoUpdateRequest;
use App\Models\Registros\Cargo;
use Illuminate\Http\Request;
use Exception;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CargoController extends Controller
{
    public function index(){
       
        return view('registros.cargos.index');
    }

    public function getCargos(Request $request){

        $cargos = Cargo::where('estado','ACTIVO')
                    ->select('id','descripcion as nombre','created_at as fecha_registro',
                    'updated_at as fecha_modificacion')
                    ->get();

        return DataTables::of($cargos)
                ->make(true);
    }

    public function store(CargoStoreRequest $request){
        
        DB::beginTransaction();
        try {

            $cargo                    =   new Cargo();
            $cargo->descripcion       =   Str::upper($request->get('descripcion'));
            $cargo->save();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'CARGO REGISTRADO']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function update(CargoUpdateRequest $request,$id){
        DB::beginTransaction();
        try {
            
            $cargo                  =   Cargo::find($id);
            $cargo->descripcion     =   Str::upper($request->get('descripcion_edit'));
            $cargo->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'CARGO ACTUALIZADO CON ÉXITO']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function destroy($id){
        DB::beginTransaction();
        try {
            $cargo                    =   Cargo::find($id);
            $cargo->estado            =   'ANULADO';
            $cargo->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'CARGO ELIMINADO']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

}
