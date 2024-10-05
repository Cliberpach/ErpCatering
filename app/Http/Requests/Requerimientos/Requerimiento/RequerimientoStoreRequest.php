<?php

namespace App\Http\Requests\Requerimientos\Requerimiento;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class RequerimientoStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'supervisor_id' => ['required', 'exists:colaboradores,id'], 
            'proyecto_id'   => ['required', 'exists:proyectos,id'], 
            'fecha_atencion' => ['required', 'date', 'after_or_equal:today']
        ];
    }

    public function messages(): array
    {
        return [
            'supervisor_id.required' => 'El campo supervisor es obligatorio.',
            'supervisor_id.exists'   => 'El supervisor no existe.',

            'proyecto_id.required'   => 'El campo proyecto es obligatorio.',
            'proyecto_id.exists'     => 'El proyecto no existe.',

            'fecha_atencion.required'       => 'La fecha de atención es obligatoria.',
            'fecha_atencion.date'           => 'La fecha de atención debe ser una fecha válida.',
            'fecha_atencion.after_or_equal' => 'La fecha de atención debe ser hoy o una fecha futura.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}

