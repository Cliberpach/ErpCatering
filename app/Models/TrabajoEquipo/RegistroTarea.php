<?php

namespace App\Models\TrabajoEquipo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistroTarea extends Model
{
    use HasFactory;

    protected $table = 'registros_tarea';

    protected $guarded = [''];
}
