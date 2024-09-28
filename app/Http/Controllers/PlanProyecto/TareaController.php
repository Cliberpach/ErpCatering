<?php

namespace App\Http\Controllers\PlanProyecto;

use App\Http\Controllers\Controller;
use App\Models\Registros\Proyecto;
use Illuminate\Http\Request;

class TareaController extends Controller
{
    public function index(){
        $proyectos  =   Proyecto::where('estado','ACTIVO')->get();
        return view('plan_proyecto.tareas.index',compact('proyectos'));
    }

    public function create($id){
        $proyecto   =   Proyecto::find($id);

        return view('plan_proyecto.tareas.create',compact('proyecto'));
    }
}
