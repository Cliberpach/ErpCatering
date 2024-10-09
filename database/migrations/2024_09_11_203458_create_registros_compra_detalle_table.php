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
        Schema::create('registros_compra_detalle', function (Blueprint $table) {
            
            $table->unsignedBigInteger('registro_compra_id');
            $table->foreign('registro_compra_id')->references('id')->on('registros_compra');
            
            $table->unsignedBigInteger('almacen_id');
            $table->foreign('almacen_id')->references('id')->on('almacenes');

            $table->unsignedBigInteger('producto_id');
            $table->foreign('producto_id')->references('id')->on('productos');

            $table->decimal('precio_soles',16,2)->unsigned();
            $table->decimal('precio_dolares',16,2)->unsigned();

            $table->decimal('precio_mas_igv_soles',16,2)->unsigned();
            $table->decimal('precio_mas_igv_dolares',16,2)->unsigned();

            $table->decimal('cantidad',16,2)->unsigned();

            $table->primary(['registro_compra_id','almacen_id', 'producto_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registros_compra_detalle');
    }
};
