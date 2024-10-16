<?php

namespace App\Http\Controllers\Compras;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Kardex\KardexController;
use App\Http\Requests\Compras\OrdenCompra\OrdenCompraUpdateRequest;
use App\Models\Compras\CotizacionCompra;
use App\Models\Compras\OrdenCompra;
use App\Models\Compras\OrdenCompraDetalle;
use App\Models\Compras\RegistroCompra;
use App\Models\Compras\RegistroCompraDetalle;
use App\Models\Registros\AlmacenProducto;
use App\Models\Registros\Categoria;
use App\Models\Registros\Marca;
use App\Models\Registros\Proyecto;
use App\Models\Requerimientos\Requerimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Dompdf\Dompdf;
use Dompdf\Options;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Style\Supervisor;

class OrdenCompraController extends Controller
{
    public function index(){
        $proyectos  =   DB::select('select pr.id,pr.nombre 
                        from proyectos as pr
                        where pr.estado <> "ANULADO"');

        return view('compras.orden_compra.index',compact('proyectos'));
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
                                    DB::raw('CASE 
                                    WHEN m.tipo = "CONTADO" THEN m.tipo 
                                    WHEN m.tipo = "CREDITO" THEN CONCAT(m.tipo, " - ", m.nro_dias," ","DÍAS") 
                                    END as modalidad_pago'),
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
                                ->get();

        return DataTables::of($ordenes_compra)
                ->make(true);
    }


    public function create(){
        return view('compras.orden_compra.create');

    }

    public function edit($id){
        
        $orden_compra   =   DB::select('select 
                            oc.*,
                            c.nombre as persona_contacto_nombre,
                            pr.nombre as proveedor_nombre,
                            td.descripcion as tipo_documento_nombre,
                            pr.nro_documento,
                            m.tipo as modalidad_pago_nombre,
                            m.nro_dias as modalidad_pago_nro_dias,
                            proy.nombre as proyecto_nombre
                            from ordenes_compra as oc
                            inner join colaboradores as c on c.id = oc.persona_contacto_id
                            inner join proveedores as pr on pr.id = oc.proveedor_id
                            inner join modalidades_pago as m on m.id = oc.modalidad_pago_id
                            inner join tipos_documento as td on td.id = pr.tipo_documento_id
                            inner join proyectos as proy on proy.id = oc.proyecto_id
                            where oc.id = ?',[$id])[0];

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
                                    where ocd.orden_compra_id = ?',[$id]);

        $proyecto_personal  =   DB::select('select
                                pp.colaborador_id,
                                CONCAT(co.nombre, " - CEL:", co.telefono) AS persona_contacto
                                from proyectos as pr
                                inner join proyecto_personal as pp on pp.proyecto_id = pr.id
                                inner join colaboradores AS co ON co.id = pp.colaborador_id
                                where pr.id = ? 
                                and (pr.estado = "PENDIENTE" or pr.estado = "EN PROCESO")',
                                [$orden_compra->proyecto_id]);

        $supervisor         =   DB::select('select 
                                co.id as supervisor_id,
                                CONCAT(co.nombre, " - CEL:", co.telefono) AS supervisor_contacto
                                from proyectos as pr
                                left join colaboradores as co on co.id = pr.supervisor_id
                                where pr.id = ?',[$orden_compra->proyecto_id])[0];

        $proyecto           =   DB::select('select * from proyectos as pr
                                where pr.id = ?',[$orden_compra->proyecto_id])[0];

        $categorias         =   Categoria::where('estado','ACTIVO')->get();

        $marcas             =   Marca::where('estado','ACTIVO')->get();
                            
        $proveedores        =   DB::select('select 
                                pr.id,
                                pr.nombre,
                                pr.nro_documento,
                                td.descripcion as tipo_documento_descripcion
                                from proveedores as pr
                                inner join tipos_documento as td on td.id = pr.tipo_documento_id
                                where pr.estado = "ACTIVO"');
                            
        $tipos_documento    =   DB::select('select * 
                                from tipos_documento as td
                                where td.estado = "ACTIVO"
                                and td.id <> "3" ');
                            
        $modalidades_pago   =   DB::select('select * from modalidades_pago as m
                                where m.estado = "ACTIVO"');
                            
        $igv                =   DB::select('select e.igv from empresas as e')[0]->igv;
                    

        return view('compras.orden_compra.edit',
        compact('orden_compra','orden_compra_detalle','categorias',
        'marcas','proveedores','tipos_documento','modalidades_pago',
        'proyecto_personal','igv','proyecto','supervisor'));
  
    }

    public function goToRegistroCompra($id){
        
        $categorias =   DB::select('select c.id,c.descripcion 
                        from categorias as c
                        where c.estado = "ACTIVO"');

        $marcas =   DB::select('select m.id,m.descripcion 
                        from marcas as m
                        where m.estado = "ACTIVO"');

        $tipos_documento    =   DB::select('select * 
                                from tipos_documento as td
                                where td.estado = "ACTIVO"
                                and td.id <> "3" ');

        $proveedores    =   DB::select('select 
                                pr.id,
                                pr.nombre,
                                pr.nro_documento,
                                td.descripcion as tipo_documento_descripcion
                                from proveedores as pr
                                inner join tipos_documento as td on td.id = pr.tipo_documento_id
                                where pr.estado = "ACTIVO"
                                and pr.id != 1');

        $orden_compra   =   DB::select('select 
                                oc.*,
                                c.nombre as persona_contacto_nombre,
                                pr.nombre as proveedor_nombre,
                                td.descripcion as tipo_documento_nombre,
                                pr.nro_documento,
                                m.tipo as modalidad_pago_nombre,
                                m.nro_dias as modalidad_pago_nro_dias,
                                proy.nombre as proyecto_nombre
                                from ordenes_compra as oc
                                inner join colaboradores as c on c.id = oc.persona_contacto_id
                                inner join proveedores as pr on pr.id = oc.proveedor_id
                                inner join modalidades_pago as m on m.id = oc.modalidad_pago_id
                                inner join tipos_documento as td on td.id = pr.tipo_documento_id
                                inner join proyectos as proy on proy.id = oc.proyecto_id
                                where oc.id = ?',[$id])[0];
    
        $orden_compra_detalle   =   DB::select('select 
                                        ocd.producto_id,
                                        ocd.cantidad,
                                        ocd.precio_soles,
                                        ocd.precio_dolares,
                                        ocd.precio_mas_igv_soles,
                                        ocd.precio_mas_igv_dolares,
                                        p.nombre as producto_nombre,
                                        c.descripcion as categoria_nombre,
                                        m.descripcion as marca_nombre,
                                        tgd.descripcion as producto_unidad_medida
                                        from orden_compra_detalle as ocd
                                        inner join productos as p on p.id = ocd.producto_id
                                        inner join marcas as m on m.id = p.marca_id 
                                        inner join categorias as c on c.id = p.categoria_id
                                        inner join tablas_generales_detalles as tgd on tgd.id = p.unidad_medida_id
                                        where ocd.orden_compra_id = ?',[$id]);

        $almacenes  =   DB::select('select 
                        a.id,
                        a.descripcion 
                        from almacenes as a
                        where a.estado = "ACTIVO"
                        and a.proyecto_id = ? or a.id = 1',
                        [$orden_compra->proyecto_id]);

        

        return view('compras.orden_compra.orden_compra_to_registro_compra',
        compact('categorias','marcas','almacenes','tipos_documento','proveedores',
        'orden_compra','orden_compra_detalle'));
    }


    /*
        array:12 [ // app\Http\Controllers\Compras\OrdenCompraController.php:239
            "_token"                => "f9lwYjwjxcyChsx4qQFtJKjwQsBYF20EUPdJhjcO"
            "fecha_emision"         => "2024-10-16"  --REQUEST
            "fecha_entrega"         => "2024-10-16"  --REQUEST
            "proveedor"             => "RUC:20419387658-CEMENTOS PACASMAYO S.A.A."
            "observacion"           => "dasdasd"     --REQUEST
            "moneda"                => "PEN"   
            "tipo_cambio"           => "3.7700"
            "tipo_doc"              => "FACTURA"
            "serie"                 => "B001"       --REQUEST
            "numero"                => "12"         --REQUEST
            "table_orden_compra_to_registro_compra_length"  => "10"
            "almacen"                                       => "1"
            lstCompra" => "[{"producto_id":1,"producto_nombre":"CEMENTO ROJO MOCHICA X 42.5 KG","categoria_nombre":"CEMENTO","marca_nombre":"MOCHICA","almacen_nombre":"CENTRAL","almacen_id":1,"producto_unidad_medida":"UNIDAD","precio":"29.50","cantidad":"2.00","total":"59.00"},{"producto_id":2,"producto_nombre":"VARILLAS DE 1/2\"  SIDER","categoria_nombre":"ACERO","marca_nombre":"SIDER","almacen_nombre":"CENTRAL","almacen_id":1,"producto_unidad_medida":"UNIDAD","precio":"1.00","cantidad":"500.00","total":"500.00"},{"producto_id":3,"producto_nombre":"VARILLAS DE 3/8\" SIDER","categoria_nombre":"ACERO","marca_nombre":"SIDER","almacen_nombre":"CENTRAL","almacen_id":1,"producto_unidad_medida":"UNIDAD","precio":"1.00","cantidad":"400.00","total":"400.00"},{"producto_id":4,"producto_nombre":"VARILLAS DE 5/8\" SIDER","categoria_nombre":"ACERO","marca_nombre":"SIDER","almacen_nombre":"CENTRAL","almacen_id":1,"producto_unidad_medida":"UNIDAD","precio":"1.00","cantidad":"200.00","total":"200.00"}]"
            "orden_compra_id"       => "3"          --VALIDACIÓN COMPLEJA
        ]
    */
    public function ordenCompraToRegistroCompra(Request $request){

        DB::beginTransaction();

        try {
            $lstCompra              =   json_decode($request->get('lstCompra'));

            $orden_compra           =   OrdenCompra::find($request->get('orden_compra_id'));
            $orden_compra_detalle   =   DB::select('select * from orden_compra_detalle as ocd
                                        where ocd.orden_compra_id = ?',[$orden_compra->id]);
    
            $registro_compra                            =   new RegistroCompra();
            $registro_compra->colaborador_registro_id   =   Auth::user()->colaborador_id;
            $registro_compra->proveedor_id              =   $orden_compra->proveedor_id;
            $registro_compra->fecha_emision             =   $request->get('fecha_emision');
            $registro_compra->fecha_entrega             =   $request->get('fecha_entrega');
            $registro_compra->serie                     =   mb_strtoupper($request->get('serie'), 'UTF-8');
            $registro_compra->correlativo               =   $request->get('numero');
            $registro_compra->moneda                    =   $orden_compra->moneda;
            $registro_compra->tipo_cambio               =   $orden_compra->tipo_cambio;
            $registro_compra->precios_igv               =   $orden_compra->precios_igv;
            $registro_compra->observacion               =   $request->get('observacion');
            $registro_compra->igv                       =   $orden_compra->igv;
            $registro_compra->subtotal                  =   $orden_compra->subtotal;
            $registro_compra->monto_igv                 =   $orden_compra->monto_igv;
            $registro_compra->total                     =   $orden_compra->total;
            $registro_compra->subtotal_soles            =   $orden_compra->subtotal_soles;
            $registro_compra->monto_igv_soles           =   $orden_compra->monto_igv_soles;
            $registro_compra->total_soles               =   $orden_compra->total_soles;
            $registro_compra->save();
    
    
            //======= GUARDANDO DETALLE ========
            foreach ($orden_compra_detalle as $item) {
    
                //======== OBTENIENDO STOCK ANTES DE LA COMPRA =========
                $stock_previo           =   0;
                $stock_posterior        =   0;
    
                //====== OBTENER EL ALMACEN ID ESTABLECIDO EN LA VISTA ====
                $producto_id_a_buscar   =   $item->producto_id;
                $producto_encontrado = array_filter($lstCompra, function($item) use ($producto_id_a_buscar) {
                    return $item->producto_id === $producto_id_a_buscar;
                });
                $producto_encontrado = reset($producto_encontrado); 
                
                if(!$producto_encontrado){
                    throw new Exception("EL DETALLE DE LA ORDEN DE COMPRA NO COINCIDE CON LA BD");
                }

                if(!$producto_encontrado->almacen_id){
                    throw new Exception("FALTA INDICAR EL ALMACÉN PARA EL PRODUCTO ".$producto_encontrado->producto_nombre);
                }
    
                $compra_detalle                         =   new RegistroCompraDetalle();
                $compra_detalle->registro_compra_id     =   $registro_compra->id;
                $compra_detalle->almacen_id             =   $producto_encontrado->almacen_id;
                $compra_detalle->producto_id            =   $item->producto_id;
                $compra_detalle->precio_soles           =   $item->precio_soles;
                $compra_detalle->cantidad               =   $item->cantidad;
                $compra_detalle->precio_soles           =   $item->precio_soles;
                $compra_detalle->precio_dolares         =   $item->precio_dolares;
                $compra_detalle->precio_mas_igv_soles   =   $item->precio_mas_igv_soles;
                $compra_detalle->precio_mas_igv_dolares =   $item->precio_mas_igv_dolares;
                $compra_detalle->save();
    
                //======= INSERTANDO STOCK ========
                $existe_almacen_producto =   DB::table('almacen_productos')
                                            ->where('almacen_id', $producto_encontrado->almacen_id)
                                            ->where('producto_id', $item->producto_id)
                                            ->exists();
    
                if(!$existe_almacen_producto){
                    $almacen_producto               =   new AlmacenProducto();
                    $almacen_producto->almacen_id   =   $producto_encontrado->almacen_id;
                    $almacen_producto->producto_id  =   $item->producto_id;
                    $almacen_producto->stock        =   $item->cantidad;
                    $almacen_producto->save();
                    $stock_posterior                =   $almacen_producto->stock;
                }else{
    
                    $almacen_producto_previo = DB::table('almacen_productos')
                    ->where('almacen_id', $producto_encontrado->almacen_id)
                    ->where('producto_id', $item->producto_id)
                    ->value('stock');
    
                    $stock_previo = $almacen_producto_previo;
    
                    DB::table('almacen_productos')
                    ->where('almacen_id', $producto_encontrado->almacen_id)
                    ->where('producto_id', $item->producto_id)
                    ->update([
                    'stock' => DB::raw('stock + ' . $item->cantidad),
                    'updated_at' => Carbon::now(),
                    ]);
    
                    $almacen_producto_posterior = DB::table('almacen_productos')
                                        ->where('almacen_id', $producto_encontrado->almacen_id)
                                        ->where('producto_id', $item->producto_id)
                                        ->value('stock');
    
                    $stock_posterior = $almacen_producto_posterior;
                }

                $item->almacen_id =   $producto_encontrado->almacen_id;
    
                KardexController::storeCompra($item,$registro_compra->id,$stock_previo,$stock_posterior);
    
            }
            
            $orden_compra->estado   =   'FACTURADO';
            $orden_compra->save();

            $cotizacion_compra      =   DB::select('select 
                                        cc.id
                                        from cotizacion_compra as cc
                                        where cc.orden_compra_id = ?'
                                        ,[$orden_compra->id]);

            if(count($cotizacion_compra) !== 0){
                $cotizacion_compra_update           =   CotizacionCompra::find($cotizacion_compra[0]->id);
                $cotizacion_compra_update->estado   =   'FACTURADO';   
                $cotizacion_compra_update->update();
            }

            $requerimiento      =   DB::select('select 
                                    r.id
                                    from requerimientos as r
                                    where r.orden_compra_id = ?'
                                    ,[$orden_compra->id]); 

            if(count($requerimiento) !== 0){
                $requerimiento_update           =   Requerimiento::find($requerimiento[0]->id);
                $requerimiento_update->estado   =   'FACTURADO';   
                $requerimiento_update->update();
            }

            DB::commit();
            return response()->json(['success'=>true,'message'=>'REGISTRO DE COMPRA GENERADO']);
    
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage(),'line'=>$th->getLine()]);
        }
       
    }


    /*
    array:20 [ // app\Http\Controllers\Compras\OrdenCompraController.php:153
        "_token"                    => "JdyhDaaj0jitzyjdAmCOH8W69P5l6g2h4R18pbly"
        "fecha_entrega"             => "2024-10-09"                 --VALIDACION REQUEST
        "igv"                       => "18.00"                      --VALIDACION REQUEST
        "valor_igv"                 => "18.00"                      --VALIDACION REQUEST
        "moneda"                    => "PEN"                        --VALIDACION REQUEST
        "tipo_cambio"               => "3.7440"                     --VALIDACION REQUEST
        "terminos_entrega"          => "PUESTO EN OBRA"             --VALIDACION REQUEST
        "proveedor"                 => "1"                          --VALIDACION REQUEST
        "direccion"                 => "AV LAS MAGNOLIAS 321"       
        "proyecto"                  => "PROYECTO HUERTA GRANDE"     
        "modalidad_pago"            => "2"                          --VALIDACION REQUEST
        "tipo_doc"                  => "FACTURA"                    --VALIDACION REQUEST
        "persona_contacto"          => "5"                          --VALIDACION REQUEST Y COMPLEJA
        "observacion"               => "LO MÁS RAPIDO"              --VALIDACION REQUEST
        "producto"                  => null
        "unidad"                    => null
        "precio"                    => null
        "cantidad"                  => null
        "table_orden_compra_detalle_length" => "10"
        "lstOrdenCompra"                    => "[{"cantidad":"20.00","categoria_nombre":"CEMENTO","marca_nombre":"MOCHICA","producto_id":1,"producto_nombre":"CEMENTO ROJO MOCHICA X 45 KG","producto_unidad_medida":"UNIDAD","precio":"29.50","total":590},{"cantidad":"10.00","categoria_nombre":"TUBO","marca_nombre":"EUROTUBO","producto_id":2,"producto_nombre":"TUBO HIDRÁULICO","producto_unidad_medida":"UNIDAD","precio":"1.00","total":10}]"
    ]
    */ 
    public function update($id,OrdenCompraUpdateRequest $request){
        DB::beginTransaction();
        try {
            
            OrdenCompraController::validacionOrdenCompraUpdate($id,$request);
            $lstOrdenCompraDetalle  =   json_decode($request->get('lstOrdenCompra'));
            OrdenCompraController::validarLstOrdenCompra($lstOrdenCompraDetalle);

            $montos     =   CotizacionCompraController::calcularMontos($lstOrdenCompraDetalle,$request->get('igv',null),$request->get('valor_igv'));

            $orden_compra                               =   OrdenCompra::find($id);
            //$orden_compra->colaborador_registrador_id =   Auth::user()->colaborador_id;
            $orden_compra->proveedor_id                 =   $request->get('proveedor');
            $orden_compra->modalidad_pago_id            =   $request->get('modalidad_pago');
            //$orden_compra->proyecto_id                =   $requerimiento->proyecto_id;
            $orden_compra->documento                    =   $request->get('tipo_doc');   
            $orden_compra->direccion_obra               =   $request->get('direccion');   
            $orden_compra->observacion                  =   $request->get('observacion');  
            $orden_compra->persona_contacto_id          =   $request->get('persona_contacto');  
            $orden_compra->fecha_entrega                =   $request->get('fecha_entrega');  
            $orden_compra->terminos_entrega             =   $request->get('terminos_entrega');
            $orden_compra->moneda                       =   $request->get('moneda');
            $orden_compra->tipo_cambio                  =   $request->get('tipo_cambio');
            $orden_compra->precios_igv                  =   $request->has('igv')?1:0;
            $orden_compra->observacion                  =   $request->get('observacion');
            $orden_compra->igv                          =   $request->get('valor_igv');
            $orden_compra->subtotal                     =   $montos->subtotal;
            $orden_compra->monto_igv                    =   $montos->monto_igv;
            $orden_compra->total                        =   $montos->total;  
            $orden_compra->primer_producto_id           =   $lstOrdenCompraDetalle[0]->producto_id;

            $moneda                                  =   $request->get('moneda');
            if($moneda === 'PEN'){
                $orden_compra->subtotal_soles        =   $montos->subtotal;
                $orden_compra->monto_igv_soles       =   $montos->monto_igv;
                $orden_compra->total_soles           =   $montos->total;
            }

            if($moneda  === "USD"){
                $orden_compra->subtotal_soles        =   $montos->subtotal  * (float)$request->get('tipo_cambio');
                $orden_compra->monto_igv_soles       =   $montos->monto_igv * (float)$request->get('tipo_cambio');
                $orden_compra->total_soles           =   $montos->total * (float)$request->get('tipo_cambio');
            }

            $orden_compra->update();
            
            DB::delete('DELETE FROM orden_compra_detalle WHERE orden_compra_id = ?', [$id]);

            foreach ($lstOrdenCompraDetalle as $item) {
                $orden_compra_detalle                          =   new OrdenCompraDetalle();
                $orden_compra_detalle->orden_compra_id         =   $orden_compra->id;
                $orden_compra_detalle->producto_id             =   $item->producto_id;
                $orden_compra_detalle->cantidad                =   $item->cantidad;
                $orden_compra_detalle->precio_soles            =   $item->precio;

                if($moneda == 'USD')
                {
                    $orden_compra_detalle->precio_soles   =   (float) $item->precio * (float) $request->get('tipo_cambio');
                    $orden_compra_detalle->precio_dolares =   (float) $item->precio;
                }

                if($moneda == 'PEN')
                {
                    $orden_compra_detalle->precio_soles   =   (float) $item->precio;
                    $orden_compra_detalle->precio_dolares =   (float) $item->precio/$request->get('tipo_cambio');
                }

                if($request->has('igv')){
                    $orden_compra_detalle->precio_mas_igv_soles   =   $item->precio;
                    $orden_compra_detalle->precio_mas_igv_dolares =   $orden_compra_detalle->precio_dolares;
                }else{
                    $orden_compra_detalle->precio_mas_igv_soles   =   $item->precio * ((100+$request->get('igv'))/100);
                    $orden_compra_detalle->precio_mas_igv_dolares =   $orden_compra_detalle->precio_dolares * ((100+$request->get('igv'))/100);
                }

                $orden_compra_detalle->save();
            }

            DB::commit();

            return response()->json(['success'=>true,'message'=>"ORDEN DE COMPRA ACTUALIZADA!!"]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,
            'message'=>$th->getMessage(),
            'line' =>$th->getLine()]);
        }
    }

    public static function calcularMontos($lstCompra,$precios_con_igv,$igv){
        $subtotal   =   0;
        $monto_igv  =   0;
        $total      =   0;
        $valor_igv  =   $igv;

        if($precios_con_igv){
            foreach ($lstCompra as $item) {
                $total  +=  (float)$item->total;
            }
            $subtotal    =   $total/((100 + (float)$valor_igv)/100);
            $monto_igv   =   $total - $subtotal;
        }else{
            //======= PRECIOS SIN IGV =======
            foreach ($lstCompra as $item) {
                $subtotal  +=  (float)$item->total;
            }

            $monto_igv   =   ((float)$valor_igv/100)*$subtotal;
            $total       =   $subtotal + $monto_igv;
        }

        return (object)['subtotal'=>$subtotal,'monto_igv'=>$monto_igv,'total'=>$total];
    }

    public static function validacionOrdenCompraUpdate($id,$request){

        //====== VALIDANDO ORDEN DE COMPRA ======
        $orden_compra   =   OrdenCompra::find($id);
        if(!$orden_compra){
            throw new Exception("NO EXISTE LA ORDEN DE COMPRA EN LA BASE DE DATOS");
        }

        //======== VALIDANDO ESTADO DE LA ORDEN DE COMPRA ==========
        if($orden_compra->estado === 'ANULADO'){
            throw new Exception("LA ORDEN DE COMPRA ESTÁ ANULADA!!!");
        }
        if($orden_compra->estado === 'FACTURADO'){
            throw new Exception("LA ORDEN DE COMPRA YA FUE FACTURADA!!!");
        }

        //======= VALIDANDO PERSONA DE CONTACTO ======
         //======== VALIDANDO LA PERSONA DE CONTACTO ========
         $persona_contacto_id    =   $request->get('persona_contacto');
         $proyecto_id            =   $orden_compra->proyecto_id;
 
         //======== COMPROBANDO SI LA PERSONA DE CONTACTO ES EL SUPERVISOR DEL PROYECTO =====
         $proyecto               =   Proyecto::find($proyecto_id);
 
         //======= EN CASO NO SEA EL SUPERVISOR DEL PROYECTO =======
         //======= VERIFICAR SI ES UN PERSONAL DEL PROYECTO =======
         if($proyecto->supervisor_id != $persona_contacto_id){
             $proyecto_personal      =   DB::select('select *
                                         from proyecto_personal as pp
                                         where pp.proyecto_id = ?
                                         and pp.colaborador_id = ?',
                                         [$proyecto_id,$persona_contacto_id]);
 
            if(count($proyecto_personal) === 0){
                throw new Exception("LA PERSONA DE CONTACTO NO ES SUPERVISOR NI FORMA PARTE DEL PERSONAL DEL PROYECTO");
            }  
        } 

    }


    public static function validarLstOrdenCompra($lstOrdenCompra){

        if(count($lstOrdenCompra) === 0){
            throw new Exception("EL DETALLE DE LA ORDEN DE COMPRA ESTÁ VACÍO!!!");
        }

        foreach ($lstOrdenCompra as $item) {
            $existe =   DB::table('productos')
                        ->where('id', $item->producto_id)
                        ->exists();

            if(!$existe){
                throw new Exception("EL PRODUCTO ".$item->producto_nombre."NO EXISTE EN LA BD");
            }
        }

    }



    /*
        {#1517 ▼ // app\Http\Controllers\Compras\OrdenCompraController.php:74
        +"id": 3
        +"proveedor_id": 1
        +"modalidad_pago_id": 2
        +"proyecto_id": 2
        +"documento": "FACTURA"
        +"direccion_obra": "AV LAS MAGNOLIAS 321"
        +"observacion": null
        +"persona_contacto_id": 2
        +"fecha_entrega": "2024-10-09"
        +"terminos_entrega": "PUESTO EN OBRA"
        +"moneda": "PEN"
        +"tipo_cambio": "3.7440"
        +"precios_igv": 1
        +"igv": "18.0000"
        +"subtotal": "508.4746"
        +"monto_igv": "91.5254"
        +"total": "600.0000"
        +"subtotal_soles": "508.4746"
        +"monto_igv_soles": "91.5254"
        +"total_soles": "600.0000"
        +"estado": "PENDIENTE"
        +"created_at": "2024-10-09 22:14:11"
        +"updated_at": "2024-10-09 22:14:11"
        +"colaborador_nombre": "LUIS DANIEL ALVA LUJAN"
        +"proveedor_nombre": "PROVEEDORES VARIOS"
        +"tipo": "CONTADO"
        +"nro_dias": 0
        }
    */ 
    public function pdf($id){
        $empresa    =   DB::select('select * from empresas as e
                        where e.id = 1')[0];

        $orden_compra   =   DB::select('select 
                            oc.*,
                            c.nombre as persona_contacto_nombre,
                            pr.nombre as proveedor_nombre,
                            td.descripcion as tipo_documento_nombre,
                            pr.nro_documento,
                            m.tipo as modalidad_pago_nombre,
                            m.nro_dias as modalidad_pago_nro_dias,
                            proy.nombre as proyecto_nombre
                            from ordenes_compra as oc
                            inner join colaboradores as c on c.id = oc.persona_contacto_id
                            inner join proveedores as pr on pr.id = oc.proveedor_id
                            inner join modalidades_pago as m on m.id = oc.modalidad_pago_id
                            inner join tipos_documento as td on td.id = pr.tipo_documento_id
                            inner join proyectos as proy on proy.id = oc.proyecto_id
                            where oc.id = ?',[$id])[0];

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
                                    where ocd.orden_compra_id = ?',[$id]);

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
        $html = view('compras.orden_compra.pdf.pdf',
        compact('empresa','orden_compra','orden_compra_detalle','fecha_impresion'))
        ->render();

        // Cargar el HTML en DOMPDF
        $dompdf->loadHtml($html);

        // Opcional: Configurar el tamaño de papel y la orientación
        $dompdf->setPaper('A4', 'portrait'); // O 'landscape'

        // Renderizar el PDF
        $dompdf->render();

        // Visualizar el PDF en una nueva ventana en lugar de descargarlo
        return $dompdf->stream('orden_compra_'.$orden_compra->id.'.pdf', ['Attachment' => false]);
    }


    /*
    array:1 [ // app\Http\Controllers\Compras\OrdenCompraController.php:479
        0 => {#1376
            +"id": 2
            +"colaborador_registrador_id": 3
            +"proveedor_id": 1
            +"modalidad_pago_id": 1
            +"proyecto_id": 2
            +"documento": "FACTURA"
            +"direccion_obra": "AV LAS MAGNOLIAS 342"
            +"observacion": null
            +"persona_contacto_id": 2
            +"fecha_entrega": "2024-10-14"
            +"terminos_entrega": "PUESTO EN OBRA"
            +"moneda": "PEN"
            +"tipo_cambio": "3.7590"
            +"precios_igv": 0
            +"igv": "18.0000"
            +"subtotal": "649.0000"
            +"monto_igv": "116.8200"
            +"total": "765.8200"
            +"subtotal_soles": "649.0000"
            +"monto_igv_soles": "116.8200"
            +"total_soles": "765.8200"
            +"estado": "PENDIENTE"
            +"created_at": "2024-10-14 22:21:42"
            +"updated_at": "2024-10-14 22:21:42"
            +"persona_contacto_nombre": "LUIS DANIEL ALVA LUJAN"
            +"proveedor_nombre": "PROVEEDORES VARIOS"
            +"tipo_documento_nombre": "DNI"
            +"nro_documento": "99999999"
            +"modalidad_pago_nombre": "CONTADO"
            +"modalidad_pago_nro_dias": 0
            +"proyecto_nombre": "PROYECTO HUERTA GRANDE"
            +"colaborador_registrador_nombre": "EVA MARIA ALVA LUJAN"
        }   
    ]
    */
    public function show($id){

        try {
            $requerimiento  =   null;

            $orden_compra   =   DB::select('select 
                                oc.*,
                                c.nombre as persona_contacto_nombre,
                                c.telefono as persona_contacto_telefono,
                                co.nombre as colaborador_registrador_nombre,
                                pr.nombre as proveedor_nombre,
                                td.descripcion as proveedor_tipo_documento,
                                pr.nro_documento as proveedor_nro_documento,
                                m.tipo as modalidad_pago_nombre,
                                m.nro_dias as modalidad_pago_nro_dias,
                                proy.nombre as proyecto_nombre
                                from ordenes_compra as oc
                                inner join colaboradores as c on c.id = oc.persona_contacto_id
                                inner join colaboradores as co on co.id = oc.colaborador_registrador_id
                                inner join proveedores as pr on pr.id = oc.proveedor_id
                                inner join modalidades_pago as m on m.id = oc.modalidad_pago_id
                                inner join tipos_documento as td on td.id = pr.tipo_documento_id
                                inner join proyectos as proy on proy.id = oc.proyecto_id
                                where oc.id = ?',[$id]);

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
                                        where ocd.orden_compra_id = ?',[$id]);
            
            if(count($orden_compra) === 0){
                throw new Exception("NO EXISTE LA ORDEN DE COMPRA EN LA BD");
            }

            //========= BUSCAR SI LA ORDEN DE COMPRA FUE GENERADA A PARTIR DE UNA COTIZACION ======
            $cotizacion_compra  =   DB::select('select 
                                    cc.id,
                                    co.nombre as colaborador_registrador_nombre,
                                    cos.nombre as supervisor_nombre,
                                    pr.nombre as proyecto_nombre,
                                    cc.created_at as fecha_registro,
                                    cc.estado
                                    from cotizacion_compra as cc
                                    inner join colaboradores as co on co.id = cc.colaborador_id
                                    left join colaboradores as cos on cos.id = cc.supervisor_id
                                    inner join proyectos as pr on pr.id = cc.proyecto_id
                                    where cc.orden_compra_id = ?',[$id]);

            if(count($cotizacion_compra) === 0){
                $cotizacion_compra  =   null;
            }else{

                $cotizacion_compra  =   $cotizacion_compra[0];
                //========= BUSCAR SI LA ORDEN DE COMPRA PARTIÓ DESDE UN REQUERIMIENTO ======
                $requerimiento  =   DB::select('select 
                                    r.id,
                                    pr.nombre as proyecto_nombre,
                                    c.nombre as supervisor_nombre,
                                    prov.nombre as proveedor_nombre,
                                    r.factura_atencion,
                                    r.fecha_atencion,
                                    r.estado,
                                    r.created_at as fecha_registro
                                    from requerimientos as r
                                    inner join proyectos as pr on pr.id = r.proyecto_id
                                    inner join colaboradores as c on c.id = r.supervisor_id
                                    inner join proveedores as prov on prov.id = r.proveedor_id
                                    where r.cotizacion_compra_id = ?
                                    and r.orden_compra_id = ?',
                                    [$cotizacion_compra->id,
                                    $orden_compra[0]->id]);

                if(count($requerimiento) === 0){
                    $requerimiento  =   null;
                }else{
                    $requerimiento  =   $requerimiento[0];
                }
            }

           
            
            return response()->json([   
                                        'success'=>true,
                                        'orden_compra'=>$orden_compra[0],
                                        'orden_compra_detalle'=>$orden_compra_detalle,
                                        'cotizacion_compra' =>  $cotizacion_compra,
                                        'requerimiento'=>$requerimiento
                                    ]);
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
        
    }

}
