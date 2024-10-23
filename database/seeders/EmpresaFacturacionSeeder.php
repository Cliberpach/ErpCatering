<?php

namespace Database\Seeders;

use App\Models\Herramientas\EmpresaFacturacion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmpresaFacturacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $empresa_facturacion                        =   new EmpresaFacturacion();
        $empresa_facturacion->empresa_id            =   1;
        $empresa_facturacion->tipo_comprobante_id   =   96;
        $empresa_facturacion->descripcion           =   "GUÍA DE REMISIÓN";
        $empresa_facturacion->simbolo               =   "09";
        $empresa_facturacion->serie                 =   "T001";
        $empresa_facturacion->nro_inicio            =   1;
        $empresa_facturacion->iniciado              =   0;
        $empresa_facturacion->save();
    }
}
