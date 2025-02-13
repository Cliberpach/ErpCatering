<?php

namespace App\Http\Controllers\Registros;

use App\Http\Controllers\Controller;
use App\Http\Requests\Registros\Horario\HorarioStoreRequest;
use App\Http\Requests\Registros\Horario\HorarioUpdateRequest;
use App\Models\Registros\Horario;
use Illuminate\Http\Request;
use Exception;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class HorarioController extends Controller
{
    public function index(){
        return view('registros.horario.index');
    }

    public function getHorario(Request $request){

        $horarios = DB::table('horarios as h')
                    ->select(
                        'h.id', 
                        'h.nombre_proyecto',
                        'h.hora_inicio',
                        'h.hora_final',
                        'h.descripcion',
                        'h.minutos_tolerancia',
                        'h.created_at as fecha_registro',
                        'h.updated_at as fecha_modificacion'
                    )
                    ->where('h.estado', 'ACTIVO')
                    ->get();

        return DataTables::of($horarios)
                ->make(true);
    }

    public function create(){
        return view('registros.horario.create');
    }

    public function store(HorarioStoreRequest $request){
        
        DB::beginTransaction();
        try {

            $horario                        = new Horario();
            $horario->nombre_proyecto       = Str::upper($request->get('nombre_proyecto'));
            $horario->hora_inicio           = $request->get('hora_inicio');
            $horario->hora_final            = $request->get('hora_final');
            $horario->descripcion           = $request->get('descripcion');
            $horario->minutos_tolerancia    = $request->get('minutos_tolerancia');
            $horario->estado                = 'ACTIVO';
            $horario->save();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'HORARIO REGISTRADO']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function edit($id){
        $horario = Horario::find($id);

        return view('registros.horario.edit', compact('horario'));
    }

    public function update(HorarioUpdateRequest $request, $id){
        DB::beginTransaction();
        try {
          
            $horario                        = Horario::find($id);
            $horario->nombre_proyecto       = Str::upper($request->get('nombre_proyecto'));
            $horario->hora_inicio           = $request->get('hora_inicio');
            $horario->hora_final            = $request->get('hora_final');
            $horario->descripcion           = $request->get('descripcion');
            $horario->minutos_tolerancia    = $request->get('minutos_tolerancia');
            $horario->save();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'HORARIO ACTUALIZADO']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function destroy($id){
        DB::beginTransaction();
        try {
            $horario            = Horario::find($id);
            $horario->estado    = 'ANULADO';
            $horario->update();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'HORARIO ELIMINADO']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }
}
