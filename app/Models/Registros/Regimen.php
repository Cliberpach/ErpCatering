<?php

namespace App\Models\Registros;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Regimen extends Model
{
    use HasFactory;
    protected $table = 'regimens';

    protected $guarded = [''];
}
