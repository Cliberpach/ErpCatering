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
        $proveedor          =   new Proveedor();
        $proveedor->nombre  =   'PROVEEDORES VARIOS';
        $proveedor->save();
    }
}
