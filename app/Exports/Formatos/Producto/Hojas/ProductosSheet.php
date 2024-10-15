<?php

namespace App\Exports\Formatos\Producto\Hojas;

use App\Models\Registros\Categoria;
use App\Models\Registros\Marca;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\NamedRange; 
use PhpOffice\PhpSpreadsheet\Spreadsheet;

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
            ['PRODUCTO 2','12345678','12345678','PRODUCTO','NACIONAL','KILOGRAMO',1,1]
        ];

        return collect($contenido);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:H1')->getFont()->setBold(true); 
        $sheet->getStyle('A1:H1')->getFill()
              ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
              ->getStartColor()->setARGB('bcd9e7');

     
        $this->addDropdownToColumnD($sheet);
        return [];
    }

    private function addDropdownToColumnD(Worksheet $sheet)
    {
        $categorias = Categoria::where('estado', 'ACTIVO')
                       ->orderBy('descripcion')
                       ->pluck('descripcion')
                       ->toArray();

        $marcas     = Marca::where('estado', 'ACTIVO')
                       ->orderBy('descripcion')
                       ->pluck('descripcion')
                       ->toArray();

        $unidades_medida    =   DB::table('tablas_generales_detalles as tgd')
                                ->where('tgd.tabla_general_id', 1)
                                ->where('tgd.estado', 'ACTIVO')
                                ->orderBy('tgd.descripcion')
                                ->pluck('tgd.descripcion')
                                ->toArray();

        $categoriasLista        = implode(',', $categorias);
        $marcasLista            = implode(',', $marcas);

        $spreadsheet    =   $sheet->getParent();
        $detallesSheet  =   $spreadsheet->getSheetByName('DETALLES');
        
        if (!$detallesSheet) {
            $detallesSheet = $spreadsheet->createSheet();
            $detallesSheet->setTitle('DETALLES');
        }

        $startRow = 2;
        foreach ($unidades_medida as $index => $unidad) {
            $detallesSheet->setCellValue("C" . ($startRow + $index), $unidad);
        }
        
        
        foreach (range(2, 100) as $row) {
            $validation = $sheet->getCell("D$row")->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_STOP);
            $validation->setAllowBlank(true);
            $validation->setShowDropDown(true);
            $validation->setFormula1('"' . $categoriasLista . '"');
            $validation->setShowErrorMessage(true);
        }

        foreach (range(2, 100) as $row) {
            $validation = $sheet->getCell("E$row")->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_STOP);
            $validation->setAllowBlank(true);
            $validation->setShowDropDown(true);
            $validation->setFormula1('"' . $marcasLista . '"');
            $validation->setShowErrorMessage(true);
        }

        foreach (range(2, 100) as $row) {
            $validation = $sheet->getCell("F$row")->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_STOP);
            $validation->setAllowBlank(true);
            $validation->setShowDropDown(true);
            
            // Usar el rango de la columna Z para la validación
            $validation->setFormula1('Z1:Z' . count($unidades_medida));
            $validation->setShowErrorMessage(true);
        }
        
        
    }

    public function title(): string
    {
        return 'PRODUCTOS';
    }
}
