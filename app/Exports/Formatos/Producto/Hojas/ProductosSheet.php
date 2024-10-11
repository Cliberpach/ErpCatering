<?php

namespace App\Exports\Formatos\Producto\Hojas;

use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class ProductosSheet implements  FromCollection, WithStyles, ShouldAutoSize, WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $contenido = [
            ['NOMBRE','CÓDIGO BARRAS','CÓDIGO INTERNO','CATEGORÍA','MARCA','UNIDAD MEDIDA','PRECIO','STOCK MÍNIMO'],
            ['PRODUCTO 1','12345678','12345678','PRODUCTO','NACIONAL','UNIDAD',1,1],
            ['PRODUCTO 2','12345678','12345678','PRODUCTO','NACIONAL','UNIDAD',1,1],
        ];

        return collect($contenido);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:H1')->getFont()->setBold(true); 
        $sheet->getStyle('A1:H1')->getFill()
              ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
              ->getStartColor()->setARGB('bcd9e7');

        return [];
    }

    public function title(): string
    {
        return 'PRODUCTOS';
    }
}
