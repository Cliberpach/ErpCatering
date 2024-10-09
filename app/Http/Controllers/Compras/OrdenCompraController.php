<?php

namespace App\Http\Controllers\Compras;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Dompdf\Dompdf;
use Dompdf\Options;
use Carbon\Carbon;

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
        return $dompdf->stream('archivo.pdf', ['Attachment' => false]);
    }

}
