<?php

namespace Database\Seeders;

use App\Models\Registros\Horario;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HorarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $horario                =   new Horario();
        $horario->nombre_proyecto   = 'Proyecto Planta de Pacasmayo';
        $horario->hora_inicio   = '08:00';
        $horario->hora_final    = '17:00';
        $horario->descripcion   = 'Horario de trabajo regular';
        $horario->minutos_tolerancia = 10;
        $horario->save();

        $horario                =   new Horario();
        $horario->nombre_proyecto   = 'Proyecto Construcción Lima';
        $horario->hora_inicio   = '09:00';
        $horario->hora_final    = '18:00';
        $horario->descripcion   = 'Horario de trabajo regular';
        $horario->minutos_tolerancia = 15;
        $horario->save();

        $horario                =   new Horario();
        $horario->nombre_proyecto   = 'Proyecto Remodelación Arequipa';
        $horario->hora_inicio   = '07:00';
        $horario->hora_final    = '16:00';
        $horario->descripcion   = 'Horario de trabajo regular';
        $horario->minutos_tolerancia = 5;
        $horario->save();

        $horario                =   new Horario();
        $horario->nombre_proyecto   = 'Proyecto Expansión Trujillo';
        $horario->hora_inicio   = '06:00';
        $horario->hora_final    = '15:00';
        $horario->descripcion   = 'Horario de trabajo regular';
        $horario->minutos_tolerancia = 10;
        $horario->save();

        $horario                =   new Horario();
        $horario->nombre_proyecto   = 'Proyecto Innovación Cusco';
        $horario->hora_inicio   = '10:00';
        $horario->hora_final    = '19:00';
        $horario->descripcion   = 'Horario de trabajo regular';
        $horario->minutos_tolerancia = 20;
        $horario->save();

        $horario                =   new Horario();
        $horario->nombre_proyecto   = 'Proyecto Desarrollo Piura';
        $horario->hora_inicio   = '08:30';
        $horario->hora_final    = '17:30';
        $horario->descripcion   = 'Horario de trabajo regular';
        $horario->minutos_tolerancia = 10;
        $horario->save();

        $horario                =   new Horario();
        $horario->nombre_proyecto   = 'Proyecto Construcción Chiclayo';
        $horario->hora_inicio   = '07:30';
        $horario->hora_final    = '16:30';
        $horario->descripcion   = 'Horario de trabajo regular';
        $horario->minutos_tolerancia = 5;
        $horario->save();
    }
}