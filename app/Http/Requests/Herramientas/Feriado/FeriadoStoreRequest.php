<?php

namespace App\Http\Requests\Herramientas\Feriado;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class FeriadoStoreRequest extends FormRequest
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
            // La fecha es obligatoria, única para estado 'activo'
            'fecha' => [
                'required',
                'date',
                Rule::unique('feriados', 'fecha')->where(function ($query) {
                    return $query->where('estado', 'ACTIVO');
                }),
            ],
            // La descripción es obligatoria, con un máximo de 200 caracteres, y única por año con estado 'activo'
            'descripcion' => [
                'required',
                'string',
                'max:200',
                Rule::unique('feriados', 'descripcion')->where(function ($query) {
                    $anio = date('Y', strtotime($this->input('fecha')));
                    return $query->where('anio', $anio)->where('estado', 'ACTIVO');
                }),
            ],
        ];
    }

    /**
     * Custom error messages for validation.
     */
    public function messages(): array
    {
        return [
            'fecha.required'        => 'La fecha es obligatoria.',
            'fecha.date'            => 'La fecha debe ser válida.',
            'fecha.unique'          => 'La fecha ya se encuentra registrada como un feriado activo.',

            'descripcion.required'  => 'La descripción es obligatoria.',
            'descripcion.string'    => 'La descripción debe ser un texto válido.',
            'descripcion.max'       => 'La descripción no puede tener más de 200 caracteres.',
            'descripcion.unique'    => 'La descripción ya existe para el mismo año en estado activo.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
