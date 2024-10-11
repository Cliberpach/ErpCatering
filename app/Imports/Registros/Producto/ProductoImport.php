<?php

namespace App\Imports\Registros\Producto;

use App\Models\Registros\Categoria;
use App\Models\Registros\Marca;
use App\Models\Registros\Producto;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ProductoImport implements ToCollection, WithMultipleSheets
{

    protected $resultado    =   null;

     /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {
        $con_errores        =   false;
        $listadoProductos   =   [];
        $nombresProcesados  =   [];
        
        foreach ($rows as $key => $row) {

          
            // Validar encabezados
            if ($key === 0) {
                $headers = ["NOMBRE", "CÓDIGO BARRAS", "CÓDIGO INTERNO", "CATEGORÍA", "MARCA", "UNIDAD MEDIDA", "PRECIO", "STOCK MÍNIMO"];
                
                foreach ($headers as $index => $header) {
                    if (strtoupper(trim($row[$index])) !== $header) {
                        throw new Exception("FORMATO INCORRECTO DEL ARCHIVO EXCEL");
                    }
                }
                continue;
            }

            // Asignación de variables
            $nombre                 = $row[0];
            $codigo_barras          = $row[1];
            $codigo_interno         = $row[2];
            $categoria              = $row[3];
            $marca                  = $row[4];
            $unidad_medida          = $row[5];
            $precio                 = $row[6];
            $stock_minimo           = $row[7];
            $error                  = '';

            // Validaciones
            if (empty($nombre) || strlen(str_replace(' ', '', $nombre)) === 0) {
                continue;
            } elseif (strlen($nombre) > 200) {
                $con_errores = true;
                $error = "El producto '$nombre' tiene más de 200 caracteres.";
            } elseif (in_array($nombre, $nombresProcesados)) {
                $con_errores = true;
                $error = "El producto '$nombre' está repetido en el archivo Excel.";
            } elseif (Producto::where('nombre', $nombre)->where('estado', '<>', 'ANULADO')->exists()) {
                $con_errores = true;
                $error = "El producto '$nombre' ya existe en productos activos.";
            }

            if (!empty($codigo_barras) && strlen($codigo_barras) > 20) {
                $con_errores    =   true;
                $error          .=  " El código de barras tiene más de 20 caracteres.";
            }

            if (!empty($codigo_interno) && strlen($codigo_interno) > 20) {
                $con_errores    =   true;
                $error          .=  " El código interno tiene más de 20 caracteres.";
            }

            if (empty($categoria) || !DB::table('categorias as c')
                    ->where('c.descripcion', $categoria)
                    ->where('c.estado', 'ACTIVO')
                    ->exists()) {
                $con_errores = true;
                $error .= " La categoría '$categoria' no es válida o no existe.";
            }

            if (empty($marca) || !Marca::where('descripcion', $marca)->exists()) {
                $con_errores    =   true;
                $error          .=  " La marca '$marca' no es válida o no existe.";
            }

            if (empty($unidad_medida) || !DB::table('tablas_generales_detalles as tgd')
                    ->where('tgd.tabla_general_id', 1)
                    ->where('tgd.descripcion', $unidad_medida)
                    ->where('tgd.estado', 'ACTIVO')
                    ->exists()) {
                $con_errores = true;
                $error .= " La Unidad de medida '$unidad_medida' no es válida o no existe.";
            }

            if (!is_numeric($precio)) {
                $con_errores = true;
                $error .= " El precio debe ser un valor numérico.";
            }

            if (!is_numeric($stock_minimo)) {
                $con_errores = true;
                $error .= " El stock mínimo debe ser un valor numérico.";
            }

            $nombresProcesados[] = $nombre;

            $listadoProductos[] = [
                'fila'                  => $key + 1,
                'nombre'                => $nombre,
                'codigo_barras'         => $codigo_barras,
                'codigo_interno'        => $codigo_interno,
                'categoria'             => $categoria,
                'marca'                 => $marca,
                'unidad_medida'         => $unidad_medida,
                'precio'                => $precio,
                'stock_minimo'          => $stock_minimo,
                'error'                 => $error,
            ];
        }

        if (count($listadoProductos) === 0) {
            throw new Exception("EL EXCEL ESTÁ VACÍO!!!");
        }

        $this->resultado = (object)['con_errores' => $con_errores, 'listadoProductos' => $listadoProductos];
        return $this->resultado;
    }

    public function sheets(): array
    {
        return [
            'PRODUCTOS' => $this
        ];
    }

    public function getResultados()
    {
        return $this->resultado;
    }
}
