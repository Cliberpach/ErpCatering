<?php

namespace App\Http\Requests\Herramientas\Usuario;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class UsuarioUpdateRequest extends FormRequest
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
            // 'nombre'            => 'required|string|max:255',
            'colaborador'       => [
                'required',
                Rule::exists('colaboradores', 'id')
            ], 
            'correo'            => 'required|email|unique:users,email,' . $this->route('id'),
            'password'          => 'required|string|min:8',
            'repetir_password'  => 'required|string|same:password',
        ];
    }

    public function messages()
    {
        return [
            // 'nombre.required'            => 'El campo nombre es obligatorio.',
            // 'nombre.string'              => 'El campo nombre debe ser una cadena de texto.',
            // 'nombre.max'                 => 'El campo nombre no puede tener más de 255 caracteres.',
            'colaborador.required'       => 'El campo colaborador es obligatorio.',
            'colaborador.integer'        => 'El campo colaborador debe ser un número entero.',
            'colaborador.exists'         => 'El colaborador seleccionado no existe en nuestra base de datos.',
            'correo.required'            => 'El campo correo es obligatorio.',
            'correo.email'               => 'El campo correo debe ser una dirección de correo electrónico válida.',
            'correo.unique'              => 'El correo electrónico ya está en uso.',
            'password.required'          => 'El campo contraseña es obligatorio.',
            'password.string'            => 'El campo contraseña debe ser una cadena de texto.',
            'password.min'               => 'La contraseña debe tener al menos 8 caracteres.',
            'repetir_password.required'  => 'El campo repetir contraseña es obligatorio.',
            'repetir_password.string'    => 'El campo repetir contraseña debe ser una cadena de texto.',
            'repetir_password.same'      => 'Las contraseñas deben coincidir.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
