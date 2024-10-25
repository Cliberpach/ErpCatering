<?php

namespace App\Models\Finanzas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenPagoDetalle extends Model
{
    use HasFactory;

    protected $table = 'ordenes_pago_detalle';

    protected $guarded = [''];
}
