<?php

namespace App\Models\Compras;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CotizacionCompra extends Model
{
    use HasFactory;
    protected $table = 'cotizacion_compra';

    protected $guarded = [''];
}
