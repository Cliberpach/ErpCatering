<?php

namespace App\Models\TrabajoEquipo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistroTareaDetalle extends Model
{
    use HasFactory;

    protected $table = 'registros_tarea_detalle';

    protected $guarded = [''];
}
