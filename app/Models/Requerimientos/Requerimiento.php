<?php

namespace App\Models\Requerimientos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requerimiento extends Model
{
    use HasFactory;
    protected $table = 'requerimientos';

    protected $guarded = [''];
}
