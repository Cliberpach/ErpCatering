<?php

namespace App\Models\Herramientas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpresaFacturacion extends Model
{
    use HasFactory;
    protected $table = 'empresas_facturacion';

    protected $guarded = [''];
}
