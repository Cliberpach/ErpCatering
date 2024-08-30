<?php

namespace Database\Seeders;

use App\Models\Herramientas\TipoDocumento;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Registros\Colaborador;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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

        //======= REGISTRO DE PERMISOS =======
        $items = [
            'panel_control.dashboard',
            'registros.colaborador',
            'registros.maquinaria',
            'registros.proyecto',
            'registros.almacen',
            'registros.categoria',
            'registros.marca',
            'registros.producto',
            'jornal.registro_labor',
            'jornal.consulta_labor',
            'trabajo_equipo.registro_tarea',
            'trabajo_equipo.consulta_tarea',
            'logistica.registro_compra',
            'logistica.registro_salida',
            'herramientas.usuarios',
            'herramientas.roles'
        ];
        
        foreach ($items as $item) {
            $permiso = new Permission();
            $permiso->name = $item;
            $permiso->save();
        }

        $adminRole = Role::updateOrCreate(['name' => 'ADMIN']);
        $permissions = Permission::all();
        $adminRole->givePermissionTo($permissions);
        $user->assignRole($adminRole);

    
    }
}
