<?php

namespace App\Http\Controllers\Compras;

use App\Http\Controllers\Controller;
use App\Http\Requests\Compras\OrdenCompra\OrdenCompraStoreRequest;
use App\Models\Compras\CotizacionCompra;
use App\Models\Compras\CotizacionCompraDetalle;
use App\Models\Compras\OrdenCompra;
use App\Models\Compras\OrdenCompraDetalle;
use App\Models\Compras\Proveedor;
use App\Models\Registros\Categoria;
use App\Models\Registros\Marca;
use App\Models\Registros\Proyecto;
use App\Models\Requerimientos\Requerimiento;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CotizacionCompraController extends Controller
{
    public function index(){
        return view('compras.cotizacion_compra.index');
    }

    public function getCotizacionesCompra(Request $request){

        $cotizaciones_compra    =   DB::table('cotizacion_compra as cc')
                                    ->leftJoin('requerimientos as r','r.cotizacion_compra_id','=','cc.id')
                                    ->leftJoin('colaboradores as c', 'c.id', '=', 'cc.colaborador_id')
                                    ->leftJoin('colaboradores as cs','cs.id','=','cc.supervisor_id')
                                    ->join('proyectos as pr','pr.id','=','cc.proyecto_id')
                                    ->select(
                                        DB::raw('CONCAT("CO-", cc.id) as simbolo'), 
                                        'cc.id', 
                                        'c.nombre as colaborador_nombre',
                                        'cc.estado',
                                        'cc.created_at as fecha_registro',
                                        'r.id as requerimiento_id',
                                        'pr.nombre as proyecto_nombre',
                                        DB::raw('CONCAT("RQ-", r.id) as simbolo_requerimiento'),
                                        DB::raw('COALESCE(cs.nombre, c.nombre) as supervisor_nombre')                                     )
                                    ->where('cc.estado','<>','ANULADO')
                                    ->get();

        return DataTables::of($cotizaciones_compra)
                ->make(true);
    }

    public function create(){
        $categorias =   Categoria::where('estado','ACTIVO')->get();
        $marcas     =   Marca::where('estado','ACTIVO')->get();

        //======== VERIFICANDO QUE FORME PARTE DE UN PROYECTO ==========
        $proyecto   =   DB::select('select 
                        pr.id as proyecto_id,
                        pr.nombre as proyecto_nombre
                        from proyecto_personal as pp
                        inner join proyectos as pr on pr.id = pp.proyecto_id
                        where pp.colaborador_id = ?',
                        [Auth::user()->colaborador_id]); 

        if(count($proyecto) === 0){
            Session::flash('cotizacion_compra_error',"DEBES FORMAR PARTE DE UN PROYECTO PARA REALIZAR COTIZACIONES");
            return back();
        }

        $proyecto   =   $proyecto[0];

        $colaborador_registrador    =   DB::select('select 
                                        co.id as colaborador_id,
                                        co.nombre as colaborador_nombre
                                        from 
                                        colaboradores as co
                                        where co.id = ?',[Auth::user()->colaborador_id]);

        if(count($colaborador_registrador) === 0){
            Session::flash('cotizacion_compra_error',"NO SE ENCUENTRA EL COLABORADOR EN LA BD");
            return back();
        }
                                
        $colaborador_registrador    =   $colaborador_registrador[0];

        return view('compras.cotizacion_compra.create',
        compact('categorias','marcas','proyecto','colaborador_registrador'));
    }

    public function edit($id){
        $cotizacion_compra_detalle  =   DB::select('select 
                                        ccd.producto_id,
                                        ccd.cantidad,
                                        p.nombre as producto_nombre,
                                        c.descripcion as categoria_nombre,
                                        m.descripcion as marca_nombre,
                                        tgd.descripcion as producto_unidad_medida
                                        from cotizacion_compra_detalle as ccd
                                        inner join productos as p on p.id = ccd.producto_id
                                        inner join marcas as m on m.id = p.marca_id 
                                        inner join categorias as c on c.id = p.categoria_id
                                        inner join tablas_generales_detalles as tgd on tgd.id = p.unidad_medida_id
                                        where ccd.cotizacion_compra_id = ?',[$id]);

        $categorias =   Categoria::where('estado','ACTIVO')->get();
        $marcas     =   Marca::where('estado','ACTIVO')->get();

        return view('compras.cotizacion_compra.edit',
        compact('cotizacion_compra_detalle','categorias','marcas','id'));
    }


    /*
        array:3 [ // app\Http\Controllers\Compras\CotizacionCompraController.php:120
            "lstCotizacionCompra"           => "[{"producto_id":1,"producto_nombre":"CEMENTO ROJO MOCHICA X 45 KG","categoria_nombre":"CEMENTO","marca_nombre":"MOCHICA","producto_unidad_medida":"UNIDAD","cantidad":"2"}]"
            "proyecto_id"                   => "2"  --VALIDACIÓN COMPLEJA
            "colaborador_registrador_id"    => "3"  --VALIDACIÓN COMPLEJA
            "supervisor_id"                 =>  "VIENE CUANDO SE CONVIERTE REQ A COT"
        ]
    */
    public function store(Request $request){
        
        DB::beginTransaction();
        try {
            $lstCotizacionCompraDetalle =   json_decode($request->get('lstCotizacionCompra'));
            if(count($lstCotizacionCompraDetalle) === 0){
                throw new Exception("EL DETALLE DE LA COTIZACIÓN DE COMPRA ESTÁ VACÍO");
            }
            
            $cotizacion_compra                  =   new CotizacionCompra();
            $cotizacion_compra->colaborador_id  =   $request->get('colaborador_registrador_id');
            $cotizacion_compra->proyecto_id     =   $request->get('proyecto_id');
            if($request->has('supervisor_id')){
                $cotizacion_compra->supervisor_id   =   $request->get('supervisor_id');
            }
            $cotizacion_compra->save();

            foreach ($lstCotizacionCompraDetalle as $item) {
                $producto_existe    =   DB::select('select p.id from productos as p
                                        where p.id = ?',[$item->producto_id]);

                if(count($producto_existe) === 0){
                    throw new Exception("NO EXISTE EL PRODUCTO"." ".$item->producto_nombre." "."EN LA BD");
                }

                $cotizacion_compra_detalle                          =   new CotizacionCompraDetalle();
                $cotizacion_compra_detalle->cotizacion_compra_id    =   $cotizacion_compra->id;
                $cotizacion_compra_detalle->producto_id             =   $item->producto_id;
                $cotizacion_compra_detalle->cantidad                =   $item->cantidad;
                $cotizacion_compra_detalle->save();
            }

            DB::commit();
            return response()->json(['success'=>true,'message'=>"COTIZACIÓN DE COMPRA REGISTRADA",
            'cid'=>$cotizacion_compra->id]);


        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function update($id,Request $request){
        DB::beginTransaction();
        try {
            $cotizacion_compra          =   CotizacionCompra::find($id);
            $lstCotizacionCompraDetalle =   json_decode($request->get('lstCotizacionCompra'));

            if(count($lstCotizacionCompraDetalle) === 0){
                throw new Exception("EL DETALLE DE LA COTIZACIÓN DE COMPRA ESTÁ VACÍO");
            }

            if(!$cotizacion_compra){
                throw new Exception("NO SE ENCONTRÓ LA COTIZACIÓN DE COMPRA");
            }

            $cotizacion_compra->colaborador_id  =   Auth::user()->colaborador_id;
            $cotizacion_compra->update();

            DB::delete('DELETE FROM cotizacion_compra_detalle 
            WHERE cotizacion_compra_id = ?', [$id]);
          
            foreach ($lstCotizacionCompraDetalle as $item) {
                $producto_existe    =   DB::select('select p.id from productos as p
                                        where p.id = ?',[$item->producto_id]);

                if(count($producto_existe) === 0){
                    throw new Exception("NO EXISTE EL PRODUCTO"." ".$item->producto_nombre." "."EN LA BD");
                }

            
                $cotizacion_compra_detalle                          =   new CotizacionCompraDetalle();
                $cotizacion_compra_detalle->cotizacion_compra_id    =   $cotizacion_compra->id;
                $cotizacion_compra_detalle->producto_id             =   $item->producto_id;
                $cotizacion_compra_detalle->cantidad                =   $item->cantidad;
                $cotizacion_compra_detalle->save();
            }

            DB::commit();
            return response()->json(['success'=>true,'message'=>"COTIZACIÓN DE COMPRA ACTUALIZADA"]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function destroy($id){
        DB::beginTransaction();
        try {
            $cotizacion_compra                    =   CotizacionCompra::find($id);
            $cotizacion_compra->estado            =   'ANULADO';
            $cotizacion_compra->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'COTIZACIÓN DE COMPRA ELIMINADA']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function pdf($id){

        $empresa    =   DB::select('select * from empresas as e
                        where e.id = 1')[0];

        $cotizacion_compra =   DB::select('select 
                                cc.id,
                                c.nombre as colaborador_nombre
                                from cotizacion_compra as cc
                                inner join colaboradores as c on c.id = cc.colaborador_id
                                where cc.id = ?',[$id])[0];

        $cotizacion_compra_detalle  =   DB::select('select 
                                ccd.producto_id,
                                ccd.cantidad,
                                p.nombre as producto_nombre,
                                c.descripcion as categoria_nombre,
                                m.descripcion as marca_nombre,
                                tgd.descripcion as producto_unidad_medida
                                from cotizacion_compra_detalle as ccd
                                inner join productos as p on p.id = ccd.producto_id
                                inner join marcas as m on m.id = p.marca_id 
                                inner join categorias as c on c.id = p.categoria_id
                                inner join tablas_generales_detalles as tgd on tgd.id = p.unidad_medida_id
                                where ccd.cotizacion_compra_id = ?',[$id]);

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
        $html = view('compras.cotizacion_compra.pdf.pdf',
        compact('empresa','cotizacion_compra','cotizacion_compra_detalle','fecha_impresion'))
            ->render();

        // Cargar el HTML en DOMPDF
        $dompdf->loadHtml($html);

        // Opcional: Configurar el tamaño de papel y la orientación
        $dompdf->setPaper('A4', 'portrait'); // O 'landscape'

        // Renderizar el PDF
        $dompdf->render();

        // Visualizar el PDF en una nueva ventana en lugar de descargarlo
        return $dompdf->stream('archivo.pdf', ['Attachment' => false]);
    }

    public function goToOrdenCompra($cotizacion_id){

        $cotizacion_compra  =   DB::select('select 
                                cc.*,
                                c.nombre as colaborador_nombre
                                from cotizacion_compra as cc
                                inner join colaboradores as c on c.id = cc.colaborador_id
                                where cc.id = ?',[$cotizacion_id])[0];

        if($cotizacion_compra->estado === 'ANULADO'){
            Session::flash('cotizacion_compra_error','LA COTIZACIÓN ESTÁ ANULADA!!!');
            return back();
        }

        if($cotizacion_compra->estado === 'FACTURADO'){
            Session::flash('cotizacion_compra_error','LA COTIZACIÓN YA FUE FACTURADA!!!');
            return back();
        }

        if($cotizacion_compra->estado === 'CON ORDEN COMPRA'){
            Session::flash('cotizacion_compra_error','LA COTIZACIÓN YA TIENE ORDEN DE COMPRA!!!');
            return back();
        }
        
        $cotizacion_compra_detalle  =   DB::select('select 
                                        ccd.producto_id,
                                        ccd.cantidad,
                                        p.nombre as producto_nombre,
                                        c.descripcion as categoria_nombre,
                                        m.descripcion as marca_nombre,
                                        tgd.descripcion as producto_unidad_medida,
                                        p.precio
                                        from cotizacion_compra_detalle as ccd
                                        inner join productos as p on p.id = ccd.producto_id
                                        inner join marcas as m on m.id = p.marca_id 
                                        inner join categorias as c on c.id = p.categoria_id
                                        inner join tablas_generales_detalles as tgd on tgd.id = p.unidad_medida_id
                                        where ccd.cotizacion_compra_id = ?',[$cotizacion_id]);

        $requerimiento  =   DB::select('select 
                            pr.nombre,
                            pr.direccion,
                            pr.supervisor_id,
                            r.id,
                            CONCAT(co.nombre, " - CEL:", co.telefono) AS persona_contacto
                            from requerimientos as r
                            join proyectos as pr on pr.id = r.proyecto_id
                            JOIN colaboradores AS co ON co.id = pr.supervisor_id
                            where r.cotizacion_compra_id = ?
                            and r.estado = "COTIZADO"
                            and (pr.estado = "PENDIENTE" or pr.estado = "EN PROCESO")',
                            [$cotizacion_id]);

        $proyecto   =   DB::select('select 
                        pr.nombre,
                        pr.direccion,
                        pr.supervisor_id,
                        CONCAT(co.nombre, " - CEL:", co.telefono) AS persona_contacto
                        from 
                        proyectos as pr
                        left join colaboradores AS co ON co.id = pr.supervisor_id
                        where pr.id = ?
                        and (pr.estado = "PENDIENTE" or pr.estado = "EN PROCESO")',
                        [$cotizacion_compra->proyecto_id])[0]; 
                
        //======= CONTROLANDO QUE EL PROYECTO TENGA SUPERVISOR ========
        if(!$proyecto->supervisor_id){
            Session::flash('cotizacion_compra_error','EL PROYECTO NO TIENE SUPERVISOR!!!');
            return back();
        }
                        
        //========= COTIZACIÓN SIN REQUERIMIENTO ========
        if(count($requerimiento) === 0){
            $requerimiento      =   null;

            $proyecto_personal  =   DB::select('select
                                    pp.colaborador_id,
                                    CONCAT(co.nombre, " - CEL:", co.telefono) AS persona_contacto
                                    from 
                                    proyectos as pr 
                                    join proyecto_personal as pp on pp.proyecto_id = pr.id
                                    JOIN colaboradores AS co ON co.id = pp.colaborador_id
                                    where pr.id = ?
                                    and (pr.estado = "PENDIENTE" or pr.estado = "EN PROCESO")',
                                    [$cotizacion_compra->proyecto_id]);
                            
        }else{
            //======= COTIZACIÓN CON REQUERIMIENTO ==========
            $requerimiento      =   $requerimiento[0];

            $proyecto_personal  =   DB::select('select
                                    pp.colaborador_id,
                                    CONCAT(co.nombre, " - CEL:", co.telefono) AS persona_contacto
                                    from requerimientos as r
                                    join proyectos as pr on pr.id = r.proyecto_id
                                    join proyecto_personal as pp on pp.proyecto_id = pr.id
                                    JOIN colaboradores AS co ON co.id = pp.colaborador_id
                                    where r.cotizacion_compra_id = ?
                                    and r.estado = "COTIZADO"
                                    and (pr.estado = "PENDIENTE" or pr.estado = "EN PROCESO")',
                                    [$cotizacion_id]);
        }
         
        $categorias         =   Categoria::where('estado','ACTIVO')->get();
        $marcas             =   Marca::where('estado','ACTIVO')->get();

        $proveedores        =   DB::select('select 
                                pr.id,
                                pr.nombre,
                                pr.nro_documento,
                                td.descripcion as tipo_documento_descripcion
                                from proveedores as pr
                                inner join tipos_documento as td on td.id = pr.tipo_documento_id
                                where pr.estado = "ACTIVO"
                                and pr.id != 1');

        $tipos_documento    =   DB::select('select * 
                                from tipos_documento as td
                                where td.estado = "ACTIVO"
                                and td.id <> "3" ');

        $modalidades_pago   =   DB::select('select * from modalidades_pago as m
                                where m.estado = "ACTIVO"');

        $igv                =   DB::select('select e.igv from empresas as e')[0]->igv;

        return view('compras.cotizacion_compra.cotizacion_to_orden',
        compact('cotizacion_compra','cotizacion_compra_detalle','categorias',
        'marcas','proveedores','tipos_documento','modalidades_pago','requerimiento','proyecto',
        'proyecto_personal','igv'));
        
    }


    /*
        array:21 [ // app\Http\Controllers\Compras\CotizacionCompraController.php:352
            "_token"            => "0JxjfDhuvq6nx8BGCa4H6q2t19ohFZxgTGC72VQ9"
            "fecha_entrega"     => "2024-10-09"             --VALIDACION REQUEST
            "igv"               => "18"                     --VALIDACION REQUEST
            "moneda"            => "USD"                    --VALIDACION REQUEST
            "tipo_cambio"       => "3.744"                  --VALIDACION REQUEST
            "terminos_entrega"  => "PUESTO EN OBRA"         --VALIDACION REQUEST
            "proveedor"         => "1"                      --VALIDACION REQUEST
            "direccion"         => "AV LAS MAGNOLIAS 321"   --VALIDACION REQUEST
            "proyecto"          => "PROYECTO HUERTA GRANDE" 
            "modalidad_pago"    => "2"                      --VALIDACION REQUEST
            "tipo_doc"          => "FACTURA"                --VALIDACION REQUEST
            "persona_contacto"  => "2"                      --VALIDACIÓN REQUEST Y COMPLEJA
            "observacion"       => null
            "producto"          => null
            "unidad"            => null
            "precio"            => null
            "cantidad"          => null
            "table_cotizacion_to_orden_detalle_length"  => "10"
            "lstCotizacionCompra"                       => "[{"cantidad":"20.00","categoria_nombre":"CEMENTO","marca_nombre":"MOCHICA","producto_id":1,"producto_nombre":"CEMENTO ROJO MOCHICA X 45 KG","producto_unidad_medida":"UNIDAD","precio":"29.50","total":590},{"cantidad":"10.00","categoria_nombre":"TUBO","marca_nombre":"EUROTUBO","producto_id":2,"producto_nombre":"TUBO HIDRÁULICO","producto_unidad_medida":"UNIDAD","precio":"2","total":20,"almacen_nombre":""}]"
            "cotizacion_compra_id"  => "1"  --VALIDACION COMPLEJA
            "requerimiento_id"      => "1"  --VALIDACION COMPLEJA
        ]
    */
    public function cotizacionToOrden(OrdenCompraStoreRequest $request){
        
        DB::beginTransaction();
        try {

            //===== VALIDACIÓN COMPLEJA ======
            CotizacionCompraController::validacionCotizacionToOrden($request);

            $lstCotizacionCompraDetalle =   json_decode($request->get('lstCotizacionCompra'));
            CotizacionCompraController::validarLstCotizacionCompra($lstCotizacionCompraDetalle);

            $montos             =   CotizacionCompraController::calcularMontos($lstCotizacionCompraDetalle,$request->get('igv',null),$request->get('valor_igv'));
            $requerimiento      =   DB::select('select r.id 
                                    from requerimientos as r
                                    where r.cotizacion_compra_id = ?',
                                    [$request->get('cotizacion_compra_id')]);

            $cotizacion_compra  =   CotizacionCompra::find($request->get('cotizacion_compra_id'));


            $orden_compra                               =   new OrdenCompra();
            $orden_compra->colaborador_registrador_id   =   Auth::user()->colaborador_id;
            $orden_compra->proveedor_id                 =   $request->get('proveedor');
            $orden_compra->modalidad_pago_id            =   $request->get('modalidad_pago');
            $orden_compra->proyecto_id                  =   $cotizacion_compra->proyecto_id;
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
            $orden_compra->primer_producto_id           =   $lstCotizacionCompraDetalle[0]->producto_id;

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

            $orden_compra->save();
            
            foreach ($lstCotizacionCompraDetalle as $item) {
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

            if(count($requerimiento) !== 0){
                //======== ACTUALIZAR ESTADO DEL REQUERIMIENTO =======
                $requerimiento                  =   Requerimiento::find($requerimiento[0]->id);
                $requerimiento->orden_compra_id =   $orden_compra->id;
                $requerimiento->estado          =   'CON ORDEN COMPRA';
                $requerimiento->update();
            }
           
            //========= ACTUALIZAR ESTADO DE COTIZACIÓN =======
            $cotizacion_compra->estado          =   'CON ORDEN COMPRA';
            $cotizacion_compra->orden_compra_id =   $orden_compra->id;
            $cotizacion_compra->update();

            DB::commit();

            return response()->json(['success'=>true,'message'=>"ORDEN DE COMPRA GENERADA!!"]);

        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public static function validarLstCotizacionCompra($lstCompra){

        if(count($lstCompra) === 0){
            throw new Exception("EL DETALLE DE LA COMPRA ESTÁ VACÍO!!!");
        }

        foreach ($lstCompra as $item) {
            $existe =   DB::table('productos')
                        ->where('id', $item->producto_id)
                        ->exists();

            if(!$existe){
                throw new Exception("EL PRODUCTO ".$item->producto_nombre."NO EXISTE EN LA BD");
            }
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

    public static function validacionCotizacionToOrden($request){

        if(!$request->has('cotizacion_compra_id')){
            throw new Exception("FALTA EL PARÁMETRO COTIZACIÓN COMPRA ID");
        }

        if(!$request->get('cotizacion_compra_id')){
            throw new Exception("FALTA EL PARÁMETRO COTIZACIÓN COMPRA ID");
        }

        //======= VALIDANDO COTIZACIÓN COMPRA EN BD ========
        $cotizacion_compra  =   CotizacionCompra::find($request->get('cotizacion_compra_id'));

        if(!$cotizacion_compra){
            throw new Exception("NO EXISTE LA COTIZACIÓN DE COMPRA EN LA BD");
        }

        if(!$cotizacion_compra->estado  === 'CON ORDEN COMPRA'){
            throw new Exception("LA COTIZACIÓN DE COMPRA YA FUE CONVERTIDA A ORDEN DE COMPRA");
        }

        if(!$cotizacion_compra->estado  === 'FACTURADO'){
            throw new Exception("LA COTIZACIÓN DE COMPRA YA FUE FACTURADA");
        }

        if(!$cotizacion_compra->estado  === 'ANULADO'){
            throw new Exception("LA COTIZACIÓN DE COMPRA ESTÁ ANULADA");
        }


        //======= VERIFICANDO SI LA COTIZACIÓN TIENE REQUERIMIENTO EN LA BD ======
        $requerimiento  =   DB::select('select r.id from requerimientos as r
                            where r.cotizacion_compra_id = ?',
                            [$request->get('cotizacion_compra_id')]);

        //======= EN CASO TENGA REQUERIMIENTO =====
        //===== VALIDAR EL REQUERIMIENTO ======
        if(count($requerimiento) === 1){

            //====== CON REQUERIMIENTO =====
            if(!$request->has('requerimiento_id')){
                throw new Exception("FALTA EL PARÁMETRO REQUERIMIENTO ID");
            }
            if(!$request->get('requerimiento_id')){
                throw new Exception("FALTA EL PARÁMETRO REQUERIMIENTO ID");
            }

             //========= VALIDANDO REQUERIMIENTO EN BD ======
            $requerimiento  =   Requerimiento::find($request->get('requerimiento_id'));

            if(!$requerimiento){
                throw new Exception("NO EXISTE EL REQUERIMIENTO EN LA BD");
            }

            if(!$requerimiento->estado  === 'PENDIENTE'){
                throw new Exception("EL REQUERIMIENTO NO ESTÁ COTIZADO");
            }

            if(!$requerimiento->estado  === 'CON ORDEN COMPRA'){
                throw new Exception("EL REQUERIMIENTO YA FUE CONVERTIDO A ORDEN DE COMPRA");
            }

            if(!$requerimiento->estado  === 'FACTURADO'){
                throw new Exception("EL REQUERIMIENTO YA FUE FACTURADO");
            }

            if(!$requerimiento->estado  === 'ANULADO'){
                throw new Exception("EL REQUERIMIENTO ESTÁ ANULADO");
            }
        }
      
        //======== VALIDANDO LA PERSONA DE CONTACTO ========
        $persona_contacto_id    =   $request->get('persona_contacto');
        $proyecto_id            =   $cotizacion_compra->proyecto_id;
      

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

   
}
