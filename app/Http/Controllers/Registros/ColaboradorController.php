<?php

namespace App\Http\Controllers\Registros;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Utils\UtilController;
use App\Http\Requests\Registros\Colaborador\ColaboradorStoreRequest;
use App\Http\Requests\Registros\Colaborador\ColaboradorUpdateRequest;
use App\Models\Registros\Cargo;
use App\Models\Registros\Colaborador;
use App\Models\Herramientas\TipoDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class ColaboradorController extends Controller
{
    public function index(){
        return view('registros.colaboradores.index');
    }

    public function getColaboradores(Request $request){

        $colaboradores  =   DB::table('colaboradores as co')
                            ->join('cargos as ca', 'ca.id', '=', 'co.cargo_id')
                            ->select(
                                'co.id', 
                                'co.nombre',
                                'co.direccion',
                                'co.telefono',
                                'co.nro_documento',
                                'co.dias_trabajo',
                                'co.dias_descanso',
                                'co.pago_mensual',
                                'ca.descripcion as cargo_nombre',
                                'co.estado'
                            )
                            ->where('co.estado','ACTIVO')
                            ->get();

        return DataTables::of($colaboradores)
                ->make(true);
    }

    public function create(){
        $tipos_documento    =   TipoDocumento::where('estado','ACTIVO')
                                ->where('id','<>',2)->get();
        $cargos             =   Cargo::where('estado','ACTIVO')->get();
       
        return view('registros.colaboradores.create',compact('tipos_documento','cargos'));
    }


/*
array:10 [ // app\Http\Controllers\Registros\ColaboradorController.php:55
  "_token"              => "d38M0zVONrs0K90ZaS3Qr4eqnPK3bSvETRV0CxjX"
  "tipo_documento"      => "1"
  "nro_documento"       => "80239830"
  "nombre"              => "HILMER JULIAN PALOMINOs"
  "cargo"               => "1"
  "direccion"           => "AV CHAVIMOCHIC 1234"
  "telefono"            => "974585471"
  "dias_trabajo"        => "24"
  "dias_descanso"       => "12"
  "pago_mensual"        => "1200"
]
*/ 
    public function store(ColaboradorStoreRequest $request){
      
        DB::beginTransaction();
        try {
            $colaborador                    =   new Colaborador();
            $colaborador->tipo_documento_id =   $request->get('tipo_documento');
            $colaborador->nombre            =   Str::upper($request->get('nombre'));
            $colaborador->cargo_id          =   $request->get('cargo');
            $colaborador->direccion         =   Str::upper($request->get('direccion'));
            $colaborador->telefono          =   $request->get('telefono');
            $colaborador->dias_trabajo      =   $request->get('dias_trabajo');
            $colaborador->dias_descanso     =   $request->get('dias_descanso');
            $colaborador->pago_mensual      =   $request->get('pago_mensual');
            $colaborador->nro_documento     =   $request->get('nro_documento');
            $colaborador->pago_dia          =   $request->get('pago_mensual')/30;
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

        $cargos             =   Cargo::where('estado','ACTIVO')->get();

       
        return view('registros.colaboradores.edit',compact('tipos_documento','colaborador','cargos'));
    }

    public function update(ColaboradorUpdateRequest $request, $id){
        DB::beginTransaction();
        try {
            $colaborador                    =   Colaborador::find($id);
            $colaborador->tipo_documento_id =   $request->get('tipo_documento');
            $colaborador->nombre            =   Str::upper($request->get('nombre'));
            $colaborador->cargo_id          =   $request->get('cargo');
            $colaborador->direccion         =   Str::upper($request->get('direccion'));
            $colaborador->telefono          =   $request->get('telefono');
            $colaborador->dias_trabajo      =   $request->get('dias_trabajo');
            $colaborador->dias_descanso     =   $request->get('dias_descanso');
            $colaborador->pago_mensual      =   $request->get('pago_mensual');
            $colaborador->nro_documento     =   $request->get('nro_documento');
            $colaborador->pago_dia          =   $request->get('pago_mensual')/30;
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
            $existe =   DB::select('select 
                        c.id 
                        from colaboradores as c
                        where c.nro_documento = ? 
                        and c.estado = "ACTIVO"',
                        [$dni]);

            if(count($existe) > 0){
                throw new Exception('El dni ya existe en la tabla colaboradores');    
            }

            //======== CONSULTANDO DNI EN API RENIEC ========
            $res_consulta_api   =   UtilController::apiDni($dni);
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

    public function getSupervisores(){
        try {
            $supervisores   =   DB::select('select co.id,co.nombre 
                                from colaboradores as co
                                inner join cargos as ca on ca.id = co.cargo_id
                                left join proyectos as pr on pr.supervisor_id = co.id 
                                where ca.descripcion = "SUPERVISOR" 
                                and co.estado =  "ACTIVO" 
                                and pr.supervisor_id is null');

            return response()->json(['success'=>true,'supervisores'=>$supervisores]);
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }
}
