<?php

namespace App\Http\Controllers\Herramientas;

use App\Http\Controllers\Controller;
use App\Models\Herramientas\Configuracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConfiguracionController extends Controller
{
    public function index(){

        $configuraciones    =   Configuracion::where('estado','ACTIVO')->get();

        return view('herramientas.configuracion.index',compact('configuraciones'));
    }

    public function ambiente_greenter($id,Request $request){
        DB::beginTransaction();
        try {

            $configuracion              =   Configuracion::find($id);
            $configuracion->propiedad   =   $request->get('modo');
            $configuracion->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'AMBIENTE '.$request->get('modo').' ESTABLECIDO']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }
}
