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
        Schema::create('proyecto_tarea_detalles', function (Blueprint $table) {

            $table->id();
            $table->unsignedBigInteger('proyecto_tarea_id');
            $table->foreign('proyecto_tarea_id')->references('id')->on('proyecto_tareas');

            $table->string('nombre',150); 
            $table->date('fecha_inicio'); 
            $table->date('fecha_fin'); 
            $table->string('observacion',300)->nullable(); 
            $table->enum('estado', ['PENDIENTE', 'FINALIZADO', 'ANULADO'])->default('PENDIENTE');

            $table->primary(['id', 'proyecto_tarea_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proyecto_tarea_detalles');
    }
};
