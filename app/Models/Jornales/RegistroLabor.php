<?php

namespace App\Models\Jornales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistroLabor extends Model
{
    use HasFactory;
    protected $table = 'registros_labor';

    protected $guarded = [''];
}
