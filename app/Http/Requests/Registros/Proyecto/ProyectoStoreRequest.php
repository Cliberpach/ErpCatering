<?php

namespace App\Http\Requests\Registros\Proyecto;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
class ProyectoStoreRequest extends FormRequest
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
    public function rules()
    {
        return [
            'nombre' => [
                'required',
                'string',
                'max:260',
                'unique:proyectos,nombre'
            ],
            'costo'         => 'required|numeric|min:0|regex:/^\d*(\.\d{1,2})?$/',
            'avance_costo'  => 'required|numeric|min:0|regex:/^\d*(\.\d{1,2})?$/',
            'diferencia'    => [
                'required',
                'numeric',
                'regex:/^-?\d*(\.\d{1,2})?$/',
                function ($attribute, $value, $fail) {
                    $costo = $this->input('costo');
                    $avance_costo = $this->input('avance_costo');

                    if ($costo !== null && $avance_costo !== null) {
                        $diferencia_esperada = $costo - $avance_costo;
                        $value = floatval($value);

                        if (abs($value - $diferencia_esperada) > 0.01) {
                            $fail('La diferencia debe ser la resta entre costo y avance costo.');
                        }
                    }
                }
            ],
        ];
    }

    public function messages()
    {
        return [
            'nombre.required'   => 'El campo nombre es obligatorio.',
            'nombre.max'        => 'El campo nombre no puede tener más de 260 caracteres.',
            'nombre.unique'     => 'El nombre ya existe en la base de datos.',

            'costo.required'    => 'El campo costo es obligatorio.',
            'costo.numeric'     => 'El campo costo debe ser un número.',
            'costo.regex'       => 'El campo costo debe ser un número decimal con hasta dos decimales.',

            'avance_costo.required' => 'El campo avance costo es obligatorio.',
            'avance_costo.numeric'  => 'El campo avance costo debe ser un número.',
            'avance_costo.regex'    => 'El campo avance costo debe ser un número decimal con hasta dos decimales.',

            'diferencia.required'   => 'El campo diferencia es obligatorio.',
            'diferencia.numeric'    => 'El campo diferencia debe ser un número.',
            'diferencia.regex'      => 'El campo diferencia debe ser un número decimal con hasta dos decimales.',
            'diferencia.custom'     => 'La diferencia debe ser la resta entre costo y avance costo.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
