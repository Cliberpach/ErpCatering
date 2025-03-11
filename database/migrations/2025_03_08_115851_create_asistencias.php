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
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colaborador_id')->constrained('colaboradores');
            $table->foreignId('proyecto_id')->constrained('proyectos');
            $table->date('fecha');
            $table->time('hora_entrada')->nullable();
            $table->time('hora_salida')->nullable();
            $table->enum('estado', ['ASISTIO', 'TARDANZA', 'FALTA', 'PERMISO'])->default('ASISTIO');
            $table->text('observacion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void 
    {
        Schema::dropIfExists('asistencias');
    }
};