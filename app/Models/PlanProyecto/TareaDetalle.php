<?php

namespace App\Models\PlanProyecto;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TareaDetalle extends Model
{
    use HasFactory;
    protected $table = 'proyecto_tarea_detalles';

    protected $guarded = [''];
}
