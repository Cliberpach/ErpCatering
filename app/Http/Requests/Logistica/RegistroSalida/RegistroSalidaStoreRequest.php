<?php

namespace App\Http\Requests\Logistica\RegistroSalida;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
class RegistroSalidaStoreRequest extends FormRequest
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
            'almacen_origen' => 'required|integer|exists:almacenes,id', 
            'almacen_destino' => 'required|integer|different:almacen_origen|exists:almacenes,id', 
        ];
    }

    public function messages(): array
    {
        return [
            'almacen_origen.required'   => 'El almacén de origen es obligatorio.',
            'almacen_origen.integer'    => 'El almacén de origen debe ser un número válido.',
            'almacen_origen.exists'     => 'El almacén de origen seleccionado no es válido.',

            'almacen_destino.required'  => 'El almacén de destino es obligatorio.',
            'almacen_destino.integer'   => 'El almacén de destino debe ser un número válido.',
            'almacen_destino.different' => 'El almacén de destino debe ser diferente del almacén de origen.',
            'almacen_destino.exists'    => 'El almacén de destino seleccionado no es válido.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
