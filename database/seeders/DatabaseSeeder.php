<?php

namespace Database\Seeders;

use App\Models\Herramientas\TipoDocumento;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Herramientas\Colaborador;
use Illuminate\Support\Facades\Hash;


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

        $colaborador            =   new Colaborador();
        $colaborador->tipo_documento_id =   1;
        $colaborador->nro_documento     =   '99999999';
        $colaborador->nombre            =   'ADMIN';
        $colaborador->direccion         =   'AV UNION 123';
        $colaborador->telefono          =   '999999999';
        $colaborador->horas_semana      =   40;
        $colaborador->pago_semana       =   2141;
        $colaborador->save();

        $user                           =   new User();
        $user->colaborador_id           =   1;
        $user->name                     =   'ADMIN';
        $user->email                    =   'admin@gmail.com';
        $user->password                 =   Hash::make('123456789');
        $user->password_visible         =   '123456789';
        $user->save();
    
    }
}
