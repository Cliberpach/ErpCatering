<?php

namespace Database\Seeders;

use App\Models\Herramientas\Empresa;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmpresaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $empresa                    =   new Empresa();
        $empresa->ruc               =   '20161515648';
        $empresa->razon_social      =   'TU_EMPRESA';
        $empresa->direccion         =   'TU DIRECCION #123';
        $empresa->telefono          =   '945124574';
        $empresa->correo            =   'tucorreo@gmail.com';
        $empresa->img_ruta          =   null;
        $empresa->img_nombre        =   null;
        $empresa->igv               =   18;
        $empresa->usuario_sol       =   'MODDATOS';
        $empresa->clave_sol         =   'MODDATOS';
        $empresa->usuario_api_guias =   '9e8eaf55-cf1d-4bf0-9837-0c3d897c08d5';
        $empresa->clave_api_guias   =   '3xSHGqcy5mglRIJzxx6eZw==';
        $empresa->save();

    }
}
