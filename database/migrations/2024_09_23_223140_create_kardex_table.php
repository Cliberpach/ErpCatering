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
        Schema::create('kardex', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('almacen_id');
            $table->foreign('almacen_id')->references('id')->on('almacenes');
            
            $table->unsignedBigInteger('producto_id');
            $table->foreign('producto_id')->references('id')->on('productos');

            $table->decimal('cantidad', 16, 2)->unsigned();
            $table->decimal('stock_previo', 16, 2)->unsigned();
            $table->decimal('stock_posterior', 16, 2)->unsigned();

            $table->unsignedBigInteger('registro_compra_id')->nullable();
            $table->foreign('registro_compra_id')->references('id')->on('registros_compra');
            
            $table->unsignedBigInteger('registro_salida_id')->nullable();
            $table->foreign('registro_salida_id')->references('id')->on('registros_salida');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kardex');
    }
};
