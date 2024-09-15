<?php

namespace App\Models\Logistica;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CotizacionCompra extends Model
{
    use HasFactory;
    protected $table = 'cotizacion_compra';

    protected $guarded = [''];
}
