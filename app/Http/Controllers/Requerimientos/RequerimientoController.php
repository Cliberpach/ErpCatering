<?php

namespace App\Http\Controllers\Requerimientos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Requerimientos\Requerimiento\RequerimientoStoreRequest;
use App\Models\Requerimientos\Requerimiento;
use App\Models\Requerimientos\RequerimientoDetalle;
use App\Models\Registros\Categoria;
use App\Models\Registros\Marca;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class RequerimientoController extends Controller
{
    public function index(){
       
        return view('requerimientos.requerimientos.index');
    }

    public function getRequerimientos(Request $request){
        
        $requerimientos =   DB::table('requerimientos as r')
                                ->join('colaboradores as c', 'c.id', '=', 'r.supervisor_id')
                                ->join('proyectos as pr', 'pr.id', '=', 'r.proyecto_id')
                                ->leftJoin('productos as p', 'p.id', '=', 'r.primer_producto_id')
                                ->leftJoin('proveedores as pro', 'pro.id', '=', 'r.proveedor_id')
                                ->select(
                                    DB::raw('CONCAT("RQ-", r.id) as simbolo'), 
                                    'r.id',
                                    'pr.nombre as proyecto_nombre', 
                                    'c.nombre as supervisor_nombre',
                                    'pro.nombre as proveedor_nombre',
                                    'r.created_at as fecha_registro',
                                    'r.fecha_atencion as fecha_atencion',
                                    'p.nombre as primer_producto_nombre',
                                    'r.estado',
                                    'r.supervisor_id'
                                )
                                ->where('r.estado','<>','ANULADO')
                                ->get();

        return DataTables::of($requerimientos)
                ->make(true);
    }

    public function create(){

        $proyecto   =   DB::select('select * from proyectos as pr
                        where pr.estado != "FINALIZADO" 
                        and pr.estado != "ANULADO"
                        and pr.supervisor_id = ?',[Auth::user()->id]);

        if(count($proyecto) === 0){
            Session::flash('requerimiento_error','NO TIENES UN PROYECTO ASIGNADO!!!');
            return back();
        }

        $proyecto   =   $proyecto[0];


        $categorias     =   Categoria::where('estado','ACTIVO')->get();
        $marcas         =   Marca::where('estado','ACTIVO')->get();

        $proveedores    =   DB::select('select 
                            pr.id,
                            pr.nombre,
                            pr.nro_documento,
                            td.descripcion as tipo_documento_descripcion
                            from proveedores as pr
                            inner join tipos_documento as td on td.id = pr.tipo_documento_id
                            where pr.estado = "ACTIVO"');

        $tipos_documento    =   DB::select('select * 
                                from tipos_documento as td
                                where td.estado = "ACTIVO"
                                and td.id <> "3" ');

        $colaborador    =   DB::select('select * from colaboradores as c
                            where c.id = ?',[Auth::user()->colaborador_id]);

        if(count($colaborador) === 0){
            Session::flash('requerimiento_error','NO TIENES DATOS DE COLABORADOR EN LA BD!!!');
            return back();
        }
        $colaborador    =   $colaborador[0];

        return view('requerimientos.requerimientos.create',
        compact('categorias','marcas','proveedores','tipos_documento','proyecto','colaborador'));

    }

    public function store(RequerimientoStoreRequest $request){
        DB::beginTransaction();
        try {
            
            $lstRequerimientos  =   json_decode($request->get('lstRequerimientos'));

            RequerimientoController::validacionCompleja($request->get('supervisor_id'),$request->get('proyecto_id'),$lstRequerimientos);

            $requerimiento                          =   new Requerimiento();
            $requerimiento->proyecto_id             =   $request->get('proyecto_id');
            $requerimiento->supervisor_id           =   $request->get('supervisor_id');
            $requerimiento->primer_producto_id      =   $lstRequerimientos[0]->producto_id;
            $requerimiento->fecha_atencion          =   $request->get('fecha_atencion');
            $requerimiento->fecha_atencion          =   $request->get('fecha_atencion');
            $requerimiento->proveedor_id            =   $request->get('proveedor');
            $requerimiento->save();

            
            foreach ($lstRequerimientos as $producto) {
                $requerimiento_detalle                      =   new RequerimientoDetalle();
                $requerimiento_detalle->requerimiento_id    =   $requerimiento->id;
                $requerimiento_detalle->producto_id         =   $producto->producto_id;
                $requerimiento_detalle->cantidad            =   $producto->cantidad;
                $requerimiento_detalle->save();     
            }

            DB::commit();
            return response()->json(['success'=>true,'message'=>"REQUERIMIENTO REGISTRADO"]);

        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public static function  validacionCompleja($supervisor_id,$proyecto_id,$lstRequerimientos){

        $proyecto   =   DB::select('select * from proyectos as pr
                        where pr.id = ? 
                        and pr.supervisor_id = ?
                        and pr.estado != "FINALIZADO" 
                        and pr.estado != "ANULADO"',[$proyecto_id,$supervisor_id]);

        if(count($proyecto) === 0){
            throw new Exception("SUPERVISOR Y PROYECTO NO COINCIDEN EN LA BD");
        }

        if(count($lstRequerimientos) === 0){
            throw new Exception("EL DETALLE DEL REQUERIMIENTO ESTÁ VACÍO");
        }
        
    }

    public function edit($id){

        $requerimiento              =   Requerimiento::find($id);

        if($requerimiento->supervisor_id != Auth::user()->colaborador_id){
            Session::flash('requerimiento_error','NO ERES EL AUTOR DEL REQUERIMIENTO!!!');
            return back();
        }

        $requerimiento_detalle      =   DB::select('select 
                                        rd.producto_id,
                                        rd.cantidad,
                                        p.nombre as producto_nombre,
                                        c.descripcion as categoria_nombre,
                                        m.descripcion as marca_nombre,
                                        tgd.descripcion as producto_unidad_medida
                                        from requerimiento_detalle as rd
                                        inner join productos as p on p.id = rd.producto_id
                                        inner join marcas as m on m.id = p.marca_id 
                                        inner join categorias as c on c.id = p.categoria_id
                                        inner join tablas_generales_detalles as tgd on tgd.id = p.unidad_medida_id
                                        where rd.requerimiento_id = ?',[$id]);

        $proyecto   =   DB::select('select * from proyectos as pr
                        where pr.estado != "FINALIZADO" 
                        and pr.estado != "ANULADO"
                        and pr.supervisor_id = ?',[Auth::user()->id]);

        if(count($proyecto) === 0){
            Session::flash('requerimiento_error','NO TIENES UN PROYECTO ASIGNADO!!!');
            return back();
        }

        $proyecto   =   $proyecto[0];


        $categorias     =   Categoria::where('estado','ACTIVO')->get();
        $marcas         =   Marca::where('estado','ACTIVO')->get();

        $proveedores    =   DB::select('select 
                            pr.id,
                            pr.nombre,
                            pr.nro_documento,
                            td.descripcion as tipo_documento_descripcion
                            from proveedores as pr
                            inner join tipos_documento as td on td.id = pr.tipo_documento_id
                            where pr.estado = "ACTIVO"');

        $tipos_documento    =   DB::select('select * 
                                from tipos_documento as td
                                where td.estado = "ACTIVO"
                                and td.id <> "3" ');

        $colaborador    =   DB::select('select * from colaboradores as c
                            where c.id = ?',[Auth::user()->colaborador_id]);

        if(count($colaborador) === 0){
            Session::flash('requerimiento_error','NO TIENES DATOS DE COLABORADOR EN LA BD!!!');
            return back();
        }

        $colaborador    =   $colaborador[0];

        return view('requerimientos.requerimientos.edit',
        compact('categorias','marcas','proveedores','tipos_documento',
        'proyecto','colaborador','requerimiento','requerimiento_detalle'));

    }


    public function update($id,RequerimientoStoreRequest $request){
        DB::beginTransaction();
        try {
            $lstRequerimientos  =   json_decode($request->get('lstRequerimientos'));

            RequerimientoController::validacionCompleja($request->get('supervisor_id'),$request->get('proyecto_id'),$lstRequerimientos);

            $requerimiento                          =   Requerimiento::find($id);

            if(!$requerimiento){
                throw new Exception("NO EXISTE EL REQUERIMIENTO EN LA BD!!!");
            }

            $requerimiento->proyecto_id             =   $request->get('proyecto_id');
            $requerimiento->primer_producto_id      =   $lstRequerimientos[0]->producto_id;
            $requerimiento->update();

            DB::delete('DELETE FROM requerimiento_detalle 
                        WHERE requerimiento_id = ?', [$id]);

            foreach ($lstRequerimientos as $producto) {
                $requerimiento_detalle                      =   new RequerimientoDetalle();
                $requerimiento_detalle->requerimiento_id    =   $id;
                $requerimiento_detalle->producto_id         =   $producto->producto_id;
                $requerimiento_detalle->cantidad            =   $producto->cantidad;
                $requerimiento_detalle->save();     
            }

            DB::commit();
            return response()->json(['success'=>true,'message'=>"REQUERIMIENTO ACTUALIZADO"]);

        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function show($id){

        try {

            $requerimiento =   DB::select('select
                                r.id,
                                p.nombre as proyecto_nombre,
                                c.nombre as supervisor_nombre,
                                pro.nombre as proveedor_nombre,
                                r.orden_compra_id,
                                r.factura_atencion,
                                r.fecha_atencion,
                                r.estado,
                                r.created_at as fecha_registro
                                from requerimientos as r
                                inner join colaboradores    as c on c.id = r.supervisor_id
                                inner join proyectos      as p on p.id = r.proyecto_id
                                left join proveedores as pro on pro.id = r.proveedor_id
                                where r.id = ?',[$id])[0];

            $requerimiento_detalle  =   DB::select('select 
                                        rd.producto_id,
                                        rd.cantidad,
                                        p.nombre as producto_nombre,
                                        c.descripcion as categoria_nombre,
                                        m.descripcion as marca_nombre,
                                        tgd.descripcion as producto_unidad_medida
                                        from requerimiento_detalle as rd
                                        inner join productos as p on p.id = rd.producto_id
                                        inner join marcas as m on m.id = p.marca_id 
                                        inner join categorias as c on c.id = p.categoria_id
                                        inner join tablas_generales_detalles as tgd on tgd.id = p.unidad_medida_id
                                        where rd.requerimiento_id = ?',[$id]);

            return response()->json(['success'=>true,
            'requerimiento_detalle' => $requerimiento_detalle,
            'requerimiento'=>$requerimiento]);
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
        
       
    }

    public function destroy($id){
        DB::beginTransaction();
        try {
            $requerimiento                    =   Requerimiento::find($id);
            $requerimiento->estado            =   'ANULADO';
            $requerimiento->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'REQUERIMIENTO ELIMINADO']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

}
