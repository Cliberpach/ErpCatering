<?php

namespace App\Http\Controllers\Compras;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Utils\UtilController;
use App\Http\Requests\Compras\Proveedor\ProveedorStoreRequest;
use App\Http\Requests\Compras\Proveedor\ProveedorUpdateRequest;
use App\Models\Compras\Proveedor;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Exception;
use Yajra\DataTables\Facades\DataTables;

class ProveedorController extends Controller
{
    public function index(){
        return view('compras.proveedores.index');
    }

    public function getProveedores(){
        $proveedores = DB::table('proveedores as pr')
                        ->join('tipos_documento as td', 'td.id', '=', 'pr.tipo_documento_id')
                        ->leftJoin('bancos as b', 'b.id', '=', 'pr.banco_id')
                        ->select(
                            'pr.id', 
                            'td.descripcion as tipo_documento_descripcion',
                            'pr.nro_documento',
                            'pr.nombre',
                            'pr.direccion',
                            'pr.telefono',
                            'pr.correo',
                            'pr.estado',
                            'b.nombre as banco_nombre',
                            'pr.nro_cuenta',
                            'pr.cci',
                            'pr.nro_cuenta_detraccion'
                        )
                        ->where('pr.estado','ACTIVO')
                        ->get();

        return DataTables::of($proveedores)
                    ->make(true);
    }


    public function create(){
        $tipos_documento    =   DB::select('select * 
                                from tipos_documento as td
                                where td.estado = "ACTIVO"
                                and td.id <> "3" ');

        $bancos             =   DB::select('select * from bancos as b
                                where b.estado = "ACTIVO"');

        return view('compras.proveedores.create',
        compact('tipos_documento','bancos'));
    }


    /*
    array:11 [ // app\Http\Controllers\Compras\ProveedorController.php:57
        "_token"            => "vIEl6FeSyG6BGHQs3ipq4uWmeqymdP7JB4y5DFwc"
        "tipo_documento"    => "1"
        "nro_documento"     => "75608753"
        "nombre"            => "LUIS DANIEL ALVA LUJAN"
        "banco"             => "2"
        "nro_cuenta"        => "41241251251"
        "cci"               => "41241241434"
        "cuenta_detraccion" => "151251255414"
        "direccion"         => "av magnolias 321"
        "telefono"          => "974585471"
        "correo"            => "EVA@GMAIL.COM"
    ]
    */
    public function store(ProveedorStoreRequest $request){
       
        DB::beginTransaction();
        try {

            $proveedor                          =   new Proveedor();
            $proveedor->tipo_documento_id       =   $request->get('tipo_documento');
            $proveedor->nro_documento           =   $request->get('nro_documento');
            $proveedor->nombre                  =   $request->get('nombre');
            $proveedor->direccion               =   $request->get('direccion');
            $proveedor->telefono                =   $request->get('telefono');
            $proveedor->correo                  =   $request->get('correo');
            $proveedor->banco_id                =   $request->get('banco');
            $proveedor->nro_cuenta              =   $request->get('nro_cuenta');
            $proveedor->cci                     =   $request->get('cci');
            $proveedor->nro_cuenta_detraccion   =   $request->get('cuenta_detraccion');
            $proveedor->save();

            DB::commit();

            return response()->json(['success' => true,'message'=>'PROVEEDOR REGISTRADO CON ÉXITO']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function edit($id){
        $tipos_documento    =   DB::select('select * 
                                from tipos_documento as td
                                where td.estado = "ACTIVO"
                                and td.id <> "3" ');

        $proveedor  =   Proveedor::find($id);

        $bancos             =   DB::select('select * from bancos as b
                                where b.estado = "ACTIVO"');

        if(!$proveedor){
            dd('EL PROVEEDOR NO EXISTE EN LA BD');
        }
        if($proveedor->estado == "ANULADO"){
            dd('PROVEEDOR ANULADO');
        }

        return view('compras.proveedores.edit',
        compact('proveedor','tipos_documento','bancos'));
    }


    /*
    array:11 [ // app\Http\Controllers\Compras\ProveedorController.php:122
        "_token"            => "vIEl6FeSyG6BGHQs3ipq4uWmeqymdP7JB4y5DFwc"
        "tipo_documento"    => "1"
        "nro_documento"     => "75608753"
        "nombre"            => "LUIS DANIEL ALVA LUJAN"
        "banco"             => "2"
        "nro_cuenta"        => "41241251251"
        "cci"               => "412412414342"
        "cuenta_detraccion" => "151251255414"
        "direccion"         => "av magnolias 321"
        "telefono"          => "974585471"
        "correo"            => "EVA@GMAIL.COM"
    ]
  */ 
    public function update($id,ProveedorUpdateRequest $request){
      
        DB::beginTransaction();
        try {
            $proveedor                          =   Proveedor::find($id);
            $proveedor->tipo_documento_id       =   $request->get('tipo_documento');
            $proveedor->nro_documento           =   $request->get('nro_documento');
            $proveedor->nombre                  =   $request->get('nombre');
            $proveedor->direccion               =   $request->get('direccion');
            $proveedor->telefono                =   $request->get('telefono');
            $proveedor->correo                  =   $request->get('correo');
            $proveedor->banco_id                =   $request->get('banco');
            $proveedor->nro_cuenta              =   $request->get('nro_cuenta');
            $proveedor->cci                     =   $request->get('cci');
            $proveedor->nro_cuenta_detraccion   =   $request->get('cuenta_detraccion');
            $proveedor->update();

            DB::commit();

            return response()->json(['success' => true,'message'=>'PROVEEDOR ACTUALIZADO CON ÉXITO']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function consultarDocumento(Request $request){
        try {
            //========= VALIDANDO QUE EL TIPO DOCUMENTO Y N° DOCUMENTO NO SEAN NULL =======
            $tipo_documento =   $request->get('tipo_documento',null);
            $nro_documento  =   $request->get('nro_documento',null);

            if(!$tipo_documento){
                throw new Exception("EL TIPO DE DOCUMENTO ES OBLIGATORIO");
            }

            if(!$nro_documento){
                throw new Exception("EL N° DOC ES OBLIGATORIO");
            }

            if (!is_numeric($nro_documento)) {
                throw new Exception("EL N° DOCUMENTO DEBE SER NUMÉRICO");
            }

            //========= VERIFICANDO QUE EXISTA EL TIPO DOC EN LA BD ========
            $exists_tipo_doc    =   DB::select('select 
                                    td.id,td.descripcion
                                    from tipos_documento as td
                                    where td.id = ?',[$tipo_documento]);

            if(count($exists_tipo_doc) === 0){
                throw new Exception("EL TIPO DE DOC NO EXISTE EN LA BD");
            }

            if($tipo_documento != 1 && $tipo_documento != 2){
                throw new Exception("SOLO SE PUEDEN CONSULTAR DNI Y RUC");
            }

            if ( $tipo_documento == 1 && strlen($nro_documento) != 8) {
                throw new Exception("EL TIPO DE DOCUMENTO DNI DEBE TENER 8 DÍGITOS");
            }

            if ( $tipo_documento == 2 && strlen($nro_documento) != 11) {
                throw new Exception("EL TIPO DE DOCUMENTO RUC DEBE TENER 11 DÍGITOS");
            }


            //======= COMPROBAR QUE NO EXISTA EL DOCUMENTO EN LA TABLA PROVEEDORES =======
            $existe_nro_documento   =   DB::select('select 
                                        pr.id,pr.nombre
                                        from proveedores as pr
                                        where 
                                        pr.tipo_documento_id = ?
                                        and pr.nro_documento = ? 
                                        and pr.estado = "ACTIVO"',
                                        [$tipo_documento,$nro_documento]);

            if(count($existe_nro_documento) > 0){
                throw new Exception($exists_tipo_doc[0]->descripcion.':'.$nro_documento.'.YA EXISTE EN LA BD');
            }
            
            if($tipo_documento == 1){

                $res_consulta_api   =   UtilController::apiDni($nro_documento);
                $res                =   $res_consulta_api->getData();

                //======= EN CASO LA CONSULTA FUE EXITOSA =====
                if($res->success){
                    return response()->json(['success'=>true,'data'=>$res->data,'message'=>'OPERACIÓN COMPLETADA']);
                }else{
                    throw new Exception($res->message);
                }
            }

            if($tipo_documento == 2){
                $res_consulta_api   =   UtilController::apiRuc($nro_documento);
                $res                =   $res_consulta_api->getData();

                //======= EN CASO LA CONSULTA FUE EXITOSA =====
                if($res->success){
                    return response()->json(['success'=>true,'data'=>$res->data,'message'=>'OPERACIÓN COMPLETADA']);
                }else{
                    throw new Exception($res->message);
                }
            }


        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function getListProveedores(){
        try {
            $proveedores    =   DB::select('select 
                                pr.id,
                                pr.nombre,
                                pr.nro_documento,
                                td.descripcion as tipo_documento_descripcion
                                from proveedores as pr
                                inner join tipos_documento as td on td.id = pr.tipo_documento_id
                                where pr.estado = "ACTIVO"');
                                
            return response()->json(['success'=>true,'lstProveedores'=>$proveedores]);
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function destroy($id){
        DB::beginTransaction();
        try {
            $proveedor  =   Proveedor::find($id);
            $proveedor->estado  =   'ANULADO';
            $proveedor->update();

            DB::commit();
            return response()->json(['success'=>true,'PROVEEDOR ELIMINADO']);
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }
}
