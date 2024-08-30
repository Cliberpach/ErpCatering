<?php

namespace App\Models\Registros;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    use HasFactory;
    protected $table = 'marcas';

    protected $guarded = [''];
}
