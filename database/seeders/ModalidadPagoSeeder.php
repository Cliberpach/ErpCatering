<?php

namespace Database\Seeders;

use App\Models\Registros\ModalidadPago;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ModalidadPagoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modalidad_pago =   new ModalidadPago();
        $modalidad_pago->descripcion    =   'CONTADO';
        $modalidad_pago->tipo           =   'CONTADO';
        $modalidad_pago->nro_dias       =   0;
        $modalidad_pago->save();

        $modalidad_pago =   new ModalidadPago();
        $modalidad_pago->descripcion    =   'CREDITO';
        $modalidad_pago->tipo           =   'CREDITO';
        $modalidad_pago->nro_dias       =   10;
        $modalidad_pago->save();

        $modalidad_pago =   new ModalidadPago();
        $modalidad_pago->descripcion    =   'CREDITO';
        $modalidad_pago->tipo           =   'CREDITO';
        $modalidad_pago->nro_dias       =   20;
        $modalidad_pago->save();

        $modalidad_pago =   new ModalidadPago();
        $modalidad_pago->descripcion    =   'CREDITO';
        $modalidad_pago->tipo           =   'CREDITO';
        $modalidad_pago->nro_dias       =   30;
        $modalidad_pago->save();

    }
}
