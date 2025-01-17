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
        Schema::create('registros_labor', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('proyecto_id')->nullable();
            $table->foreign('proyecto_id')->references('id')->on('proyectos');

            $table->unsignedBigInteger('supervisor_id');
            $table->foreign('supervisor_id')->references('id')->on('colaboradores');

            $table->date('fecha_asistencia');

            $table->unsignedInteger('cant_trabajadores')->default(0);
            $table->string('observacion',300)->nullable();

            $table->unsignedBigInteger('feriado_id')->nullable();
            $table->foreign('feriado_id')->references('id')->on('feriados');

            $table->boolean('feriado')->default(false);

            $table->enum('estado', ['ACTIVO', 'ANULADO','FINALIZADO'])->default('ACTIVO');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registros_labor');
    }
};
