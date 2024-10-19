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
        Schema::create('guias_remision', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('registro_salida_id')->nullable();
            $table->foreign('registro_salida_id')->references('id')->on('registros_salida');
            
            $table->unsignedBigInteger('conductor_id');
            $table->foreign('conductor_id')->references('id')->on('conductores');
            
            $table->unsignedBigInteger('vehiculo_id');
            $table->foreign('vehiculo_id')->references('id')->on('vehiculos');
            
            $table->string('codigo_traslado');
            $table->string('modo_traslado');
            $table->dateTime('fecha_traslado');
            $table->decimal('peso_total',15,2)->unsigned();
            $table->string('unidad_peso_total');
            $table->decimal('nro_bultos',15,2)->unsigned();

            $table->unsignedBigInteger('almacen_origen_id');
            $table->foreign('almacen_origen_id')->references('id')->on('almacenes');
            
            $table->unsignedBigInteger('almacen_destino_id');
            $table->foreign('almacen_destino_id')->references('id')->on('almacenes');
            
            $table->string('direccion_origen_nombre');
            $table->string('direccion_origen_ubigeo');

            $table->string('direccion_destino_nombre');
            $table->string('direccion_destino_ubigeo');

            $table->string('tipo_documento')->default('09');
            $table->string('version')->default('2022');

            $table->string('serie');
            $table->unsignedBigInteger('correlativo');

            $table->dateTime('fecha_emision');

            $table->unsignedBigInteger('empresa_emisora_id');
            $table->foreign('empresa_emisora_id')->references('id')->on('empresas');

            $table->string('destinatario_tipo_documento');
            $table->string('destintario_nro_documento');
            $table->string('destintario_razon_social');

            $table->longText('ticket')->nullable();
            $table->string('despatch_name', 200)->nullable();
            $table->string('response_success', 10)->nullable();
            $table->string('response_code', 10)->nullable();
            $table->longText('cdrzip_name')->nullable();
            $table->string('cdr_response_id', 10)->nullable();
            $table->string('cdr_response_code', 10)->nullable();
            $table->longText('cdr_response_description')->nullable();
            $table->longText('cdr_response_notes')->nullable();
            $table->longText('cdr_response_reference')->nullable();
            $table->longText('ruta_cdr')->nullable();
            $table->longText('ruta_xml')->nullable();
            $table->longText('ruta_qr')->nullable();
            $table->string('response_error_code', 10)->nullable();
            $table->longText('response_error_message')->nullable();

            $table->enum('estado', ['PENDIENTE', 'ANULADO','ACEPTADO','ENVIADO','ACEPTADO CON ERRORES','RECHAZADO'])
            ->default('PENDIENTE');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guias_remision');
    }
};
