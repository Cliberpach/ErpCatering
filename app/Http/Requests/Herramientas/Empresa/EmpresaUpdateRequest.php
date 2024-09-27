<?php

namespace App\Http\Requests\Herramientas\Empresa;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
class EmpresaUpdateRequest extends FormRequest
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
            'ruc'           => 'required|numeric|digits:11',
            'razon_social'  => 'required|string|max:150',
            'direccion'     => 'nullable|string|max:150',
            'telefono'      => 'nullable|string|max:20|regex:/^\+?[0-9\s\-]*$/',
            'correo'        => 'nullable|email|max:100',
        ];
    }

    public function messages()
    {
        return [
            'ruc.required'          => 'El campo RUC es obligatorio.',
            'ruc.numeric'           => 'El RUC debe ser numérico.',
            'ruc.digits'            => 'El RUC debe tener exactamente 11 dígitos.',
            
            'razon_social.required' => 'El campo razón social es obligatorio.',
            'razon_social.max'      => 'La razón social no puede exceder de 150 caracteres.',
            
            'direccion.max'         => 'La dirección no puede exceder de 150 caracteres.',
            
            'telefono.max'          => 'El teléfono no puede exceder de 20 caracteres.',
            'telefono.regex'        => 'El formato del teléfono es inválido. Solo se permiten números, espacios, guiones y el símbolo más.',
            
            'correo.email'          => 'El correo debe tener un formato válido.',
            'correo.max'            => 'El correo no puede exceder de 100 caracteres.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
