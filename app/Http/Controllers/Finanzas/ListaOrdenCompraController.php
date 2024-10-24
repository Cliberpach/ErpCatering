<?php

namespace App\Http\Controllers\Finanzas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

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
                            td.descripcion as proveedor_tipo_documento,
                            oc.documento,
                            oc.proyecto_id,
                            oc.moneda
                            from ordenes_compra as oc
                            inner join proveedores as prov on prov.id = oc.proveedor_id
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

}
