<?php

namespace App\Http\Requests\Compras\OrdenCompra;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrdenCompraUpdateRequest extends FormRequest
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
            'fecha_entrega'     => 'required|date',
            'igv' => [
                'nullable',
                'numeric',
                function ($attribute, $value, $fail) {
                    $empresaIgv = DB::table('empresas')->where('id', 1)->value('igv');
                    if (!is_null($value) && $value != $empresaIgv) {
                        $fail('El valor de IGV debe coincidir con el valor configurado en la empresa.');
                    }
                }
            ],
            'valor_igv' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) {
                    $empresaIgv = DB::table('empresas')->where('id', 1)->value('igv');
                    if (!is_null($value) && $value != $empresaIgv) {
                        $fail('El valor de IGV debe coincidir con el valor configurado en la empresa.');
                    }
                }
            ],
            'moneda'            => 'required|in:PEN,USD',
            'tipo_cambio'       => 'required|numeric|gt:0',
            'terminos_entrega'  => 'required|string|max:100',
            'proveedor'         => 'required|exists:proveedores,id,estado,ACTIVO',
            'direccion'         => 'required|string|max:200',
            'modalidad_pago'    => 'required|exists:modalidades_pago,id,estado,ACTIVO',
            'tipo_doc'          => 'required|in:FACTURA,BOLETA',
            'persona_contacto'  => 'required|exists:colaboradores,id,estado,ACTIVO',
            'observacion'       => 'nullable|string|max:200',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'fecha_entrega.required'    => 'La fecha de entrega es obligatoria.',
            'fecha_entrega.date'        => 'La fecha de entrega debe estar en un formato de fecha válido.',
            
            'igv.numeric'               => 'El campo IGV debe ser un número válido.',

            'valor_igv.required'        => 'El valor del IGV es obligatorio.',
            'valor_igv.numeric'         => 'El valor del IGV debe ser numérico.',

            'moneda.required'           => 'El campo moneda es obligatorio.',
            'moneda.in'                 => 'El campo moneda debe ser PEN o USD.',

            'tipo_cambio.required'      => 'El tipo de cambio es obligatorio.',
            'tipo_cambio.numeric'       => 'El tipo de cambio debe ser un número válido.',
            'tipo_cambio.gt'            => 'El tipo de cambio debe ser mayor a 0.',

            'terminos_entrega.required' => 'Los términos de entrega son obligatorios.',
            'terminos_entrega.max'      => 'Los términos de entrega no pueden exceder 100 caracteres.',
            
            'proveedor.required'        => 'El proveedor es obligatorio.',
            'proveedor.exists'          => 'El proveedor seleccionado no existe o no está activo.',
            
            'direccion.required'        => 'La dirección es obligatoria.',
            'direccion.max'             => 'La dirección no puede exceder 200 caracteres.',
            
            'modalidad_pago.required'   => 'La modalidad de pago es obligatoria.',
            'modalidad_pago.exists'     => 'La modalidad de pago seleccionada no existe o no está activa.',
            
            'tipo_doc.required'         => 'El tipo de documento es obligatorio.',
            'tipo_doc.in'               => 'El tipo de documento debe ser FACTURA o BOLETA.',
            
            'persona_contacto.required' => 'La persona de contacto es obligatoria.',
            'persona_contacto.exists'   => 'La persona de contacto seleccionada no existe o no está activa.',
            
            'observacion.max'           => 'La observación no puede exceder 200 caracteres.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
