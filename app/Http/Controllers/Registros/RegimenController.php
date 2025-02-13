<?php

namespace App\Http\Controllers\Registros;

use App\Http\Controllers\Controller;
use App\Http\Requests\Registros\Regimen\RegimenStoreRequest;
use App\Http\Requests\Registros\Regimen\RegimenUpdateRequest;
use App\Models\Registros\Regimen;
use Illuminate\Http\Request;
use Exception;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class RegimenController extends Controller
{
    public function index(){
        return view('registros.regimen.index');
    }

    public function getRegimen(Request $request){

        $regimen = DB::table('regimens as r')
                    ->select(
                        'r.id', 
                        'r.nombre',
                        'r.descripcion',
                        'r.dias_trabajo',
                        'r.dias_descanso',
                        'r.created_at as fecha_registro',
                        'r.updated_at as fecha_modificacion'
                    )
                    ->where('r.estado','ACTIVO')
                    ->get();


        return DataTables::of($regimen)
                ->make(true);
    }

    public function create(){
        return view('registros.regimen.create');
    }

    public function store(RegimenStoreRequest $request){
        
        DB::beginTransaction();
        try {

            $regimen                   =   new Regimen();
            $regimen->nombre           =   Str::upper($request->get('nombre'));
            $regimen->descripcion    =   $request->get('descripcion');
            $regimen->dias_trabajo      =   $request->get('dias_trabajo');
            $regimen->dias_descanso      =   $request->get('dias_descanso');
            $regimen->save();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'REGIMEN REGISTRADO']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function edit($id){
        $regimen     =   Regimen::find($id);

        return view('registros.regimen.edit',compact('regimen'));
    }

    public function update(RegimenUpdateRequest $request, $id){
        DB::beginTransaction();
        try {
          
            $regimen                   =   Regimen::find($id);
            $regimen->nombre           =   Str::upper($request->get('nombre'));
            $regimen->descripcion    =   $request->get('descripcion');
            $regimen->dias_trabajo     =   $request->get('dias_trabajo');
            $regimen->dias_descanso      =   $request->get('dias_descanso');
            $regimen->save();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'REGIMEN ACTUALIZADO']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function destroy($id){
        DB::beginTransaction();
        try {
            $regimen                    =   Regimen::find($id);
            $regimen->estado            =   'ANULADO';
            $regimen->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'REGIMEN ELIMINADO']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

}
