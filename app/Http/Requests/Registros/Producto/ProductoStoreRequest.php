<?php

namespace App\Http\Requests\Registros\Producto;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class ProductoStoreRequest extends FormRequest
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
                'max:200',
                Rule::unique('productos', 'nombre')->where(function ($query) {
                    return $query->where('estado', '<>', 'ANULADO');
                })
            ],
            'categoria'     => 'required|exists:categorias,id',
            'marca'         => 'required|exists:marcas,id',
            'unidad_medida' => 'required|exists:tablas_generales_detalles,id',
            'precio'        => 'required|numeric', 
            'stock_minimo'  => 'required|numeric', 
            
            'codigo_barras' => 'nullable|string|max:20',
            'codigo_interno'=> 'nullable|string|max:20',
        ];
    }

    public function messages()
{
    return [
        'nombre.required'           => 'El campo Nombre es obligatorio.',
        'nombre.string'             => 'El campo Nombre debe ser una cadena de texto.',
        'nombre.max'                => 'El campo Nombre no puede tener más de 200 caracteres.',
        'nombre.unique'             => 'El nombre ya está en uso, por favor elige otro.',
        
        'categoria.required'        => 'El campo Categoría es obligatorio.',
        'categoria.exists'          => 'La categoría seleccionada no existe.',
        
        'marca.required'            => 'El campo Marca es obligatorio.',
        'marca.exists'              => 'La marca seleccionada no existe.',
        
        'unidad_medida.required'    => 'El campo Unidad de Medida es obligatorio.',
        'unidad_medida.exists'      => 'La unidad de medida seleccionada no existe.',
        
        'precio.required'           => 'El campo Precio es obligatorio.',
        'precio.numeric'            => 'El campo Precio debe ser un número.',
                
        'stock_minimo.required'     => 'El campo Stock Mínimo es obligatorio.',
        'stock_minimo.numeric'      => 'El campo Stock Mínimo debe ser un número.',
 
        'codigo_barras.string'  => 'El código de barras debe ser una cadena de texto.',
        'codigo_barras.max'     => 'El código de barras no puede exceder los 20 caracteres.',

        'codigo_interno.string' => 'El código interno debe ser una cadena de texto.',
        'codigo_interno.max'    => 'El código interno no puede exceder los 20 caracteres.',
    ];
}

protected function failedValidation(Validator $validator)
{
    throw new ValidationException($validator, response()->json([
        'errors' => $validator->errors()
    ], 422));
}


}
