<?php

namespace App\Http\Controllers\Logistica;

use App\Http\Controllers\Controller;
use App\Http\Requests\Logistica\RegistroSalida\RegistroSalidaStoreRequest;
use App\Models\Logistica\RegistroSalida;
use App\Models\Logistica\RegistroSalidaDetalle;
use App\Models\Registros\Almacen;
use App\Models\Registros\Categoria;
use App\Models\Registros\Marca;
use Auth;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
class RegistroSalidaController extends Controller
{
    public function index(){
        return view('logistica.registro_salida.index');
    }

    public function create(){
        $categorias =   Categoria::where('estado','ACTIVO')->get();
        $marcas     =   Marca::where('estado','ACTIVO')->get();
        $almacenes  =   Almacen::where('estado','ACTIVO')->get();

        return view('logistica.registro_salida.create',compact('categorias','marcas','almacenes'));
    }

    public function getSalidas(Request $request){
        $salidas    =   DB::table('registros_salida as rs')
                        ->join('colaboradores as c', 'c.id', '=', 'rs.colaborador_id')
                        ->join('almacenes as ao', 'ao.id', '=', 'rs.almacen_origen_id')
                        ->join('almacenes as ad', 'ad.id', '=', 'rs.almacen_destino_id')
                        ->select(
                        DB::raw('CONCAT("RS-", rs.id) as simbolo'), 
                            'rs.id', 
                            'c.nombre as colaborador_nombre',
                            'rs.estado',
                            'ao.descripcion as almacen_origen_nombre',
                            'ad.descripcion as almacen_destino_nombre',
                            'rs.created_at as fecha_registro'
                        )
                        ->where('rs.estado','<>','ANULADO')
                        ->get();

        return DataTables::of($salidas)
                ->make(true);
    }

    public function store(RegistroSalidaStoreRequest $request){
        DB::beginTransaction();
        try {
            $lstSalida                              =   json_decode($request->get('lstSalida'));

            RegistroSalidaController::validacionLstSalida($lstSalida,$request->get('almacen_origen'));

            $registro_salida                        =   new RegistroSalida();
            $registro_salida->colaborador_id        =   Auth::user()->colaborador_id;
            $registro_salida->almacen_origen_id     =   $request->get('almacen_origen');
            $registro_salida->almacen_destino_id    =   $request->get('almacen_destino');
            $registro_salida->save();

            foreach ($lstSalida as $producto) {
                //======== OBTENIENDO STOCK ANTES DE LA COMPRA =========
                $stock_previo           =   0;
                $stock_posterior        =   0;

                $almacen_origen_producto_previo    =   DB::select('select
                                                        ap.stock 
                                                        from almacen_productos as ap
                                                        where ap.almacen_id = ?
                                                        and ap.producto_id = ?',
                                                        [$request->get('almacen_origen'),$producto->producto_id]);

                if(count($almacen_origen_producto_previo) === 0){
                    throw new Exception("NO SE ENCONTRÓ EL ALMACEN ORIGEN DEL PRODUCTO");
                }

                $stock_previo   =   $almacen_origen_producto_previo[0]->stock;

                //======== DECREMENTANDO STOCK EN ALMACÉN ORIGEN ========
                DB::update('UPDATE almacen_productos 
                SET stock = stock - ?, updated_at = ? 
                WHERE almacen_id = ? 
                AND producto_id = ?', 
                [$producto->cantidad, Carbon::now(), 
                $request->get('almacen_origen'), $producto->producto_id]);
                
                $almacen_origen_producto_posterior  =   DB::select('select
                                                        ap.stock 
                                                        from almacen_productos as ap
                                                        where ap.almacen_id = ?
                                                        and ap.producto_id = ?',
                                                        [$request->get('almacen_origen'),$producto->producto_id]);

                if(count($almacen_origen_producto_posterior) === 0){
                    throw new Exception("NO SE ENCONTRÓ EL ALMACEN ORIGEN DEL PRODUCTO");
                }

                $stock_posterior   =   $almacen_origen_producto_posterior[0]->stock;

                //======== INCREMENTANDO STOCK EN ALMACÉN DESTINO ========
                //========= VERIFICAR SI EXISTE EL PRODUCTO EN EL DESTINO =======
                $existe_producto =  DB::table('almacen_productos')
                                    ->where('almacen_id', $request->get('almacen_destino'))
                                    ->where('producto_id', $producto->producto_id)
                                    ->first();

                if ($existe_producto) {

                    DB::table('almacen_productos')
                    ->where('almacen_id', $request->get('almacen_destino'))
                    ->where('producto_id', $producto->producto_id)
                    ->update([
                        'stock' => DB::raw('stock + ' . $producto->cantidad),
                        'updated_at' => Carbon::now(), 
                    ]);

                } else {

                    DB::table('almacen_productos')->insert([
                        'almacen_id'    => $request->get('almacen_destino'),
                        'producto_id'   => $producto->producto_id,
                        'stock'         => $producto->cantidad,
                        'created_at'    => Carbon::now(),
                        'updated_at'    => Carbon::now(),
                    ]);

                }

                $registro_salida_detalle                        =   new RegistroSalidaDetalle();
                $registro_salida_detalle->registro_salida_id    =   $registro_salida->id;
                $registro_salida_detalle->producto_id           =   $producto->producto_id;
                $registro_salida_detalle->cantidad              =   $producto->cantidad;
                $registro_salida_detalle->save();

                KardexController::storeSalida($producto,$registro_salida,$stock_previo,$stock_posterior);
            }


            DB::commit();
            return response()->json(['success'=>true,'message'=>"MOVIMIENTO REGISTRADO!!"]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public static function validacionLstSalida($lstSalida,$almacen_origen_id){
        if(count($lstSalida) === 0){
            throw new Exception("EL DETALLE DE SALIDA ESTÁ VACÍO");
        }

        $almacen_origen =   DB::select('select 
                            a.id,
                            a.descripcion
                            from 
                            almacenes as a
                            where a.id = ?',[$almacen_origen_id]);

        if(count($almacen_origen) === 0){
            throw new Exception("NO SE ENCONTRÓ EL ALMACÉN DE ORIGEN EN LA BD");
        }

        foreach ($lstSalida as $producto) {
            $almacen_producto   =   DB::select('select 
                                    ap.almacen_id,
                                    ap.producto_id,
                                    ap.stock 
                                    from almacen_productos as ap
                                    where ap.almacen_id = ?
                                    and ap.producto_id = ?',[$almacen_origen_id,$producto->producto_id]);

            if(count($almacen_producto) === 0){
                throw new Exception("EL PRODUCTO:".' '.$producto->producto_nombre.',NO EXISTE EN EL ALMACÉN: '.$almacen_origen[0]->descripcion);
            }

            if($producto->cantidad > $almacen_producto[0]->stock){
                throw new Exception("PRODUCTO: " . $producto->producto_nombre . ", CANTIDAD: (" . $producto->cantidad . ") MAYOR AL STOCK DISPONIBLE: " . $almacen_producto[0]->stock);
            }
        }
        
    }

    public function validarCantidad($almacen_id,$producto_id,$cantidad){
        try {

            if(!$almacen_id){
                throw new Exception("NO SE ENCONTRÓ EL ALMACÉN DE ORIGEN EN LA CONSULTA");   
            }
            if(!$producto_id){
                throw new Exception("NO SE ENCONTRÓ EL PRODUCTO EN LA CONSULTA");   
            }

            $existe = DB::table('almacen_productos as ap')
                        ->where('ap.almacen_id', $almacen_id) 
                        ->where('ap.producto_id', $producto_id) 
                        ->exists();

            $stock      =   null;
            $message    =   null;
            $validacion =   false;
            if(!$existe){
                $stock  =   0;
            }else{
                $stock  =   DB::select ('select ap.stock,ap.almacen_id,ap.producto_id
                            from almacen_productos as ap
                            where 
                            ap.almacen_id = ?  
                            and ap.producto_id = ?',[$almacen_id,$producto_id])[0]->stock;
            }

            if($stock < $cantidad){
                $message    =   'STOCK INSUFICIENTE: '.$stock;
            }else{
                $message    =   'CANTIDAD VÁLIDA PARA EL STOCK: '.$stock;
                $validacion =   true;
            }

            return response()->json(['success'=>true,'message'=>$message,'validacion'=>$validacion]);

            
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function show($id){
        try {
            $registro_salida    =   DB::select('select 
                                    rs.id,
                                    c.nombre as colaborador_nombre,
                                    ao.descripcion almacen_origen_nombre,
                                    ad.descripcion as almacen_destino_nombre,
                                    rs.created_at as fecha_registro
                                    from registros_salida as rs
                                    inner join colaboradores as c on c.id = rs.colaborador_id
                                    inner join almacenes as ao on ao.id = rs.almacen_origen_id
                                    inner join almacenes as ad on ad.id = rs.almacen_destino_id
                                    where rs.id = ?',[$id]);


            $registro_salida_detalle    =   DB::select('select 
                                            p.nombre as producto_nombre,
                                            c.descripcion as categoria_nombre,
                                            m.descripcion as marca_nombre,
                                            rsd.cantidad
                                            from registros_salida_detalle as rsd    
                                            inner join productos as p on p.id = rsd.producto_id
                                            inner join categorias as c on c.id = p.categoria_id
                                            inner join marcas as m on m.id = p.marca_id
                                            where rsd.registro_salida_id = ?',[$id]);

            if(count($registro_salida) === 0){
                throw new Exception("NO SE ENCONTRÓ EL REGISTRO DE SALIDA EN LA BD");
            }

            return response()->json(['success'=>true,'registro_salida'=>$registro_salida[0],'registro_salida_detalle'=>$registro_salida_detalle]);
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }
}
