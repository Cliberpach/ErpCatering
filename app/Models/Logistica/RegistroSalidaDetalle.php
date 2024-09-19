<?php

namespace App\Models\Logistica;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistroSalidaDetalle extends Model
{
    use HasFactory;
    protected $table = 'registros_salida_detalle';

    protected $guarded = [''];
}
