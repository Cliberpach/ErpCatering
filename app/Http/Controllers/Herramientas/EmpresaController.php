<?php

namespace App\Http\Controllers\Herramientas;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Utils\UtilController;
use App\Http\Requests\Herramientas\Empresa\EmpresaUpdateRequest;
use App\Models\Herramientas\Empresa;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    public function index(){
        $empresa    =   DB::select('select * from empresas as e
                        where e.id = ?',[1])[0];

        return view('herramientas.empresa.index',compact('empresa'));
    }

    public function update($id,EmpresaUpdateRequest $request){
       
        DB::beginTransaction();
        try {

            $empresa    =   Empresa::find($id);
            if(!$empresa){
                throw new Exception("LA EMPRESA NO EXISTE EN LA BD");
            }

            $empresa->ruc           =   $request->get('ruc');
            $empresa->razon_social  =   mb_strtoupper($request->get('razon_social'), 'UTF-8');
            $empresa->direccion     =   mb_strtoupper($request->get('direccion'), 'UTF-8');
            $empresa->telefono      =   $request->get('telefono');
            $empresa->correo        =   mb_strtoupper($request->get('correo'), 'UTF-8');
            $empresa->update();

            //======== EN CASO SE ESTÉ ENVIANDO IMAGEN NUEVA ========
            if($request->hasFile('img_empresa')){

                $carpeta_destino    =   public_path('img/empresa');
            
                if (!File::exists($carpeta_destino)) {
                    File::makeDirectory($carpeta_destino, 0755, true);
                }

                //========= ELIMINAR IMAGEN PREVIA =======
                $ruta_imagen_previa =   $empresa->img_ruta;
                if (File::exists($ruta_imagen_previa)) {
                    File::delete($ruta_imagen_previa);
                }

                //========== MANEJAR NUEVA IMAGEN ======
                $file               =   $request->file('img_empresa');
                $extension          =   $file->getClientOriginalExtension();
                $fileName           =   'img_empresa.'.$extension;
                $file->move($carpeta_destino, $fileName);

                $empresa->img_nombre    =   $fileName;
                $empresa->img_ruta      =   'img/empresa/'.$fileName;
                $empresa->update();
            }

            DB::commit();
            return response()->json(['success'=>true,'message'=>"EMPRESA ACTUALIZADA",'empresa'=>$empresa]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function consultarDocumento(Request $request){
        try {
            $nro_documento  =   $request->get('nro_documento',null);
            $tipo_documento =   $request->get('tipo_documento',null);
    
            if(!$nro_documento){
                throw new Exception("Es obligatorio el N° DE DOCUMENTO para realizar la consulta");  
            }

            if(!$tipo_documento){
                throw new Exception("Es obligatorio el TIPO DE DOCUMENTO para realizar la consulta");  
            }

            if (!is_numeric($nro_documento)) {
                throw new Exception("EL N° DE DOCUMENTO DEBE SER NUMÉRICO");  
            }

            if(!strlen($nro_documento) == 11){
                throw new Exception("EL N° DE DOCUMENTO DEBE TENER 11 DÍGITOS");  
            }

            $existe_tipo_documento  =   DB::select('select * from tipos_documento as td
                                        where td.id = ?',[$tipo_documento]);

            if(count($existe_tipo_documento) === 0){
                throw new Exception("nO EXISTE EL TIPO DE DOCUMENTO EN LA BD");  
            }

            if($tipo_documento != 2){
                throw new Exception("SOLO SE PUEDEN CONSULTAR N° DE RUC");  
            }

            $res_consulta_ruc   =   UtilController::apiRuc($nro_documento);            
            $res                =   $res_consulta_ruc->getData();

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
