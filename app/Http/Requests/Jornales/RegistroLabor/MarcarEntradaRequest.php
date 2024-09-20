<?php

namespace App\Http\Requests\Jornales\RegistroLabor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use DB;
use Auth;
class MarcarEntradaRequest extends FormRequest
{
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
        $rol    =   DB::table('model_has_roles')
                    ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                    ->where('roles.name', 'SUPERVISOR')
                    ->where('model_has_roles.model_id', Auth::user()->id)
                    ->exists();

        if (!$rol) {
            $this->authorizationError = 'Usted no cuenta con el rol de SUPERVISOR.';
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
            'tipo_asistencia'   => 'nullable|in:AUTOMATICO',
            'hora_entrada'      => 'required_without:tipo_asistencia|nullable|date_format:H:i',
            'registro_labor_id' => 'required|exists:registros_labor,id',
            'colaborador_id'    => 'required|exists:colaboradores,id',
        ];
    }

    public function messages(): array
    {
        return [
            'tipo_asistencia.in'            => 'Valor no válido para el tipo de asistencia.',

            'hora_entrada.required_without' => 'Hora entrada obligatoria para asistencia manual.',
            'hora_entrada.date_format'      => 'La hora de entrada debe tener el formato HH:MM.',

            'registro_labor_id.required'    => 'El registro de labor es obligatorio.',
            'registro_labor_id.exists'      => 'No existe el registro de labor en la BD.',

            'colaborador_id.required'       => 'El colaborador es obligatorio.',
            'colaborador_id.exists'         => 'No existe el colaborador en la BD.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
