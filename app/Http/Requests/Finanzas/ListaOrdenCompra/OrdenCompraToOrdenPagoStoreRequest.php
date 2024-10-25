<?php

namespace App\Http\Requests\Finanzas\ListaOrdenCompra;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class OrdenCompraToOrdenPagoStoreRequest extends FormRequest
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
            'observacion'       => 'nullable|string|max:200',
            'fecha_registro'    => 'required|date|after_or_equal:today',
            'medio_pago'        => ['required', 'string', Rule::in(['TRANSFERENCIA'])],

        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'observacion.max'               => 'La observación no puede tener más de 200 caracteres.',
            
            'fecha_registro.required'       => 'La fecha de registro es obligatoria.',
            'fecha_registro.date'           => 'La fecha de registro debe ser una fecha válida.',
            'fecha_registro.after_or_equal' => 'La fecha de registro debe ser hoy o una fecha posterior.',
            
            'medio_pago.required'           => 'El medio de pago es obligatorio.',
            'medio_pago.in'                 => 'El medio de pago debe ser "transferencia".'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
