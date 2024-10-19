<?php

namespace App\Http\Requests\Registros\Vehiculo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class VehiculoUpdateRequest extends FormRequest
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
        $vehiculoId = $this->route('id');

        return [
            'placa'     => [
                'required',
                'string',
                'min:6',
                'max:8',
                Rule::unique('vehiculos')->ignore($vehiculoId)->where(function ($query) {
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
            'placa.required'    => 'La placa es obligatoria.',
            'placa.string'      => 'La placa debe ser una cadena de texto.',
            'placa.min'         => 'La placa debe tener al menos 6 caracteres.',
            'placa.max'         => 'La placa no debe exceder 8 caracteres.',
            'placa.unique'      => 'La placa ya está registrada para un vehículo con estado ACTIVO.',
            
            'modelo.required'   => 'El modelo es obligatorio.',
            'modelo.string'     => 'El modelo debe ser una cadena de texto.',
            'modelo.max'        => 'El modelo no debe exceder 100 caracteres.',
            
            'marca.required'    => 'La marca es obligatoria.',
            'marca.string'      => 'La marca debe ser una cadena de texto.',
            'marca.max'         => 'La marca no debe exceder 100 caracteres.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
