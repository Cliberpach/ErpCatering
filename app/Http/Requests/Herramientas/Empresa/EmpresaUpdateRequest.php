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

            'nro_inicio'        => 'required|integer|gt:0',

            'usuario_sol'       => 'nullable|string|max:100',
            'clave_sol'         => 'nullable|string|max:100',
            'usuario_api_guias' => 'nullable|string|max:100',
            'clave_api_guias'   => 'nullable|string|max:100',
            'certificado'       => 'nullable|file|mimetypes:text/plain,application/x-pem-file',  // Permitir archivos .pem
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

            'nro_inicio.required'    => 'El campo número de inicio es obligatorio.', 
            'nro_inicio.integer'     => 'El número de inicio debe ser un entero.',
            'nro_inicio.gt'          => 'El número de inicio debe ser mayor a 0.',

            'usuario_sol.max'       => 'El campo USUARIO SOL no debe exceder los 100 caracteres.',
            'clave_sol.max'         => 'El campo CLAVE SOL no debe exceder los 100 caracteres.',
            'usuario_api_guias.max' => 'El campo USUARIO API no debe exceder los 100 caracteres.',
            'clave_api_guias.max'   => 'El campo CLAVE API no debe exceder los 100 caracteres.',

            'certificado.file'      => 'El campo CERTIFICADO debe ser un archivo.',
            'certificado.mimetypes' => 'El archivo del CERTIFICADO debe ser de tipo PEM.',
            'certificado.regex'     => 'El archivo del CERTIFICADO debe tener la extensión .pem.',        
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
