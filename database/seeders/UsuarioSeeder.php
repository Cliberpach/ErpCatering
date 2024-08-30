<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user                           =   new User();
        $user->colaborador_id           =   1;
        $user->name                     =   'ADMIN';
        $user->email                    =   'admin@gmail.com';
        $user->password                 =   Hash::make('123456789');
        $user->password_visible         =   '123456789';
        $user->save();
    }
}
