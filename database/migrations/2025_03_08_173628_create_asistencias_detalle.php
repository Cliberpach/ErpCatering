<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('asistencia_detalles', function (Blueprint $table) {
            $table->id(); // ID único
            $table->foreignId('asistencia_id')->constrained('asistencias'); // Relación con la tabla asistencias
            $table->foreignId('motivo_id')->nullable()->constrained('motivo_descanso'); // Relación con la tabla motivos
            $table->text('motivo_permiso')->nullable(); // Motivo de tardanza o falta justificada
            $table->time('hora_entrada')->nullable(); // Hora de entrada al turno
            $table->time('hora_entrada_break')->nullable(); // Hora de entrada después del break
            $table->time('hora_salida_break')->nullable(); // Hora de salida al final del break (fin del turno)
            $table->time('hora_salida')->nullable(); // Hora de salida del turno
            $table->time('retraso')->nullable(); // Tiempo de retraso, si aplica
            $table->time('adelanto')->nullable(); // Tiempo de retraso, si aplica
            $table->integer('horas_extra')->nullable();  // Tiempo de retraso, si aplica
            $table->string('horas_no_trabajadas')->nullable(); // Almacenar horas no trabajadas como string
            $table->string('tiempo_trabajado')->nullable(); // Almacenar tiempo trabajado como string            
            $table->enum('estado', ['ACTIVO', 'INACTIVO'])->default('ACTIVO'); // Estado del detalle de la asistencia
            $table->timestamps(); // Tiempos de creación y actualización
        });
        
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistencia_detalles');
    }
};
