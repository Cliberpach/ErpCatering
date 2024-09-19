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
        Schema::create('registros_salida_detalle', function (Blueprint $table) {
            $table->unsignedBigInteger('registro_salida_id');
            $table->foreign('registro_salida_id')->references('id')->on('registros_salida');
            
            $table->unsignedBigInteger('producto_id');
            $table->foreign('producto_id')->references('id')->on('productos');

            $table->decimal('cantidad', 16, 2)->unsigned();

            $table->timestamps();
            $table->primary(['registro_salida_id', 'producto_id']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registros_salida_detalle');
    }
};
