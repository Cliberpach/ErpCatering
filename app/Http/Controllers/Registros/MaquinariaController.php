<?php

namespace App\Http\Controllers\Registros;

use App\Http\Controllers\Controller;
use App\Http\Requests\Registros\Maquinaria\MaquinariaStoreRequest;
use App\Http\Requests\Registros\Maquinaria\MaquinariaUpdateRequest;
use App\Models\Registros\Maquinaria;
use Illuminate\Http\Request;
use Exception;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class MaquinariaController extends Controller
{
    public function index(){
        return view('registros.maquinarias.index');
    }

    public function getMaquinarias(Request $request){

        $maquinarias = DB::table('maquinarias as m')
                    ->join('tablas_generales_detalles as tgd', 'tgd.id', '=', 'm.tipo_gasto_id')
                    ->select(
                        'm.id', 
                        'm.nombre',
                        'm.costo_gasto',
                        'm.observacion',
                        'tgd.descripcion as tipo_gasto_nombre',
                        'm.created_at as fecha_registro',
                        'm.updated_at as fecha_modificacion'
                    )
                    ->where('m.estado','ACTIVO')
                    ->get();


        return DataTables::of($maquinarias)
                ->make(true);
    }

    public function create(){
       
        $tipos_gasto    =   DB::select('select tgd.id,tgd.descripcion,tgd.simbolo
                                from tablas_generales_detalles as tgd
                                where tgd.tabla_general_id = 2');

        return view('registros.maquinarias.create',compact('tipos_gasto'));
    }

    public function store(MaquinariaStoreRequest $request){
        
        DB::beginTransaction();
        try {

            $maquinaria                   =   new Maquinaria();
            $maquinaria->nombre           =   Str::upper($request->get('nombre'));
            $maquinaria->tipo_gasto_id    =   $request->get('tipo_gasto');
            $maquinaria->costo_gasto      =   $request->get('costo_gasto');
            $maquinaria->observacion      =   $request->get('observacion');
            $maquinaria->save();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'MAQUINARIA REGISTRADA']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function edit($id){
      
        $tipos_gasto    =   DB::select('select tgd.id,tgd.descripcion,tgd.simbolo
                                from tablas_generales_detalles as tgd
                                where tgd.tabla_general_id = 2');

        $maquinaria     =   Maquinaria::find($id);

        return view('registros.maquinarias.edit',compact('tipos_gasto','maquinaria'));
    }

    public function update(MaquinariaUpdateRequest $request, $id){
        DB::beginTransaction();
        try {
          
            $maquinaria                   =   Maquinaria::find($id);
            $maquinaria->nombre           =   Str::upper($request->get('nombre'));
            $maquinaria->tipo_gasto_id    =   $request->get('tipo_gasto');
            $maquinaria->costo_gasto      =   $request->get('costo_gasto');
            $maquinaria->observacion      =   $request->get('observacion');
            $maquinaria->save();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'MAQUINARIA ACTUALIZADA']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function destroy($id){
        DB::beginTransaction();
        try {
            $maquinaria                    =   Maquinaria::find($id);
            $maquinaria->estado            =   'ANULADO';
            $maquinaria->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'MAQUINARIA ELIMINADA']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

}
