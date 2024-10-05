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
        Schema::create('cotizacion_compra', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('colaborador_id');
            $table->foreign('colaborador_id')->references('id')->on('colaboradores');

            $table->unsignedBigInteger('supervisor_id')->nullable();
            $table->foreign('supervisor_id')->references('id')->on('colaboradores');

            $table->enum('estado', ['PENDIENTE', 'ANULADO', 'COMPRADO'])->default('PENDIENTE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cotizacion_compra');
    }
};
