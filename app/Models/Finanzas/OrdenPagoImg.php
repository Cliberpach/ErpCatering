<?php

namespace App\Models\Finanzas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenPagoImg extends Model
{
    use HasFactory;
    protected $table = 'ordenes_pago_img_pagos';

    protected $guarded = [''];
}
