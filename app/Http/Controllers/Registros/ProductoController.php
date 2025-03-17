<?php

namespace App\Http\Controllers\Registros;

use App\Exports\Formatos\Producto\ProductoExport;
use App\Exports\Registros\Producto\ProductoExport as ListProductoExport;
use App\Http\Requests\Registros\Producto\ProductoStoreRequest;
use App\Http\Requests\Registros\Producto\ProductoUpdateRequest;
use App\Models\Registros\Producto;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Registros\Producto\ProductoImportExcelRequest;
use App\Imports\Registros\Producto\ProductoImport;
use App\Models\Registros\Marca;
use App\Models\Registros\Categoria;
use Carbon\Carbon;
use Exception;
use Throwable;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Maatwebsite\Excel\Facades\Excel;


class ProductoController extends Controller
{
    public function index(){
        $marcas             =   Marca::where('estado','ACTIVO')->get();
        $categorias         =   Categoria::where('estado','ACTIVO')->get();

        return view('registros.productos.index',compact('marcas','categorias'));
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
        $tipo_producto  =   $request->get('tipo_producto');

        $productos = DB::table('productos as p')
                    ->leftJoin('almacen_productos as ap', function($join) {
                        $join->on('ap.producto_id', '=', 'p.id')
                            ->where('ap.almacen_id', '=', 1); // Filtrar por almacen_id = 1
                    })
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
                        DB::raw('IFNULL(ap.stock, 0) as stock'), 
                        'p.stock_minimo',
                        'm.descripcion as marca_nombre',
                        'c.descripcion as categoria_nombre',
                        'tgd.descripcion as unidad_medida_nombre',
                        'p.estado'
                    )
                    ->where('p.estado', 'ACTIVO');
    

        if($categoria_id){
            $productos  =   $productos->where('p.categoria_id',$categoria_id);
        }

        if($marca_id){
            $productos  =   $productos->where('p.marca_id',$marca_id);
        }

        if($tipo_producto){
            $productos = $productos->where('tipo_producto',$tipo_producto);
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
            $producto->tipo_producto     =   $request->get('tipo_producto');
            $producto->unidad_medida_id =   $request->get('unidad_medida');
            $producto->precio           =   $request->get('precio');
            $producto->codigo_barras    =   $request->get('codigo_barras');
            $producto->codigo_interno   =   $request->get('codigo_interno');
            $producto->stock_minimo     =   $request->get('stock_minimo');
            $producto->save();

            if($request->get('codigo_barras')){
                $res_generar_barcode            =   ProductoController::generarCodigoBarras($request->get('codigo_barras'));
                $producto->ruta_codigo_barras   =   $res_generar_barcode->path;
                $producto->codigo_barras        =   $res_generar_barcode->codigo_barras;
                $producto->update();
            }

            DB::commit();
            return response()->json(['success'=>true,'message'=>'PRODUCTO REGISTRADO']);

        } catch (Throwable $th) {
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
            $producto->tipo_producto     =   $request->get('tipo_producto');
            $producto->unidad_medida_id =   $request->get('unidad_medida');
            $producto->precio           =   $request->get('precio');
            $producto->codigo_barras    =   $request->get('codigo_barras');
            $producto->codigo_interno   =   $request->get('codigo_interno');
            $producto->stock_minimo     =   $request->get('stock_minimo');
            $producto->update();

            if($request->get('codigo_barras')){
                $res_generar_barcode            =   ProductoController::generarCodigoBarras($request->get('codigo_barras'));
                $producto->ruta_codigo_barras   =   $res_generar_barcode->path;
                $producto->codigo_barras        =   $res_generar_barcode->codigo_barras;
                $producto->update();
            }

            DB::commit();
            return response()->json(['success'=>true,'message'=>'PRODUCTO ACTUALIZADO']);

        } catch (Throwable $th) {
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

        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function show($id){
        try {
            $producto   =   DB::select('select 
                            p.nombre as producto_nombre,
                            m.descripcion as marca_nombre,
                            ca.descripcion as categoria_nombre,
                            tgd.descripcion as unidad_medida_nombre,
                            p.precio as producto_precio,
                            p.tipo_producto as tipo_producto,
                            p.ruta_codigo_barras,
                            p.codigo_interno
                            from productos as p
                            inner join marcas as m on m.id = p.marca_id 
                            inner join categorias as ca on ca.id = p.categoria_id
                            inner join tablas_generales_detalles as tgd on tgd.id = p.unidad_medida_id 
                            where p.id = ? ',[$id])[0];

            $stocks     =   DB::select('select 
                            p.nombre as producto_nombre,
                            a.descripcion as almacen_nombre,
                            ap.stock as producto_stock
                            from almacen_productos as ap
                            inner join productos as p on p.id = ap.producto_id
                            inner join almacenes as a on a.id = ap.almacen_id
                            where ap.producto_id = ?',[$id]);


            return response()->json(['success'=>true,'producto'=>$producto,'stocks'=>$stocks]);
        } catch (Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }        
    }

    public function getProductosByAlmacen(Request $request){
        try {
            $categoria_id   =   $request->get('categoria_id');
            $marca_id       =   $request->get('marca_id');
            $almacen_id     =   $request->get('almacen_id'); 

            $productos  =   DB::table('productos as p')
                            ->leftJoin('almacen_productos as ap', function($join) use ($almacen_id) {
                                $join->on('ap.producto_id', '=', 'p.id')
                                    ->where('ap.almacen_id', '=', $almacen_id); 
                            })
                            ->leftJoin('marcas as m', 'm.id', '=', 'p.marca_id')
                            ->leftJoin('categorias as c', 'c.id', '=', 'p.categoria_id')
                            ->leftJoin('tablas_generales_detalles as tgd', 'tgd.id', '=', 'p.unidad_medida_id')
                            ->select(
                                'p.id', 
                                'p.marca_id',
                                'p.categoria_id',
                                'p.nombre',
                                'm.descripcion as marca_nombre',
                                'c.descripcion as categoria_nombre',
                                'ap.almacen_id',
                                DB::raw('IF(ap.stock is null, 0, ap.stock) as stock'), 
                                'tgd.descripcion as unidad_medida_nombre',
                                'p.estado'
                            )
                            ->where('p.estado', 'ACTIVO')
                            ->where('ap.stock','>', 0); ; 

            if ($categoria_id) {
                $productos = $productos->where('p.categoria_id', $categoria_id);
            }

            if ($marca_id) {
                $productos = $productos->where('p.marca_id', $marca_id);
            }

            $productos = $productos->get();

            return DataTables::of($productos)->make(true);
            
        } catch (Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public static function generarCodigoBarras($codigo_barras)
    {
     
        $carpeta_destino    =   public_path('img/codigos_barra/productos');

        if (!File::exists($carpeta_destino)) {
            File::makeDirectory($carpeta_destino, 0755, true);
        }

        // Generar el código de barras
        $generator  =   new BarcodeGeneratorPNG();
        $barcode    =   $generator->getBarcode($codigo_barras, $generator::TYPE_CODE_128);

        // Definir la ruta donde se guardará el archivo PNG en la carpeta pública
        $barcodePath = $carpeta_destino . '/' . $codigo_barras . '.png';

        // Guardar la imagen PNG del código de barras
        file_put_contents($barcodePath, $barcode);

        return (object)['barcode'=>$barcode,
        'codigo_barras'=>$codigo_barras,
        'path'=>'img/codigos_barra/productos/'.$codigo_barras.'.png']; 
    }

    public function descargarFormatoExcel(Request $request)
    {
        return Excel::download(new ProductoExport(), 'formato_import_productos.xlsx');
    }



    /*
    {#2060 // app\Http\Controllers\Registros\ProductoController.php:299
        +"con_errores": true
        +"listadoProductos": array:2 [
            0 => array:10 [
            "fila" => 2
            "nombre" => "PRODUCTO 1"
            "codigo_barras" => 12345678
            "codigo_interno" => 12345678
            "categoria" => "PRODUCTO"
            "marca" => "NACIONAL"
            "unidad_medida" => "UNIDAD"
            "precio" => 1
            "stock_minimo" => 1
            "error" => "El nombre 'PRODUCTO 1' ya existe en productos activos."
            ]
            1 => array:10 [
            "fila" => 3
            "nombre" => "PRODUCTO 2"
            "codigo_barras" => 12345678
            "codigo_interno" => 12345678
            "categoria" => "PRODUCTO"
            "marca" => "NACIONAL"
            "unidad_medida" => "UNIDAD"
            "precio" => 1
            "stock_minimo" => 1
            "error" => "El nombre 'PRODUCTO 2' ya existe en productos activos."
            ]
        ]
    }
    */ 
    public function importarProductosExcel(ProductoImportExcelRequest $request)
    {
        DB::beginTransaction();
        try {

            $import = new ProductoImport();

            Excel::import($import, $request->file('productos_import_excel'));

            $resultado = $import->getResultados();

            if($resultado->con_errores){
                return response()->json(['success'=>false,'message'=>'ERRORES EN EL EXCEL','resultado'=>$resultado]);
            }else{
                $lstProductos  =   $resultado->listadoProductos;
                foreach ($lstProductos as $producto_excel) {

                    $categoria      =   DB::select('select c.id 
                                        from categorias as c
                                        where c.estado = "ACTIVO" 
                                        and c.descripcion = ?',[$producto_excel['categoria']])[0];

                    $marca          =   DB::select('select m.id 
                                        from marcas as m
                                        where m.estado = "ACTIVO" 
                                        and m.descripcion = ?',[$producto_excel['marca']])[0];
                                        
                    $unidad_medida  =   DB::select('select tgd.id 
                                        from tablas_generales_detalles as tgd
                                        where tgd.estado = "ACTIVO" 
                                        and tgd.tabla_general_id = 1
                                        and tgd.descripcion = ?',[$producto_excel['unidad_medida']])[0];

                    $producto                   =   new Producto();
                    $producto->marca_id         =   $marca->id;
                    $producto->categoria_id     =   $categoria->id;
                    $producto->unidad_medida_id =   $unidad_medida->id;
                    $producto->nombre           =   mb_strtoupper($producto_excel['nombre'], 'UTF-8');
                    $producto->codigo_barras    =   $producto_excel['codigo_barras'];
                    $producto->codigo_interno   =   $producto_excel['codigo_interno'];
                    $producto->precio           =   $producto_excel['precio'];
                    $producto->stock_minimo     =   $producto_excel['stock_minimo'];
                    $producto->save();

                    if (!empty($producto_excel['codigo_barras'])) {
                        $res_generar_barcode            =   ProductoController::generarCodigoBarras($producto_excel['codigo_barras']);
                        $producto->ruta_codigo_barras   =   $res_generar_barcode->path;
                        $producto->update();
                    }
                }
                DB::commit();
                return response()->json(['success'=>true,'message'=>'EXCEL IMPORTADO CON ÉXITO','resultado'=>$resultado]);
            }

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage(),'line'=>$th->getLine()]);
        }
    }

    public function excel(Request $request){
        $categoriaId    =   $request->query('categoriaId');
        $marcaId        =   $request->query('marcaId');
        $fecha_actual   =   Carbon::now();

        return Excel::download(new ListProductoExport($categoriaId,$marcaId,$fecha_actual), 'lista_productos_'.$fecha_actual.'.xlsx');
    }
}
