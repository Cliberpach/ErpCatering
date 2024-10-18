<?php

namespace App\Models\Registros;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conductor extends Model
{
    use HasFactory;
    protected $table = 'conductores';

    protected $guarded = [''];
}
