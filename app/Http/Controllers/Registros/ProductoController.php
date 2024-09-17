<?php

namespace App\Http\Controllers\Registros;

use App\Http\Requests\Registros\Producto\ProductoStoreRequest;
use App\Http\Requests\Registros\Producto\ProductoUpdateRequest;
use App\Models\Registros\Producto;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Registros\Marca;
use App\Models\Registros\Categoria;
use Exception;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductoController extends Controller
{
    public function index(){
        return view('registros.productos.index');
    }

    public function create(){
        $marcas             =   Marca::where('estado','ACTIVO')->get();
        $categorias         =   Categoria::where('estado','ACTIVO')->get();
        
        $unidades_medida    =   DB::select('select tgd.id,tgd.descripcion,tgd.simbolo
                                from tablas_generales_detalles as tgd
                                where tgd.tabla_general_id = 1');

        return view('registros.productos.create',compact('marcas','categorias','unidades_medida'));
    }

    public function getProductos(Request $request){

        $categoria_id   =   $request->get('categoria_id');
        $marca_id       =   $request->get('marca_id');

        $productos = DB::table('productos as p')
                    ->join('marcas as m', 'm.id', '=', 'p.marca_id')
                    ->join('categorias as c', 'c.id', '=', 'p.categoria_id')
                    ->join('tablas_generales_detalles as tgd', 'tgd.id', '=', 'p.unidad_medida_id')
                    ->select(
                        'p.id', 
                        'p.marca_id',
                        'p.categoria_id',
                        'p.unidad_medida_id',
                        'p.nombre',
                        'p.precio',
                        'p.stock',
                        'p.stock_minimo',
                        'm.descripcion as marca_nombre',
                        'c.descripcion as categoria_nombre',
                        'tgd.descripcion as unidad_medida_nombre',
                        'p.estado'
                    )
                    ->where('p.estado','ACTIVO');

        if($categoria_id){
            $productos  =   $productos->where('p.categoria_id',$categoria_id);
        }

        if($marca_id){
            $productos  =   $productos->where('p.marca_id',$marca_id);
        }

        $productos  =   $productos->get();


        return DataTables::of($productos)
                ->make(true);
    }

    public function store(ProductoStoreRequest $request){
        
        DB::beginTransaction();
        try {

            $producto                   =   new Producto();
            $producto->nombre           =   Str::upper($request->get('nombre'));
            $producto->marca_id         =   $request->get('marca');
            $producto->categoria_id     =   $request->get('categoria');
            $producto->unidad_medida_id =   $request->get('unidad_medida');
            $producto->precio           =   $request->get('precio');
            $producto->stock            =   $request->get('stock');
            $producto->stock_minimo     =   $request->get('stock_minimo');
            $producto->save();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'PRODUCTO REGISTRADO']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function edit($id){
        $marcas             =   Marca::where('estado','ACTIVO')->get();
        $categorias         =   Categoria::where('estado','ACTIVO')->get();
        
        $unidades_medida    =   DB::select('select tgd.id,tgd.descripcion,tgd.simbolo
                                from tablas_generales_detalles as tgd
                                where tgd.tabla_general_id = 1');

        $producto           =   Producto::find($id);

        return view('registros.productos.edit',compact('marcas','categorias','unidades_medida','producto'));
    }

    public function update(ProductoUpdateRequest $request, $id){
        DB::beginTransaction();
        try {
            $producto                   =   Producto::find($id);
            $producto->nombre           =   Str::upper($request->get('nombre'));
            $producto->marca_id         =   $request->get('marca');
            $producto->categoria_id     =   $request->get('categoria');
            $producto->unidad_medida_id =   $request->get('unidad_medida');
            $producto->precio           =   $request->get('precio');
            $producto->stock            =   $request->get('stock');
            $producto->stock_minimo     =   $request->get('stock_minimo');
            $producto->save();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'PRODUCTO ACTUALIZADO']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function destroy($id){
        DB::beginTransaction();
        try {
            $producto                    =   Producto::find($id);
            $producto->estado            =   'ANULADO';
            $producto->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'PRODUCTO ELIMINADO']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function getProductosByCategoria($categoria_id){
        try {
            $productos  =   DB::select('select 
                            p.id as producto_id,
                            p.marca_id,
                            p.categoria_id,
                            p.nombre as producto_nombre,
                            m.descripcion as marca_nombre,
                            c.descripcion as categoria_nombre,
                            p.stock as producto_stock,
                            tgd.descripcion as producto_unidad_medida
                            from productos as p
                            inner join marcas as m on m.id = p.marca_id
                            inner join categorias as c on c.id = p.categoria_id 
                            inner join tablas_generales_detalles as tgd on tgd.id = p.unidad_medida_id
                            where p.estado = "ACTIVO" and p.categoria_id = ?',
                            [$categoria_id]);

            return response()->json(['success'=>true,'productos'=>$productos]);
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }
}
