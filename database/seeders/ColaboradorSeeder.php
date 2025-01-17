<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Registros\Colaborador;

class ColaboradorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colaborador                    =   new Colaborador();
        $colaborador->cargo_id          =   1;
        $colaborador->tipo_documento_id =   1;
        $colaborador->nro_documento     =   '99999999';
        $colaborador->nombre            =   'ADMIN';
        $colaborador->direccion         =   'AV UNION 123';
        $colaborador->telefono          =   '999999999';
        $colaborador->dias_descanso     =   10;
        $colaborador->dias_trabajo      =   30;
        $colaborador->pago_mensual      =   1200;
        $colaborador->pago_dia          =   30;
        $colaborador->save();

    }
}
