<?php

namespace App\Http\Controllers\Herramientas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Herramientas\Rol\RolStoreRequest;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class RolController extends Controller
{   
    public function index(){
        return view('herramientas.roles.index');
    }

    public function create(){
        $permisos   =   Permission::all();

        return view('herramientas.roles.create',compact('permisos'));
    }

    public function getRoles(Request $request){
        $usuarios = Role::where('estado','ACTIVO')
                    ->select('id','name as nombre','created_at as fecha_registro')
                    ->get();

        return DataTables::of($usuarios)
                ->make(true);
    }

    public function edit($id){

        //========== OBTENER EL ROL Y SUS PERMISOS ======
        $rol                    =   Role::find($id);
        $permisos               =   Permission::all();
        $permisos_asignados     =   DB::select('select * from role_has_permissions as rhp
                                    where rhp.role_id = ?',[$id]); 
                                    
        return view('herramientas.roles.edit',compact('rol','permisos','permisos_asignados'));
    }


    public function store(RolStoreRequest $request){
        DB::beginTransaction();
        try {

            $lstPermisosAsignados   =   json_decode($request->get('lstPermisosAsignados'));
            
            $rol        =   new Role();
            $rol->name  =   Str::upper($request->get('nombre'));
            $rol->save();

            //======== INSERTANDO PERMISOS =========
            foreach ($lstPermisosAsignados as $permiso) {
                DB::insert('insert into role_has_permissions (permission_id, role_id) values (?, ?)',
                [$permiso, $rol->id]);
            }

            DB::commit();
            return response()->json(['success'=>true,'message'=>'ROL REGISTRADO CON ÉXITO']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }
}
