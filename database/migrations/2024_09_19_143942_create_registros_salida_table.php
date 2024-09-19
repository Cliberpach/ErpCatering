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
        Schema::create('registros_salida', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('colaborador_id');
            $table->foreign('colaborador_id')->references('id')->on('colaboradores');

            $table->unsignedBigInteger('almacen_origen_id');
            $table->foreign('almacen_origen_id')->references('id')->on('almacenes');

            $table->unsignedBigInteger('almacen_destino_id');
            $table->foreign('almacen_destino_id')->references('id')->on('almacenes');
            
            $table->enum('estado', ['ACTIVO', 'ANULADO'])->default('ACTIVO');
           
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registros_salida');
    }
};
