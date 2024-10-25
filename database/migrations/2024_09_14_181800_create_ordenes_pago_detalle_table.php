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
        Schema::create('ordenes_pago_detalle', function (Blueprint $table) {

            $table->unsignedBigInteger('orden_pago_id');
            $table->foreign('orden_pago_id')->references('id')->on('ordenes_pago');

            $table->unsignedBigInteger('producto_id');
            $table->foreign('producto_id')->references('id')->on('productos');

            $table->decimal('cantidad', 16, 2)->unsigned();
            
            $table->decimal('precio_soles',16,2)->unsigned();
            $table->decimal('precio_dolares',16,2)->unsigned();

            $table->timestamps();
            $table->primary(['orden_pago_id', 'producto_id']);
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordenes_pago_detalle');
    }
};
