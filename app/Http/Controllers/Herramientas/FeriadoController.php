<?php

namespace App\Http\Controllers\Herramientas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Herramientas\Feriado\FeriadoStoreRequest;
use App\Http\Requests\Herramientas\Feriado\FeriadoUpdateRequest;
use App\Models\Herramientas\Feriado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class FeriadoController extends Controller
{
    public function index(){
        return view('herramientas.feriados.index');
    }

    public function getFeriados(Request $request){

        $feriados   =   DB::table('feriados as f')
                        ->select(
                            'f.*'
                        )
                        ->where('f.estado', 'ACTIVO');
    

        $feriados  =   $feriados->get();


        return DataTables::of($feriados)->make(true);
    }

    public function create(){
        return view('herramientas.feriados.create');
    }

/*
array:2 [ // app\Http\Controllers\Herramientas\FeriadoController.php:36
  "fecha"       => "2025-01-06"
  "descripcion" => "dia del ingeniero"
]
*/ 
    public function store(FeriadoStoreRequest $request){
        DB::beginTransaction();
        try {

            $feriado                =   new Feriado();
            $feriado->fecha         =   $request->get('fecha');
            $feriado->descripcion   =   mb_strtoupper($request->get('descripcion'), 'UTF-8');
            $feriado->anio          =   date('Y', strtotime($request->get('fecha')));
            $feriado->save();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'FERIADO REGISTRADO CON ÉXITO']);

        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function edit($id){

        $feriado    =   Feriado::find($id);

        return view('herramientas.feriados.edit',compact('feriado'));
    }

/*
array:2 [ // app\Http\Controllers\Herramientas\FeriadoController.php:69
  "fecha"       => "2025-01-14"
  "descripcion" => "DIA DEL COMPUTADOR2"
]
*/ 
    public function update(FeriadoUpdateRequest $request,$id){
        DB::beginTransaction();
        try {

            $feriado                =   Feriado::find($id);
            $feriado->fecha         =   $request->get('fecha');
            $feriado->descripcion   =   mb_strtoupper($request->get('descripcion'), 'UTF-8');
            $feriado->anio          =   date('Y', strtotime($request->get('fecha')));
            $feriado->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'FERIADO ACTUALIZADO CON ÉXITO']);

        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function destroy($id){
        DB::beginTransaction();
        try {
            $feriado                    =   Feriado::find($id);
            $feriado->estado            =   'ANULADO';
            $feriado->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'FERIADO ELIMINADO']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }
}
