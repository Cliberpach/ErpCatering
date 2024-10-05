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

        //========= ROL SUPERVISOR ======
        $prefixes = ['registros', 'jornal', 'trabajo_equipo', 'requerimientos', 'plan_proyecto', 'consultas'];
        $permissions_supervisor = Permission::all()->filter(function ($permission) use ($prefixes) {
            foreach ($prefixes as $prefix) {
                if (str_starts_with($permission->name, $prefix)) {
                    return true;
                }
            }
            return false;
        });
        $supervisorRole = Role::create(['name' => 'SUPERVISOR']);
        $supervisorRole->givePermissionTo($permissions_supervisor);

        
        //======== ROL LOGÍSTICA =======
        $prefixes = ['registros', 'logistica', 'compras'];
        $permissions = Permission::all()->filter(function ($permission) use ($prefixes) {
            foreach ($prefixes as $prefix) {
                if (str_starts_with($permission->name, $prefix)) {
                    return true;
                }
            }
            return false;
        });
        $logisticaRole = Role::create(['name' => 'LOGISTICA']);
        $logisticaRole->givePermissionTo($permissions);
    }
}
