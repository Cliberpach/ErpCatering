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
}
