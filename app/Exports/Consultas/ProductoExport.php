<?php

namespace App\Exports\Consultas;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use App\Models\Registros\Proyecto;
class ProductoExport implements FromCollection, ShouldAutoSize, WithStyles
{
    protected $fecha_inicio;
    protected $fecha_fin;
    protected $proyecto_id;

    protected $almacen_id;

    public function __construct($fecha_inicio, $fecha_fin, $proyecto_id,$almacen_id)
    {
        $this->fecha_inicio = $fecha_inicio;
        $this->fecha_fin = $fecha_fin;
        $this->proyecto_id = $proyecto_id;
        $this->almacen_id = $almacen_id;

    }

    public function collection()
    {

        $proyecto   = Proyecto::find($this->proyecto_id);

        $consulta = DB::table('kardex as k')
                    ->join('productos as p', 'p.id', '=', 'k.producto_id')
                    ->join('almacenes as a','a.id','k.almacen_id')
                    ->select(
                        'p.id as producto_id',
                        'p.nombre as producto_nombre',
                        DB::raw('(SELECT stock_previo FROM kardex WHERE producto_id = k.producto_id AND almacen_id = k.almacen_id ORDER BY created_at ASC LIMIT 1) as stock_inicial'),
                        DB::raw('SUM(CASE WHEN k.registro_compra_id IS NOT NULL THEN k.cantidad ELSE 0 END) as ingreso'),
                        DB::raw('SUM(CASE WHEN k.registro_salida_id IS NOT NULL THEN k.cantidad ELSE 0 END) as salida'),
                        DB::raw('(SELECT stock_posterior FROM kardex WHERE producto_id = k.producto_id AND almacen_id = k.almacen_id ORDER BY created_at DESC LIMIT 1) as stock_final')
                    )
                    ->groupBy('p.id', 'p.nombre', 'k.almacen_id');

        if ($this->almacen_id) {
            $consulta->where('k.almacen_id', $this->almacen_id);
        }

        if ($this->proyecto_id) {
            $consulta->where('a.proyecto_id', $this->proyecto_id);
        }

        if ($this->fecha_inicio) {
            $consulta->where('k.created_at', '>=', $this->fecha_inicio . ' 00:00:00');
        }

        if ($this->fecha_fin) {
            $consulta->where('k.created_at', '<=', $this->fecha_fin . ' 23:59:59');
        }

        $data = $consulta->get();


        $data->prepend(['ID', 'PRODUCTO', 'STOCK INICIO','INGRESO','SALIDA','STOCK FINAL']);
        $data->prepend(['']);
        $data->prepend(['FECHA REPORTE:',Carbon::now(),'','USUARIO:',Auth::user()->name]);
        $data->prepend(['FECHA INICIO REPORTE:',$this->fecha_inicio,'','FECHA FIN REPORTE:',$this->fecha_fin]);
        $data->prepend(['PROYECTO:',$proyecto?$proyecto->nombre:'-']);
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
