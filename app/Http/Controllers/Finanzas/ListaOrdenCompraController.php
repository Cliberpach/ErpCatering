<?php

namespace App\Http\Controllers\Finanzas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Finanzas\ListaOrdenCompra\OrdenCompraToOrdenPagoStoreRequest;
use App\Models\Compras\OrdenCompra;
use App\Models\Finanzas\OrdenPago;
use App\Models\Finanzas\OrdenPagoDetalle;
use App\Models\Finanzas\OrdenPagoImg;
use App\Models\Registros\Colaborador;
use App\Models\Requerimientos\Requerimiento;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\File;

class ListaOrdenCompraController extends Controller
{
    public function index(){
        return view('finanzas.lista_ordenes_compra.index');
    }

    public function getOrdenesCompra(Request $request){

        $ordenes_compra    =   DB::table('ordenes_compra as oc')
                                ->leftJoin('cotizacion_compra as cc','cc.orden_compra_id','oc.id')
                                ->join('proveedores as prov','prov.id','=','oc.proveedor_id')
                                ->join('modalidades_pago as m', 'm.id', '=', 'oc.modalidad_pago_id')
                                ->join('proyectos as proy','proy.id','=','oc.proyecto_id')
                                ->join('colaboradores as co','co.id','=','oc.persona_contacto_id')
                                ->join('colaboradores as col','col.id','=','oc.colaborador_registrador_id')
                                ->join('productos as p','p.id','oc.primer_producto_id')
                                ->select(
                                    DB::raw('CONCAT("OC-", oc.id) as simbolo'), 
                                    'oc.id', 
                                    'prov.nombre as proveedor_nombre',
                                    'm.tipo as modalidad_pago',
                                    'proy.nombre as proyecto_nombre',
                                    'oc.documento as orden_compra_documento',
                                    'oc.direccion_obra as orden_compra_direccion_obra',
                                    'oc.observacion as orden_compra_observacion',
                                    'co.nombre as persona_contacto_nombre',
                                    'col.nombre as colaborador_registrador_nombre',
                                    'oc.fecha_entrega as orden_compra_fecha_entrega',
                                    'oc.terminos_entrega as orden_compra_terminos_entrega',
                                    'oc.estado as orden_compra_estado',
                                    'oc.created_at as orden_compra_fecha_registro',
                                    'cc.id as cotizacion_compra_id',
                                    DB::raw('CONCAT("CO-", cc.id) as simbolo_cotizacion_compra'),
                                    'p.nombre as primer_producto_nombre' 
                                )
                                ->where('oc.estado','<>','ANULADO')
                                ->where('m.tipo', '=', 'CONTADO')
                                ->get();

        return DataTables::of($ordenes_compra)
                ->make(true);
    }

    public function ordenCompraToOrdenPagoCreate($orden_compra_id){

        //======= VALIDANDO QUE LA ORDEN DE COMPRA TENGA ESTADO PENDIENTE =========
        $orden_compra   =   DB::select('select 
                            oc.id,
                            oc.estado,
                            prov.nombre as proveedor_nombre,
                            prov.nro_documento as proveedor_nro_documento,
                            b.nombre as banco_nombre,
                            prov.nro_cuenta as proveedor_nro_cuenta,
                            prov.cci as proveedor_cci, 
                            prov.nro_cuenta_detraccion as proveedor_nro_cuenta_detraccion,
                            td.descripcion as proveedor_tipo_documento,
                            oc.documento,
                            oc.proyecto_id,
                            oc.moneda,
                            oc.subtotal,
                            oc.monto_igv,
                            oc.total
                            from ordenes_compra as oc
                            inner join proveedores as prov on prov.id = oc.proveedor_id
                            left join bancos as b on b.id = prov.banco_id
                            inner join tipos_documento as td on td.id = prov.tipo_documento_id
                            where 
                            oc.id = ?',
                            [$orden_compra_id]);

        
        if(count($orden_compra) === 0){
            Session::flash('lista_orden_compra_error','LA ORDEN DE COMPRA NO EXISTE EN LA BD');
            return back();
        }

        if($orden_compra[0]->estado !== 'PENDIENTE'){
            Session::flash('lista_orden_compra_error','LA ORDEN DE COMPRA TIENE ESTADO: '.$orden_compra[0]->estado);
            return back(); 
        }
        $orden_compra   =   $orden_compra[0];


        //======== VALIDANDO COLABORADOR REGISTRADOR ========
        $colaborador_registrador    =   DB::select('select 
                                        c.id,
                                        c.nombre
                                        from colaboradores as c
                                        where c.id = ?',
                                        [Auth::user()->colaborador_id]);

        if(count($colaborador_registrador) === 0){
            Session::flash('lista_orden_compra_error','EL COLABORADOR NO EXISTE EN LA BD!!');
            return back(); 
        }
        $colaborador_registrador    =   $colaborador_registrador[0];

        //=========== VALIDANDO PROVEEDOR DE LA ORDEN DE COMPRA ========
        if(!$orden_compra->banco_nombre){
            Session::flash('lista_orden_compra_error','EL PROVEEDOR NO TIENE UN BANCO ASIGNADO!!!');
            return back(); 
        }
        if(!$orden_compra->proveedor_nro_cuenta){
            Session::flash('lista_orden_compra_error','EL PROVEEDOR NO TIENE UN N° DE CUENTA ASIGNADO!!!');
            return back(); 
        }

        //===== VALIDANDO PROYECTO DE LA ORDEN DE COMPRA =======
        $proyecto   =   DB::select('select 
                        proy.id,
                        proy.nombre,
                        proy.estado
                        from proyectos as proy
                        where proy.id = ?',
                        [$orden_compra->proyecto_id]);

        if(count($proyecto) === 0){
            Session::flash('lista_orden_compra_error','NO EXISTE EL PROYECTO DE LA ORDEN DE COMPRA EN LA BD!!');
            return back(); 
        }

        if($proyecto[0]->estado !== 'PENDIENTE' && $proyecto[0]->estado !== 'EN PROCESO'){
            Session::flash('lista_orden_compra_error','EL PROYECTO DE LA ORDEN DE COMPRA TIENE ESTADO: '.$proyecto[0]->estado);
            return back(); 
        }

        $proyecto   =   $proyecto[0];

        $orden_compra_detalle   =   DB::select('select 
                                    ocd.producto_id,
                                    ocd.cantidad,
                                    ocd.precio_soles,
                                    ocd.precio_dolares,
                                    p.nombre as producto_nombre,
                                    c.descripcion as categoria_nombre,
                                    m.descripcion as marca_nombre,
                                    tgd.descripcion as producto_unidad_medida
                                    from orden_compra_detalle as ocd
                                    inner join productos as p on p.id = ocd.producto_id
                                    inner join marcas as m on m.id = p.marca_id 
                                    inner join categorias as c on c.id = p.categoria_id
                                    inner join tablas_generales_detalles as tgd on tgd.id = p.unidad_medida_id
                                    where ocd.orden_compra_id = ?',[$orden_compra_id]);


        return view('finanzas.lista_ordenes_compra.orden_compra_to_orden_pago',
        compact('orden_compra','colaborador_registrador','proyecto','orden_compra_detalle'));
    }

    /*
   array:15 [ // app\Http\Controllers\Finanzas\ListaOrdenCompraController.php:189
    "_token"                        => "L9jU9dv3QfyoQf262cOSNdDVqrgB1jYXx3vf5UFZ"
    "fecha_registro"                => "2024-10-25"
    "colaborador_registrador"       => "HILMER JULIAN PALOMINO"
    "proveedor"                     => "RUC:20376113443-EUROTUBO S.A.C."
    "documento"                     => "FACTURA"
    "moneda"                        => "PEN"
    "medio_pago"                    => "TRANSFERENCIA"
    "observacion"                   => "observazao"
    "banco"                         => "BCP"
    "nro_cuenta"                    => "4212535247"
    "cci"                           => "25427854542"
    "table_imagenes_pago_length"    => "10"
    "orden_compra_id"               => "1"
    "colaborador_registrador_id"    => "4"
    "lstImagenesPago" => array:2 [
        0 => 
    Illuminate\Http
    \
    UploadedFile {#1645
        -test: false
        -originalName: "icons8-proyecto-96.png"
        -mimeType: "image/png"
        -error: 0
        -originalPath: "icons8-proyecto-96.png"
        #hashName: null
        path: "C:\xampp8.2\tmp"
        filename: "phpF990.tmp"
        basename: "phpF990.tmp"
        pathname: "C:\xampp8.2\tmp\phpF990.tmp"
        extension: "tmp"
        realPath: "
    C:\xampp8.2
    \
    tmp\phpF990.tmp"
        aTime: 2024-10-25 17:43:15
        mTime: 2024-10-25 17:43:15
        cTime: 2024-10-25 17:43:15
        inode: 7318349394661684
        size: 1529
        perms: 0100666
        owner: 0
        group: 0
        type: "file"
        writable: true
        readable: true
        executable: false
        file: true
        dir: false
        link: false
        linkTarget: "C:\xampp8.2\tmp\phpF990.tmp"
        }
        1 => 
    Illuminate\Http
    \
    UploadedFile {#1646
        -test: false
        -originalName: "WhatsApp Image 2024-10-15 at 7.01.10 PM.jpeg"
        -mimeType: "image/jpeg"
        -error: 0
        -originalPath: "WhatsApp Image 2024-10-15 at 7.01.10 PM.jpeg"
        #hashName: null
        path: "C:\xampp8.2\tmp"
        filename: "phpF991.tmp"
        basename: "phpF991.tmp"
        pathname: "C:\xampp8.2\tmp\phpF991.tmp"
        extension: "tmp"
        realPath: "
    C:\xampp8.2
    \
    tmp\phpF991.tmp"
        aTime: 2024-10-25 17:43:15
        mTime: 2024-10-25 17:43:15
        cTime: 2024-10-25 17:43:15
        inode: 3096224744002185
        size: 51099
        perms: 0100666
        owner: 0
        group: 0
        type: "file"
        writable: true
        readable: true
        executable: false
        file: true
        dir: false
        link: false
        linkTarget: "C:\xampp8.2\tmp\phpF991.tmp"
        }
    ]
    ]
    */ 
    public function ordenCompraToOrdenPagoStore(OrdenCompraToOrdenPagoStoreRequest $request){
        
        DB::beginTransaction();

        try {

            $orden_compra   =   ListaOrdenCompraController::ordenCompraToOrdenPagoValidacion($request);

            $proyecto       =       DB::select('select 
                                    proy.id,
                                    proy.nombre,
                                    proy.estado
                                    from proyectos as proy
                                    where proy.id = ?',
                                    [$orden_compra->proyecto_id])[0];
    
            $colaborador_registrador    =   DB::select('select 
                                            c.id,
                                            c.nombre
                                            from colaboradores as c
                                            where c.id = ?',
                                            [$request->get('colaborador_registrador_id')])[0];

            $orden_compra_detalle   =       DB::select('select 
                                            ocd.producto_id,
                                            ocd.cantidad,
                                            ocd.precio_soles,
                                            ocd.precio_dolares,
                                            p.nombre as producto_nombre,
                                            c.descripcion as categoria_nombre,
                                            m.descripcion as marca_nombre,
                                            tgd.descripcion as producto_unidad_medida
                                            from orden_compra_detalle as ocd
                                            inner join productos as p on p.id = ocd.producto_id
                                            inner join marcas as m on m.id = p.marca_id 
                                            inner join categorias as c on c.id = p.categoria_id
                                            inner join tablas_generales_detalles as tgd on tgd.id = p.unidad_medida_id
                                            where ocd.orden_compra_id = ?',
                                            [$request->get('orden_compra_id')]);
            
            $orden_pago                                 =   new OrdenPago();
            $orden_pago->orden_compra_id                =   $request->get('orden_compra_id');
            $orden_pago->proveedor_id                   =   $orden_compra->proveedor_id;
            $orden_pago->proveedor_nombre               =   $orden_compra->proveedor_nombre;
            $orden_pago->proveedor_tipo_documento       =   $orden_compra->proveedor_tipo_documento;
            $orden_pago->proveedor_nro_documento        =   $orden_compra->proveedor_nro_documento; 
            $orden_pago->banco_id                       =   $orden_compra->banco_id;
            $orden_pago->banco_nombre                   =   $orden_compra->banco_nombre;
            $orden_pago->nro_cuenta                     =   $orden_compra->proveedor_nro_cuenta;
            $orden_pago->cci                            =   $orden_compra->proveedor_cci;
            $orden_pago->nro_cuenta_detraccion          =   $orden_compra->proveedor_nro_cuenta_detraccion;
            $orden_pago->proyecto_id                    =   $proyecto->id;
            $orden_pago->proyecto_nombre                =   $proyecto->nombre;
            $orden_pago->colaborador_registrador_id     =   $request->get('colaborador_registrador_id');
            $orden_pago->colaborador_registrador_nombre =   $colaborador_registrador->nombre;
            $orden_pago->documento                      =   $orden_compra->documento;
            $orden_pago->medio_pago                     =   $request->get('medio_pago');
            $orden_pago->moneda                         =   $orden_compra->moneda;
            $orden_pago->observacion                    =   $request->get('observacion');
            $orden_pago->subtotal                       =   $orden_compra->subtotal;
            $orden_pago->monto_igv                      =   $orden_compra->monto_igv;
            $orden_pago->total                          =   $orden_compra->total;
            $orden_pago->save();

            foreach ($orden_compra_detalle as $producto) {
                $orden_pago_detalle                 =   new OrdenPagoDetalle();
                $orden_pago_detalle->orden_pago_id  =   $orden_pago->id;
                $orden_pago_detalle->producto_id    =   $producto->producto_id;
                $orden_pago_detalle->cantidad       =   $producto->cantidad;
                $orden_pago_detalle->precio_soles   =   $producto->precio_soles;
                $orden_pago_detalle->precio_dolares =   $producto->precio_dolares;
                $orden_pago_detalle->save();
            }

            //========== ACTUALIZANDO ESTADO DE LA ORDEN DE COMPRA =========
            DB::table('ordenes_compra')
            ->where('id', $request->get('orden_compra_id'))
            ->update([
                'estado'                =>  'CON ORDEN PAGO',
                'orden_pago_id'         =>  $orden_pago->id,
                'updated_at'            =>  now() 
            ]);

            //========= ACTUALIZANDO ESTADO DE LA COTIZACIÓN DE LA ORDEN DE COMPRA ======
            DB::table('cotizacion_compra')
            ->where('orden_compra_id', $request->get('orden_compra_id'))
            ->update([
                'estado'                =>  'CON ORDEN PAGO',
                'orden_pago_id'         =>  $orden_pago->id,
                'updated_at'            =>  now() 
            ]);

            //======== BUSCANDO SI LA COTIZACIÓN COMPRA PARTIÓ DESDE UN REQUERIMIENTO =======
            DB::table('requerimientos')
            ->where('orden_compra_id', $request->get('orden_compra_id'))
            ->update([
                'estado'                =>  'CON ORDEN PAGO',
                'orden_pago_id'         =>  $orden_pago->id,
                'updated_at'            =>  now() 
            ]);

            //========= GUARDANDO IMAGENES DE PAGO =======
            if($request->has('lstImagenesPago')){
                $lstImagenesPago = $request->file('lstImagenesPago');
                
                $carpeta_destino = public_path('img/ordenes_pago/pagos/');

                if (!File::exists($carpeta_destino)) {
                    File::makeDirectory($carpeta_destino, 0755, true);
                }

                $indice = 0;
                foreach ($lstImagenesPago as $imagen) {
                    $indice++;
                    $extension = strtolower($imagen->getClientOriginalExtension());
        
                    if (in_array($extension, ['png', 'jpg', 'jpeg'])) {

                        $nombre_personalizado = 'op_'.$orden_pago->id.'_'.$indice.'.' . $extension;

                        $imagen->move($carpeta_destino, $nombre_personalizado);

                        $orden_pago_img                 =   new OrdenPagoImg();
                        $orden_pago_img->orden_pago_id  =   $orden_pago->id;
                        $orden_pago_img->img_nombre     =   $nombre_personalizado;
                        $orden_pago_img->img_ruta       =   'img/ordenes_pago/pagos/'.$nombre_personalizado;
                        $orden_pago_img->save();

                    }
                    // } else {
                    //     throw new Exception('Tipo de archivo no válido: ' . $imagen->getClientOriginalName());
                    // }

                }
            }
            
            DB::commit();
            return response()->json(['success'=>true,'message'=>'ORDEN DE PAGO GENERADA!!']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
      

    }


    public static function ordenCompraToOrdenPagoValidacion($request){

        if(!$request->get('orden_compra_id',null)){
            throw new Exception("NO EXISTE EL ID DE LA ORDEN DE COMPRA EN LA PETICIÓN!!!");
        }

        if(!$request->get('colaborador_registrador_id',null)){
            throw new Exception("NO EXISTE EL ID DEL COLABORADOR REGISTRADOR EN LA PETICIÓN!!!");
        }

        //=========== VALIDANDO ORDEN DE COMPRA ========
        $orden_compra   =   DB::select('select 
                            oc.id,
                            oc.estado,
                            oc.proveedor_id,
                            prov.nombre as proveedor_nombre,
                            prov.nro_documento as proveedor_nro_documento,
                            b.id as banco_id,
                            b.nombre as banco_nombre,
                            prov.nro_cuenta as proveedor_nro_cuenta,
                            prov.cci as proveedor_cci, 
                            prov.nro_cuenta_detraccion as proveedor_nro_cuenta_detraccion,
                            td.descripcion as proveedor_tipo_documento,
                            oc.documento,
                            oc.proyecto_id,
                            oc.moneda,
                            oc.subtotal,
                            oc.monto_igv,
                            oc.total
                            from ordenes_compra as oc
                            inner join proveedores as prov on prov.id = oc.proveedor_id
                            left join bancos as b on b.id = prov.banco_id
                            inner join tipos_documento as td on td.id = prov.tipo_documento_id
                            where 
                            oc.id = ?',
                            [$request->get('orden_compra_id')]);

        if(count($orden_compra) === 0){
            throw new Exception("NO EXISTE LA ORDEN DE COMPRA EN LA BD!!!");
        }
        if($orden_compra[0]->estado !== 'PENDIENTE'){
            throw new Exception("LA ORDEN DE COMPRA YA SE ENCUENTRA CON ESTADO: ".$orden_compra[0]->estado);
        }

        //=========== VALIDANDO PROVEEDOR DE LA ORDEN DE COMPRA ========
        if(!$orden_compra[0]->banco_nombre){
            Session::flash('lista_orden_compra_error','EL PROVEEDOR NO TIENE UN BANCO ASIGNADO!!!');
            return back(); 
        }
        if(!$orden_compra[0]->proveedor_nro_cuenta){
            Session::flash('lista_orden_compra_error','EL PROVEEDOR NO TIENE UN N° DE CUENTA ASIGNADO!!!');
            return back(); 
        }

        //===== VALIDANDO PROYECTO DE LA ORDEN DE COMPRA =======
        $proyecto   =   DB::select('select 
                        proy.id,
                        proy.nombre,
                        proy.estado
                        from proyectos as proy
                        where proy.id = ?',
                        [$orden_compra[0]->proyecto_id]);

        if(count($proyecto) === 0){
            throw new Exception("NO EXISTE EL PROYECTO EN LA BD!!!");
        }

        if($proyecto[0]->estado !== 'PENDIENTE' && $proyecto[0]->estado !== 'EN PROCESO'){
            throw new Exception('EL PROYECTO DE LA ORDEN DE COMPRA TIENE ESTADO: '.$proyecto[0]->estado);
        }

        //======= VALIDANDO COLABORADOR REGISTRADOR =========
        $colaborador_registrador    =   DB::select('select 
                                        c.id,
                                        c.nombre,
                                        c.estado
                                        from colaboradores as c
                                        where c.id = ?',
                                        [$request->get('colaborador_registrador_id')]);

        if(count($colaborador_registrador) === 0){
            throw new Exception("NO EXISTE EL COLABORADOR REGISTRADOR EN LA BD!!!");
        }

        if($colaborador_registrador[0]->estado !== 'ACTIVO'){
            throw new Exception("EL COLABORADOR REGISTRADOR NO ESTÁ ACTIVO!!!");
        }

        return $orden_compra[0];

    }

}
