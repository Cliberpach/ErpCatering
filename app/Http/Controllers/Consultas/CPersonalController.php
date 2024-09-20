<?php

namespace App\Http\Controllers\Consultas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CPersonalController extends Controller
{
    public function index(){
        return view('consultas.personal.index');
    }
}
