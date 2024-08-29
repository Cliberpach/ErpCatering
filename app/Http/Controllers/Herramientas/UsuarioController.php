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
        
        return view('herramientas.usuarios.create',compact('colaboradores'));
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

            $usuario                    =   new User();
            $usuario->colaborador_id    =   $request->get('colaborador');
            $usuario->name              =   Str::upper($colaborador[0]->nombre);
            $usuario->email             =   Str::upper($request->get('correo'));
            $usuario->password          =   Hash::make($request->get('password'));
            $usuario->password_visible  =   $request->get('password');
            $usuario->save();

            Session::flash('message_success', 'USUARIO REGISTRADO CON ÉXITO.');
            DB::commit();
            return response()->json(['success'=>true,'message'=>'CLIENTE REGISTRADO']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function edit($id){

        $usuario        =   User::find($id);

        $colaboradores  = Colaborador::where('colaboradores.estado', 'ACTIVO')
        ->join('tipos_documento', 'colaboradores.tipo_documento_id', '=', 'tipos_documento.id')
        ->select('colaboradores.*', 'tipos_documento.descripcion as tipo_documento_nombre')
        ->get();        

        return view('herramientas.usuarios.edit',compact('usuario','colaboradores'));
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

            $usuario                        =   User::find($id);
            $usuario->colaborador_id        =   $request->get('colaborador');
            $usuario->name                  =   Str::upper($colaborador[0]->nombre);
            $usuario->email                 =   Str::upper($request->get('correo'));
            $usuario->password              =   Hash::make($request->get('password'));
            $usuario->password_visible      =   $request->get('password');
            $usuario->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'USUARIO ACTUALIZADO']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function destroy($id){
        
    }
}
