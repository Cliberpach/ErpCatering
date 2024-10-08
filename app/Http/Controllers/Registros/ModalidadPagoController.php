<?php

namespace App\Http\Controllers\Registros;

use App\Http\Controllers\Controller;
use App\Http\Requests\Registros\ModalidadPago\ModalidadPagoStoreRequest;
use App\Http\Requests\Registros\ModalidadPago\ModalidadPagoUpdateRequest;
use App\Models\Registros\ModalidadPago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ModalidadPagoController extends Controller
{
    public function index(){
        return view('registros.modalidad_pago.index');
    }

    public function getModalidadesPago(Request $request){

        $modalidades_pago   =   DB::table('modalidades_pago as m')
                                ->select(
                                    'm.*'
                                )
                                ->where('m.estado','ACTIVO')
                                ->get();


        return DataTables::of($modalidades_pago)
                ->make(true);
    }

    public function create(){

        return view('registros.modalidad_pago.create');
    }

    public function store(ModalidadPagoStoreRequest $request){

        DB::beginTransaction();

        try {
            $nombre_modalidad   =   null;

            if($request->get('tipo') == 1){
                $nombre_modalidad   =   'CONTADO';
            }

            if($request->get('tipo') == 2){
                $nombre_modalidad   =   'CREDITO';
            }

            $modalidad_pago                 =   new ModalidadPago();
            $modalidad_pago->descripcion    =   $nombre_modalidad;
            $modalidad_pago->tipo           =   $nombre_modalidad;
            $modalidad_pago->nro_dias       =   $request->get('nro_dias');
            $modalidad_pago->save();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'MODALIDAD PAGO REGISTRADA!!']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }

    }

    public function edit($id){
        $modalidad_pago =   ModalidadPago::find($id);
        return view('registros.modalidad_pago.edit',compact('modalidad_pago'));
    }

    public function update(ModalidadPagoUpdateRequest $request,$id){
        DB::beginTransaction();

        try {
            $nombre_modalidad   =   null;

            if($request->get('tipo') == 1){
                $nombre_modalidad   =   'CONTADO';
            }

            if($request->get('tipo') == 2){
                $nombre_modalidad   =   'CREDITO';
            }

            $modalidad_pago                 =   ModalidadPago::find($id);
            $modalidad_pago->descripcion    =   $nombre_modalidad;
            $modalidad_pago->tipo           =   $nombre_modalidad;
            $modalidad_pago->nro_dias       =   $request->get('nro_dias');
            $modalidad_pago->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'MODALIDAD PAGO ACTUALIZADA!!']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function destroy($id){
        DB::beginTransaction();
        try {
            $modalidad_pago                    =   ModalidadPago::find($id);
            $modalidad_pago->estado            =   'ANULADO';
            $modalidad_pago->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'MODALIDAD DE PAGO ELIMINADA']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

}
