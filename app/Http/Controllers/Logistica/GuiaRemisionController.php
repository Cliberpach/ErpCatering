<?php

namespace App\Http\Controllers\Logistica;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Greenter\Model\Response\BillResult;
use Greenter\Model\Sale\FormaPagos\FormaPagoContado;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Sale\SaleDetail;
use Greenter\Model\Sale\Legend;
use Greenter\Model\Client\Client;
use Greenter\Model\Company\Company;
use Greenter\Model\Company\Address;

use Greenter\Ws\Services\SunatEndpoints;
use DateTime;
use App\Greenter\Utils\Util;
use App\Models\Herramientas\Empresa;
use App\Models\Logistica\GuiaRemision;
use Exception;
use Greenter\Model\Sale\Note;
use Luecano\NumeroALetras\NumeroALetras;
use BaconQrCode\Renderer\GDLibRenderer;
use BaconQrCode\Writer;
use Greenter\Model\Despatch\Despatch;
use Greenter\Model\Despatch\DespatchDetail;
use Greenter\Model\Despatch\Direction;
use Greenter\Model\Despatch\Shipment;
use Greenter\Model\Despatch\Vehicle;
use Greenter\Model\Despatch\Driver;
use Greenter\Model\Response\CdrResponse;
use Greenter\Model\Response\SummaryResult;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response; 

class GuiaRemisionController extends Controller
{

    public function index(){
        return view('logistica.guias_remision.index');
    }

    public function getGuiasRemision(){

        $guias_remision    =   DB::table('guias_remision as gr')
                                ->leftJoin('registros_salida as rs', 'rs.guia_remision_id', '=', 'gr.id')
                                ->select(
                                    DB::raw('CONCAT("RS-", rs.id) as simbolo_salida'),
                                    DB::raw('CONCAT("GR-", gr.id) as simbolo'),  
                                    'gr.id', 
                                    'gr.created_at as fecha_registro',
                                    'gr.created_at as fecha_traslado',
                                    'gr.peso_total',
                                    'gr.nro_bultos',
                                    DB::raw('CONCAT(gr.serie,"-",gr.correlativo) as serie'), 
                                    'gr.result_ticket',
                                    'gr.result_success',
                                    'gr.estado' 
                                )
                                ->where('gr.estado','<>','ANULADO')
                                ->get();

        return DataTables::of($guias_remision)
        ->make(true);

    }


    public static function isActive(){
        $empresa_facturacion    =   DB::select('select 
                                        ef.*
                                        from empresas_facturacion as ef
                                        where 
                                        ef.tipo_comprobante_id = 96  
                                        and ef.simbolo = "09"
                                        and ef.estado = "ACTIVO"
                                        and ef.empresa_id = 1
                                    ');
        $res    =   null;

        if(count($empresa_facturacion) === 0){
            $res    =   (object)['success'=>false,
                                'message'=>'GUÍA DE REMISIÓN NO ESTÁ ACTIVA EN LA EMPRESA'];
        }else{
            $res    =   (object)['success'=>true,
                                'message'=>'GUÍA DE REMISIÓN ACTIVA EN LA EMPRESA',
                                'data'=>$empresa_facturacion[0]];
        }

        return $res;
    }

    public static function getCorrelativo(){

        $correlativo            =   null;
        $serie                  =   null;
        $empresa_facturacion    =   DB::select('select 
                                        ef.*
                                        from empresas_facturacion as ef
                                        where 
                                        ef.tipo_comprobante_id = 96  
                                        and ef.simbolo = "09"
                                        and ef.estado = "ACTIVO"
                                        and ef.empresa_id = 1
                                    ')[0];

        if($empresa_facturacion->iniciado == 0){

            $correlativo    =   $empresa_facturacion->nro_inicio;
            $serie          =   $empresa_facturacion->serie;

            DB::table('empresas_facturacion')
            ->where('tipo_comprobante_id', '96') 
            ->where('simbolo', '09') 
            ->where('estado', 'ACTIVO') 
            ->where('empresa_id', '1') 
            ->update([
                'iniciado' => 1,
            ]);

        }else{

            $cantidad_guias_remision    =   DB::select('select 
                                            count(*) as cantidad
                                            from guias_remision as gr
                                            where gr.estado != "ANULADO"');

            $correlativo                +=  $cantidad_guias_remision[0]->cantidad;
            $serie                      =   $empresa_facturacion->serie;

        }

        return (object)['correlativo'=>$correlativo,'serie'=>$serie];

    }


    /*
    ========= RESPUESTA 99 SIN CDR ========
    Greenter\Model\Response\StatusResult {#1653 // app\Http\Controllers\Logistica\GuiaRemisionController.php:176
    #success: false
    #error: Greenter\Model\Response\Error {#1650
        #code: "2567"
        #message: "Vehiculo principal: 2567 (nodo: "cac:TransportEquipment/cbc:ID" valor: "AUX-3242")"
    }
    #cdrZip: null
    #cdrResponse: null
    #code: "99"
    }
    ======== 

    =========== RESPUESTA 0 CON CDR =======
    Greenter\Model\Response\StatusResult {#1653 // app\Http\Controllers\Logistica\GuiaRemisionController.php:211
    #success: true
    #error: null
    #cdrZip: b"PK
    \x00\x00\e\x00\x00\x00\x00\x00\x00\x00\x01\x00\x00\x00
    ñü
    \x00\x00\x00\x00
    R-20161515648-09-T001-1.xmlPK
    \x05\x06\x00\x00\x00\x00\x01\x00\x01\x00
    I
    \x00\x00\x00\x07\x04\x00\x00\x00\x00
    "
    #cdrResponse: 
    Greenter\Model\Response
    \
    CdrResponse {#1640
        #id: "T001-1"
        #code: "0"
        #description: "ACEPTADA"
        #notes: array:1 [
        0 => "CDR de prueba"
        ]
        #reference: "https://url-test?hashqr=test"
    }
    #code: "0"
    }
    ===================================
    response_success
    response_code

    response_error_code
    response_error_message

    cdrzip_name

    cdr_response_id
    cdr_response_code
    cdr_response_description
    cdr_response_notes
    cdr_response_reference
    ========
    */ 
    public function consulta_sunat(Request $request){

        try {

            $guia_remision_id   =   $request->get('guia_remision_id',null);
            
            if(!$guia_remision_id){
                throw new Exception("EL PARÁMETRO GUÍA DE REMISIÓN ID ES NULO");
            }

            $guia_remision  =   GuiaRemision::find($guia_remision_id);

            if(!$guia_remision){
                throw new Exception("NO EXISTE LA GUÍA DE REMISIÓN EN LA BD");
            }

            $ticket =   $guia_remision->result_ticket;

            if(!$ticket){
                throw new Exception("LA GUÍA DE REMISIÓN NO TIENE TICKET");
            }

            $util = Util::getInstance();

            //===== INICIAR GREENTER API ====
            $empresa    =   Empresa::find(1);
            $see        =   $this->configuracionGreenter($util,$empresa);

            //======== CONSULTANDO ESTADO DE LA GUÍA =====
            $res        = $see->getStatus($ticket);

            //======== response estructura =======
            /*  code: 99(envío con error)   |   cdrResponse (null o con contenido)
                code: 98(envío en proceso)  |   cdrResponse(aún sin cdr)
                code: 0(envío ok)           |   cdrResponse(con contenido)    
            */

            $code_estado    =   $res->getCode();
            $cdr_response   =   $res->getCdrResponse();
            $descripcion    =   null;
    
            $guia_remision->response_success =   $res->isSuccess()?'1':'0';
            $message        =   '';


            if($code_estado == 0){
                $guia_remision->estado              =   'ACEPTADO';
                $guia_remision->response_code       =   $code_estado;
                

                //==== GUARDANDO DATOS DEL CDRZIP =====
                $guia_remision->cdr_response_id          =   $cdr_response->getId();
                $guia_remision->cdr_response_code        =   $cdr_response->getCode();
                $guia_remision->cdr_response_description =   $cdr_response->getDescription();
                $guia_remision->cdr_response_reference   =   $cdr_response->getReference();

                //========= GUARDANDO NOTES ======
                $guia_remision->cdr_response_notes = '|' . implode('|', $cdr_response->getNotes()) . '|';

                //====== GUARDANDO CDR  =========== 
                $util->writeCdr(null, $res->getCdrZip(), "GUIA REMISION",$guia_remision->despatch_name);
                $guia_remision->ruta_cdr      =   'greenter/guias_remision/cdr/'.$guia_remision->despatch_name.'.zip';
                
                //========= GENERANDO QR =========
                $directory = public_path('greenter/guias_remision/qr');
                if (!File::exists($directory)) {
                    File::makeDirectory($directory, 0755, true); 
                }

                $renderer = new GDLibRenderer(400); // Tamaño del QR
                $writer = new Writer($renderer);

                $contenido  =   $cdr_response->getReference(); 
                $filePath   =   $directory . '/' . $guia_remision->despatch_name . '.png';  
                $writer->writeFile($contenido, $filePath);

                $guia_remision->ruta_qr  =   'greenter/guias_remision/qr/'.$guia_remision->despatch_name . '.png';   
                $guia_remision->update();
                $message        =   'GUÍA DE REMISIÓN ACEPTADA Y CDR RECIBIDO';
            }

            if($code_estado == 98){
                $guia_remision->estado              =   'EN PROCESO';
                $guia_remision->response_code       =   $code_estado;
                $guia_remision->update();
                $message                            =   'GUÍA DE REMISIÓN EN ESPERA';
            }

            if($code_estado == 99 && $cdr_response){
            
                $guia_remision->estado              =   'ACEPTADO CON ERRORES';
                $guia_remision->response_code       =   $code_estado;
               
                //==== GUARDANDO DATOS DEL CDRZIP =====
                $guia_remision->cdr_response_id          =   $cdr_response->getId();
                $guia_remision->cdr_response_code        =   $cdr_response->getCode();
                $guia_remision->cdr_response_description =   $cdr_response->getDescription();
                $guia_remision->cdr_response_reference   =   $cdr_response->getReference();

                //========= GUARDANDO NOTES ======
                $guia_remision->cdr_response_notes = '|' . implode('|', $cdr_response->getNotes()) . '|';

                //===== GUARDANDO ERRORES ======
                if($res->getError()){
                    $guia_remision->response_error_code      =   $res->getError()->getCode();
                    $guia_remision->response_error_message   =   $res->getError()->getMessage();
                }
                  
                //====== GUARDANDO CDR  =========== 
                $util->writeCdr(null, $res->getCdrZip(), "GUIA REMISION",$guia_remision->despatch_name);
                $guia_remision->ruta_cdr      =   'greenter/guías_remisión/cdr/'.$guia_remision->despatch_name.'.zip';
                $guia_remision->update();

                $message = 'GUÍA DE REMISIÓN ACEPTADA CON ERRORES, SIN CDR. <br>' .
                            '<strong>CODE:</strong> ' . $res->getError()->getCode() . '<br>' .
                            '<strong>MESSAGE:</strong> ' . $res->getError()->getMessage();
            }

            if($code_estado == '99' && !$cdr_response){
                $guia_remision->response_code   =   $code_estado;

                //===== GUARDANDO ERRORES ======
                if($res->getError()){
                    $guia_remision->response_error_code      =   $res->getError()->getCode();
                    $guia_remision->response_error_message   =   $res->getError()->getMessage();
                }

                $guia_remision->estado   =   'RECHAZADO';
                $guia_remision->update();
                $message = 'GUÍA DE REMISIÓN RECHAZADA, SIN CDR. <br>' .
                            '<strong>CODE:</strong> ' . $res->getError()->getCode() . '<br>' .
                            '<strong>MESSAGE:</strong> ' . $res->getError()->getMessage();

                throw new Exception($message);
            }


            return response()->json(['success'=>true,'message'=>$message]);


        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);

        }

    }


    /*
        ==== RESPUESTA A ENVÍO ==== 
        Greenter\Model\Response\SummaryResult {#3705 // app\Http\Controllers\Logistica\GuiaRemisionController.php:320
            #success: true
            #error: null
            #ticket: "test-e409417c-bcb7-4771-893a-603f83fab918"
        }

        ========= result_success , result_error , result_ticket ===========
    */ 
    public function send_sunat(Request $request){
        try {
           
            
            $guia_remision_id    =   $request->get('guia_remision_id');

            if(!$guia_remision_id){
                throw new Exception("NO EXISTE EL ID DE LA GUÍA EN LA PETICIÓN");
            }
            
            $empresa        =   Empresa::first();

            $guia_remision  =   DB::select('select
                                gr.*,
                                v.placa,
                                c.nro_documento as conductor_nro_documento,
                                c.licencia as conductor_licencia,
                                c.nombres as conductor_nombre,
                                c.apellidos as conductor_apellido,
                                td.descripcion as conductor_tipo_documento
                                from guias_remision as gr
                                inner join vehiculos as v on v.id = gr.vehiculo_id
                                inner join conductores as c  on c.id = gr.conductor_id
                                inner join tipos_documento as td on td.id = c.tipo_documento_id
                                where gr.id = ?
                                and gr.estado != "ANULADO"',
                                [$guia_remision_id])[0];

            $guia_remision_detalle      =   DB::select('select 
                                            p.nombre as producto_nombre,
                                            grd.descripcion,
                                            grd.codigo,
                                            grd.cantidad,
                                            tgd.descripcion as unidad_medida_nombre
                                            from guias_remision_detalle as grd    
                                            inner join productos as p on p.id = grd.producto_id
                                            inner join categorias as c on c.id = p.categoria_id
                                            inner join marcas as m on m.id = p.marca_id
                                            inner join tablas_generales_detalles as tgd on tgd.id = p.unidad_medida_id
                                            where grd.guia_remision_id = ?',
                                            [$guia_remision_id]);


            $util = Util::getInstance();


            //===== VEHÍCULO =====
            $vehiculoPrincipal = (new Vehicle())
                                ->setPlaca($guia_remision->placa);

            $chofer = (new Driver())
            ->setTipo('Principal')
            ->setTipoDoc('1')
            ->setNroDoc($guia_remision->conductor_nro_documento)
            ->setLicencia($guia_remision->conductor_licencia)
            ->setNombres($guia_remision->conductor_nombre)
            ->setApellidos($guia_remision->conductor_apellido);

            //====== MODOS TRASLADO:  PUBLICO 01 - PRIVADO 02 ======
            //=========== Envío ================
            $envio = new Shipment();
            $envio
                ->setCodTraslado($guia_remision->codigo_traslado) // Cat.20 - Venta
                ->setModTraslado($guia_remision->modo_traslado) // Cat.18 - Transp. Privado
                ->setFecTraslado(new DateTime($guia_remision->fecha_traslado))
                ->setPesoTotal($guia_remision->peso_total)
                ->setUndPesoTotal($guia_remision->unidad_peso_total)
                ->setVehiculo($vehiculoPrincipal)
                ->setChoferes([$chofer])
                ->setLlegada(
                    (new Direction($guia_remision->direccion_origen_ubigeo, $guia_remision->direccion_origen_nombre))
                    ->setRuc($empresa->ruc)
                    ->setCodLocal($empresa->codigo_local))
                ->setPartida(
                    (new Direction($guia_remision->direccion_destino_ubigeo, $guia_remision->direccion_destino_nombre))
                    ->setRuc($empresa->ruc)
                    ->setCodLocal($empresa->codigo_local));

            
            $despatch = new Despatch();
            $despatch->setVersion('2022')
                    ->setTipoDoc($guia_remision->tipo_documento) //09
                    ->setSerie($guia_remision->serie)
                    ->setCorrelativo($guia_remision->correlativo)
                    ->setFechaEmision(new DateTime($guia_remision->fecha_emision))
                    ->setCompany($util->shared->getCompany())
                    ->setDestinatario((new Client())
                        ->setTipoDoc('6')
                        ->setNumDoc($empresa->ruc)
                        ->setRznSocial($empresa->razon_social)) // misma empresa
                    ->setEnvio($envio);

            //===== LLENANDO DETALLE =======
            $detalles   =   [];
            foreach ($guia_remision_detalle as $producto) {
                 
                $detail = new DespatchDetail();
                $detail->setCantidad($producto->cantidad)
                     ->setUnidad($producto->unidad_medida_nombre)
                     ->setDescripcion($producto->descripcion)
                     ->setCodigo($producto->codigo);
                     
                $detalles[] =   $detail;

             }

           
            $despatch->setDetails($detalles);

            //========== CONFIGURACIÓN GREENTER ========
            $see    =   $this->configuracionGreenter($util,$empresa);

            //======== CONSTRUYENDO XML Y ENVIANDO A SUNAT ==========
            $res = $see->send($despatch);  
          
            $guia_to_actualizar =   GuiaRemision::find($request->get('guia_remision_id')); 

            //======== RESPONSE ESTRUCTURA ========
            // ticket(string) | success(boolean) | error
            //==== GUARDANDO XML ====
            $util->writeXml($despatch, $see->getLastXml(),"GUIA REMISION",null);
            $guia_to_actualizar->ruta_xml      =   'greenter/guias_remision/xml/'.$despatch->getName().'.xml';
      
            //===== VERIFICANDO CONEXIÓN CON SUNAT =======
            if($res->isSuccess()){
                                
                //==== OBTENER Y GUARDAR TICKET ====
                $ticket                             =   $res->getTicket();
                $guia_to_actualizar->result_success =   '1';
                $guia_to_actualizar->result_ticket  =   $ticket;
                $guia_to_actualizar->estado         =   'ENVIADO';
                $guia_to_actualizar->despatch_name  =   $despatch->getName();
                $guia_to_actualizar->update();
                
                return response()->json(['success'=>true,'message'=>"GUÍA DE REMISIÓN REGISTRADA Y ENVIADA A SUNAT"]);
            } else{

                //COMO SUNAT NO LO ADMITE VUELVE A SER 0
                $guia_to_actualizar->result_success     =   '0';
                $guia_to_actualizar->estado             =   'PENDIENTE';
                $guia_to_actualizar->despatch_name      =   $despatch->getName();
                $guia_to_actualizar->update();

                return response()->json(['success'=>false,'message'=>'GUÍA DE REMISIÓN REGISTRADA, ERROR AL ENVIAR A SUNAT']);

            }

        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function configuracionGreenter($util,$empresa){
        //======== REVIZANDO CONFIGURACIÓN DEL AMBIENTE GREENTER ========
        $see            =   null;
        $configuracion  =   DB::select('select * 
                               from configuracion as c
                               where 
                               c.id = 1
                               and c.estado = "ACTIVO"');

        if(count($configuracion) === 0){
               throw new Exception("NO EXISTE LA CONFIGURACIÓN EN LA BD");
         }

        //======= VERIFICAR QUE EXISTA EL CERTIFICADO TEST EN PUBLIC/GREENTER/CERTIFICADO/certificado_test.pem =======
        if($configuracion[0]->propiedad === 'BETA'){

               $ruta_certificado_test          = public_path('greenter/certificado/certificado_test.pem');
               $ruta_origen_certificado_test   = app_path('Greenter/certificado/certificado_test.pem');
               
               if (!File::exists($ruta_certificado_test)) {
                   if (File::exists($ruta_origen_certificado_test)) {
                       File::copy($ruta_origen_certificado_test, $ruta_certificado_test);
                   } 
               }

             
               $configuracion[0]   =   $util->setCredencialesBetaApi($configuracion[0]);
              
               $see                =   $util->getSeeApi($configuracion[0]);

        }

        //========= VERIFICAR QUE EXISTA EL CERTIFICADO INDICADO EN LA TABLA EMPRESA =======
        if($configuracion[0]->propiedad === 'PRODUCCION'){

            if(!$empresa->certificado_ruta){
                   throw new Exception("NO HA ESTABLECIDO UN CERTIFICADO EN HERRAMIENTAS/EMPRESA");
            }

            $ruta_certificado_produccion          = public_path($empresa->certificado_ruta);

            if (!File::exists($ruta_certificado_produccion)) {
                throw new Exception("NO EXISTE EL CERTIFICADO EN LA RUTA INDICADA: ".$empresa->certificado_ruta);
            }

            $configuracion[0]->ruc                  =   $empresa->ruc;
            $configuracion[0]->certificado_ruta     =   $empresa->certificado_ruta;
            $configuracion[0]->usuario_sol          =   $empresa->usuario_sol;
            $configuracion[0]->clave_sol            =   $empresa->clave_sol;
            $configuracion[0]->usuario_api_guias    =   $empresa->usuario_api_guias;
            $configuracion[0]->clave_api_guias      =   $empresa->clave_api_guias;

            $see    =   $util->getSeeApi($configuracion[0]);

        }

        if(!$see){
            throw new Exception('ERROR EN LA CONFIGURACIÓN DE GREENTER, SEE ES NULO');
        }

        return $see;
    }


    /*
    {#1538 ▼ // app\Http\Controllers\Logistica\GuiaRemisionController.php:126
    +"id": 4
    +"registro_salida_id": null
    +"conductor_id": 1
    +"vehiculo_id": 1
    +"codigo_traslado": "04"
    +"modo_traslado": "02"
    +"fecha_traslado": "2024-10-21 00:00:00"
    +"peso_total": "1.00"
    +"unidad_peso_total": "KGM"
    +"nro_bultos": "1.00"
    +"almacen_origen_id": 1
    +"almacen_destino_id": 2
    +"direccion_origen_nombre": "DIRECCION PRINCIPAL"
    +"direccion_origen_ubigeo": "130101"
    +"direccion_destino_nombre": "CAR. PANAMERICANA SUR NRO. 241  PANAMERICANA SUR, ICA - PISCO - PARACAS"
    +"direccion_destino_ubigeo": "140101"
    +"tipo_documento": "09"
    +"version": "2022"
    +"serie": "T001"
    +"correlativo": 1
    +"fecha_emision": "2024-10-21 00:00:00"
    +"empresa_emisora_id": 1
    +"destinatario_tipo_documento": "6"
    +"destinatario_nro_documento": "20161515648"
    +"destinatario_razon_social": "TU_EMPRESA"
    +"ticket": null
    +"despatch_name": null
    +"response_success": null
    +"response_code": null
    +"cdrzip_name": null
    +"cdr_response_id": null
    +"cdr_response_code": null
    +"cdr_response_description": null
    +"cdr_response_notes": null
    +"cdr_response_reference": null
    +"ruta_cdr": null
    +"ruta_xml": null
    +"ruta_qr": null
    +"response_error_code": null
    +"response_error_message": null
    +"estado": "PENDIENTE"
    +"created_at": "2024-10-21 19:36:01"
    +"updated_at": "2024-10-21 19:36:01"
    +"motivo_traslado": "TRASLADO ENTRE ESTABLECIMIENTOS DE LA MISMA EMPRESA"
    +"modo_traslado_descripcion": "TRANSPORTE PRIVADO"
    +"placa": "AUX-3242"
    +"conductor_nro_documento": "75542134"
    +"conductor_tipo_documento": "DNI"
    }
    */

    /*
    array:1 [▼ // app\Http\Controllers\Logistica\GuiaRemisionController.php:188
    0 => {#1612 ▼
        +"producto_nombre": "CEMENTO ROJO MOCHICA X 45 KG"
        +"categoria_nombre": "CEMENTO"
        +"marca_nombre": "MOCHICA"
        +"cantidad": "10.00"
        +"unidad_medida_nombre": "UNIDAD"
    }
    ]
    */
    public function pdf($id){

        $empresa    =   DB::select('select * from empresas as e
                        where e.id = 1')[0];

        $guia_remision =   DB::select('select
                                gr.*,
                                v.placa,
                                c.nro_documento as conductor_nro_documento,
                                td.descripcion as conductor_tipo_documento
                                from guias_remision as gr
                                inner join vehiculos as v on v.id = gr.vehiculo_id
                                inner join conductores as c  on c.id = gr.conductor_id
                                inner join tipos_documento as td on td.id = c.tipo_documento_id
                                where gr.id = ?
                                and gr.estado != "ANULADO"',
                                [$id])[0];

        $guia_remision_detalle      =   DB::select('select 
                                        p.nombre as producto_nombre,
                                        c.descripcion as categoria_nombre,
                                        m.descripcion as marca_nombre,
                                        grd.cantidad,
                                        tgd.descripcion as unidad_medida_nombre
                                        from guias_remision_detalle as grd    
                                        inner join productos as p on p.id = grd.producto_id
                                        inner join categorias as c on c.id = p.categoria_id
                                        inner join marcas as m on m.id = p.marca_id
                                        inner join tablas_generales_detalles as tgd on tgd.id = p.unidad_medida_id
                                        where grd.guia_remision_id = ?',
                                        [$id]);

        Carbon::setLocale('es');
        $fecha_impresion = Carbon::now();
        $fecha_impresion = $fecha_impresion->translatedFormat('l, d \d\e F \d\e\l Y');
        $fecha_impresion = strtoupper($fecha_impresion);


        // Configurar las opciones de DOMPDF si es necesario
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');

        // Instanciar el objeto DOMPDF
        $dompdf = new Dompdf($options);

        // Definir el contenido del PDF (HTML)
        $html = view('logistica.guias_remision.pdf.pdf',
        compact('empresa','guia_remision','fecha_impresion','guia_remision_detalle'))
            ->render();

        // Cargar el HTML en DOMPDF
        $dompdf->loadHtml($html);

        // Opcional: Configurar el tamaño de papel y la orientación
        $dompdf->setPaper('A4', 'portrait'); // O 'landscape'

        // Renderizar el PDF
        $dompdf->render();

        // Visualizar el PDF en una nueva ventana en lugar de descargarlo
        return $dompdf->stream('guia_remision.pdf', ['Attachment' => false]);
    }
}
