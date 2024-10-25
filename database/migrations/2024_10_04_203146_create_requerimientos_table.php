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
        Schema::create('requerimientos', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('proyecto_id');
            $table->foreign('proyecto_id')->references('id')->on('proyectos');

            $table->unsignedBigInteger('supervisor_id');
            $table->foreign('supervisor_id')->references('id')->on('colaboradores');

            $table->unsignedBigInteger('proveedor_id')->nullable();
            $table->foreign('proveedor_id')->references('id')->on('proveedores');

            $table->unsignedBigInteger('cotizacion_compra_id')->nullable();
            $table->foreign('cotizacion_compra_id')->references('id')->on('cotizacion_compra');


            $table->unsignedBigInteger('orden_compra_id')->nullable();
            $table->foreign('orden_compra_id')->references('id')->on('ordenes_compra');

            $table->unsignedBigInteger('orden_pago_id')->nullable(); 
            $table->foreign('orden_pago_id')->references('id')->on('ordenes_pago');

            $table->string('factura_atencion')->nullable();

            $table->date('fecha_atencion'); 

            $table->unsignedBigInteger('primer_producto_id');
            $table->foreign('primer_producto_id')->references('id')->on('productos');

            $table->enum('estado', ['PENDIENTE', 'COTIZADO', 'CON ORDEN COMPRA',
            'FACTURADO','ANULADO','CON ORDEN PAGO'])->default('PENDIENTE');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requerimientos');
    }
};
