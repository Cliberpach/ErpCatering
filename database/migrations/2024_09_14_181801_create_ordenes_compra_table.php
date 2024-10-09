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
        Schema::create('ordenes_compra', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('proveedor_id');
            $table->foreign('proveedor_id')->references('id')->on('proveedores');

            $table->unsignedBigInteger('modalidad_pago_id');
            $table->foreign('modalidad_pago_id')->references('id')->on('modalidades_pago');

            $table->unsignedBigInteger('proyecto_id');
            $table->foreign('proyecto_id')->references('id')->on('proyectos');

            $table->string('documento',100);
            $table->string('direccion_obra',200);
            $table->string('observacion',200)->nullable();

            $table->unsignedBigInteger('persona_contacto_id');
            $table->foreign('persona_contacto_id')->references('id')->on('colaboradores');

            $table->date('fecha_entrega');
            $table->string('terminos_entrega',100);

            $table->string('moneda',100);
            $table->decimal('tipo_cambio',10,4)->unsigned()->nullable();

            $table->tinyInteger('precios_igv')->unsigned();
            $table->decimal('igv',16,4)->unsigned();

            $table->decimal('subtotal',16,4)->unsigned();
            $table->decimal('monto_igv',16,4)->unsigned();
            $table->decimal('total',16,4)->unsigned();

            $table->decimal('subtotal_soles',16,4)->unsigned();
            $table->decimal('monto_igv_soles',16,4)->unsigned();
            $table->decimal('total_soles',16,4)->unsigned();

            $table->enum('estado', ['PENDIENTE', 'ANULADO', 'FACTURADO'])->default('PENDIENTE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordenes_compra');
    }
};
