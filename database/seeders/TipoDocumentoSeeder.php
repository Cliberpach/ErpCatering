<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Herramientas\TipoDocumento;

class TipoDocumentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipo_doc                   =   new TipoDocumento();
        $tipo_doc->descripcion      =   'DNI';
        $tipo_doc->save();

        $tipo_doc                   =   new TipoDocumento();
        $tipo_doc->descripcion      =   'RUC';
        $tipo_doc->save();
        
        $tipo_doc1                  =   new TipoDocumento();
        $tipo_doc1->descripcion     =   'CARNET EXTRANJERÍA';
        $tipo_doc1->save();

      

    }
}
