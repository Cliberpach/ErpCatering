<?php

namespace App\Models\Compras;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistroCompraDetalle extends Model
{
    use HasFactory;
    protected $table = 'registros_compra_detalle';

    protected $guarded = [''];
}
