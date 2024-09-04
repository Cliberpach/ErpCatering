<?php

namespace App\Models\Registros;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProyectoPersonal extends Model
{
    use HasFactory;
    protected $table = 'proyecto_personal';

    protected $guarded = [''];
}
