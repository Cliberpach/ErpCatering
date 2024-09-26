<?php

namespace Database\Seeders;

use App\Models\Registros\Proveedor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProveedorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $proveedor                      =   new Proveedor();
        $proveedor->tipo_documento_id   =   1;
        $proveedor->nro_documento       =   99999999;
        $proveedor->nombre              =   'PROVEEDORES VARIOS';
        $proveedor->save();
    }
}
