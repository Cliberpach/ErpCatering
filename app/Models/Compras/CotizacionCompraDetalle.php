<?php

namespace App\Models\Compras;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CotizacionCompraDetalle extends Model
{
    use HasFactory;
    protected $table = 'cotizacion_compra_detalle';

    protected $guarded = [''];
}
