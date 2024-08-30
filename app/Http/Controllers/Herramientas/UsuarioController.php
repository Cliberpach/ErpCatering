<?php

namespace App\Http\Controllers\Herramientas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Herramientas\Usuario\UsuarioStoreRequest;
use App\Http\Requests\Herramientas\Usuario\UsuarioUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Facades\Session;
use App\Models\Registros\Colaborador;
use Exception;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UsuarioController extends Controller
{
    public function index(){
        return view('herramientas.usuarios.index');
    }

    public function getUsuarios(Request $request){
        $usuarios = User::where('estado','ACTIVO')
                    ->select('id','name as nombre','email as correo','created_at as fecha_registro')
                    ->get();

        return DataTables::of($usuarios)
                ->make(true);
    }

    public function create(){

        $colaboradores = Colaborador::where('colaboradores.estado', 'ACTIVO')
                        ->join('tipos_documento', 'colaboradores.tipo_documento_id', '=', 'tipos_documento.id')
                        ->select('colaboradores.*', 'tipos_documento.descripcion as tipo_documento_nombre')
                        ->get();  
                        
        $roles          =   Role::where('estado','ACTIVO')->get();
        
        return view('herramientas.usuarios.create',compact('colaboradores','roles'));
    }

    public function store(UsuarioStoreRequest $request){
        
        DB::beginTransaction();
        try {

            //===== BUSCANDO COLABORADOR ======
            $colaborador                =   DB::select('select c.* from colaboradores as c
                                            where c.id = ?',[$request->get('colaborador')]);

            if(count($colaborador) === 0){
                throw new Exception("COLABORADOR NO ENCONTRADO EN LA BASE DE DATOS");
            }

            //====== BUSCANDO ROL ========
            $rol                        =   Role::find($request->get('rol'));
        
            $usuario                    =   new User();
            $usuario->colaborador_id    =   $request->get('colaborador');
            $usuario->name              =   Str::upper($colaborador[0]->nombre);
            $usuario->email             =   Str::upper($request->get('correo'));
            $usuario->password          =   Hash::make($request->get('password'));
            $usuario->password_visible  =   $request->get('password');
            $usuario->save();

            //======= ASIGNANDO ROL ========
            $usuario->assignRole($rol->name);

            Session::flash('message_success', 'USUARIO REGISTRADO CON ÉXITO.');
            DB::commit();
            return response()->json(['success'=>true,'message'=>'CLIENTE REGISTRADO']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function edit($id){

        $usuario    =   DB::table('users as u')
                        ->join('model_has_roles as mhr', 'u.id', '=', 'mhr.model_id')
                        ->select('u.*', 'mhr.role_id as rol_id')
                        ->where('u.id', $id)
                        ->first();

        $colaboradores  = Colaborador::where('colaboradores.estado', 'ACTIVO')
        ->join('tipos_documento', 'colaboradores.tipo_documento_id', '=', 'tipos_documento.id')
        ->select('colaboradores.*', 'tipos_documento.descripcion as tipo_documento_nombre')
        ->get();        

        $roles          =   Role::where('estado','ACTIVO')->get();

        return view('herramientas.usuarios.edit',compact('usuario','colaboradores','roles'));
    }

    public function update(UsuarioUpdateRequest $request,$id){
        DB::beginTransaction();
        try {

            //===== BUSCANDO COLABORADOR ======
            $colaborador                =   DB::select('select c.* from colaboradores as c
                                            where c.id = ?',[$request->get('colaborador')]);

            if(count($colaborador) === 0){
                throw new Exception("COLABORADOR NO ENCONTRADO EN LA BASE DE DATOS");
            }

            //====== BUSCANDO ROL ========
            $rol                        =   Role::find($request->get('rol'));

            $usuario                        =   User::find($id);
            $usuario->colaborador_id        =   $request->get('colaborador');
            $usuario->name                  =   Str::upper($colaborador[0]->nombre);
            $usuario->email                 =   Str::upper($request->get('correo'));
            $usuario->password              =   Hash::make($request->get('password'));
            $usuario->password_visible      =   $request->get('password');
            $usuario->update();

            //======== QUITAR ROL PREVIO ======
            $usuario->syncRoles([]);

            //======= ASIGNANDO nuevo ROL ========
            $usuario->assignRole($rol->name);

            DB::commit();
            return response()->json(['success'=>true,'message'=>'USUARIO ACTUALIZADO']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function destroy($id){
        DB::beginTransaction();
        try {
            $usuario                    =   User::find($id);
            $usuario->estado            =   'ANULADO';
            $usuario->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'USUARIO ELIMINADO']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }
}
