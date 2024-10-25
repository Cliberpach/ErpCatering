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
        Schema::create('ordenes_pago', function (Blueprint $table) {
            $table->id();

            $table->string('moneda',100);

            $table->unsignedBigInteger('orden_compra_id');
            $table->foreign('orden_compra_id')->references('id')->on('ordenes_compra');

            $table->unsignedBigInteger('proveedor_id');
            $table->foreign('proveedor_id')->references('id')->on('proveedores');

            $table->string('proveedor_nombre', 200);
            $table->string('proveedor_tipo_documento', 200);
            $table->string('proveedor_nro_documento', 20);


            $table->unsignedBigInteger('banco_id');
            $table->foreign('banco_id')->references('id')->on('bancos');

            $table->string('banco_nombre',160);
            $table->string('nro_cuenta',40);
            $table->string('cci',40);
            $table->string('nro_cuenta_detraccion',40);


            $table->unsignedBigInteger('proyecto_id');
            $table->foreign('proyecto_id')->references('id')->on('proyectos');

            $table->string('proyecto_nombre', 260);


            $table->unsignedBigInteger('colaborador_registrador_id');
            $table->foreign('colaborador_registrador_id')->references('id')->on('colaboradores');

            $table->string('colaborador_registrador_nombre',260);

            $table->string('documento',100);
            $table->string('medio_pago',100);
            $table->string('observacion',200)->nullable();

            $table->decimal('subtotal',16,4)->unsigned();
            $table->decimal('monto_igv',16,4)->unsigned();
            $table->decimal('total',16,4)->unsigned();
          
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordenes_pago');
    }
};
