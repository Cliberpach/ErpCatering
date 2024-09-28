<?php

namespace App\Http\Controllers\Registros;

use App\Exports\Formatos\Marca\MarcaExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Registros\Marca\MarcaImportExcelRequest;
use App\Http\Requests\Registros\Marca\MarcaStoreRequest;
use App\Http\Requests\Registros\Marca\MarcaUpdateRequest;
use App\Imports\Registros\Marca\MarcaImport;
use App\Models\Registros\Marca;
use Illuminate\Http\Request;
use Exception;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class MarcaController extends Controller
{
    public function index(){
        return view('registros.marcas.index');
    }

    public function getMarcas(Request $request){

        $marcas = Marca::where('estado','ACTIVO')
                    ->select('id','descripcion as nombre','created_at as fecha_registro',
                    'updated_at as fecha_modificacion')
                    ->get();

        return DataTables::of($marcas)
                ->make(true);
    }

    public function create(){
        return view('registros.marcas.create');
    }


    public function store(MarcaStoreRequest $request){
        
        DB::beginTransaction();
        try {

            $marca                    =   new Marca();
            $marca->descripcion       =   Str::upper($request->get('descripcion'));
            $marca->save();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'MARCA REGISTRADA']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function update(MarcaUpdateRequest $request,$id){
        DB::beginTransaction();
        try {

            $marca                  =   Marca::find($id);
            $marca->descripcion     =   Str::upper($request->get('descripcion_edit'));
            $marca->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'MARCA ACTUALIZADA CON ÉXITO']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function destroy($id){
        DB::beginTransaction();
        try {
            $marca                    =   Marca::find($id);
            $marca->estado            =   'ANULADO';
            $marca->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'MARCA ELIMINADA']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function descargarFormatoExcel(Request $request)
    {
        return Excel::download(new MarcaExport(), 'formato_import_marcas.xlsx');
    }

    public function importarMarcasExcel(MarcaImportExcelRequest $request)
    {
        DB::beginTransaction();
        try {

            $import = new MarcaImport();

            Excel::import($import, $request->file('marcas_import_excel'));

            $resultado = $import->getResultados();

            if($resultado->con_errores){
                return response()->json(['success'=>false,'message'=>'ERRORES EN EL EXCEL','resultado'=>$resultado]);
            }else{
                $lstMarcas  =   $resultado->listadoMarcas;
                foreach ($lstMarcas as $marca_excel) {
                    $marca              =   new Marca();
                    $marca->descripcion =   $marca_excel['nombre']; 
                    $marca->save();
                }
                DB::commit();
                return response()->json(['success'=>true,'message'=>'EXCEL IMPORTADO CON ÉXITO','resultado'=>$resultado]);
            }

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function getListMarcas(){
        try {
            $marcas =   Marca::where('estado','ACTIVO')->get();
            return response()->json(['success'=>true,'lstMarcas'=>$marcas]);
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }
}
