<?php

namespace Database\Seeders;

use App\Models\Registros\Producto;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $producto                   =   new Producto();
        $producto->nombre           =   'CEMENTO ROJO MOCHICA X 45 KG';
        $producto->categoria_id     =   2;
        $producto->marca_id         =   3;
        $producto->unidad_medida_id =   54;
        $producto->precio           =   29.50;
        $producto->stock            =   1;
        $producto->stock_minimo     =   1;
        $producto->save();
    }
}
