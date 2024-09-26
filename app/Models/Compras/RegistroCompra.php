<?php

namespace App\Models\Compras;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistroCompra extends Model
{
    use HasFactory;
    protected $table = 'registros_compra';

    protected $guarded = [''];
}
