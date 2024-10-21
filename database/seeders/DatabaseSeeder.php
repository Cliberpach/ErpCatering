<?php

namespace Database\Seeders;

use App\Models\Herramientas\TipoDocumento;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Registros\Colaborador;


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

        $this->call(ConfiguracionSeeder::class);
        $this->call(DepartamentoSeeder::class);
        $this->call(ProvinciaSeeder::class);
        $this->call(DistritoSeeder::class);
        $this->call(EmpresaSeeder::class);
        $this->call(ProyectoSeeder::class);
        $this->call(AlmacenSeeder::class);
        $this->call(TablaGeneralSeeder::class);
        $this->call(TipoDocumentoSeeder::class);
        $this->call(ModalidadPagoSeeder::class);
        $this->call(ProveedorSeeder::class);
        $this->call(CargoSeed::class);
        $this->call(ColaboradorSeeder::class);
        $this->call(PermisoSeeder::class);
        $this->call(UsuarioSeeder::class);
        $this->call(RolSeeder::class);
        $this->call(CategoriaSeeder::class);
        $this->call(MarcaSeeder::class);
        $this->call(ProductoSeeder::class);
        $this->call(TipoGastoSeeder::class);
        $this->call(TipoComprobanteSunatSeeder::class);

    }
}
