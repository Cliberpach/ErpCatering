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
        Schema::create('proyecto_tareas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre',100); 
            $table->dateTime('fecha_inicio'); 
            $table->dateTime('fecha_fin'); 
            $table->decimal('avance',16,2)->unsigned();
            $table->unsignedInteger('dias_faltantes');            
            $table->string('observacion',300)->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proyecto_tareas');
    }
};
