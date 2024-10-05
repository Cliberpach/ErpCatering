<?php

namespace App\Http\Requests\Logistica\Requerimiento;

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
        ];
    }

    public function messages(): array
    {
        return [
            'supervisor_id.required' => 'El campo supervisor es obligatorio.',
            'supervisor_id.exists'   => 'El supervisor no existe.',
            'proyecto_id.required'   => 'El campo proyecto es obligatorio.',
            'proyecto_id.exists'     => 'El proyecto no existe.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}

