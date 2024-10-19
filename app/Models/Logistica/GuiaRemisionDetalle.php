<?php

namespace App\Models\Logistica;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuiaRemisionDetalle extends Model
{
    use HasFactory;
    protected $table = 'guias_remision_detalle';

    protected $guarded = [''];
}
