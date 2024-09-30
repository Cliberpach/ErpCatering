<?php

namespace App\Models\PlanProyecto;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarea extends Model
{
    use HasFactory;
    protected $table = 'proyecto_tareas';

    protected $guarded = [''];
}
