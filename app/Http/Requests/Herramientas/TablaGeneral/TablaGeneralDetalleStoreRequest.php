<?php

namespace App\Http\Requests\Herramientas\TablaGeneral;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class TablaGeneralDetalleStoreRequest extends FormRequest
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
            'descripcion' => [
                'required',
                'string',
                'max:200',
                Rule::unique('tablas_generales_detalles', 'descripcion')
                    ->where(function ($query) {
                        return $query->where('tabla_general_id', $this->input('tabla_general_id'));
                    })
            ],
            'simbolo'           =>   [
                'required',
                'string',
                'max:10',
                Rule::unique('tablas_generales_detalles', 'simbolo')
                    ->where(function ($query) {
                        return $query->where('tabla_general_id', $this->input('tabla_general_id'));
                    })
            ],
            'tabla_general_id'  => 'required|integer|exists:tablas_generales,id',
        ];
    }

    public function messages(): array
    {
        return [
            'descripcion.required'  => 'El campo descripción es obligatorio.',
            'descripcion.string'    => 'El campo descripción debe ser una cadena de texto.',
            'descripcion.max'       => 'El campo descripción no puede tener más de 200 caracteres.',
            'descripcion.unique'    => 'La descripción ya existe para el id de tabla general especificado.',

            'simbolo.required'      => 'El campo símbolo es obligatorio.',
            'simbolo.string'        => 'El campo símbolo debe ser una cadena de texto.',
            'simbolo.max'           => 'El campo símbolo no puede tener más de 10 caracteres.', 
            'simbolo.unique'        => 'El campo símbolo ya existe para el id de tabla general especificado.',

            'tabla_general_id.required'     => 'El campo ID de la tabla general es obligatorio.',
            'tabla_general_id.integer'      => 'El campo ID de la tabla general debe ser un número entero.',
            'tabla_general_id.exists'       => 'El campo ID de la tabla general debe existir en la tabla tablas_generales.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}

