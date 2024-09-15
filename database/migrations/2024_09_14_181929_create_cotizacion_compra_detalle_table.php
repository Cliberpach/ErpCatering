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
        Schema::create('cotizacion_compra_detalle', function (Blueprint $table) {

            $table->unsignedBigInteger('cotizacion_compra_id');
            $table->foreign('cotizacion_compra_id')->references('id')->on('cotizacion_compra');

            $table->unsignedBigInteger('producto_id');
            $table->foreign('producto_id')->references('id')->on('productos');

            $table->decimal('cantidad', 16, 2)->unsigned();

            $table->timestamps();
            $table->primary(['cotizacion_compra_id', 'producto_id']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cotizacion_compra_detalle');
    }
};
