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
        Schema::create('requerimiento_detalle', function (Blueprint $table) {

            $table->unsignedBigInteger('requerimiento_id');
            $table->foreign('requerimiento_id')->references('id')->on('requerimientos');

            $table->unsignedBigInteger('producto_id');
            $table->foreign('producto_id')->references('id')->on('productos');

            $table->decimal('cantidad', 16, 2)->unsigned();

            $table->primary(['requerimiento_id', 'producto_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requerimiento_detalle');
    }
};
