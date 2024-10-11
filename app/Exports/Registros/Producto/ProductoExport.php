<?php

namespace App\Exports\Registros\Producto;

use App\Models\Registros\Categoria;
use App\Models\Registros\Marca;
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
    protected $categoria_id;
    protected $marca_id;

    public function __construct($categoria_id, $marca_id)
    {
        $this->categoria_id = $categoria_id;
        $this->marca_id     = $marca_id;
    }

    public function collection()
    {

        $productos  =   DB::table('productos as p')
                        ->join('marcas as m', 'm.id', '=', 'p.marca_id')
                        ->join('categorias as c', 'c.id', '=', 'p.categoria_id')
                        ->join('tablas_generales_detalles as tgd', 'tgd.id', '=', 'p.unidad_medida_id')
                        ->select(
                            'p.nombre',
                            'p.codigo_barras',
                            'p.codigo_interno',
                            'c.descripcion as categoria_nombre',
                            'm.descripcion as marca_nombre',
                            'tgd.descripcion as unidad_medida_nombre',
                            'p.precio',
                            'p.stock_minimo',
                        )
                        ->where('p.estado', 'ACTIVO');


        if($this->categoria_id){
            $productos  =   $productos->where('p.categoria_id',$this->categoria_id);
        }

        if($this->marca_id){
            $productos  =   $productos->where('p.marca_id',$this->marca_id);
        }

        $data  =   $productos->get();

        $categoria_nombre   =   Categoria::find($this->categoria_id);
        $marca_nombre       =   Marca::find($this->marca_id);

        $data->prepend(['NOMBRE', 'CÓDIGO BARRAS', 'CÓDIGO INTERNO','CATEGORÍA','MARCA','UNIDAD MEDIDA','PRECIO','STOCK MÍNIMO']);
        $data->prepend(['']);
        $data->prepend(['CATEGORÍA:',$categoria_nombre?$categoria_nombre->descripcion:'','','MARCA:',$marca_nombre?$marca_nombre->descripcion:'']);
        $data->prepend(['FECHA REPORTE:',Carbon::now(),'','USUARIO:',Auth::user()->name]);
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
        $sheet->getStyle( 'D2')->applyFromArray($styleBold);
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

        $sheet->getStyle('A5:H5')->applyFromArray($styleHeaders);
        $sheet->getStyle( 'A5:H5')->applyFromArray($styleA1);

    }
}
