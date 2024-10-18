<?php

namespace App\Http\Controllers\Registros;

use App\Http\Controllers\Controller;
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
                        'co.nombre',
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
    array:6 [ // app\Http\Controllers\Registros\ConductorController.php:26
        "_token" => "aamvFabq3HuzkI0aW08dE5FhpYaK1tnmCZAfssq3"
        "tipo_documento" => "1"
        "nro_documento" => "75608753"
        "nombre" => "LUIS DANIEL ALVA LUJAN"
        "licencia" => "4124sad"
        "telefono" => "945356916"
    ]
    */
    public function store(Request $request){
        DB::beginTransaction();
        try {

            $conductor                      =   new Conductor();
            $conductor->tipo_documento_id   =   $request->get('tipo_documento');
            $conductor->nro_documento       =   $request->get('nro_documento');
            $conductor->nombre              =   $request->get('nombre');
            $conductor->telefono            =   $request->get('telefono');
            $conductor->licencia            =   $request->get('licencia');
            $conductor->save();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'CONDUCTOR REGISTRADO']);
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    /*
    array:6 [ // app\Http\Controllers\Registros\ConductorController.php:88
        "_token"            => "aamvFabq3HuzkI0aW08dE5FhpYaK1tnmCZAfssq3"
        "tipo_documento"    => "1"
        "nro_documento"     => "75654124"
        "nombre"            => "LUIS DANIEL ALVA LUJAN"
        "licencia"          => "as213a"
        "telefono"          => "945356916"
    ]
    */
    public function update(Request $request,$id){
        DB::beginTransaction();
        try {
            $conductor                      =   Conductor::find($id);
            $conductor->tipo_documento_id   =   $request->get('tipo_documento');
            $conductor->nro_documento       =   $request->get('nro_documento');
            $conductor->nombre              =   $request->get('nombre');
            $conductor->telefono            =   $request->get('telefono');
            $conductor->licencia            =   $request->get('licencia');
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
