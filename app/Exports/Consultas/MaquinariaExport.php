<?php

namespace App\Exports\Consultas;

use Maatwebsite\Excel\Concerns\FromCollection;
use Auth;
use Carbon\Carbon;
use DB;
use Exception;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use App\Models\Registros\Proyecto;


class MaquinariaExport implements FromCollection, ShouldAutoSize, WithStyles
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

        $consulta   =   DB::table('registros_tarea as rt')
                        ->join('colaboradores as c', 'c.id', 'rt.supervisor_id')
                        ->join('maquinarias as m', 'm.id', 'rt.maquinaria_id')
                        ->select(
                            'm.nombre as maquinaria_nombre',
                            'c.nombre as supervisor_nombre',
                            DB::raw("IFNULL(SUM(rt.cantidad_horas_viajes), 0) as cantidad_horas_viajes"),
                            DB::raw("ROUND(IFNULL(SUM(rt.importe), 0), 2) as importe")
                        );

        if ($this->proyecto_id) {
            $consulta->where('rt.proyecto_id', $this->proyecto_id);
        }

        if ($this->fecha_inicio) {
            $consulta->where('rt.created_at', '>=', $this->fecha_inicio . ' 00:00:00');
        }

        if ($this->fecha_fin) {
            $consulta->where('rt.created_at', '<=', $this->fecha_fin . ' 23:59:59');
        }

        $consulta->groupBy('m.nombre', 'c.nombre');

        $data = $consulta->get();

        $data->prepend(['MAQUINARIA', 'SUPERVISOR', 'CANT HORAS/VUELTAS','IMPORTE']);
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

        $sheet->getStyle('A6:H6')->applyFromArray($styleHeaders);
        $sheet->getStyle( 'A6:H6')->applyFromArray($styleA1);

    }
}
