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
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('tipo_documento_id');
            $table->foreign('tipo_documento_id')->references('id')->on('tipos_documento');
            
            $table->unsignedBigInteger('banco_id');
            $table->foreign('banco_id')->references('id')->on('bancos');
            
            $table->string('nro_documento',20)->unique();
            $table->string('nombre',200);
            $table->string('direccion',150)->nullable();
            $table->string('telefono',20)->nullable();
            $table->string('correo',150)->nullable();

            $table->string('nro_cuenta',40)->nullable();
            $table->string('cci',40)->nullable();
            $table->string('nro_cuenta_detraccion',40)->nullable();

            $table->enum('estado', ['ACTIVO', 'ANULADO'])->default('ACTIVO');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proveedores');
    }
};
