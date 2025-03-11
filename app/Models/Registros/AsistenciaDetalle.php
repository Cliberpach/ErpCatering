<?php

namespace App\Models\Registros;

use App\Models\Herramientas\Feriado;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsistenciaDetalle extends Model
{
    use HasFactory;

    protected $fillable = [
        'asistencia_id',
        'hora_entrada',
        'hora_salida',
        'hora_entrada_break',
        'hora_salida_break',
        'tiempo_trabajado',
        'retraso', // Agregar el campo retraso aquí
        'adelanto',
        'horas_extra',
        'horas_no_trabajadas',
        'motivo_id',
        'motivo_permiso',
        'estado',
    ];
    

    /**
     * Relación inversa con Asistencias
     */
    public function asistencia()
    {
        return $this->belongsTo(Asistencias::class, 'asistencia_id'); // Aquí se especifica 'asistencia_id'
    }

    /**
     * Relación con Feriado
     */
    public function feriado()
    {
        return $this->belongsTo(Feriado::class);
    }
}

