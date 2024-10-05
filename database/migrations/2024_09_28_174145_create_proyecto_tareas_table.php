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

            $table->unsignedBigInteger('proyecto_id');
            $table->foreign('proyecto_id')->references('id')->on('proyectos');
            
            $table->string('nombre',150); 
            $table->date('fecha_inicio'); 
            $table->date('fecha_fin'); 
            $table->decimal('avance',16,2)->unsigned();
            $table->unsignedInteger('dias_faltantes');            
            $table->string('observacion',300)->nullable(); 
            $table->enum('estado', ['PENDIENTE', 'EN PROCESO','FINALIZADO','ANULADO'])->default('PENDIENTE');
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
