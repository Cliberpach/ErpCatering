<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //======= ROL ADMINISTRADOR ========
        $adminRole      = Role::updateOrCreate(['name' => 'ADMIN']);
        $permissions    = Permission::all();
        $adminRole->givePermissionTo($permissions);

        //======= ASIGNANDO AL USUARIO 1=======
        $user   =   User::find(1);
        $user->assignRole($adminRole);

    }
}
