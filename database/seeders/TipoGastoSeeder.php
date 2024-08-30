<?php

namespace Database\Seeders;

use App\Models\Herramientas\TablaGeneralDetalle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Herramientas\TablaGeneral;

class TipoGastoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tabla_general          =   new TablaGeneral();
        $tabla_general->nombre  =   'TIPOS DE GASTO';
        $tabla_general->simbolo =   'tipos_gasto';
        $tabla_general->save();

     
        $item                   = new TablaGeneralDetalle();
        $item->descripcion      = 'EPP (Equipo de Protección Personal)';
        $item->simbolo          = 'EPP';
        $item->tabla_general_id = $tabla_general->id; 
        $item->save();
      

    }
}
