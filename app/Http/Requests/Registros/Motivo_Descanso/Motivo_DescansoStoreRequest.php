<?php

namespace App\Http\Requests\Registros\MotivoDescanso;

use Illuminate\Foundation\Http\FormRequest;

class Motivo_DescansoStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'descripcion' => 'required|string|max:255|unique:motivo_descansos,descripcion',
        ];
    }

    public function messages()
    {
        return [
            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.string'   => 'La descripción debe ser un texto.',
            'descripcion.max'      => 'La descripción no puede exceder los 255 caracteres.',
            'descripcion.unique'   => 'Ya existe un motivo de descanso con esta descripción.',
        ];
    }
}
