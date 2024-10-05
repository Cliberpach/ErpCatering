<?php

namespace App\Models\Logistica;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequerimientoDetalle extends Model
{
    use HasFactory;
    protected $table = 'requerimiento_detalle';

    protected $guarded = [''];
}
