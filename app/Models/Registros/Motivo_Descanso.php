<?php

namespace App\Models\Registros;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Motivo_Descanso extends Model
{
    use HasFactory;
    protected $table = 'motivo_descanso';

    protected $guarded = [''];
}
