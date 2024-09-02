<?php

namespace App\Http\Controllers\Jornales;

use App\Http\Controllers\Controller;
use App\Models\Jornales\RegistroLabor;
use Illuminate\Http\Request;
use Exception;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RegistroLaborController extends Controller
{
    public function index(){
        return view('jornales.registro_labor.index');
    }

    public function getRegistrosLabor(Request $request){

        $registros_labor = DB::table('registros_labor as rl')
                            ->join('users as u', 'u.id', '=', 'rl.supervisor_id')
                            ->select(
                                'rl.id', 
                                'u.name as supervisor_nombre',
                                'rl.cant_trabajadores',
                                'rl.observacion',
                                'rl.created_at as fecha_registro',
                                'rl.created_at as observacion',
                            )
                            ->get();

        return DataTables::of($registros_labor)
                ->make(true);
    }

    public function store(Request $request){
        DB::beginTransaction();
        try {
            
            //======== VALIDANDO ROL DE USUARIO =====
            $rol    =   DB::select('select u.* from model_has_roles as mhr 
                        inner join users as u on u.id =  mhr.model_id
                        inner join roles as r on r.id =  mhr.role_id
                        where r.name = "SUPERVISOR" and u.id = ?',[Auth::user()->id]);
            
            if(count($rol) === 0){
                throw new Exception("Usted no cuenta con el rol de SUPERVISOR!!");
            }

            //========== REGISTRAR MAESTRO ASISTENCIA =======
            $registro_labor     =   new RegistroLabor();
            $registro_labor->supervisor_id  =   Auth::user()->id;
            $registro_labor->save();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'SE HA INICIADO LA ASISTENCIA!!']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }
}
