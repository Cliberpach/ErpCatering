<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('ruc', 11)->index(); 
            $table->string('razon_social', 150); 
            $table->string('direccion',150)->nullable(); 
            $table->string('telefono',20)->nullable(); 
            $table->string('correo',100)->nullable(); 
            $table->longText('img_ruta')->nullable();
            $table->string('img_nombre', 100)->nullable(); 
            $table->decimal('igv', 16, 2)->unsigned();

            $table->string('usuario_sol', 100)->nullable(); 
            $table->string('clave_sol', 100)->nullable(); 
            $table->string('usuario_api_guias', 100)->nullable(); 
            $table->string('clave_api_guias', 100)->nullable();
            $table->longText('certificado_ruta')->nullable(); 
            $table->longText('certificado_nombre')->nullable(); 

            $table->timestamps(); 
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
