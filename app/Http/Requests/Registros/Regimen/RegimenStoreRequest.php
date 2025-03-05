<?php

namespace App\Http\Requests\Registros\Regimen;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class RegimenStoreRequest extends FormRequest
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
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('regimens')
            ],
            'descripcion' => 'nullable|string|max:500',
            'dias_trabajo' => 'required|integer|min:1',
            'dias_descanso' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El campo "nombre" es obligatorio.',
            'nombre.string' => 'El campo "nombre" debe ser una cadena de texto.',
            'nombre.max' => 'El campo "nombre" no debe exceder los 255 caracteres.',
            'nombre.unique' => 'Este nombre de régimen ya está registrado.',

            'descripcion.required' => 'El campo "descripción" es obligatorio.',
            'descripcion.string' => 'El campo "descripción" debe ser una cadena de texto.',
            'descripcion.max' => 'El campo "descripción" no debe exceder los 500 caracteres.',

            'dias_trabajo.required' => 'El campo "días de trabajo" es obligatorio.',
            'dias_trabajo.integer' => 'El campo "días de trabajo" debe ser un número entero.',
            'dias_trabajo.min' => 'El campo "días de trabajo" debe ser al menos 1.',

            'dias_descanso.required' => 'El campo "días de descanso" es obligatorio.',
            'dias_descanso.integer' => 'El campo "días de descanso" debe ser un número entero.',
            'dias_descanso.min' => 'El campo "días de descanso" debe ser al menos 1.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}