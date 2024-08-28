<?php

namespace App\Http\Controllers\Herramientas;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Utils\UtilController;
use App\Http\Requests\Herramientas\Colaborador\ColaboradorStoreRequest;
use App\Http\Requests\Herramientas\Colaborador\ColaboradorUpdateRequest;
use App\Models\Herramientas\Colaborador;
use App\Models\Herramientas\TipoDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Yajra\DataTables\Facades\DataTables;

class ColaboradorController extends Controller
{
    public function index(){
        return view('herramientas.colaboradores.index');
    }

    public function getColaboradores(Request $request){
        $colaboradores = Colaborador::where('estado','ACTIVO')->get();

        return DataTables::of($colaboradores)
                ->make(true);
    }

    public function create(){
        $tipos_documento    =   TipoDocumento::where('estado','ACTIVO')->get();
       
        return view('herramientas.colaboradores.create',compact('tipos_documento'));
    }

    public function store(ColaboradorStoreRequest $request){
        DB::beginTransaction();
        try {
            $colaborador    =   new Colaborador();
            $colaborador->tipo_documento_id =   $request->get('tipo_documento');
            $colaborador->nombre            =   $request->get('nombre');
            $colaborador->direccion         =   $request->get('direccion');
            $colaborador->telefono          =   $request->get('telefono');
            $colaborador->horas_semana      =   $request->get('horas_semana');
            $colaborador->pago_semana       =   $request->get('pago_semana');
            $colaborador->nro_documento     =   $request->get('nro_documento');
            $colaborador->save();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'COLABORADOR REGISTRADO']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function edit($id){
        $tipos_documento    =   TipoDocumento::where('estado','ACTIVO')->get();
        $colaborador        =   DB::select('select * from colaboradores as c
                                where c.id = ?',[$id])[0];

        
       
        return view('herramientas.colaboradores.edit',compact('tipos_documento','colaborador'));
    }

    public function update(ColaboradorUpdateRequest $request, $id){
        DB::beginTransaction();
        try {
            $colaborador                    =   Colaborador::find($id);
            $colaborador->tipo_documento_id =   $request->get('tipo_documento');
            $colaborador->nombre            =   $request->get('nombre');
            $colaborador->direccion         =   $request->get('direccion');
            $colaborador->telefono          =   $request->get('telefono');
            $colaborador->horas_semana      =   $request->get('horas_semana');
            $colaborador->pago_semana       =   $request->get('pago_semana');
            $colaborador->nro_documento     =   $request->get('nro_documento');
            $colaborador->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'COLABORADOR ACTUALIZADO']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function destroy($id){
        DB::beginTransaction();
        try {
            $colaborador                    =   Colaborador::find($id);
            $colaborador->estado            =   'ANULADO';
            $colaborador->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'COLABORADOR ELIMINADO']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    //======== VALIDAR DNI ÚNICO EN LA BASE DE DATOS, COLABORADORES ========    
    public function consultarDni($dni){
        
        try {
            //======== VALIDANDO FORMATO DNI ========
            if(strlen($dni) !== 8){
                throw new Exception("EL DNI DEBE CONTAR CON 8 DÍGITOS");
            }

            //======== VALIDAR DNI ÚNICO =========
            $existe =   DB::select('select c.id from colaboradores as c
                    where c.nro_documento = ?',[$dni]);

            if(count($existe) > 0){
                throw new Exception('El dni ya existe en la tabla colaboradores');    
            }

            //======== CONSULTANDO DNI EN API RENIEC ========
            $utilController     =   new UtilController();
            $res_consulta_api   =   $utilController->apiDni($dni);
            $res                =   $res_consulta_api->getData();

            //======= EN CASO LA CONSULTA FUE EXITOSA =====
            if($res->success){
                return response()->json(['success'=>true,'data'=>$res->data,'message'=>'OPERACIÓN COMPLETADA']);
            }else{
                throw new Exception($res->message);
            }

        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
      
    }
}
