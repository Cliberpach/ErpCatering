<?php

namespace App\Http\Controllers\Consultas;

use App\Http\Controllers\Controller;
use App\Models\Registros\Proyecto;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use App\Exports\Consultas\PersonalExport;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use Dompdf\Dompdf;
use Dompdf\Options;
class CPersonalController extends Controller
{
    public function index(){
        $proyectos  =   Proyecto::where('estado','<>','ANULADO')->get();
        return view('consultas.personal.index',compact('proyectos'));
    }

    public function getConsultaPersonal(Request $request){

        $fecha_inicio   =   $request->get('fecha_inicio',null);
        $fecha_fin      =   $request->get('fecha_fin',null);
        $proyecto_id    =   $request->get('proyecto_id',null);

        $consulta   =   DB::table('registros_labor_detalle as rld')
                        ->join('colaboradores as c', 'c.id', 'rld.colaborador_id')
                        ->join('cargos as ca', 'ca.id', 'c.cargo_id')
                        ->join('tipos_documento as td', 'td.id', 'c.tipo_documento_id')
                        ->select(
                            'td.descripcion as tipo_documento',
                            'c.nro_documento',
                            'c.nombre as colaborador_nombre',
                            'ca.descripcion as cargo',
                            DB::raw("SEC_TO_TIME(IFNULL(SUM(TIME_TO_SEC(rld.tiempo_trabajado)), 0)) as tiempo_trabajado"),
                            DB::raw("LEAST(48, FLOOR(IFNULL(SUM(TIME_TO_SEC(rld.tiempo_trabajado) / 3600), 0))) as horas_trabajadas"),
                            DB::raw("ROUND(IFNULL(c.pago_hora, 0), 2) as pago_hora"),
                            DB::raw("ROUND(IFNULL(c.pago_hora, 0) * LEAST(48, FLOOR(IFNULL(SUM(TIME_TO_SEC(rld.tiempo_trabajado) / 3600), 0))), 2) as pago") 
                        );

        if($proyecto_id){
            $consulta->where('rld.proyecto_id', $proyecto_id ); 
        }

        if ($fecha_inicio) {
            $consulta->where('rld.created_at', '>=', $fecha_inicio . ' 00:00:00'); 
        }
                    
        if ($fecha_fin) {
            $consulta->where('rld.created_at', '<=', $fecha_fin . ' 23:59:59'); 
        }
                          
        $consulta->groupBy('c.id', 'c.nombre', 'c.pago_hora', 'td.descripcion', 'c.nro_documento', 'ca.descripcion');
                
        return DataTables::of($consulta->get())
                        ->make(true);
    
    }

    public function excel(Request $request)
    {
        $fecha_inicio   =   $request->query('fecha_inicio');
        $fecha_fin      =   $request->query('fecha_fin');
        $proyecto_id    =   $request->query('proyecto_id');
        $fecha_actual   =   Carbon::now();

        return Excel::download(new PersonalExport($fecha_inicio, $fecha_fin, $proyecto_id), 'reporte_personal_'.$fecha_actual.'.xlsx');
    }

    public function pdf(Request $request){

        $fecha_inicio   =   $request->get('fecha_inicio',null);
        $fecha_fin      =   $request->get('fecha_fin',null);
        $proyecto_id    =   $request->get('proyecto_id',null);

        $proyecto   =   Proyecto::find($proyecto_id);
        if (!$proyecto_id) {
            dd('EL PROYECTO NO EXISTE EN LA BD');
        }

        $consulta   =   DB::table('registros_labor_detalle as rld')
                        ->join('colaboradores as c', 'c.id', 'rld.colaborador_id')
                        ->join('cargos as ca', 'ca.id', 'c.cargo_id')
                        ->join('tipos_documento as td', 'td.id', 'c.tipo_documento_id')
                        ->select(
                            'td.descripcion as tipo_documento',
                            'c.nro_documento',
                            'c.nombre as colaborador_nombre',
                            'ca.descripcion as cargo',
                            DB::raw("SEC_TO_TIME(IFNULL(SUM(TIME_TO_SEC(rld.tiempo_trabajado)), 0)) as tiempo_trabajado"),
                            DB::raw("LEAST(48, FLOOR(IFNULL(SUM(TIME_TO_SEC(rld.tiempo_trabajado) / 3600), 0))) as horas_trabajadas"),
                            DB::raw("ROUND(IFNULL(c.pago_hora, 0), 2) as pago_hora"),
                            DB::raw("ROUND(IFNULL(c.pago_hora, 0) * LEAST(48, FLOOR(IFNULL(SUM(TIME_TO_SEC(rld.tiempo_trabajado) / 3600), 0))), 2) as pago") 
                        );

        if($proyecto_id){
            $consulta->where('rld.proyecto_id', $proyecto_id ); 
        }

        if ($fecha_inicio) {
            $consulta->where('rld.created_at', '>=', $fecha_inicio . ' 00:00:00'); 
        }
                    
        if ($fecha_fin) {
            $consulta->where('rld.created_at', '<=', $fecha_fin . ' 23:59:59'); 
        }
                          
        $consulta   =   $consulta->groupBy('c.id', 'c.nombre', 'c.pago_hora', 'td.descripcion', 
                        'c.nro_documento', 'ca.descripcion')->get();

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
        $html = view('consultas.personal.pdf.pdf',
        compact('empresa','consulta','fecha_impresion','proyecto','fecha_inicio','fecha_fin'))
            ->render();

        // Cargar el HTML en DOMPDF
        $dompdf->loadHtml($html);

        // Opcional: Configurar el tamaño de papel y la orientación
        $dompdf->setPaper('A4', 'portrait'); // O 'landscape'

        // Renderizar el PDF
        $dompdf->render();

        // Visualizar el PDF en una nueva ventana en lugar de descargarlo
        return $dompdf->stream('reporte_personal.pdf', ['Attachment' => false]);
    }
}
