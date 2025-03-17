<?php

namespace App\Models\Registros;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlatoDetalle extends Model
{
    use HasFactory;
    protected $table = 'plato_detalles';

    protected $guarded = [''];
}
