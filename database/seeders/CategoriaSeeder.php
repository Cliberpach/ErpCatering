<?php

namespace Database\Seeders;

use App\Models\Registros\Categoria;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoria              =   new Categoria();
        $categoria->descripcion =   'PRODUCTO';
        $categoria->save();
    }
}
