<?php

namespace App\Http\Controllers\Logistica;

use App\Http\Controllers\Controller;
use App\Models\Registros\Categoria;
use Illuminate\Http\Request;

class RegistroCompraController extends Controller
{
    public function index(){
        return view('logistica.registro_compra.index');
    }

    public function create(){
        $categorias =   Categoria::where('estado','ACTIVO')->get();

        return view('logistica.registro_compra.create',compact('categorias'));
    }
}
