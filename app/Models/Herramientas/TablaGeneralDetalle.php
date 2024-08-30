<?php

namespace App\Models\Herramientas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TablaGeneralDetalle extends Model
{
    use HasFactory;
    protected $table = 'tablas_generales_detalles';

    protected $guarded = [''];
}
