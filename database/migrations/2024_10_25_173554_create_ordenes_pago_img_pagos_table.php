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
        Schema::create('ordenes_pago_img_pagos', function (Blueprint $table) {

            $table->id();
            
            $table->unsignedBigInteger('orden_pago_id');
            $table->foreign('orden_pago_id')->references('id')->on('ordenes_pago');

            $table->longText('img_nombre');
            $table->longText('img_ruta');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordenes_pago_img_pagos');
    }
};
