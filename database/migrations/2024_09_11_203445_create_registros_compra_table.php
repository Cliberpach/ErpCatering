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
        Schema::create('registros_compra', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('colaborador_registro_id');
            $table->foreign('colaborador_registro_id')->references('id')->on('colaboradores');

            //$table->unsignedBigInteger('colaborador_compra_id');
            //$table->foreign('colaborador_compra_id')->references('id')->on('colaboradores');

            $table->unsignedBigInteger('proveedor_id');
            $table->foreign('proveedor_id')->references('id')->on('proveedores');

            $table->timestamp('fecha_emision')->nullable();
            $table->timestamp('fecha_entrega')->nullable();

            $table->string('serie',20);
            $table->bigInteger('correlativo')->unsigned(); 

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

            $table->string('observacion',300)->nullable();

            $table->enum('estado', ['ACTIVO', 'ANULADO','FINALIZADO'])->default('ACTIVO');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registros_compra');
    }
};
