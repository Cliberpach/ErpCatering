<?php

namespace App\Http\Controllers\Registros;

use App\Http\Controllers\Controller;
use App\Models\Registros\Proyecto;
use Illuminate\Http\Request;
use Exception;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AsistenciasController extends Controller
{
    public function index(){
        $proyecto = Proyecto::find(3); // Aquí puedes cambiar el ID por el que necesites
        
        return view('registros.asistencias.index', [
            'proyectoNombre' => $proyecto ? $proyecto->nombre : 'Proyecto Desconocido'
        ]);
    }
    
}