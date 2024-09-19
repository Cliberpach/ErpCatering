<?php

namespace App\Http\Requests\Logistica\RegistroCompra;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
class RegistroCompraStoreRequest extends FormRequest
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
            'fecha_emision' => 'required|date_format:Y-m-d',
            'fecha_entrega' => 'required|date_format:Y-m-d',
            'proveedor'     => 'required|exists:proveedores,id',
            'moneda'        => 'required|in:SOLES,DÓLARES',
            'tipo_cambio'   => 'required|numeric',
            'tipo_doc'      => 'required|in:BOLETA,FACTURA',
            'serie'         => 'required|max:20',
            'numero'        => 'required|numeric|digits_between:1,20',
            'observacion'   => 'max:300',

            'lstCompra' => 'required|json', 
        ];
    }

    public function messages(): array
    {
        $messages = [
            'fecha_emision.required'        => 'La fecha de emisión es obligatoria.',
            'fecha_emision.date_format'     => 'El formato de la fecha de emisión debe ser YYYY-MM-DD.',
            
            'fecha_entrega.required'        => 'La fecha de entrega es obligatoria.',
            'fecha_entrega.date_format'     => 'El formato de la fecha de entrega debe ser YYYY-MM-DD.',
            
            'proveedor.required'            => 'El proveedor es obligatorio.',
            'proveedor.exists'              => 'El proveedor seleccionado no existe.',
            
            'moneda.required'               => 'La moneda es obligatoria.',
            'moneda.in'                     => 'La moneda debe ser SOLES o DÓLARES.',
            
            'tipo_cambio.required'          => 'El tipo de cambio es obligatorio.',
            'tipo_cambio.numeric'           => 'El tipo de cambio debe ser un número.',
            
            'tipo_doc.required'             => 'El tipo de documento es obligatorio.',
            'tipo_doc.in'                   => 'El tipo de documento debe ser BOLETA o FACTURA.',
            
            'serie.required'                => 'La serie es obligatoria.',
            'serie.max'                     => 'La serie no puede tener más de 20 caracteres.',
            
            'numero.required'               => 'El número es obligatorio.',
            'numero.numeric'                => 'El número debe ser un valor numérico.',
            'numero.digits_between'         => 'El campo número debe tener entre 1 y 20 dígitos.',
            
            'observacion.max'               => 'La observación no puede tener más de 300 caracteres.',
            
            'lstCompra.required'            => 'La lista de compra es obligatoria.',
            'lstCompra.array'               => 'La lista de compra debe ser un arreglo válido.',
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
