<?php

namespace App\Http\Requests\TrabajoEquipos\RegistroTarea;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegistroTareaCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        //======== VERIFICAR SI EL USUARIO TIENE ROL DE SUPERVISOR =========
        $rol = DB::table('colaboradores as co')
        ->join('cargos as ca', 'ca.id', '=', 'co.cargo_id')
        ->where('ca.descripcion', 'SUPERVISOR')
        ->where('co.id', Auth::user()->colaborador_id)
        ->exists();

        if (!$rol) {
            $this->authorizationError = 'Usted no cuenta con el cargo de SUPERVISOR.';
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
