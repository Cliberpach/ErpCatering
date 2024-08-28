<?php

namespace App\Http\Controllers\Herramientas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Herramientas\Usuario\UsuarioStoreRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Facades\Session;
use App\Models\Herramientas\Colaborador;

class UsuarioController extends Controller
{
    public function index(){
        return view('herramientas.usuarios.index');
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
            $usuario    =   new User();
            $usuario->colaborador_id    =   $request->get('colaborador');
            $usuario->name            =   $request->get('nombre');
            $usuario->email            =   $request->get('correo');
            $usuario->password          =   Hash::make($request->get('password'));
            $usuario->password_visible  =   $request->get('password');
            $usuario->save();

            Session::flash('message_success', 'CLIENTE REGISTRADO CON ÉXITO.');
            return response()->json(['success'=>true,'message'=>'CLIENTE REGISTRADO']);

        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }
}
