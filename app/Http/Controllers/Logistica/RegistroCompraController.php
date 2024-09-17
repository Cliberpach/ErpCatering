<?php

namespace App\Http\Controllers\Logistica;

use App\Http\Controllers\Controller;
use App\Http\Requests\Logistica\RegistroCompra\RegistroCompraStoreRequest;
use App\Models\Logistica\RegistroCompra;
use App\Models\Registros\Categoria;
use App\Models\Registros\Marca;
use App\Models\Registros\Proveedor;
use Auth;
use DB;
use Exception;
use Illuminate\Http\Request;


class RegistroCompraController extends Controller
{
    public function index(){
        return view('logistica.registro_compra.index');
    }

    public function create(){
        $categorias     =   Categoria::where('estado','ACTIVO')->get();
        $marcas         =   Marca::where('estado','ACTIVO')->get();
        $proveedores    =   Proveedor::all();

        return view('logistica.registro_compra.create',
        compact('categorias','marcas','proveedores'));
    }

    public function store(RegistroCompraStoreRequest $request){
        DB::beginTransaction();
        
        try {   
            $lstCompra                                 =   json_decode($request->get('lstCompra'));
            if(count($lstCompra) === 0){
                throw new Exception("EL DETALLE DE LA COMPRA ESTÁ VACÍO!!!");
            }

         

            $registro_compra                            =   new RegistroCompra();
            $registro_compra->colaborador_id_registro   =   Auth::user()->id;
            $registro_compra->proveedor_id              =   $request->get('proveedor');
            $registro_compra->fecha_emision             =   $request->get('fecha_emision');
            $registro_compra->fecha_entrega             =   $request->get('fecha_entrega');
            $registro_compra->serie                     =   $request->get('serie');
            $registro_compra->numero                    =   $request->get('numero');
            $registro_compra->precios_igv               =   $request->has('igv')?1:0;
            $registro_compra->observacion               =   $request->get('observacion');


        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public static function calcularMontos($lstCompra){
        
    }


}
