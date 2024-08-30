<?php

namespace App\Http\Requests\Herramientas\Rol;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
class RolUpdateRequest extends FormRequest
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
            'nombre' => 'required|string|max:255|unique:roles,name,'.$this->route('id'),
        ];
    }

    public function messages()
    {
        return [
            'nombre.required'               =>  'El campo nombre es obligatorio.',
            'nombre.string'                 =>  'El campo nombre debe ser una cadena de texto.',
            'nombre.max'                    =>  'El campo nombre no puede tener más de 255 caracteres.',
            'nombre.unique'                 =>  'El nombre ya está en uso, por favor elige otro.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
