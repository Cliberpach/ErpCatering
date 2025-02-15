<?php

namespace App\Http\Requests\Registros\Sedes;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class SedesUpdateRequest extends FormRequest
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
        $sedeId = $this->route('id');

        return [
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('sedes')->ignore($sedeId)
            ],
            'direccion' => 'required|string|max:500',
            'encargado' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El campo "nombre" es obligatorio.',
            'nombre.string' => 'El campo "nombre" debe ser una cadena de texto.',
            'nombre.max' => 'El campo "nombre" no debe exceder los 255 caracteres.',
            'nombre.unique' => 'Este nombre de sede ya está registrado.',

            'direccion.required' => 'El campo "dirección" es obligatorio.',
            'direccion.string' => 'El campo "dirección" debe ser una cadena de texto.',
            'direccion.max' => 'El campo "dirección" no debe exceder los 500 caracteres.',

            'encargado.required' => 'El campo "encargado" es obligatorio.',
            'encargado.string' => 'El campo "encargado" debe ser una cadena de texto.',
            'encargado.max' => 'El campo "encargado" no debe exceder los 255 caracteres.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
