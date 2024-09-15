<?php

namespace App\Models\Logistica;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CotizacionCompraDetalle extends Model
{
    use HasFactory;
    protected $table = 'cotizacion_compra_detalle';

    protected $guarded = [''];
}
