<?php

namespace Database\Seeders;

use App\Models\Registros\Motivo_Descanso;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Motivo_DescansoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $motivo_descanso                =   new Motivo_Descanso();
        $motivo_descanso->descripcion   =   'Descanso Medico';
        $motivo_descanso->save();

        $motivo_descanso                =   new Motivo_Descanso();
        $motivo_descanso->descripcion   =   'Vacaciones';
        $motivo_descanso->save();

        $motivo_descanso                =   new Motivo_Descanso();
        $motivo_descanso->descripcion   =   'Permiso Personal';
        $motivo_descanso->save();

        $motivo_descanso                =   new Motivo_Descanso();
        $motivo_descanso->descripcion   =   'Licencia por Maternidad';
        $motivo_descanso->save();

        $motivo_descanso                =   new Motivo_Descanso();
        $motivo_descanso->descripcion   =   'Licencia por Paternidad';
        $motivo_descanso->save();

        $motivo_descanso                =   new Motivo_Descanso();
        $motivo_descanso->descripcion   =   'Día Festivo';
        $motivo_descanso->save();
    }
}