<?php

namespace App\Http\Controllers\Compras;

use App\Http\Controllers\Controller;
use App\Models\Compras\CotizacionCompra;
use App\Models\Compras\CotizacionCompraDetalle;
use App\Models\Compras\OrdenCompra;
use App\Models\Compras\OrdenCompraDetalle;
use App\Models\Compras\Proveedor;
use App\Models\Registros\Categoria;
use App\Models\Registros\Marca;
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
                                    ->join('colaboradores as c', 'c.id', '=', 'cc.colaborador_id')
                                    ->leftJoin('colaboradores as cs','cs.id','=','cc.supervisor_id')
                                    ->select(
                                        DB::raw('CONCAT("CO-", cc.id) as simbolo'), 
                                        'cc.id', 
                                        'c.nombre as colaborador_nombre',
                                        'cc.estado',
                                        'cc.created_at as fecha_registro',
                                        'r.id',
                                        DB::raw('CONCAT("RQ-", r.id) as simbolo_requerimiento'),
                                        'cs.nombre as supervisor_nombre'

                                    )
                                    ->where('cc.estado','<>','ANULADO')
                                    ->get();

        return DataTables::of($cotizaciones_compra)
                ->make(true);
    }

    public function create(){
        $categorias =   Categoria::where('estado','ACTIVO')->get();
        $marcas     =   Marca::where('estado','ACTIVO')->get();

        return view('compras.cotizacion_compra.create',compact('categorias','marcas'));
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

    public function store(Request $request){
        
        DB::beginTransaction();
        try {
            $lstCotizacionCompraDetalle =   json_decode($request->get('lstCotizacionCompra'));
            if(count($lstCotizacionCompraDetalle) === 0){
                throw new Exception("EL DETALLE DE LA COTIZACIÓN DE COMPRA ESTÁ VACÍO");
            }
            
            $cotizacion_compra                  =   new CotizacionCompra();
            $cotizacion_compra->colaborador_id  =   Auth::user()->colaborador_id;
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
            return response()->json(['success'=>true,'message'=>"COTIZACIÓN DE COMPRA REGISTRADA",'cid'=>$cotizacion_compra->id]);


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

        if(count($requerimiento) === 0){
            Session::flash('cotizacion_compra_error','NO SE ENCONTRÓ EL REQUERIMIENTO ASOCIADO A LA COTIZACIÓN!!!');
            return back();
        }
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

        return view('compras.cotizacion_compra.cotizacion_to_orden',
        compact('cotizacion_compra','cotizacion_compra_detalle','categorias',
        'marcas','proveedores','tipos_documento','modalidades_pago','requerimiento',
        'proyecto_personal'));
        
    }

    public function cotizacionToOrden(Request $request){
        DB::beginTransaction();
        try {

            $requerimiento  =   Requerimiento::find($request->get('requerimiento_id'));
            
            $lstCotizacionCompraDetalle =   json_decode($request->get('lstCotizacionCompra'));
            if(count($lstCotizacionCompraDetalle) === 0){
                throw new Exception("EL DETALLE DE LA ORDEN DE COMPRA ESTÁ VACÍO");
            }

            $orden_compra                       =   new OrdenCompra();
            $orden_compra->proveedor_id         =   $request->get('proveedor');
            $orden_compra->modalidad_pago_id    =   $request->get('modalidad_pago');
            $orden_compra->proyecto_id          =   $requerimiento->proyecto_id;
            $orden_compra->documento            =   $request->get('documento');   
            $orden_compra->documento            =   $request->get('tipo_doc');   
            $orden_compra->direccion_obra       =   $request->get('direccion');   
            $orden_compra->observacion          =   $request->get('observacion');  
            $orden_compra->persona_contacto_id  =   $request->get('persona_contacto');  
            $orden_compra->fecha_entrega        =   $request->get('fecha_entrega');  
            $orden_compra->terminos_entrega     =   $request->get('terminos_entrega');  
            $orden_compra->save();

            foreach ($lstCotizacionCompraDetalle as $item) {
                $producto_existe    =   DB::select('select p.id from productos as p
                                        where p.id = ?',[$item->producto_id]);

                if(count($producto_existe) === 0){
                    throw new Exception("NO EXISTE EL PRODUCTO"." ".$item->producto_nombre." "."EN LA BD");
                }

                $orden_compra_detalle                          =   new OrdenCompraDetalle();
                $orden_compra_detalle->orden_compra_id         =   $orden_compra->id;
                $orden_compra_detalle->producto_id             =   $item->producto_id;
                $orden_compra_detalle->cantidad                =   $item->cantidad;
                $orden_compra_detalle->save();
            }

            DB::commit();

            return response()->json(['success'=>true,'message'=>"ORDEN DE COMPRA GENERADA!!"]);

        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }
}
