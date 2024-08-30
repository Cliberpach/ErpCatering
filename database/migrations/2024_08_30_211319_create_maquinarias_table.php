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
        Schema::create('maquinarias', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('tipo_gasto_id');
            $table->foreign('tipo_gasto_id')->references('id')->on('tablas_generales_detalles');

            $table->string('nombre',200);
            $table->decimal('costo_gasto',20,2)->unsigned();
            $table->string('observacion',260)->nullable();
            $table->enum('estado', ['ACTIVO', 'ANULADO'])->default('ACTIVO');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maquinarias');
    }
};
