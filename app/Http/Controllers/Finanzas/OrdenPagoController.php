<?php

namespace App\Http\Controllers\Finanzas;

use App\Http\Controllers\Controller;
use App\Models\Finanzas\OrdenPago;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Dompdf\Dompdf;
use Dompdf\Options;

class OrdenPagoController extends Controller
{
    public function index(){

        return view('finanzas.orden_pago.index');
    }

    public function getOrdenesPago(Request $request){

        $ordenes_pago    =   DB::table('ordenes_pago as op')
                                    ->leftJoin('ordenes_compra as oc','oc.orden_pago_id','op.id')
                                    ->select(
                                        DB::raw('CONCAT("OP-", op.id) as simbolo'), 
                                        DB::raw('CONCAT("OC-", oc.id) as simbolo_orden_compra'),
                                        'op.*'
                                    )
                                    ->get();
    
        return DataTables::of($ordenes_pago)->make(true); 
    }

    public function pdf($id){
        
        $empresa    =   DB::select('select * from empresas as e
        where e.id = 1')[0];

        $orden_pago             =       OrdenPago::find($id);

        $orden_pago_detalle     =       DB::select('select 
                                        opd.producto_id,
                                        opd.cantidad,
                                        opd.precio_soles,
                                        opd.precio_dolares,
                                        p.nombre as producto_nombre,
                                        c.descripcion as categoria_nombre,
                                        m.descripcion as marca_nombre,
                                        tgd.descripcion as producto_unidad_medida
                                        from ordenes_pago_detalle as opd
                                        inner join productos as p on p.id = opd.producto_id
                                        inner join marcas as m on m.id = p.marca_id 
                                        inner join categorias as c on c.id = p.categoria_id
                                        inner join tablas_generales_detalles as tgd on tgd.id = p.unidad_medida_id
                                        where opd.orden_pago_id = ?',
                                        [$id]);


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
        $html = view('finanzas.orden_pago.pdf.pdf',
        compact('empresa','orden_pago','orden_pago_detalle','fecha_impresion'))
        ->render();

        // Cargar el HTML en DOMPDF
        $dompdf->loadHtml($html);

        // Opcional: Configurar el tamaño de papel y la orientación
        $dompdf->setPaper('A4', 'portrait'); // O 'landscape'

        // Renderizar el PDF
        $dompdf->render();

        // Visualizar el PDF en una nueva ventana en lugar de descargarlo
        return $dompdf->stream('orden_pago_'.$orden_pago->id.'.pdf', ['Attachment' => false]);
    }

}
