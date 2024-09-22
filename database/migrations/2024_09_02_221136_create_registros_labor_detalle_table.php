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
        Schema::create('registros_labor_detalle', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('registro_labor_id');
            $table->foreign('registro_labor_id')->references('id')->on('registros_labor');

            $table->unsignedBigInteger('colaborador_id');
            $table->foreign('colaborador_id')->references('id')->on('colaboradores');

            $table->unsignedBigInteger('proyecto_id')->nullable();
            $table->foreign('proyecto_id')->references('id')->on('proyectos');

            $table->unsignedBigInteger('supervisor_id');
            $table->foreign('supervisor_id')->references('id')->on('colaboradores');


            $table->time('hora_entrada')->nullable();
            $table->time('hora_salida')->nullable();

            $table->time('tiempo_trabajado')->nullable();

            $table->longText('img_ruta')->nullable();
            $table->string('img_nombre',260)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registros_labor_detalle');
    }
};
