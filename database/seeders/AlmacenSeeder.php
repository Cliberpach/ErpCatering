<?php

namespace Database\Seeders;

use App\Models\Registros\Almacen;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AlmacenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $almacen                =   new Almacen();
        $almacen->descripcion   =   'CENTRAL';
        $almacen->save();
    }
}
