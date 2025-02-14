<?php

namespace App\Http\Requests\Registros\Motivo_Descanso;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class Motivo_DescansoStoreRequest extends FormRequest
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
            'descripcion' => 'required|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
          
            'descripcion.required' => 'El campo "descripción" es obligatoraio.',
            'descripcion.string' => 'El campo "descripción" debe ser una cadena de texto.',
            'descripcion.max' => 'El campo "descripción" no debe exceder los 500 caracteres.',

        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}