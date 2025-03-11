<?php

namespace App\Models\Registros;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asistencias extends Model
{
    use HasFactory;
    protected $table = 'asistencias';
    protected $guarded = [''];
}
