<?php

namespace App\Http\Controllers\Logistica;

use App\Http\Controllers\Compras\CotizacionCompraController;
use App\Http\Controllers\Controller;
use App\Models\Registros\Categoria;
use App\Models\Registros\Marca;
use App\Models\Requerimientos\Requerimiento;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ListaRequerimientoController extends Controller
{
    public function index(){
        return view('logistica.lista_requerimientos.index');
    }

    public function getRequerimientos(Request $request){
        
        $requerimientos =   DB::table('requerimientos as r')
                                ->join('colaboradores as c', 'c.id', '=', 'r.supervisor_id')
                                ->join('proyectos as pr', 'pr.id', '=', 'r.proyecto_id')
                                ->leftJoin('productos as p', 'p.id', '=', 'r.primer_producto_id')
                                ->leftJoin('proveedores as pro', 'pro.id', '=', 'r.proveedor_id')
                                ->select(
                                    DB::raw('CONCAT("RQ-", r.id) as simbolo'), 
                                    'r.id',
                                    'pr.nombre as proyecto_nombre', 
                                    'c.nombre as supervisor_nombre',
                                    'pro.nombre as proveedor_nombre',
                                    'r.created_at as fecha_registro',
                                    'r.fecha_atencion as fecha_atencion',
                                    'p.nombre as primer_producto_nombre',
                                    'r.estado',
                                    'r.supervisor_id'
                                )
                                ->where('r.estado','<>','ANULADO')
                                ->get();

        return DataTables::of($requerimientos)
                ->make(true);
    }

    public function goToCotizacionCompra($requerimiento_id){
        $categorias     =   Categoria::where('estado','ACTIVO')->get();
        $marcas         =   Marca::where('estado','ACTIVO')->get();

        $requerimiento  =   DB::select('select
                            r.id,
                            p.nombre as proyecto_nombre,
                            c.nombre as supervisor_nombre,
                            pro.nombre as proveedor_nombre,
                            r.orden_compra_id,
                            r.factura_atencion,
                            r.fecha_atencion,
                            r.estado,
                            r.created_at as fecha_registro
                            from requerimientos as r
                            inner join colaboradores    as c on c.id = r.supervisor_id
                            inner join proyectos      as p on p.id = r.proyecto_id
                            left join proveedores as pro on pro.id = r.proveedor_id
                            where r.id = ?',[$requerimiento_id])[0];

        $requerimiento_detalle  =   DB::select('select 
                                    rd.producto_id,
                                    rd.cantidad,
                                    p.nombre as producto_nombre,
                                    c.descripcion as categoria_nombre,
                                    m.descripcion as marca_nombre,
                                    tgd.descripcion as producto_unidad_medida
                                    from requerimiento_detalle as rd
                                    inner join productos as p on p.id = rd.producto_id
                                    inner join marcas as m on m.id = p.marca_id 
                                    inner join categorias as c on c.id = p.categoria_id
                                    inner join tablas_generales_detalles as tgd on tgd.id = p.unidad_medida_id
                                    where rd.requerimiento_id = ?',[$requerimiento_id]);

        return view('logistica.lista_requerimientos.requerimiento_to_cotizacion',
        compact('categorias','marcas','requerimiento','requerimiento_detalle'));
    }


    /*
    array:13 [ // app\Http\Controllers\Logistica\ListaRequerimientoController.php:86
        "_token"                => "JdyhDaaj0jitzyjdAmCOH8W69P5l6g2h4R18pbly"
        "proyecto"              => "PROYECTO HUERTA GRANDE"
        "supervisor"            => "LUIS DANIEL ALVA LUJAN"
        "proveedor"             => "PROVEEDORES VARIOS"
        "registrador"           => "EVA MARIA ALVA LUJAN"
        "registrador_id"        => "3"
        "fecha_registro"        => "2024-10-11"
        "producto"              => null
        "unidad"                => null
        "cantidad"              => null
        "table_requerimiento_to_cotizacion_detalle_length"  => "10"
        "lstCotizacionCompra"                               => "[{"cantidad":"2.00","categoria_nombre":"TUBO","marca_nombre":"EUROTUBO","producto_id":2,"producto_nombre":"TUBO HIDRÁULICO","producto_unidad_medida":"UNIDAD"},{"cantidad":"10.00","categoria_nombre":"PRODUCTO","marca_nombre":"NACIONAL","producto_id":22,"producto_nombre":"PRODUCTO 1","producto_unidad_medida":"UNIDAD"},{"cantidad":"30.00","categoria_nombre":"PRODUCTO","marca_nombre":"NACIONAL","producto_id":23,"producto_nombre":"PRODUCTO 2","producto_unidad_medida":"UNIDAD"},{"producto_id":3,"producto_nombre":"LADRILLO PANDERETA","categoria_nombre":"LADRILLO","marca_nombre":"MOCHICA","producto_unidad_medida":"UNIDAD","cantidad":"20"}]"
        "requerimiento_id"                                  => "2"
    ]
    */ 
    public function requerimientoToCotizacion(Request $request){
        
        DB::beginTransaction();
        try {

            //======== VALIDACIÓN COMPLEJA ======
            ListaRequerimientoController::validarRequerimientoToCotizacionCompra($request);

            $requerimiento  =   Requerimiento::find($request->get('requerimiento_id'));

            if(!$requerimiento){
                throw new Exception("NO EXISTE EL REQUERIMIENTO EN LA BD"); 
            }

            //========= CONVIRTIENDO A COTIZACIÓN =========
            $request_store_cotizacion   =   new Request();
            $request_store_cotizacion->merge([
                'supervisor_id'                 =>  $requerimiento->supervisor_id,
                'lstCotizacionCompra'           =>  $request->get('lstCotizacionCompra'),
                'colaborador_registrador_id'    =>  $request->get('registrador_id'),
                'proyecto_id'                   =>  $requerimiento->proyecto_id  
            ]);

            $cotizacion_controller  =   new CotizacionCompraController();
            $res_cotizacion_store   =   $cotizacion_controller->store($request_store_cotizacion);
            $res_cotizacion_store   =   $res_cotizacion_store->getData();

            if(!$res_cotizacion_store){
                throw new Exception($res_cotizacion_store->message);
            }

            $requerimiento->cotizacion_compra_id    =   $res_cotizacion_store->cid;
            $requerimiento->estado                  =   'COTIZADO';
            $requerimiento->update();      

            DB::commit();
            return response()->json(['success'=>true,'message'=>$res_cotizacion_store->message]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }

    }

    public static function validarRequerimientoToCotizacionCompra($request){

        if(!$request->has('requerimiento_id') || !$request->get('requerimiento_id')){
            throw new Exception("FALTA EL PARÁMETRO REQUERIMIENTO ID EN LA PETICIÓN!!");
        }

        $requerimiento  =   DB::select('select r.id,r.estado 
                            from requerimientos as r
                            where r.id = ? ',[$request->get('requerimiento_id')]);

        if(count($requerimiento) === 0){
            throw new Exception("NO EXISTE EL REQUERIMIENTO EN LA BD!!");
        }

        if($requerimiento[0]->estado === 'COTIZADO'){
            throw new Exception("EL REQUERIMIENTO YA FUE COTIZADO!!");
        }

        if($requerimiento[0]->estado === 'CON ORDEN COMPRA'){
            throw new Exception("EL REQUERIMIENTO YA TIENE ORDEN DE COMPRA!!");
        }

        if($requerimiento[0]->estado === 'FACTURADO'){
            throw new Exception("EL REQUERIMIENTO YA FUE FACTURADO!!");
        }

    }

    public function generarCotizacion(Request $request){
        
        DB::beginTransaction();

        try {

            //======== VALIDANDO ======
            $requerimiento  =   Requerimiento::find($request->get('requerimiento_id'));

            if(!$requerimiento){
                throw new Exception("NO EXISTE EL REQUERIMIENTO EN LA BD"); 
            }

            $requerimiento_detalle  =   DB::select('select 
                                        rd.producto_id,
                                        p.nombre as producto_nombre,
                                        rd.cantidad
                                        from requerimiento_detalle as rd
                                        inner join productos as p on p.id = rd.producto_id
                                        where rd.requerimiento_id = ?',[$requerimiento->id]);
            
            $requerimiento_detalle  =   json_encode($requerimiento_detalle);                      

            //========= CONVIRTIENDO A COTIZACIÓN =========
            $request_store_cotizacion   =   new Request();
            $request_store_cotizacion->merge([
                'supervisor_id'         =>  $requerimiento->supervisor_id,
                'lstCotizacionCompra'   =>  $requerimiento_detalle,
            ]);

            $cotizacion_controller  =   new CotizacionCompraController();
            $res_cotizacion_store   =   $cotizacion_controller->store($request_store_cotizacion);
            $res_cotizacion_store   =   $res_cotizacion_store->getData();

            if(!$res_cotizacion_store){
                throw new Exception($res_cotizacion_store->message);
            }

            $requerimiento->cotizacion_compra_id    =   $res_cotizacion_store->cid;
            $requerimiento->estado                  =   'COTIZADO';
            $requerimiento->update();      

            DB::commit();
            return response()->json(['success'=>true,'message'=>$res_cotizacion_store->message]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
       
    }

    public function show($id){

        try {

            $requerimiento =   DB::select('select
                                r.id,
                                p.nombre as proyecto_nombre,
                                c.nombre as supervisor_nombre,
                                pro.nombre as proveedor_nombre,
                                r.orden_compra_id,
                                r.factura_atencion,
                                r.fecha_atencion,
                                r.estado,
                                r.created_at as fecha_registro
                                from requerimientos as r
                                inner join colaboradores    as c on c.id = r.supervisor_id
                                inner join proyectos      as p on p.id = r.proyecto_id
                                left join proveedores as pro on pro.id = r.proveedor_id
                                where r.id = ?',[$id])[0];

            $requerimiento_detalle  =   DB::select('select 
                                        rd.producto_id,
                                        rd.cantidad,
                                        p.nombre as producto_nombre,
                                        c.descripcion as categoria_nombre,
                                        m.descripcion as marca_nombre,
                                        tgd.descripcion as producto_unidad_medida
                                        from requerimiento_detalle as rd
                                        inner join productos as p on p.id = rd.producto_id
                                        inner join marcas as m on m.id = p.marca_id 
                                        inner join categorias as c on c.id = p.categoria_id
                                        inner join tablas_generales_detalles as tgd on tgd.id = p.unidad_medida_id
                                        where rd.requerimiento_id = ?',[$id]);

            return response()->json(['success'=>true,
            'requerimiento_detalle' => $requerimiento_detalle,
            'requerimiento'=>$requerimiento]);
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
        
       
    }
}
