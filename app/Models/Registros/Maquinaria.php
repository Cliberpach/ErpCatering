<?php

namespace App\Models\Registros;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maquinaria extends Model
{
    use HasFactory;
    protected $table = 'maquinarias';

    protected $guarded = [''];
}
