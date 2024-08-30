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
        Schema::create('tablas_generales_detalles', function (Blueprint $table) {

            $table->id();
            $table->unsignedBigInteger('tabla_general_id');

            $table->string('descripcion',200);
            $table->string('simbolo',10);
            $table->enum('estado', ['ACTIVO', 'ANULADO'])->default('ACTIVO');

            $table->foreign('tabla_general_id')->references('id')->on('tablas_generales');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tablas_generales_detalles');
    }
};
