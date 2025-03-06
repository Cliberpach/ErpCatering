<?php

namespace App\Http\Controllers\Registros;

use App\Http\Controllers\Controller;
use App\Http\Requests\Registros\Proyecto\ProyectoAsignarSupervisorRequest;
use App\Http\Requests\Registros\Proyecto\ProyectoStoreRequest;
use App\Http\Requests\Registros\Proyecto\ProyectoUpdateRequest;
use App\Models\General\Departamento;
use App\Models\General\Distrito;
use App\Models\General\Provincia;
use App\Models\Registros\Almacen;
use App\Models\Registros\Colaborador;
use App\Models\Registros\Horario;
use App\Models\Registros\Proyecto;
use App\Models\Registros\ProyectoMaquinaria;
use App\Models\Registros\ProyectoPersonal;
use App\Models\Registros\Regimen;
use Illuminate\Http\Request;
use Exception;
use Throwable;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
class ProyectoController extends Controller
{
    public function index(){

        

        return view('registros.proyectos.index');
    }

    public function create(){
        $departamentos  =   Departamento::all();
        $provincias     =   Provincia::all();
        $distritos      =   Distrito::all();
        
        return view('registros.proyectos.create',compact('departamentos','provincias','distritos'));
    }

    public function getProyectos(Request $request){

        $proyectos = Proyecto::where('proyectos.estado','<>', 'ANULADO')
                    ->leftJoin('colaboradores', 'proyectos.supervisor_id', '=', 'colaboradores.id')
                    ->select('proyectos.id', 
                    'proyectos.nombre', 
                    'colaboradores.nombre as supervisor_nombre',
                    'proyectos.costo',
                    'proyectos.avance_costo',
                    'proyectos.diferencia',
                    DB::raw('CONCAT(proyectos.avance, "%") AS avance'),                   
                    'proyectos.estado') 
                    ->get();

        return DataTables::of($proyectos)
                ->make(true);
    }

    public function store(ProyectoStoreRequest $request){
        
        DB::beginTransaction();
        try {

            $proyecto                       =   new Proyecto();
            $proyecto->nombre               =   Str::upper($request->get('nombre'));
            $proyecto->costo                =   $request->get('costo');
            $proyecto->avance_costo         =   $request->get('avance_costo');
            $proyecto->diferencia           =   $request->get('diferencia');
            $proyecto->direccion            =   mb_strtoupper($request->get('direccion'), 'UTF-8');
            $proyecto->departamento_id      =   $request->get('departamento');
            $proyecto->provincia_id         =   $request->get('provincia');
            $proyecto->distrito_id          =   $request->get('distrito');

            $proyecto->departamento_nombre  =   DB::select('select d.nombre 
                                                from departamentos as d
                                                where d.id = ?',
                                                [$request->get('departamento')])[0]->nombre;
            
            $proyecto->provincia_nombre     =   DB::select('select p.nombre 
                                                from provincias as p
                                                where p.id = ?',
                                                [$request->get('provincia')])[0]->nombre;
                                                
            $proyecto->distrito_nombre      =   DB::select('select d.nombre 
                                                from distritos as d
                                                where d.id = ?',
                                                [$request->get('distrito')])[0]->nombre;
           
            $proyecto->ubigeo               =   $request->get('distrito');  
            $proyecto->save();

            
            DB::commit();
            return response()->json(['success'=>true,'message'=>'PROYECTO REGISTRADO']);

        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function edit($id){
      
        $proyecto           =   Proyecto::find($id);

        return view('registros.proyectos.edit',compact('proyecto'));
    }

    public function update(ProyectoUpdateRequest $request, $id){
        DB::beginTransaction();
        try {

            $proyecto                   =   Proyecto::find($id);
            $proyecto->nombre           =   Str::upper($request->get('nombre'));
            $proyecto->costo            =   $request->get('costo');
            $proyecto->avance_costo     =   $request->get('avance_costo');
            $proyecto->diferencia       =   $request->get('diferencia');
            $proyecto->direccion        =   mb_strtoupper($request->get('direccion'), 'UTF-8');
            $proyecto->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'PROYECTO ACTUALIZADO']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function show($id){
        try {
            $proyecto   =   DB::select('select 
                            pr.id,
                            pr.nombre,
                            pr.costo,
                            pr.avance_costo,
                            pr.diferencia,
                            pr.direccion,
                            c.nombre as supervisor_nombre,
                            c.id as supervisor_id
                            from proyectos as pr
                            left join colaboradores as c on c.id = pr.supervisor_id
                            where pr.id = ?',[$id]);

            if(count($proyecto) === 0){
                throw new Exception("NO SE ENONCTRÓ EL PROYECTO EN LA BD!!!");
            }

            $proyecto   =   $proyecto[0];

            $personal   =   DB::select('select 
                            c.nombre,
                            td.descripcion as tipo_documento_descripcion,
                            c.nro_documento
                            from proyecto_personal as pp
                            left join colaboradores as c on c.id = pp.colaborador_id
                            left join tipos_documento as td on td.id = c.tipo_documento_id
                            where pp.proyecto_id = ?',[$id]);

            $maquinaria =   DB::select('select 
                            m.nombre,
                            m.costo_gasto,
                            tgd.descripcion as tipo_gasto_descripcion,
                            m.observacion
                            from proyecto_maquinaria as pm
                            left join maquinarias as m on m.id = pm.maquinaria_id
                            left join tablas_generales_detalles as tgd on tgd.id = m.tipo_gasto_id
                            where pm.proyecto_id = ? 
                            and tgd.tabla_general_id = 2',
                            [$id]);

            return response()->json(['success'=>true,
                                        'proyecto'=>$proyecto,
                                        'personal'=>$personal,
                                        'maquinaria'=>$maquinaria]);
        } catch (Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }        
    }


    public function destroy($id){
        DB::beginTransaction();
        try {
            $proyecto                    =   Proyecto::find($id);
            $proyecto->estado            =   'ANULADO';
            $proyecto->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'PROYECTO ELIMINADO']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function finalizarProyecto(Request $request,$id){
        DB::beginTransaction();
        try {
            $proyecto                    =   Proyecto::find($id);
            $proyecto->estado            =   'FINALIZADO';
            $proyecto->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'PROYECTO FINALIZADO']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function asignarSupervisor(ProyectoAsignarSupervisorRequest $request,$id){
        DB::beginTransaction();
        try {

            $proyecto                   =   Proyecto::find($id);
            $proyecto->supervisor_id    =   $request->get('supervisor');
            $proyecto->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'SUPERVISOR ASIGNADO CON ÉXITO']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function asignarPersonalCreate($id){
        
        //=========== OBTENER TODOS LOS USUARIOS LIBRES QUE NO ESTÉN ASIGNADOS A PROYECTOS DIFERENTES A ESTE ACTUALMENTE =======
        //========== ADEMÁS QUE NO SEAN SUPERVISORES ======
        $colaboradores_libres   =   DB::select('SELECT 
                                            co.id as colaborador_id,
                                            co.nombre as colaborador_nombre,
                                            co.nro_documento as colaborador_nro_documento,
                                            ca.descripcion as cargo_nombre
                                        FROM colaboradores AS co
                                        inner join cargos as ca on ca.id = co.cargo_id 
                                        WHERE co.id NOT IN (
                                            SELECT pp.colaborador_id
                                            FROM proyecto_personal AS pp
                                            WHERE pp.estado = "ACTIVO" AND pp.proyecto_id != ?
                                        ) AND ca.descripcion != "ADMIN" && ca.descripcion != "SUPERVISOR"
                                    ',[$id]);

        $colaboradores_asignados    =   DB::select('select 
                                        pp.colaborador_id 
                                        from proyecto_personal as pp
                                        where pp.proyecto_id = ?',[$id]);  

        $idsAsignados = array_column($colaboradores_asignados, 'colaborador_id');

                    
        $proyecto_id    =   $id;
        $proyecto       =   Proyecto::find($id);

       
        return view('registros.proyectos.asignar_personal',
        compact('colaboradores_libres','proyecto_id','idsAsignados','proyecto'));

    }

    public function asignarPersonalStore(Request $request){
        DB::beginTransaction();
        try {
            $lstUsuariosAsignados   =   json_decode($request->get('lstUsuariosAsignados'));
            $proyecto_id            =   $request->get('proyecto_id');

            DB::table('proyecto_personal')
            ->where('proyecto_id', $proyecto_id)
            ->delete();

            foreach ($lstUsuariosAsignados as  $usuario_asignado) {
                $proyecto_personal                  =   new ProyectoPersonal();
                $proyecto_personal->proyecto_id     =   $proyecto_id;
                $proyecto_personal->colaborador_id  =   $usuario_asignado;
                $proyecto_personal->save();
            }

            DB::commit();
            return response()->json(['success'=>true,'message'=>'PERSONAL ASIGNADO CON ÉXITO']);
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function asignarMaquinariaCreate($id){
        
        //=========== OBTENER TODAS LAS MAQUINARIAS LIBRES QUE NO ESTÉN ASIGNADOS A PROYECTOS DIFERENTES A ESTE ACTUALMENTE =======
        //========== ADEMÁS QUE NO SEAN SUPERVISORES ======
        $maquinarias_libres   =   DB::select('SELECT 
                                            m.id as maquinaria_id,
                                            m.nombre as maquinaria_nombre
                                        FROM maquinarias AS m
                                        WHERE m.id NOT IN (
                                            SELECT pm.maquinaria_id
                                            FROM proyecto_maquinaria AS pm
                                            WHERE pm.estado = "ACTIVO" AND pm.proyecto_id != ?
                                        )
                                    ',[$id]);

        $maquinarias_asignadas    =   DB::select('select 
                                        pm.maquinaria_id 
                                        from proyecto_maquinaria as pm
                                        where pm.proyecto_id = ?',[$id]);  

        $idsAsignados = array_column($maquinarias_asignadas, 'maquinaria_id');

                    
        $proyecto_id    =   $id;
        $proyecto       =   Proyecto::find($id);

       
        return view('registros.proyectos.asignar_maquinaria',
        compact('maquinarias_libres','proyecto_id','idsAsignados','proyecto'));

    }

    public function asignarMaquinariaStore(Request $request){
        DB::beginTransaction();
        try {
            $lstMaquinariasAsignadas    =   json_decode($request->get('lstMaquinariasAsignadas'));
            $proyecto_id                =   $request->get('proyecto_id');

            DB::table('proyecto_maquinaria')
            ->where('proyecto_id', $proyecto_id)
            ->delete();

            foreach ($lstMaquinariasAsignadas as  $maquinaria_asignada) {
                $proyecto_maquinaria                    =   new ProyectoMaquinaria();
                $proyecto_maquinaria->proyecto_id       =   $proyecto_id;
                $proyecto_maquinaria->maquinaria_id     =   $maquinaria_asignada;
                $proyecto_maquinaria->save();
            }

            DB::commit();
            return response()->json(['success'=>true,'message'=>'MAQUINARIA ASIGNADA CON ÉXITO']);
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }
    public function getProyectosDireccion(Request $request) {
        $proyectoId = $request->input('proyecto_id');
    
        if (!$proyectoId) {
            return response()->json(['data' => []]); // Si no hay proyecto, retorna vacío
        }
    
        $proyecto = Proyecto::where('proyectos.id', $proyectoId)
            ->whereNotIn('proyectos.estado', ['ANULADO', 'INACTIVO'])
            ->leftJoin('colaboradores', 'proyectos.supervisor_id', '=', 'colaboradores.id')
            ->select(
                'proyectos.id', 
                'proyectos.nombre', 
                'colaboradores.nombre as supervisor_nombre',
                'proyectos.direccion'
            )
            ->get(); // Solo un resultado
    return DataTables::of($proyecto)->make(true);
    }


    
    public function vista(){
        $proyectos = Proyecto::where('estado','<>','ANULADO')->get();
        $horarios = Horario::where('estado','<>','ANULADO')->get();
        $regimenes = Regimen::where('estado','<>','ANULADO')->get();
        $primerProyecto = $proyectos->first();

        return view('registros.proyectos.vista',compact('proyectos','primerProyecto','horarios','regimenes'));
    }
    public function getColaboradoresRegimen(Request $request)
{
    $proyectoId = $request->input('proyecto_id');

    if (!$proyectoId) {
        return response()->json(['data' => []]); 
    }

    $colaboradores = DB::table('proyecto_personal as pp')
        ->join('colaboradores as c', 'pp.colaborador_id', '=', 'c.id') 
        ->leftJoin('colaborador_proyecto as cp', 'pp.colaborador_id', '=', 'cp.colaborador_id')
        ->leftJoin('horarios as h', 'cp.horario_id', '=', 'h.id') 
        ->leftJoin('regimens as r', 'cp.regimen_id', '=', 'r.id') 
        ->where('pp.proyecto_id', $proyectoId)
        ->select(
            'c.id as colaborador_id',
            'c.nombre as nombre',
            'c.nro_documento as dni',
            DB::raw('COALESCE(h.nombre_proyecto, "Sin asignar") as horario'),
            DB::raw('COALESCE(r.nombre, "Sin asignar") as regimen')
        )
        ->get();

    return DataTables::of($colaboradores)->make(true);
}
public function asignarHorarioRegimen(Request $request)
{
    $request->validate([
        'colaborador_id' => 'required|exists:colaboradores,id',
        'horario_id' => 'nullable|exists:horarios,id',
        'regimen_id' => 'nullable|exists:regimenes,id',
    ]);

    $colaborador = Colaborador::findOrFail($request->colaborador_id);
    $colaborador->horario_id = $request->horario_id;
    $colaborador->regimen_id = $request->regimen_id;
    $colaborador->save();

    return response()->json(['success' => true, 'message' => 'Horario y régimen asignados correctamente.']);
}


public function asignarHorarioRegimenCreate($proyectoId, $colaboradorId)
{
    // Verificar el ID del proyecto
    if (!is_numeric($proyectoId)) {
        return response()->json(['success' => false, 'message' => 'ID de proyecto inválido.']);
    }

    // Obtener datos del proyecto
    $proyecto = Proyecto::find($proyectoId);

    // Verificar si el proyecto fue encontrado
    if (!$proyecto) {
        return response()->json(['success' => false, 'message' => 'Proyecto no encontrado.']);
    }

    // Obtener datos del colaborador
    $colaborador = Colaborador::find($colaboradorId);

    // Verificar si el colaborador fue encontrado
    if (!$colaborador) {
        return response()->json(['success' => false, 'message' => 'Colaborador no encontrado.']);
    }

    // Obtener listas de horarios y regímenes
    $horarios = DB::table('horarios')->select('id', 'nombre_proyecto')->get();
    $regimenes = DB::table('regimens')->select('id', 'nombre')->get();

    return view('registros.proyectos.asignar_horario_regimen', compact('colaborador', 'horarios', 'regimenes', 'proyecto'));
}
public function asignarHorarioRegimenStore(Request $request)
{
    DB::beginTransaction();
    try {
        $colaborador_id = $request->get('colaborador_id');
        $horario_id = $request->get('horario_id') ?? null;
        $regimen_id = $request->get('regimen_id') ?? null;

        // Eliminar cualquier asignación previa del colaborador
        DB::table('colaborador_proyecto')
            ->where('colaborador_id', $colaborador_id)
            ->delete();

        // Insertar la nueva asignación
        DB::table('colaborador_proyecto')->insert([
            'colaborador_id' => $colaborador_id,
            'horario_id' => $horario_id,
            'regimen_id' => $regimen_id,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::commit();
        return response()->json(['success' => true, 'message' => 'Horario y régimen asignados correctamente.']);
    } catch (\Throwable $th) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Error al asignar horario y régimen.',
            'error' => $th->getMessage(),
        ]);
    }
}

}