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
            $table->id(); // ID único
            $table->foreignId('colaborador_id')->constrained('colaboradores'); // Relación con la tabla colaboradores
            $table->foreignId('proyecto_id')->constrained('proyectos'); // Relación con la tabla proyectos
            $table->date('fecha_asistencia'); // Fecha de la asistencia
            $table->tinyInteger('feriado')->default(0); // 1 o 0 si es feriado
            $table->foreignId('feriado_id')->nullable()->constrained('feriados'); // Relación con la tabla feriados (si corresponde)
            $table->enum('estado', ['ASISTIO', 'TARDANZA', 'FALTA', 'PERMISO'])->default('ASISTIO'); // Estado de la asistencia
            $table->text('observacion')->nullable(); // Observaciones adicionales
            $table->timestamps(); // Tiempos de creación y actualización
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