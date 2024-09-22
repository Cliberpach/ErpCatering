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
        Schema::create('registros_tarea', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('proyecto_id');
            $table->foreign('proyecto_id')->references('id')->on('proyectos');

            $table->unsignedBigInteger('maquinaria_id');
            $table->foreign('maquinaria_id')->references('id')->on('maquinarias');

            $table->unsignedBigInteger('supervisor_id');
            $table->foreign('supervisor_id')->references('id')->on('colaboradores');

            $table->string('observacion',300)->nullable();
            $table->unsignedInteger('cantidad_horas_viajes');
            $table->enum('estado', ['ACTIVO', 'ANULADO','FINALIZADO'])->default('ACTIVO');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registros_tarea');
    }
};
