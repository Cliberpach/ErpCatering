<?php

namespace App\Http\Requests\TrabajoEquipos\RegistroTarea;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class RegistroTareaStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
         //======== VERIFICAR SI EL USUARIO TIENE CARGO DE SUPERVISOR =========
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
            'proyecto_id'       => 'required|exists:proyectos,id',
            'proyecto_nombre'   => 'required|string|max:260|exists:proyectos,nombre',
            'maquinaria'        => [
                'required',
                'exists:maquinarias,id',
                function ($attribute, $value, $fail) {
                    $proyecto_id = $this->input('proyecto_id');
                    $exists = \DB::table('proyecto_maquinaria')
                        ->where('proyecto_id', $proyecto_id)
                        ->where('maquinaria_id', $value)
                        ->exists();
                    
                    if (!$exists) {
                        $fail('La maquinaria seleccionada no está asociada con el proyecto.');
                    }
                },
            ],
            'cant_horas_viajes' => 'required|numeric',
            'observacion'       => 'nullable|string|max:300',
        ];
    }

    public function messages()
    {
        return [
            'proyecto_id.required'      => 'El campo ID del proyecto es obligatorio.',
            'proyecto_id.exists'        => 'El ID del proyecto seleccionado no existe en la base de datos.',
            
            'proyecto_nombre.required'  => 'El campo nombre del proyecto es obligatorio.',
            'proyecto_nombre.string'    => 'El campo nombre del proyecto debe ser una cadena de texto.',
            'proyecto_nombre.max'       => 'El campo nombre del proyecto no puede tener más de 260 caracteres.',
            'proyecto_nombre.exists'    => 'El nombre del proyecto seleccionado no existe en la base de datos.',
            
            'maquinaria.required'   => 'El campo maquinaria es obligatorio.',
            'maquinaria.exists'     => 'La maquinaria seleccionada no existe.',
            'maquinaria.custom'     => 'La maquinaria seleccionada no está asociada con el proyecto.',
            
            'cant_horas_viajes.required'    => 'El campo N° Horas o Viajes es obligatorio.',
            'cant_horas_viajes.numeric'     => 'El campo N° Horas o Viajes debe ser un número.',
            
            'observacion.string'            => 'El campo observación debe ser una cadena de texto.',
            'observacion.max'               => 'El campo observación no puede tener más de 300 caracteres.',
        ];
    }
    
    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }

}
