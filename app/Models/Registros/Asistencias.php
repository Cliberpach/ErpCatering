<?php

namespace App\Models\Registros;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asistencias extends Model
{
    use HasFactory;

    protected $fillable = ['colaborador_id', 'proyecto_id', 'fecha_asistencia', 'estado', 'observacion'];

    /**
     * Relación uno a muchos con AsistenciaDetalle
     */
    public function detalles()
    {
        return $this->hasMany(AsistenciaDetalle::class, 'asistencia_id'); // Define la relación con la clave foránea 'asistencia_id'
    }
}

