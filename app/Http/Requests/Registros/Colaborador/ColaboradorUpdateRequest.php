<?php

namespace App\Http\Requests\Registros\Colaborador;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
class ColaboradorUpdateRequest extends FormRequest
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
            'tipo_documento' => 'required|integer|in:1,2',
            'nro_documento' => [
                'required',
                'string',
                'unique:colaboradores,nro_documento,' . $this->route('id'),
                function($attribute, $value, $fail) {
                    if ($this->input('tipo_documento') == 1) {
                        // Si tipo_documento es 1, el nro_documento debe tener exactamente 8 caracteres
                        if (strlen($value) != 8) {
                            $fail('El número de documento debe tener exactamente 8 caracteres para el tipo de documento DNI.');
                        }
                    } elseif ($this->input('tipo_documento') == 2) {
                        // Si tipo_documento es 2, el nro_documento debe tener como máximo 20 caracteres
                        if (strlen($value) > 20) {
                            $fail('El número de documento no debe superar los 20 caracteres para el tipo de documento CARNET DE EXTRANJERÍA.');
                        }
                    }
                },
            ],
            'nombre'        => 'required|max:260|unique:colaboradores,nombre,'.$this->route('id'),
            'direccion'     => 'nullable|max:200',
            'telefono'      => ['required', 'max:20', 'regex:/^[0-9]+$/'],
            'horas_semana'  => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'pago_semana'   => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'tipo_documento.required'   => 'El tipo de documento es obligatorio.',
            'tipo_documento.integer'    => 'El tipo de documento debe ser un número entero.',
            'tipo_documento.in'         => 'El tipo de documento debe ser 1 o 2.',

            'nro_documento.required'    => 'El número de documento es obligatorio.',
            'nro_documento.string'      => 'El número de documento debe ser una cadena de texto.',
            'nro_documento.unique'      => 'El número de documento ya existe.', 

            'nombre.required'           => 'El nombre es obligatorio.',
            'nombre.max'                => 'El nombre no debe superar los 260 caracteres.',
            'nombre.unique'             => 'El nombre ya está en uso, por favor elige otro.',

            'direccion.max'             => 'La dirección no debe superar los 200 caracteres.',

            'telefono.required'         => 'El teléfono es obligatorio.',
            'telefono.max'              => 'El teléfono no debe superar los 20 caracteres.',
            'telefono.regex'            => 'El teléfono debe contener solo números.',

            'horas_semana.required'     => 'Las horas de la semana son obligatorias.',
            'horas_semana.numeric'      => 'Las horas de la semana deben ser un número.',
            'horas_semana.regex'        => 'Las horas de la semana deben tener como máximo 2 decimales.',

            'pago_semana.required'      => 'El pago semanal es obligatorio.',
            'pago_semana.numeric'       => 'El pago semanal debe ser un número.',
            'pago_semana.regex'         => 'El pago semanal debe tener como máximo 2 decimales.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
