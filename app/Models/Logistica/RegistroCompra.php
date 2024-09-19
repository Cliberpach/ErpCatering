<?php

namespace App\Models\Logistica;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistroCompra extends Model
{
    use HasFactory;
    protected $table = 'registros_compra';

    protected $guarded = [''];
}
