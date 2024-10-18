<?php

namespace Database\Seeders;

use App\Models\Herramientas\Configuracion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConfiguracionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configuracion              =   new Configuracion();
        $configuracion->nombre      =   'AMBIENTE GREENTER';
        $configuracion->propiedad   =   'BETA';
        $configuracion->save();
    }
}
