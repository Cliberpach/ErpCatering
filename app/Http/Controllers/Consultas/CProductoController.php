<?php

namespace App\Http\Controllers\Consultas;

use App\Exports\Consultas\ProductoExport;
use App\Http\Controllers\Controller;
use App\Models\Registros\Almacen;
use App\Models\Registros\Proyecto;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use Dompdf\Dompdf;
use Dompdf\Options;

class CProductoController extends Controller
{
    public function index(){
        $proyectos  =   Proyecto::where('estado','<>','ANULADO')->get();
        $almacenes  =   Almacen::where('estado','<>','ANULADO')->get();

        return view('consultas.producto.index',compact('proyectos','almacenes'));
    }

    public function getConsultaProducto(Request $request){

        $fecha_inicio   =   $request->get('fecha_inicio',null);
        $fecha_fin      =   $request->get('fecha_fin',null);
        $proyecto_id    =   $request->get('proyecto_id',null);
        $almacen_id     =   $request->get('almacen_id',null);

        $consulta = DB::table('kardex as k')
                                ->join('productos as p', 'p.id', '=', 'k.producto_id')
                                ->join('almacenes as a', 'a.id', '=', 'k.almacen_id')
                                ->select(
                                    'p.id as producto_id',
                                    'p.nombre as producto_nombre',
                                    'a.proyecto_id',
                                    DB::raw('(SELECT stock_previo FROM kardex WHERE producto_id = k.producto_id AND almacen_id = k.almacen_id ORDER BY created_at ASC LIMIT 1) as stock_inicial'),
                                    DB::raw('(SELECT stock_posterior FROM kardex WHERE producto_id = k.producto_id AND almacen_id = k.almacen_id ORDER BY created_at DESC LIMIT 1) as stock_final'),
                                    DB::raw('SUM(CASE WHEN k.stock_posterior > k.stock_previo THEN k.cantidad ELSE 0 END) as ingreso'),
                                    DB::raw('SUM(CASE WHEN k.stock_posterior < k.stock_previo THEN k.cantidad ELSE 0 END) as salida')
                                )
                                ->groupBy('p.id', 'p.nombre', 'a.proyecto_id', 'k.almacen_id','k.producto_id');

        if ($almacen_id) {
            $consulta->where('k.almacen_id', $almacen_id);
        }

        if ($proyecto_id) {
            $consulta->where('a.proyecto_id', $proyecto_id);
        }

        if ($fecha_inicio) {
            $consulta->where('k.created_at', '>=', $fecha_inicio . ' 00:00:00');
        }

        if ($fecha_fin) {
            $consulta->where('k.created_at', '<=', $fecha_fin . ' 23:59:59');
        }

        $consulta = $consulta->get();
        
        return DataTables::of($consulta)
                        ->make(true);
    
    }

    public function excel(Request $request)
    {
        $fecha_inicio   =   $request->query('fecha_inicio');
        $fecha_fin      =   $request->query('fecha_fin');
        $proyecto_id    =   $request->query('proyecto_id');
        $almacen_id     =   $request->get('almacen_id',null);
        $fecha_actual   =   Carbon::now();

        return Excel::download(new ProductoExport($fecha_inicio, $fecha_fin, $proyecto_id,$almacen_id), 'reporte_productos_'.$fecha_actual.'.xlsx');
    }

    public function pdf(Request $request){

        $fecha_inicio   =   $request->get('fecha_inicio',null);
        $fecha_fin      =   $request->get('fecha_fin',null);
        $proyecto_id    =   $request->get('proyecto_id',null);
        $almacen_id     =   $request->get('almacen_id',null);

        $proyecto   =   Proyecto::find($proyecto_id);
       
        $consulta = DB::table('kardex as k')
                    ->join('productos as p', 'p.id', '=', 'k.producto_id')
                    ->join('almacenes as a', 'a.id', '=', 'k.almacen_id')
                    ->select(
                        'p.id as producto_id',
                        'p.nombre as producto_nombre',
                        'a.proyecto_id',
                        DB::raw('(SELECT stock_previo FROM kardex WHERE producto_id = k.producto_id AND almacen_id = k.almacen_id ORDER BY created_at ASC LIMIT 1) as stock_inicial'),
                        DB::raw('(SELECT stock_posterior FROM kardex WHERE producto_id = k.producto_id AND almacen_id = k.almacen_id ORDER BY created_at DESC LIMIT 1) as stock_final'),
                        DB::raw('SUM(CASE WHEN k.registro_compra_id IS NOT NULL THEN k.cantidad ELSE 0 END) as ingreso'),
                        DB::raw('SUM(CASE WHEN k.registro_salida_id IS NOT NULL THEN k.cantidad ELSE 0 END) as salida')
                    )
                    ->groupBy('p.id', 'p.nombre', 'a.proyecto_id', 'k.almacen_id','k.producto_id');

        if ($almacen_id) {
            $consulta->where('k.almacen_id', $almacen_id);
        }

        if ($proyecto_id) {
            $consulta->where('a.proyecto_id', $proyecto_id);
        }

        if ($fecha_inicio) {
            $consulta->where('k.created_at', '>=', $fecha_inicio . ' 00:00:00');
        }

        if ($fecha_fin) {
            $consulta->where('k.created_at', '<=', $fecha_fin . ' 23:59:59');
        }

        $consulta = $consulta->get();

        Carbon::setLocale('es');
        $fecha_impresion = Carbon::now();
        $fecha_impresion = $fecha_impresion->translatedFormat('l, d \d\e F \d\e\l Y');
        $fecha_impresion = strtoupper($fecha_impresion);


        $empresa    =   DB::select('select * from empresas as e
                        where e.id = 1')[0];
                
        // Configurar las opciones de DOMPDF si es necesario
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');

        // Instanciar el objeto DOMPDF
        $dompdf = new Dompdf($options);

        // Definir el contenido del PDF (HTML)
        $html = view('consultas.producto.pdf.pdf',
        compact('empresa','consulta','fecha_impresion','proyecto','fecha_inicio','fecha_fin'))
            ->render();

        // Cargar el HTML en DOMPDF
        $dompdf->loadHtml($html);

        // Opcional: Configurar el tamaño de papel y la orientación
        $dompdf->setPaper('A4', 'portrait'); // O 'landscape'

        // Renderizar el PDF
        $dompdf->render();

        // Visualizar el PDF en una nueva ventana en lugar de descargarlo
        return $dompdf->stream('reporte_maquinaria.pdf', ['Attachment' => false]);
    }
}
