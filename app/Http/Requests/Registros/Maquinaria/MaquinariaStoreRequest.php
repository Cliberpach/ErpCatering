<?php

namespace App\Http\Requests\Registros\Maquinaria;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class MaquinariaStoreRequest extends FormRequest
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
        $rules = [
            'nombre' => [
                'required',
                'string',
                'max:200',
                Rule::unique('maquinarias')->where(function ($query) {
                    return $query->where('estado', '<>', 'ANULADO');
                })
            ],
            'tipo_gasto' => 'required|exists:tablas_generales_detalles,id',
            'costo_gasto' => 'required|numeric|min:0',
            'observacion' => 'nullable|string|max:260',
        ];
    
        
        return $rules;
    }

    public function messages(){
        $messages = [
            'nombre.required'   => 'El campo Nombre es obligatorio.',
            'nombre.string'     => 'El campo Nombre debe ser una cadena de texto.',
            'nombre.max'        => 'El campo Nombre no debe exceder los 200 caracteres.',
            'nombre.unique'     => 'El Nombre ingresado ya existe para una maquinaria activa.',

            'tipo_gasto.required'   => 'El campo TIPO DE GASTO es obligatorio.',
            'tipo_gasto.exists'     => 'El TIPO DE GASTO seleccionado no es válido.',

            'costo_gasto.required'  => 'El campo COSTO GASTO es obligatorio.',
            'costo_gasto.numeric'   => 'El campo COSTO GASTO debe ser un número.',
            'costo_gasto.min'       => 'El campo COSTO GASTO debe ser un valor positivo.',

            'observacion.string' => 'El campo Observación debe ser una cadena de texto.',
            'observacion.max' => 'El campo Observación no debe exceder los 1000 caracteres.',
        ];

        return $messages;
    }

    protected function failedValidation(Validator $validator)
{
    throw new ValidationException($validator, response()->json([
        'errors' => $validator->errors()
    ], 422));
}

}
