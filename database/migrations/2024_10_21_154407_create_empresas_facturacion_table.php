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
        Schema::create('empresas_facturacion', function (Blueprint $table) {
           
            $table->id();

            $table->unsignedBigInteger('empresa_id');
            $table->foreign('empresa_id')->references('id')->on('empresas');

            $table->unsignedBigInteger('tipo_comprobante_id');
            $table->foreign('tipo_comprobante_id')->references('id')->on('tablas_generales_detalles');

            $table->string('descripcion');
            $table->string('simbolo');

            $table->string('serie');

            $table->unsignedInteger('nro_inicio')->default(1);
            $table->boolean('iniciado')->default(false);
            $table->enum('estado', ['ACTIVO', 'ANULADO'])->default('ACTIVO');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresas_facturacion');
    }
};
