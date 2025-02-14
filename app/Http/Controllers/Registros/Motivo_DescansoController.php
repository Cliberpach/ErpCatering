<?php

namespace App\Http\Controllers\Registros;

use App\Http\Controllers\Controller;
use App\Models\Registros\Motivo_Descanso;
use App\Http\Requests\Registros\MotivoDescanso\Motivo_DescansoStoreRequest;
use App\Http\Requests\Registros\MotivoDescanso\Motivo_DescansoUpdateRequest;
use Illuminate\Http\Request;
use Exception;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class Motivo_DescansoController extends Controller
{
    public function index(){
        return view('registros.motivo_descanso.index');
    }

    public function getMotivoDescanso(Request $request){

        $motivos = DB::table('motivo_descanso as m')
                    ->select(
                        'm.id', 
                        'm.descripcion',
                        'm.estado',
                        'm.created_at as fecha_registro',
                        'm.updated_at as fecha_modificacion'
                    )
                    ->where('m.estado', 'ACTIVO')
                    ->get();

        return DataTables::of($motivos)
                ->make(true);
    }

    public function create(){
        return view('registros.motivo_descanso.create');
    }

    public function store(Motivo_DescansoStoreRequest $request){
        
        DB::beginTransaction();
        try {

            $motivo              = new Motivo_Descanso();
            $motivo->descripcion = Str::upper($request->get('descripcion'));
            $motivo->estado      = 'ACTIVO';
            $motivo->save();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'MOTIVO DE DESCANSO REGISTRADO']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function edit($id){
        $motivo = Motivo_Descanso::find($id);

        return view('registros.motivo_descanso.edit', compact('motivo'));
    }

    public function update(Motivo_DescansoUpdateRequest $request, $id){
        DB::beginTransaction();
        try {
          
            $motivo              = Motivo_Descanso::find($id);
            $motivo->descripcion = Str::upper($request->get('descripcion'));
            $motivo->save();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'MOTIVO DE DESCANSO ACTUALIZADO']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function destroy($id){
        DB::beginTransaction();
        try {
            $motivo         = Motivo_Descanso::find($id);
            $motivo->estado = 'ANULADO';
            $motivo->update();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'MOTIVO DE DESCANSO ELIMINADO']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }
}
