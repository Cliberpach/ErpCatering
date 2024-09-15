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
            $table->string('nombre', 255); 
            $table->string('ruc', 11)->index(); 
            $table->text('direccion')->nullable(); 
            $table->string('departamento', 200)->nullable(); 
            $table->string('provincia', 200)->nullable(); 
            $table->string('distrito', 200)->nullable(); 
            $table->string('telefono', 20)->nullable(); 
            $table->string('correo', 200)->nullable(); 
            $table->longText('img_ruta')->nullable();
            $table->string('img_nombre', 100)->nullable(); 
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
