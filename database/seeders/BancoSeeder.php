<?php

namespace Database\Seeders;

use App\Models\Registros\Banco;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BancoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banco          =   new Banco();
        $banco->nombre  =   'BCP';
        $banco->save();

        $banco          =   new Banco();
        $banco->nombre  =   'INTERBANK';
        $banco->save();

        $banco          =   new Banco();
        $banco->nombre  =   'SCOTIABANK';
        $banco->save();

        $banco          =   new Banco();
        $banco->nombre  =   'BBVA';
        $banco->save();

        $banco          =   new Banco();
        $banco->nombre  =   'PICHINCHA';
        $banco->save();

        $banco          =   new Banco();
        $banco->nombre  =   'BANBIF';
        $banco->save();

    }
}
