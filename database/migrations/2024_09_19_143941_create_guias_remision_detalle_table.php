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
        Schema::create('guias_remision_detalle', function (Blueprint $table) {

            $table->unsignedBigInteger('guia_remision_id');
            $table->foreign('guia_remision_id')->references('id')->on('guias_remision');
            
            $table->unsignedBigInteger('producto_id');
            $table->foreign('producto_id')->references('id')->on('productos');
            
            $table->decimal('cantidad',15,2)->unsigned();
            $table->string('unidad');
            $table->string('descripcion');
            $table->string('codigo');

            $table->primary(['guia_remision_id','producto_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guias_remision_detalle');
    }
};
