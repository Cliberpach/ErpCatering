<?php

namespace Database\Seeders;

use App\Models\Registros\Regimen;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RegimenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $regimen                 =   new Regimen();
        $regimen->nombre   = 'Regimen 14 dias trabajo 7 descanso';
        $regimen->descripcion   = 'Este regimen laboral consiste en trabajar 14 dias y descansar 7 dias';
        $regimen->dias_trabajo    = 14;
        $regimen->dias_descanso   = 7;
        $regimen->save();

        $regimen                 =   new Regimen();
        $regimen->nombre   = 'Regimen 21 dias trabajo 7 descanso';
        $regimen->descripcion   = 'Este regimen laboral consiste en trabajar 21 dias y descansar 7 dias';
        $regimen->dias_trabajo    = 21;
        $regimen->dias_descanso   = 7;
        $regimen->save();

        $regimen                 =   new Regimen();
        $regimen->nombre   = 'Regimen 10 dias trabajo 4 descanso';
        $regimen->descripcion   = 'Este regimen laboral consiste en trabajar 10 dias y descansar 4 dias';
        $regimen->dias_trabajo    = 10;
        $regimen->dias_descanso   = 4;
        $regimen->save();

        $regimen                 =   new Regimen();
        $regimen->nombre   = 'Regimen 5 dias trabajo 2 descanso';
        $regimen->descripcion   = 'Este regimen laboral consiste en trabajar 5 dias y descansar 2 dias';
        $regimen->dias_trabajo    = 5;
        $regimen->dias_descanso   = 2;
        $regimen->save();

        $regimen                 =   new Regimen();
        $regimen->nombre   = 'Regimen 6 dias trabajo 1 descanso';
        $regimen->descripcion   = 'Este regimen laboral consiste en trabajar 6 dias y descansar 1 dia';
        $regimen->dias_trabajo    = 6;
        $regimen->dias_descanso   = 1;
        $regimen->save();

        $regimen                 =   new Regimen();
        $regimen->nombre   = 'Regimen 4 dias trabajo 3 descanso';
        $regimen->descripcion   = 'Este regimen laboral consiste en trabajar 4 dias y descansar 3 dias';
        $regimen->dias_trabajo    = 4;
        $regimen->dias_descanso   = 3;
        $regimen->save();
    }
}