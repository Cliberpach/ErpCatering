<?php

namespace App\Http\Controllers\Logistica;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Kardex\KardexController;
use App\Http\Requests\Logistica\RegistroSalida\RegistroSalidaStoreRequest;
use App\Models\Logistica\RegistroSalida;
use App\Models\Logistica\RegistroSalidaDetalle;
use App\Models\Registros\Almacen;
use App\Models\Registros\Categoria;
use App\Models\Registros\Marca;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class RegistroSalidaController extends Controller
{
    public function index(){
        return view('logistica.registro_salida.index');
    }

    public function create(){

        $categorias     =   Categoria::where('estado','ACTIVO')->get();
        $marcas         =   Marca::where('estado','ACTIVO')->get();
        $proyecto_id    =   null;

        //======== VERIFICANDO SI EL USUARIO ES SUPERVISOR DE ALGÚN PROYECTO PENDIENTE O EN PROCESO ======
        $proyecto   =   DB::select('select
                        pr.id 
                        from proyectos as pr
                        where 
                        pr.supervisor_id = ?
                        and pr.estado != "ANULADO" 
                        and pr.estado != "FINALIZADO"',
                        [Auth::user()->colaborador_id]);

        //======== EN CASO EL USUARIO NO SEA SUPERVISOR DE ALGÚN PROYECTO PENDIENTE O EN PROCESO =====
        if(count($proyecto) === 0){
            
            //========= VERIFICAR SI EL USUARIO FORMA PARTE DEL EQUIPO DE UN PROYECTO PENDIENTE O EN PROCESO =======
            $proyecto_personal  =   DB::select('select 
                                    pr.id
                                    from proyecto_personal as pp
                                    inner join proyectos as pr on pr.id = pp.proyecto_id
                                    where 
                                    pp.colaborador_id = ?
                                    and pr.estado != "ANULADO" 
                                    and pr.estado != "FINALIZADO"',
                                    [Auth::user()->colaborador_id]); 
                                    
            //======== EN CASO EL USUARIO NO FORME PARTE DE UN EQUIPO ======
            if(count($proyecto_personal) === 0){
                Session::flash('registro_salida_error','USTED NO FORMA PARTE DE UN PROYECTO ACTUALMENTE');
                return back();
            }else{
                //========= EN CASO FORME PARTE DE UN EQUIPO =======
                $proyecto_id    =   $proyecto_personal[0]->id;
            }
        }else{
            //======= EN CASO SEA SUPERVISOR DE UN PROYECTO ======
            $proyecto_id    =   $proyecto[0]->id;
        }

        //======= OBTENIENDO ALMACENES DEL PROYECTO ACTUAL DEL USUARIO ========
        $almacenes  =   DB::select('select 
                        a.id,
                        a.descripcion
                        from almacenes as a
                        where a.proyecto_id = ?',[$proyecto_id]);
    
        //$almacenes  =   Almacen::where('estado','ACTIVO')->get();

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

                $stock_previo_origen   =   $almacen_origen_producto_previo[0]->stock;

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

                $stock_posterior_origen   =   $almacen_origen_producto_posterior[0]->stock;

                KardexController::storeSalidaOrigen($producto,$registro_salida,$stock_previo_origen,$stock_posterior_origen);

                //======== INCREMENTANDO STOCK EN ALMACÉN DESTINO ========
                //========= VERIFICAR SI EXISTE EL PRODUCTO EN EL DESTINO =======
                $existe_producto =  DB::table('almacen_productos')
                                    ->where('almacen_id', $request->get('almacen_destino'))
                                    ->where('producto_id', $producto->producto_id)
                                    ->first();

                $stock_previo_destino           =   0;
                $stock_posterior_destino        =   0;
                    
                               

                if ($existe_producto) {

                    $stock_previo_destino   =   $existe_producto->stock;

                    DB::table('almacen_productos')
                    ->where('almacen_id', $request->get('almacen_destino'))
                    ->where('producto_id', $producto->producto_id)
                    ->update([
                        'stock' => DB::raw('stock + ' . $producto->cantidad),
                        'updated_at' => Carbon::now(), 
                    ]);

                    $producto_posterior_destino =  DB::table('almacen_productos')
                                                    ->where('almacen_id', $request->get('almacen_destino'))
                                                    ->where('producto_id', $producto->producto_id)
                                                    ->first();

                    $stock_posterior_destino    =   $producto_posterior_destino->stock;

                } else {

                    $stock_previo_destino   =   0;

                    DB::table('almacen_productos')->insert([
                        'almacen_id'    => $request->get('almacen_destino'),
                        'producto_id'   => $producto->producto_id,
                        'stock'         => $producto->cantidad,
                        'created_at'    => Carbon::now(),
                        'updated_at'    => Carbon::now(),
                    ]);

                    $producto_posterior_destino =  DB::table('almacen_productos')
                                                    ->where('almacen_id', $request->get('almacen_destino'))
                                                    ->where('producto_id', $producto->producto_id)
                                                    ->first();

                    $stock_posterior_destino    =   $producto_posterior_destino->stock;

                }

                $registro_salida_detalle                        =   new RegistroSalidaDetalle();
                $registro_salida_detalle->registro_salida_id    =   $registro_salida->id;
                $registro_salida_detalle->producto_id           =   $producto->producto_id;
                $registro_salida_detalle->cantidad              =   $producto->cantidad;
                $registro_salida_detalle->save();

                KardexController::storeSalidaDestino($producto,$registro_salida,$stock_previo_destino,$stock_posterior_destino);

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

    public function pdf($id){

        $empresa    =   DB::select('select * from empresas as e
                        where e.id = 1')[0];


        $registro_salida =   DB::select('select
                                rs.*,
                                ao.descripcion as almacen_origen_nombre,
                                ad.descripcion as almacen_destino_nombre,
                                c.nombre as colaborador_nombre
                                from registros_salida as rs
                                inner join colaboradores as c on c.id = rs.colaborador_id
                                inner join almacenes as ao on ao.id = rs.almacen_origen_id
                                inner join almacenes as ad on ad.id =  rs.almacen_destino_id
                                where rs.id = ?',[$id])[0];

        $registro_salida_detalle    =   DB::select('select 
                                p.nombre as producto_nombre,
                                c.descripcion as categoria_nombre,
                                m.descripcion as marca_nombre,
                                rsd.cantidad,
                                tgd.descripcion as unidad_medida_nombre
                                from registros_salida_detalle as rsd    
                                inner join productos as p on p.id = rsd.producto_id
                                inner join categorias as c on c.id = p.categoria_id
                                inner join marcas as m on m.id = p.marca_id
                                inner join tablas_generales_detalles as tgd on tgd.id = p.unidad_medida_id
                                where rsd.registro_salida_id = ?',[$id]);

        Carbon::setLocale('es');
        $fecha_impresion = Carbon::now();
        $fecha_impresion = $fecha_impresion->translatedFormat('l, d \d\e F \d\e\l Y');
        $fecha_impresion = strtoupper($fecha_impresion);


        // Configurar las opciones de DOMPDF si es necesario
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');

        // Instanciar el objeto DOMPDF
        $dompdf = new Dompdf($options);

        // Definir el contenido del PDF (HTML)
        $html = view('logistica.registro_salida.pdf.pdf',
        compact('empresa','registro_salida','fecha_impresion','registro_salida_detalle'))
            ->render();

        // Cargar el HTML en DOMPDF
        $dompdf->loadHtml($html);

        // Opcional: Configurar el tamaño de papel y la orientación
        $dompdf->setPaper('A4', 'portrait'); // O 'landscape'

        // Renderizar el PDF
        $dompdf->render();

        // Visualizar el PDF en una nueva ventana en lugar de descargarlo
        return $dompdf->stream('archivo.pdf', ['Attachment' => false]);
    }



}
