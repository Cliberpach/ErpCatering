<?php

namespace App\Http\Controllers\Compras;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Kardex\KardexController;
use App\Http\Controllers\Utils\UtilController;
use App\Http\Requests\Logistica\RegistroCompra\RegistroCompraStoreRequest;
use App\Models\Kardex\Kardex;
use App\Models\Compras\RegistroCompra;
use App\Models\Compras\RegistroCompraDetalle;
use App\Models\Registros\Almacen;
use App\Models\Registros\AlmacenProducto;
use App\Models\Registros\Categoria;
use App\Models\Registros\Marca;
use App\Models\Registros\Proveedor;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RegistroCompraController extends Controller
{
    public function index(){
        return view('compras.registro_compra.index');
    }

    public function getCompras(Request $request){

        $compras    =   DB::table('registros_compra as rc')
                        ->join('colaboradores as c', 'c.id', '=', 'rc.colaborador_registro_id')
                        ->select(
                            DB::raw('CONCAT("RC-", rc.id) as simbolo'),
                            'rc.id',
                            'c.nombre as colaborador_nombre',
                            DB::raw('CONCAT(rc.serie,"-",rc.correlativo) as documento'),
                            'rc.created_at as fecha_registro',
                            'rc.subtotal_soles',
                            'rc.monto_igv_soles',
                            'rc.total_soles',
                            'rc.precios_igv'
                        )
                        ->where('rc.estado','<>','ANULADO')
                        ->get();

        return DataTables::of($compras)
                ->make(true);
    }

    public function create(){
        $categorias     =   Categoria::where('estado','ACTIVO')->get();
        $marcas         =   Marca::where('estado','ACTIVO')->get();

        $proveedores    =   DB::select('select 
                            pr.id,
                            pr.nombre,
                            pr.nro_documento,
                            td.descripcion as tipo_documento_descripcion
                            from proveedores as pr
                            inner join tipos_documento as td on td.id = pr.tipo_documento_id
                            where pr.estado = "ACTIVO"
                            and pr.id != 1');

        $tipos_documento    =   DB::select('select * 
                                from tipos_documento as td
                                where td.estado = "ACTIVO"
                                and td.id <> "3" ');

        $almacenes      =   Almacen::where('estado','ACTIVO')->get();

        return view('compras.registro_compra.create',
        compact('categorias','marcas','proveedores','almacenes','tipos_documento'));
    }

    public function store(RegistroCompraStoreRequest $request){

        DB::beginTransaction();

        try {
            $lstCompra                                 =   json_decode($request->get('lstCompra'));
            RegistroCompraController::validarLstCompra($lstCompra);

            $igv    =   DB::select('select e.igv from empresas as e')[0]->igv;
            $montos =   RegistroCompraController::calcularMontos($lstCompra,$request->get('igv',null),$igv);


            $registro_compra                            =   new RegistroCompra();
            $registro_compra->colaborador_registro_id   =   Auth::user()->colaborador_id;
            $registro_compra->proveedor_id              =   $request->get('proveedor');
            $registro_compra->fecha_emision             =   $request->get('fecha_emision');
            $registro_compra->fecha_entrega             =   $request->get('fecha_entrega');
            $registro_compra->serie                     =   mb_strtoupper($request->get('serie'), 'UTF-8');
            $registro_compra->correlativo               =   $request->get('numero');
            $registro_compra->moneda                    =   $request->get('moneda');
            $registro_compra->tipo_cambio               =   $request->get('tipo_cambio');
            $registro_compra->precios_igv               =   $request->has('igv')?1:0;
            $registro_compra->observacion               =   $request->get('observacion');
            $registro_compra->igv                       =   $igv;
            $registro_compra->subtotal                  =   $montos->subtotal;
            $registro_compra->monto_igv                 =   $montos->monto_igv;
            $registro_compra->total                     =   $montos->total;

            $moneda                                     =   $request->get('moneda');
            if($moneda === 'SOLES'){
                $registro_compra->subtotal_soles        =   $montos->subtotal;
                $registro_compra->monto_igv_soles       =   $montos->monto_igv;
                $registro_compra->total_soles           =   $montos->total;
            }

            if($moneda  === "DÓLARES"){
                $registro_compra->subtotal_soles        =   $montos->subtotal  * (float)$request->get('tipo_cambio');
                $registro_compra->monto_igv_soles       =   $montos->monto_igv * (float)$request->get('tipo_cambio');
                $registro_compra->total_soles           =   $montos->total * (float)$request->get('tipo_cambio');
            }

            $registro_compra->save();

            //======= GUARDANDO DETALLE ========
            foreach ($lstCompra as $item) {

                //======== OBTENIENDO STOCK ANTES DE LA COMPRA =========
                $stock_previo           =   0;
                $stock_posterior        =   0;

                $compra_detalle                     =   new RegistroCompraDetalle();
                $compra_detalle->registro_compra_id =   $registro_compra->id;
                $compra_detalle->almacen_id         =   $item->almacen_id;
                $compra_detalle->producto_id        =   $item->producto_id;
                $compra_detalle->precio_soles       =   $item->precio;
                $compra_detalle->cantidad           =   $item->cantidad;

                if($moneda == 'DOLARES')
                {
                    $compra_detalle->precio_soles   =   (float) $item->precio * (float) $request->get('tipo_cambio');
                    $compra_detalle->precio_dolares =   (float) $item->precio;
                }

                if($moneda == 'SOLES')
                {
                    $compra_detalle->precio_soles   =   (float) $item->precio;
                    $compra_detalle->precio_dolares =   (float) $item->precio/$request->get('tipo_cambio');
                }

                if($request->has('igv')){
                    $compra_detalle->precio_mas_igv_soles   =   $item->precio;
                    $compra_detalle->precio_mas_igv_dolares =   $compra_detalle->precio_dolares;
                }else{
                    $compra_detalle->precio_mas_igv_soles   =   $item->precio * ((100+$igv)/100);
                    $compra_detalle->precio_mas_igv_dolares =   $compra_detalle->precio_dolares * ((100+$igv)/100);
                }

                $compra_detalle->save();

                //======= INSERTANDO STOCK ========
                $existe_almacen_producto =   DB::table('almacen_productos')
                                            ->where('almacen_id', $item->almacen_id)
                                            ->where('producto_id', $item->producto_id)
                                            ->exists();

                if(!$existe_almacen_producto){
                    $almacen_producto               =   new AlmacenProducto();
                    $almacen_producto->almacen_id   =   $item->almacen_id;
                    $almacen_producto->producto_id  =   $item->producto_id;
                    $almacen_producto->stock        =   $item->cantidad;
                    $almacen_producto->save();
                    $stock_posterior                =   $almacen_producto->stock;
                }else{

                    $almacen_producto_previo = DB::table('almacen_productos')
                    ->where('almacen_id', $item->almacen_id)
                    ->where('producto_id', $item->producto_id)
                    ->value('stock');

                    $stock_previo = $almacen_producto_previo;

                    DB::table('almacen_productos')
                    ->where('almacen_id', $item->almacen_id)
                    ->where('producto_id', $item->producto_id)
                    ->update([
                    'stock' => DB::raw('stock + ' . $item->cantidad),
                    'updated_at' => Carbon::now(),
                    ]);

                    $almacen_producto_posterior = DB::table('almacen_productos')
                                        ->where('almacen_id', $item->almacen_id)
                                        ->where('producto_id', $item->producto_id)
                                        ->value('stock');

                    $stock_posterior = $almacen_producto_posterior;
                }

                KardexController::storeCompra($item,$registro_compra->id,$stock_previo,$stock_posterior);

            }


            DB::commit();
            return response()->json(['success'=>true,'message'=>'REGISTRO DE COMPRA GUARDADO']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function show($id){
        $compra =   DB::select('select
                    rc.id,
                    c.nombre as colaborador_nombre,
                    p.nombre as proveedor_nombre,
                    rc.fecha_emision,
                    rc.fecha_entrega,
                    rc.serie,
                    rc.correlativo,
                    rc.moneda,
                    rc.tipo_cambio,
                    rc.precios_igv,
                    rc.igv,
                    rc.subtotal,
                    rc.monto_igv,
                    rc.total,
                    rc.subtotal_soles,
                    rc.monto_igv_soles,
                    rc.total_soles,
                    rc.observacion,
                    rc.created_at as fecha_registro,
                    rc.updated_at as fecha_modificacion
                    from registros_compra as rc
                    inner join colaboradores    as c on c.id = rc.colaborador_registro_id
                    inner join proveedores      as p on p.id = rc.proveedor_id
                    where rc.id = ?',[$id])[0];

        $productos  =   DB::select('select
                        a.descripcion as almacen_nombre,
                        p.nombre as producto_nombre,
                        rcd.precio_soles,
                        rcd.precio_dolares,
                        rcd.precio_mas_igv_soles,
                        rcd.precio_mas_igv_dolares,
                        rcd.cantidad
                        from registros_compra_detalle as rcd
                        inner join almacenes as a on a.id = rcd.almacen_id
                        inner join productos as p on p.id = rcd.producto_id
                        where rcd.registro_compra_id = ?',[$compra->id]);


        return view('compras.registro_compra.show',compact('compra','productos'));
    }

    public static function validarLstCompra($lstCompra){

        if(count($lstCompra) === 0){
            throw new Exception("EL DETALLE DE LA COMPRA ESTÁ VACÍO!!!");
        }

        foreach ($lstCompra as $item) {
            $existe =   DB::table('productos')
                        ->where('id', $item->producto_id)
                        ->exists();

            if(!$existe){
                throw new Exception("EL PRODUCTO ".$item->producto_nombre."NO EXISTE EN LA BD");
            }else{

                //======= VALIDANDO ALMACÉN ID =======
                $existe_almacen =    DB::table('almacenes')
                                    ->where('id', $item->almacen_id)
                                    ->exists();

                if(!$existe_almacen){
                    throw new Exception("EL ALMACÉN ".$item->almacen_nombre."NO EXISTE EN LA BD");
                }

                if($item->cantidad == 0){
                    throw new Exception("EL PRODUCTO ".$item->producto_nombre."TIENE UNA CANTIDAD NO VÁLIDA");
                }
                if($item->precio == 0){
                    throw new Exception("EL PRODUCTO ".$item->producto_nombre."TIENE UN PRECIO NO VÁLIDO");
                }
            }
        }

    }

    public static function calcularMontos($lstCompra,$precios_con_igv,$igv){
        $subtotal   =   0;
        $monto_igv  =   0;
        $total      =   0;
        $valor_igv  =   $igv;

        if($precios_con_igv){
            foreach ($lstCompra as $item) {
                $total  +=  (float)$item->total;
            }
            $subtotal    =   $total/((100 + (float)$valor_igv)/100);
            $monto_igv   =   $total - $subtotal;
        }else{
            //======= PRECIOS SIN IGV =======
            foreach ($lstCompra as $item) {
                $subtotal  +=  (float)$item->total;
            }

            $monto_igv   =   ((float)$valor_igv/100)*$subtotal;
            $total       =   $subtotal + $monto_igv;
        }

        return (object)['subtotal'=>$subtotal,'monto_igv'=>$monto_igv,'total'=>$total];
    }


  public function pdf($id){

        $empresa    =   DB::select('select * from empresas as e
                        where e.id = 1')[0];


        $registro_compra =   DB::select('select
                                rc.*,
                                pr.nombre as proveedor_nombre,
                                c.nombre as colaborador_nombre
                                from registros_compra as rc
                                inner join colaboradores as c on c.id = rc.colaborador_registro_id
                                inner join proveedores as pr on pr.id = rc.proveedor_id
                                where rc.id = ?',[$id])[0];


        $registro_compra_detalle  =   DB::select('select
                                        rcd.*,
                                        p.nombre as producto_nombre,
                                        c.descripcion as categoria_nombre,
                                        m.descripcion as marca_nombre,
                                        tgd.descripcion as producto_unidad_medida,
                                        a.descripcion as almacen_nombre
                                        from registros_compra_detalle as rcd
                                        inner join productos as p on p.id = rcd.producto_id
                                        inner join marcas as m on m.id = p.marca_id
                                        inner join categorias as c on c.id = p.categoria_id
                                        inner join tablas_generales_detalles as tgd on tgd.id = p.unidad_medida_id
                                        inner join almacenes as a on a.id = rcd.almacen_id
                                        where rcd.registro_compra_id = ?',[$id]);

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
        $html = view('compras.registro_compra.pdf.pdf',
        compact('empresa','registro_compra','fecha_impresion','registro_compra_detalle'))
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
