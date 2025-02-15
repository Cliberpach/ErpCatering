<?php

namespace App\Http\Controllers\Registros;

use App\Http\Controllers\Controller;
use App\Models\Registros\Sedes;
use App\Http\Requests\Registros\Sedes\SedesStoreRequest;
use App\Http\Requests\Registros\Sedes\SedesUpdateRequest;
use Illuminate\Http\Request;
use Exception;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class SedesController extends Controller
{
    public function index(){
        return view('registros.sedes.index');
    }

    public function getSedes(Request $request){
        $sedes = DB::table('sedes as s')
                    ->select(
                        's.id', 
                        's.nombre',
                        's.direccion',
                        's.encargado',
                        's.estado',
                        's.created_at as fecha_registro',
                        's.updated_at as fecha_modificacion'
                    )
                    ->where('s.estado', 'ACTIVO')
                    ->get();

        return DataTables::of($sedes)
                ->make(true);
    }

    public function create(){
        return view('registros.sedes.create');
    }

    public function store(SedesStoreRequest $request){
        DB::beginTransaction();
        try {
            $sede             = new Sedes();
            $sede->nombre     = Str::upper($request->get('nombre'));
            $sede->direccion  = Str::upper($request->get('direccion'));
            $sede->encargado  = Str::upper($request->get('encargado'));
            $sede->estado     = 'ACTIVO';
            $sede->save();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'SEDE REGISTRADA']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function edit($id){
        $sede = Sedes::find($id);
        return view('registros.sedes.edit', compact('sede'));
    }

    public function update(SedesUpdateRequest $request, $id){
        DB::beginTransaction();
        try {
            $sede            = Sedes::find($id);
            $sede->nombre    = Str::upper($request->get('nombre'));
            $sede->direccion = Str::upper($request->get('direccion'));
            $sede->encargado = Str::upper($request->get('encargado'));
            $sede->save();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'SEDE ACTUALIZADA']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function destroy($id){
        DB::beginTransaction();
        try {
            $sede         = Sedes::find($id);
            $sede->estado = 'ANULADO';
            $sede->update();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'SEDE ELIMINADA']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }
}
