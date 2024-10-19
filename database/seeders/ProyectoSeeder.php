<?php

namespace Database\Seeders;

use App\Models\Registros\Proyecto;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProyectoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $proyecto                       =   new Proyecto();
        $proyecto->nombre               =   'PROYECTO PRINCIPAL';
        $proyecto->costo                =   1;
        $proyecto->avance_costo         =   0;
        $proyecto->diferencia           =   1;
        $proyecto->departamento_id      =   13;
        $proyecto->provincia_id         =   1301;
        $proyecto->distrito_id          =   130101;
        $proyecto->departamento_nombre  =   'LA LIBERTAD';
        $proyecto->provincia_nombre     =   'TRUJILLO';
        $proyecto->distrito_nombre      =   'TRUJILLO';
        $proyecto->ubigeo               =   130101;
        $proyecto->direccion            =   'DIRECCION PRINCIPAL';
        $proyecto->save();
    }
}
