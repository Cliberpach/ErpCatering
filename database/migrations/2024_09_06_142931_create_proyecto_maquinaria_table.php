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
        Schema::create('proyecto_maquinaria', function (Blueprint $table) {
            $table->unsignedBigInteger('proyecto_id');
            $table->foreign('proyecto_id')->references('id')->on('proyectos');

            $table->unsignedBigInteger('maquinaria_id');
            $table->foreign('maquinaria_id')->references('id')->on('maquinarias');

            $table->primary(['proyecto_id', 'maquinaria_id']);
            $table->enum('estado', ['ACTIVO', 'ANULADO','FINALIZADO'])->default('ACTIVO');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proyecto_maquinaria');
    }
};
