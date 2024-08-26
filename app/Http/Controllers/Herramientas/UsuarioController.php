<?php

namespace App\Http\Controllers\Herramientas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function index(){
        return view('herramientas.usuarios.index');
    }
}
