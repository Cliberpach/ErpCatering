<?php

namespace App\Http\Controllers\Registros;

use App\Http\Controllers\Controller;
use App\Http\Requests\Registros\Conductor\ConductorStoreRequest;
use App\Http\Requests\Registros\Conductor\ConductorUpdateRequest;
use App\Models\Herramientas\TipoDocumento;
use App\Models\Registros\Cargo;
use App\Models\Registros\Conductor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class ConductorController extends Controller
{
    public function index(){
        return view('registros.conductores.index');
    }

    public function getConductores(Request $request){
        $conductores = DB::table('conductores as co')
                    ->join('tipos_documento as td','td.id','co.tipo_documento_id')
                    ->select(
                        'co.id',
                        'co.nombre_completo as nombre',
                        'td.descripcion as tipo_documento',
                        'co.nro_documento',
                        'co.telefono',
                        'co.licencia'
                    )
                    ->where('co.estado','ACTIVO')
                    ->get();

        return DataTables::of($conductores)
                ->make(true);
    }

    public function create(){
        $tipos_documento    =   TipoDocumento::where('estado','ACTIVO')
                                ->where('id','<>',2)->get();


        return view('registros.conductores.create',compact('tipos_documento'));
    }

    public function edit($id){
        $tipos_documento    =   TipoDocumento::where('estado','ACTIVO')
                                ->where('id','<>',2)->get();

        $conductor          =   Conductor::find($id);

        return view('registros.conductores.edit',compact('conductor','tipos_documento'));
    }

    /*
    array:7 [ // app\Http\Controllers\Registros\ConductorController.php:67
    "_token"            => "40VPBenxpHS6nf6bF8zTNjfnxCLwLU3OGy5CRd1c"
    "tipo_documento"    => "1"
    "nro_documento"     => "80239830"
    "nombre"            => "CLIBER LESTER"
    "apellido"          => "PACHECO PERINANGO"
    "licencia"          => "4124sad"
    "telefono"          => "974585471"
    ]
    */
    public function store(ConductorStoreRequest $request){
        
        DB::beginTransaction();
        try {

            $conductor                      =   new Conductor();
            $conductor->tipo_documento_id   =   $request->get('tipo_documento');
            $conductor->nro_documento       =   $request->get('nro_documento');
            $conductor->nombre_completo     =   mb_strtoupper($request->get('nombre') . ' ' . $request->get('apellido'));
            $conductor->nombres             =   mb_strtoupper($request->get('nombre'));
            $conductor->apellidos           =   mb_strtoupper($request->get('apellido'));
            $conductor->telefono            =   $request->get('telefono');
            $conductor->licencia            =   mb_strtoupper($request->get('licencia'));
            $conductor->save();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'CONDUCTOR REGISTRADO']);
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    /*
    array:7 [ // app\Http\Controllers\Registros\ConductorController.php:67
        "_token"            => "40VPBenxpHS6nf6bF8zTNjfnxCLwLU3OGy5CRd1c"
        "tipo_documento"    => "1"
        "nro_documento"     => "80239830"
        "nombre"            => "CLIBER LESTER"
        "apellido"          => "PACHECO PERINANGO"
        "licencia"          => "4124sad"
        "telefono"          => "974585471"
    ]
    */
    public function update(ConductorUpdateRequest $request,$id){
        DB::beginTransaction();
        try {
            $conductor                      =   Conductor::find($id);
            $conductor->tipo_documento_id   =   $request->get('tipo_documento');
            $conductor->nro_documento       =   $request->get('nro_documento');
            $conductor->nombre_completo     =   mb_strtoupper($request->get('nombre') . ' ' . $request->get('apellido'));
            $conductor->nombres             =   mb_strtoupper($request->get('nombre'));
            $conductor->apellidos           =   mb_strtoupper($request->get('apellido'));
            $conductor->telefono            =   $request->get('telefono');
            $conductor->licencia            =   mb_strtoupper($request->get('licencia'));
            $conductor->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'CONDUCTOR ACTUALIZADO']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage(),'line'=>$th->getLine()]);
        }
    }

    public function destroy($id){
        DB::beginTransaction();
        try {
            $conductor                    =   Conductor::find($id);
            $conductor->estado            =   'ANULADO';
            $conductor->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'CONDUCTOR ELIMINADO']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

}
