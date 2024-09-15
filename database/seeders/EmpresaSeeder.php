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
        $empresa                =   new Empresa();
        $empresa->nombre        =   'TU_EMPRESA';
        $empresa->ruc           =   '12345678901';
        $empresa->direccion     =   'TU DIRECCION #123';
        $empresa->departamento  =   'LA LIBERTAD';
        $empresa->provincia     =   'TRUJILLO';
        $empresa->distrito      =   'TRUJILLO';
        $empresa->telefono      =   '945124574';
        $empresa->correo        =   'tucorreo@gmail.com';
        $empresa->img_ruta      =   'img/empresa/img_default.png';
        $empresa->img_nombre    =   'img_default.png';
        $empresa->save();

    }
}
