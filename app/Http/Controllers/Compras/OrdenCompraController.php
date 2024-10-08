<?php

namespace App\Http\Controllers\Compras;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class OrdenCompraController extends Controller
{
    public function index(){
        return view('compras.orden_compra.index');
    }

    public function getOrdenesCompra(Request $request){

        $ordenes_compra    =   DB::table('ordenes_compra as oc')
                                    ->join('proveedores as prov','prov.id','=','oc.proveedor_id')
                                    ->join('modalidades_pago as m', 'm.id', '=', 'oc.modalidad_pago_id')
                                    ->join('proyectos as proy','proy.id','=','oc.proyecto_id')
                                    ->join('colaboradores as co','co.id','=','oc.persona_contacto_id')
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
                                        'oc.fecha_entrega as orden_compra_fecha_entrega',
                                        'oc.terminos_entrega as orden_compra_terminos_entrega',
                                        'oc.estado as orden_compra_estado',
                                        'oc.created_at as orden_compra_fecha_registro',

                                    )
                                    ->where('oc.estado','<>','ANULADO')
                                    ->get();

        return DataTables::of($ordenes_compra)
                ->make(true);
    }


    public function create(){
        return view('compras.orden_compra.create');

    }

}
