<?php

namespace App\Http\Controllers\Herramientas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Herramientas\TablaGeneral\TablaGeneralDetalleStoreRequest;
use App\Models\Herramientas\TablaGeneralDetalle;
use Illuminate\Http\Request;
use Exception;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
class TablaGeneralDetalleController extends Controller
{
    public function store(TablaGeneralDetalleStoreRequest $request){
        DB::beginTransaction();
        try {

            $tabla_general_detalle                      =   new TablaGeneralDetalle();
            $tabla_general_detalle->descripcion         =   Str::upper($request->get('descripcion'));
            $tabla_general_detalle->simbolo             =   $request->get('simbolo');
            $tabla_general_detalle->tabla_general_id    =   $request->get('tabla_general_id');
            $tabla_general_detalle->save();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'ITEM REGISTRADO']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function getListTablaGeneralDetalles($tabla_general_id){
        try {
            $detalles   =   TablaGeneralDetalle::where('estado','ACTIVO')
                            ->where('tabla_general_id',$tabla_general_id)
                            ->get();

            return response()->json(['success'=>true,'lstDetalles'=>$detalles]);
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

}
