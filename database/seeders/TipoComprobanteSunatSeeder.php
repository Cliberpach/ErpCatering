<?php

namespace Database\Seeders;

use App\Models\Herramientas\TablaGeneral;
use App\Models\Herramientas\TablaGeneralDetalle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipoComprobanteSunatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //========= TABLA TIPOS DE COMPROBANTE =====
        $table_general_tipos_comprobante          =   new TablaGeneral();
        $table_general_tipos_comprobante->nombre  =   'COMPROBANTES SUNAT';
        $table_general_tipos_comprobante->simbolo =   'comprobantes_sunat';
        $table_general_tipos_comprobante->save();

        $item                   =   new TablaGeneralDetalle();
        $item->descripcion      =   'GUÍA DE REMISIÓN';
        $item->simbolo          =   '09';
        $item->tabla_general_id =   $table_general_tipos_comprobante->id; 
        $item->save();          

    }
}
