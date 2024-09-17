<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Registros\Marca;

class MarcaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $marca              =   new Marca();
        $marca->descripcion =   'NACIONAL';
        $marca->save();

        $marca              =   new Marca();
        $marca->descripcion =   'PACASMAYO';
        $marca->save();

        $marca              =   new Marca();
        $marca->descripcion =   'MOCHICA';
        $marca->save();

        $marca              =   new Marca();
        $marca->descripcion =   'SIDER';
        $marca->save();

        $marca              =   new Marca();
        $marca->descripcion =   'AREQUIPA';
        $marca->save();

        $marca              =   new Marca();
        $marca->descripcion =   'EUROTUBO';
        $marca->save();
    }
}
