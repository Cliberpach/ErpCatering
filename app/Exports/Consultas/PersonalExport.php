<?php
namespace App\Exports\Consultas;

use App\Models\Registros\Proyecto;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PersonalExport implements FromCollection, ShouldAutoSize, WithStyles
{
    protected $fecha_inicio;
    protected $fecha_fin;
    protected $proyecto_id;

    public function __construct($fecha_inicio, $fecha_fin, $proyecto_id)
    {
        $this->fecha_inicio = $fecha_inicio;
        $this->fecha_fin = $fecha_fin;
        $this->proyecto_id = $proyecto_id;
    }

    public function collection()
    {

        $proyecto   = Proyecto::find($this->proyecto_id);
        if(!$proyecto){
            return ['EL PROYECTO NO EXISTE EN LA BD'];
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
                            DB::raw('IFNULL(COUNT(CASE WHEN rld.feriado = 0 AND rld.estado = "ASISTIO" THEN 1 END), "0") as no_feriados_trabajados'),
                            DB::raw('IFNULL(COUNT(CASE WHEN rld.feriado = 1 AND rld.estado = "ASISTIO" THEN 1 END), "0") as feriados_trabajados'),
                            DB::raw('IFNULL(COUNT(CASE WHEN rld.estado = "ASISTIO" THEN 1 END), "0") as total_dias_trabajados'),
                            DB::raw('FORMAT(c.pago_dia, 2) as pago_dia'),
                            'c.pago_mensual'    
                        )
                        ->where('c.estado','ACTIVO');

        if ($this->proyecto_id) {
            $consulta->where('rld.proyecto_id', $this->proyecto_id);
        }

        if ($this->fecha_inicio) {
            $consulta->where('rld.created_at', '>=', $this->fecha_inicio . ' 00:00:00');
        }

        if ($this->fecha_fin) {
            $consulta->where('rld.created_at', '<=', $this->fecha_fin . ' 23:59:59');
        }

        $consulta->groupBy('c.id', 'c.nombre', 'c.pago_mensual','c.pago_dia', 'td.descripcion', 'c.nro_documento', 'ca.descripcion');

        $data = $consulta->get();

        $data->prepend(['TIPO DOC', 'N° DOC', 'PERSONAL', 'CARGO', 'DÍAS TRABAJADOS', 'FERIADOS TRABAJADOS','TOTAL DÍAS TRABAJADOS', 'PAGO/DÍA', 'PAGO MENSUAL']);
        $data->prepend(['']);
        $data->prepend(['FECHA REPORTE:',Carbon::now(),'','USUARIO:',Auth::user()->name]);
        $data->prepend(['FECHA INICIO REPORTE:',$this->fecha_inicio,'','FECHA FIN REPORTE:',$this->fecha_fin]);
        $data->prepend(['PROYECTO:',$proyecto->nombre]);
        $data->prepend(['EMPRESA:','TU EMPRESA']);
        

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        $styleA1 = [
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFB0E0F0', // Azul claro
                ],
            ],
        ];

        $styleBold = [
            'font' => [
                'bold' => true,
            ],
        ];

        $sheet->setCellValue('A1', 'EMPRESA');
        $sheet->getStyle('A1:A3')->applyFromArray($styleBold);
        $sheet->getStyle( 'D3')->applyFromArray($styleBold);
        $sheet->getStyle( 'A4')->applyFromArray($styleBold);
        $sheet->getStyle( 'D4')->applyFromArray($styleBold);

        // Estilo para encabezados
        $styleHeaders = [
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFEFEFEF', // Color gris claro
                ],
            ],
        ];

        $sheet->getStyle('A6:I6')->applyFromArray($styleHeaders);
        $sheet->getStyle( 'A6:I6')->applyFromArray($styleA1);

    }
}
