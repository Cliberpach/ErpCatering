<?php

namespace App\Http\Controllers\Notificaciones;

use App\Http\Controllers\Controller;
use App\Models\Notificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificacionController extends Controller
{
    /*
        {#1902 // app\Http\Controllers\Requerimientos\RequerimientoController.php:156
            +"notificacion_id": 10
            +"requerimiento_id": 10
            +"supervisor_nombre": "LUIS DANIEL ALVA LUJAN"
            +"proyecto_nombre": "PROYECTO CHAVIMOCHIC"
            +"fecha_registro": "2024-10-14 17:35:04"
            +"estado": "PENDIENTE"
        }
    */ 
    public function destroy($id){

        DB::beginTransaction();
        try {
            $notificacion           =   Notificacion::find($id);
            $notificacion->estado   =   'ANULADO';
            $notificacion->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'NOTIFICACIÓN ELIMINADA']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }
}
