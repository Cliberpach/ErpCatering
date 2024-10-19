<?php

namespace App\Http\Requests\Registros\Vehiculo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class VehiculoStoreRequest extends FormRequest
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
            'placa'     => [
                'required',
                'string',
                'min:6',
                'max:8',
                Rule::unique('vehiculos')->where(function ($query) {
                    return $query->where('estado', 'ACTIVO'); 
                })
            ],
            'modelo'    => 'required|string|max:100',
            'marca'     => 'required|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'placa.required'    => 'El campo "placa" es obligatorio.',
            'placa.string'      => 'El campo "placa" debe ser una cadena de texto.',
            'placa.min'         => 'El campo "placa" debe tener al menos 6 caracteres.',
            'placa.max'         => 'El campo "placa" no debe exceder los 8 caracteres.',
            'placa.unique'      => 'La placa ya está registrada para un vehículo con estado ACTIVO.',

            'modelo.required'   => 'El campo "modelo" es obligatorio.',
            'modelo.string'     => 'El campo "modelo" debe ser una cadena de texto.',
            'modelo.max'        => 'El campo "modelo" no debe exceder los 100 caracteres.',

            'marca.required'    => 'El campo "marca" es obligatorio.',
            'marca.string'      => 'El campo "marca" debe ser una cadena de texto.',
            'marca.max'         => 'El campo "marca" no debe exceder los 100 caracteres.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
