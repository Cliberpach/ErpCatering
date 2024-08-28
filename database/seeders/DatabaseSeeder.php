<?php

namespace Database\Seeders;

use App\Models\Herramientas\TipoDocumento;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        //     'colaborador_id'=>1
        // ]);

        $tipo_doc               =   new TipoDocumento();
        $tipo_doc->descripcion  =   'DNI';
        $tipo_doc->save();

        $tipo_doc1               =   new TipoDocumento();
        $tipo_doc1->descripcion  =   'CARNET EXTRANJERÍA';
        $tipo_doc1->save();

    }
}
