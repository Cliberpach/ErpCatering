<?php

namespace App\Http\Requests\Jornales\RegistroLabor;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegistroLaborStoreRequest extends FormRequest
{
    protected $authorizationError = null;


    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if(!Auth::user()->colaborador_id){
            $this->authorizationError = 'Su usuario no tiene asignado un registro de colaborador.';
            return false;
        }

        //======== VERIFICAR SI EL USUARIO TIENE ROL DE SUPERVISOR =========
        $rol = DB::table('model_has_roles')
        ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
        ->where('roles.name', 'SUPERVISOR')
        ->where('model_has_roles.model_id', Auth::user()->id)
        ->exists();

        if (!$rol) {
            $this->authorizationError = 'Usted no cuenta con el rol de SUPERVISOR.';
            return false;
        }

        //======= VERIFICAR SI EL USUARIO ESTÁ SUPERVISANDO ALGÚN PROYECTO =========
        $supervisandoProyecto = DB::table('proyectos')
                                ->where('supervisor_id', Auth::user()->colaborador_id)
                                ->exists();

        if (!$supervisandoProyecto) {
            $this->authorizationError = 'Usted no se encuentra supervisando ningún proyecto.';
            return false;
        }

        //========= VERIFICAR QUE EL USUARIO NO TENGA OTRO MAESTRO DE ASISTENCIA ACTIVO =====
        $maestro_asistencia =   DB::table('registros_labor')
                                ->where('supervisor_id', Auth::user()->colaborador_id)
                                ->where('estado', "ACTIVO")
                                ->exists();

        if ($maestro_asistencia) {
            $this->authorizationError = 'YA CUENTAS CON UN PROCESO DE ASISTENCIA ACTIVO!!.';
            return false;
        }

        //=========== VERIFICAR QUE EL USUARIO NO INICIE 2 PROCESOS DE ASISTENCIA EN EL MISMO DÍA =========
        $maestro_asistencia =   DB::table('registros_labor')
                                ->where('supervisor_id', Auth::user()->colaborador_id)
                                ->where('estado', "FINALIZADO")
                                ->whereDate('fecha_asistencia', Carbon::today())                                
                                ->exists();

        if ($maestro_asistencia) {
            $this->authorizationError = 'SOLO SE PERMITE 1 PROCESO DE ASISTENCIA POR DÍA!!.';
            return false;
        }                        


        return true;
    }

    protected function failedAuthorization()
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => $this->authorizationError
        ], 403));    
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
        ];
    }
}
