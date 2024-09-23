<?php

namespace App\Http\Controllers\Consultas;

use App\Exports\Consultas\MaquinariaExport;
use App\Http\Controllers\Controller;
use App\Models\Registros\Proyecto;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use Dompdf\Dompdf;
use Dompdf\Options;

class CMaquinariaController extends Controller
{
    public function index(){
        $proyectos  =   Proyecto::where('estado','<>','ANULADO')->get();
        return view('consultas.maquinaria.index',compact('proyectos'));
    }

    public function getConsultaMaquinaria(Request $request){

        $fecha_inicio   =   $request->get('fecha_inicio',null);
        $fecha_fin      =   $request->get('fecha_fin',null);
        $proyecto_id    =   $request->get('proyecto_id',null);

        $consulta   =   DB::table('registros_tarea as rt')
                        ->join('colaboradores as c', 'c.id', 'rt.supervisor_id')
                        ->join('maquinarias as m', 'm.id', 'rt.maquinaria_id')
                        ->select(
                            'm.nombre as maquinaria_nombre',
                            'c.nombre as supervisor_nombre',
                            DB::raw("IFNULL(SUM(rt.cantidad_horas_viajes), 0) as cantidad_horas_viajes"),
                            DB::raw("ROUND(IFNULL(SUM(rt.importe), 0), 2) as importe")
                        );


        if($proyecto_id){
            $consulta->where('rt.proyecto_id', $proyecto_id ); 
        }

        if ($fecha_inicio) {
            $consulta->where('rt.created_at', '>=', $fecha_inicio . ' 00:00:00'); 
        }
                    
        if ($fecha_fin) {
            $consulta->where('rt.created_at', '<=', $fecha_fin . ' 23:59:59'); 
        }
                          
        $consulta->groupBy('m.nombre', 'c.nombre');
          
        return DataTables::of($consulta->get())
                        ->make(true);
    
    }

    public function excel(Request $request)
    {
        $fecha_inicio   =   $request->query('fecha_inicio');
        $fecha_fin      =   $request->query('fecha_fin');
        $proyecto_id    =   $request->query('proyecto_id');
        $fecha_actual   =   Carbon::now();

        return Excel::download(new MaquinariaExport($fecha_inicio, $fecha_fin, $proyecto_id), 'reporte_maquinaria_'.$fecha_actual.'.xlsx');
    }

    public function pdf(Request $request){

        $fecha_inicio   =   $request->get('fecha_inicio',null);
        $fecha_fin      =   $request->get('fecha_fin',null);
        $proyecto_id    =   $request->get('proyecto_id',null);

        $proyecto   =   Proyecto::find($proyecto_id);
        if (!$proyecto_id) {
            dd('EL PROYECTO NO EXISTE EN LA BD');
        }

        $consulta   =   DB::table('registros_tarea as rt')
                        ->join('colaboradores as c', 'c.id', 'rt.supervisor_id')
                        ->join('maquinarias as m', 'm.id', 'rt.maquinaria_id')
                        ->select(
                            'm.nombre as maquinaria_nombre',
                            'c.nombre as supervisor_nombre',
                            DB::raw("IFNULL(SUM(rt.cantidad_horas_viajes), 0) as cantidad_horas_viajes"),
                            DB::raw("ROUND(IFNULL(SUM(rt.importe), 0), 2) as importe")
                        );

        if($proyecto_id){
            $consulta->where('rt.proyecto_id', $proyecto_id ); 
        }

        if ($fecha_inicio) {
            $consulta->where('rt.created_at', '>=', $fecha_inicio . ' 00:00:00'); 
        }
                    
        if ($fecha_fin) {
            $consulta->where('rt.created_at', '<=', $fecha_fin . ' 23:59:59'); 
        }
                          
        $consulta   =   $consulta->groupBy('m.nombre', 'c.nombre')->get();

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
        $html = view('consultas.maquinaria.pdf.pdf',
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
