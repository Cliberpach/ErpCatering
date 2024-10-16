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
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ProductosSheet implements  FromCollection, WithStyles, ShouldAutoSize, WithTitle,WithEvents
{

    protected $categoriasCount;
    protected $marcasCount;
    protected $unidadesCount;

    public function __construct()
    {
        $this->categoriasCount  =   Categoria::where('estado', 'ACTIVO')->count();
        $this->marcasCount      =   Marca::where('estado', 'ACTIVO')->count();
        $this->unidadesCount    =   DB::table('tablas_generales_detalles')
                                    ->where('estado', 'ACTIVO')
                                    ->count();    
    }

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

        return [];
    }

    public function afterSheet(\Maatwebsite\Excel\Events\AfterSheet $event)
 {
     $sheet = $event->sheet->getDelegate();


     $categoriasRange = "'DETALLES'!\$A\$2:\$A\$" . ($this->categoriasCount + 1);
     $marcasRange = "'DETALLES'!\$B\$2:\$B\$" . ($this->marcasCount + 1);
     $unidadesRange = "'DETALLES'!\$C\$2:\$C\$" . ($this->unidadesCount + 1);
     
     for ($row = 2; $row <= 100; $row++) {

         $sheet->getCell("D{$row}")->getDataValidation()
               ->setType(DataValidation::TYPE_LIST)
               ->setFormula1($categoriasRange)
               ->setShowDropDown(true);

         $sheet->getCell("E{$row}")->getDataValidation()
               ->setType(DataValidation::TYPE_LIST)
               ->setFormula1($marcasRange)
               ->setShowDropDown(true);

         $sheet->getCell("F{$row}")->getDataValidation()
               ->setType(DataValidation::TYPE_LIST)
               ->setFormula1($unidadesRange)
               ->setShowDropDown(true);
     }
 }

 public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $this->afterSheet($event);
            },
        ];
    }


  
    public function title(): string
    {
        return 'PRODUCTOS';
    }
}
