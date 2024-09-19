<?php

namespace App\Http\Controllers\Logistica;

use App\Http\Controllers\Controller;
use App\Models\Registros\Almacen;
use App\Models\Registros\Categoria;
use App\Models\Registros\Marca;
use Illuminate\Http\Request;

class RegistroSalidaController extends Controller
{
    public function index(){
        return view('logistica.registro_salida.index');
    }

    public function create(){
        $categorias =   Categoria::where('estado','ACTIVO')->get();
        $marcas     =   Marca::where('estado','ACTIVO')->get();
        $almacenes  =   Almacen::where('estado','ACTIVO')->get();

        return view('logistica.registro_salida.create',compact('categorias','marcas','almacenes'));
    }
}
