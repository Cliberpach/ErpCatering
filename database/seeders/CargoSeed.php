<?php

namespace Database\Seeders;

use App\Models\Registros\Cargo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CargoSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cargo_1                =   new Cargo();
        $cargo_1->descripcion   =   'ADMIN';
        $cargo_1->save();

        $cargo_1                =   new Cargo();
        $cargo_1->descripcion   =   'SUPERVISOR';
        $cargo_1->save();

        $cargo_1                =   new Cargo();
        $cargo_1->descripcion   =   'OPERARIO';
        $cargo_1->save();

        $cargo_1                =   new Cargo();
        $cargo_1->descripcion   =   'AYUDANTE';
        $cargo_1->save();

    }
}
