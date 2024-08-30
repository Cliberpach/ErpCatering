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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('marca_id');
            $table->foreign('marca_id')->references('id')->on('marcas');
        
            $table->unsignedBigInteger('categoria_id');
            $table->foreign('categoria_id')->references('id')->on('categorias');

            $table->unsignedBigInteger('unidad_medida_id');
            $table->foreign('unidad_medida_id')->references('id')->on('tablas_generales_detalles');

            $table->string('nombre',200);
            $table->decimal('precio', 20, 2)->unsigned();
            $table->decimal('stock', 20, 2)->unsigned();
            $table->decimal('stock_minimo', 20, 2)->unsigned();

            $table->enum('estado', ['ACTIVO', 'ANULADO'])->default('ACTIVO');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
