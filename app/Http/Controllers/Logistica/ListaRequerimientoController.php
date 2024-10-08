<?php

namespace App\Http\Controllers\Logistica;

use App\Http\Controllers\Compras\CotizacionCompraController;
use App\Http\Controllers\Controller;
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
