<?php

namespace App\Http\Controllers\Logistica;

use App\Http\Controllers\Controller;
use App\Models\Logistica\CotizacionCompra;
use App\Models\Logistica\CotizacionCompraDetalle;
use App\Models\Registros\Categoria;
use App\Models\Registros\Marca;
use Auth;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Dompdf\Dompdf;
use Dompdf\Options;

class CotizacionCompraController extends Controller
{
    public function index(){
        return view('logistica.cotizacion_compra.index');
    }

    public function getCotizacionesCompra(Request $request){

        $cotizaciones_compra    =   DB::table('cotizacion_compra as cc')
                                    ->join('colaboradores as c', 'c.id', '=', 'cc.colaborador_id')
                                    ->select(
                                        DB::raw('CONCAT("CO-", cc.id) as simbolo'), 
                                        'cc.id', 
                                        'c.nombre as colaborador_nombre',
                                        'cc.estado',
                                        'cc.created_at as fecha_registro',
                                    )
                                    ->where('cc.estado','<>','ANULADO')
                                    ->get();

        return DataTables::of($cotizaciones_compra)
                ->make(true);
    }

    public function create(){
        $categorias =   Categoria::where('estado','ACTIVO')->get();
        $marcas     =   Marca::where('estado','ACTIVO')->get();

        return view('logistica.cotizacion_compra.create',compact('categorias','marcas'));
    }

    public function edit($id){
        $cotizacion_compra_detalle  =   DB::select('select 
                                        ccd.producto_id,
                                        ccd.cantidad,
                                        p.nombre as producto_nombre,
                                        c.descripcion as categoria_nombre,
                                        m.descripcion as marca_nombre,
                                        tgd.descripcion as producto_unidad_medida
                                        from cotizacion_compra_detalle as ccd
                                        inner join productos as p on p.id = ccd.producto_id
                                        inner join marcas as m on m.id = p.marca_id 
                                        inner join categorias as c on c.id = p.categoria_id
                                        inner join tablas_generales_detalles as tgd on tgd.id = p.unidad_medida_id
                                        where ccd.cotizacion_compra_id = ?',[$id]);

        $categorias =   Categoria::where('estado','ACTIVO')->get();
        $marcas     =   Marca::where('estado','ACTIVO')->get();

        return view('logistica.cotizacion_compra.edit',
        compact('cotizacion_compra_detalle','categorias','marcas','id'));
    }

    public function store(Request $request){
        
        DB::beginTransaction();
        try {
            $lstCotizacionCompraDetalle =   json_decode($request->get('lstCotizacionCompra'));
            if(count($lstCotizacionCompraDetalle) === 0){
                throw new Exception("EL DETALLE DE LA COTIZACIÓN DE COMPRA ESTÁ VACÍO");
            }
            
            $cotizacion_compra                  =   new CotizacionCompra();
            $cotizacion_compra->colaborador_id  =   Auth::user()->colaborador_id;
            $cotizacion_compra->save();

            foreach ($lstCotizacionCompraDetalle as $item) {
                $producto_existe    =   DB::select('select p.id from productos as p
                                        where p.id = ?',[$item->producto_id]);

                if(count($producto_existe) === 0){
                    throw new Exception("NO EXISTE EL PRODUCTO"." ".$item->producto_nombre." "."EN LA BD");
                }

                $cotizacion_compra_detalle                          =   new CotizacionCompraDetalle();
                $cotizacion_compra_detalle->cotizacion_compra_id    =   $cotizacion_compra->id;
                $cotizacion_compra_detalle->producto_id             =   $item->producto_id;
                $cotizacion_compra_detalle->cantidad                =   $item->cantidad;
                $cotizacion_compra_detalle->save();
            }

            DB::commit();
            return response()->json(['success'=>true,'message'=>"COTIZACIÓN DE COMPRA REGISTRADA"]);


        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function update($id,Request $request){
        DB::beginTransaction();
        try {
            $cotizacion_compra          =   CotizacionCompra::find($id);
            $lstCotizacionCompraDetalle =   json_decode($request->get('lstCotizacionCompra'));

            if(count($lstCotizacionCompraDetalle) === 0){
                throw new Exception("EL DETALLE DE LA COTIZACIÓN DE COMPRA ESTÁ VACÍO");
            }

            if(!$cotizacion_compra){
                throw new Exception("NO SE ENCONTRÓ LA COTIZACIÓN DE COMPRA");
            }

            $cotizacion_compra->colaborador_id  =   Auth::user()->colaborador_id;
            $cotizacion_compra->update();

            DB::delete('DELETE FROM cotizacion_compra_detalle 
            WHERE cotizacion_compra_id = ?', [$id]);
          
            foreach ($lstCotizacionCompraDetalle as $item) {
                $producto_existe    =   DB::select('select p.id from productos as p
                                        where p.id = ?',[$item->producto_id]);

                if(count($producto_existe) === 0){
                    throw new Exception("NO EXISTE EL PRODUCTO"." ".$item->producto_nombre." "."EN LA BD");
                }

            
                $cotizacion_compra_detalle                          =   new CotizacionCompraDetalle();
                $cotizacion_compra_detalle->cotizacion_compra_id    =   $cotizacion_compra->id;
                $cotizacion_compra_detalle->producto_id             =   $item->producto_id;
                $cotizacion_compra_detalle->cantidad                =   $item->cantidad;
                $cotizacion_compra_detalle->save();
            }

            DB::commit();
            return response()->json(['success'=>true,'message'=>"COTIZACIÓN DE COMPRA ACTUALIZADA"]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function destroy($id){
        DB::beginTransaction();
        try {
            $cotizacion_compra                    =   CotizacionCompra::find($id);
            $cotizacion_compra->estado            =   'ANULADO';
            $cotizacion_compra->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'COTIZACIÓN DE COMPRA ELIMINADA']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function pdf($id){

        $empresa    =   DB::select('select * from empresas as e
                        where e.id = 1')[0];

        $cotizacion_compra =   DB::select('select 
                                cc.id,
                                c.nombre as colaborador_nombre
                                from cotizacion_compra as cc
                                inner join colaboradores as c on c.id = cc.colaborador_id
                                where cc.id = ?',[$id])[0];

        $cotizacion_compra_detalle  =   DB::select('select 
                                ccd.producto_id,
                                ccd.cantidad,
                                p.nombre as producto_nombre,
                                c.descripcion as categoria_nombre,
                                m.descripcion as marca_nombre,
                                tgd.descripcion as producto_unidad_medida
                                from cotizacion_compra_detalle as ccd
                                inner join productos as p on p.id = ccd.producto_id
                                inner join marcas as m on m.id = p.marca_id 
                                inner join categorias as c on c.id = p.categoria_id
                                inner join tablas_generales_detalles as tgd on tgd.id = p.unidad_medida_id
                                where ccd.cotizacion_compra_id = ?',[$id]);

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
        $html = view('logistica.cotizacion_compra.pdf.pdf',
        compact('empresa','cotizacion_compra','cotizacion_compra_detalle','fecha_impresion'))
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
