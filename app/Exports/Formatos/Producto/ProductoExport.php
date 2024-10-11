<?php

namespace App\Exports\Formatos\Producto;

use App\Exports\Formatos\Producto\Hojas\DetallesSheet;
use App\Exports\Formatos\Producto\Hojas\InstruccionesSheet;
use App\Exports\Formatos\Producto\Hojas\ProductosSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;


class ProductoExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new ProductosSheet(),    // Primera hoja con datos de productos
            new InstruccionesSheet(), // Segunda hoja con subtítulo "INSTRUCCIONES"
            new DetallesSheet()
        ];
    }
}
