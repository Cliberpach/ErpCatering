<?php

namespace App\Models\Registros;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModalidadPago extends Model
{
    use HasFactory;
    protected $table = 'modalidades_pago';

    protected $guarded = [''];
}
