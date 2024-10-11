<?php

namespace App\Exports\Formatos\Producto\Hojas;

use App\Models\Registros\Categoria;
use App\Models\Registros\Marca;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DetallesSheet implements FromCollection, WithTitle,ShouldAutoSize,WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $categorias         =   Categoria::where('estado', 'ACTIVO')
                                ->pluck('descripcion');

        $marcas             =   Marca::where('estado', 'ACTIVO')
                                ->pluck('descripcion');

        $unidades_medida    =   DB::table('tablas_generales_detalles')
                                ->where('estado', 'ACTIVO')
                                ->pluck('descripcion'); 

        $maxCount           =   max($categorias->count(), $marcas->count(), $unidades_medida->count());

        $categorias         =   $categorias->pad($maxCount, null);
        $marcas             =   $marcas->pad($maxCount, null);
        $unidades_medida    =   $unidades_medida->pad($maxCount, null);

        $combined           =   $categorias->zip($marcas, $unidades_medida)->map(function ($item) {
            return [
                'CATEGORÍA'        => $item[0],
                'MARCAS'           => $item[1],
                'UNIDADES DE MEDIDA' => $item[2],
            ];
        });

        return $combined->prepend([
            'CATEGORÍA'             => 'CATEGORÍA',
            'MARCAS'                => 'MARCAS',
            'UNIDADES DE MEDIDA'    => 'UNIDADES DE MEDIDA'
        ]);

    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:C1')->getFont()->setBold(true); 
        $sheet->getStyle('A1:C1')->getFill()
              ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
              ->getStartColor()->setARGB('bcd9e7');

        return [];
    }

    public function title(): string
    {
        return 'DETALLES';
    }
}
