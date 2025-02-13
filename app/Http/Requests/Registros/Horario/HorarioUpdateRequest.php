<?php

namespace App\Http\Requests\Registros\Horario;

use Illuminate\Foundation\Http\FormRequest;

class HorarioUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nombre_proyecto'    => 'required|string|max:255',
            'hora_inicio'        => 'required|date_format:H:i',
            'hora_final'         => 'required|date_format:H:i|after:hora_inicio',
            'descripcion'        => 'nullable|string|max:500',
            'minutos_tolerancia' => 'required|integer|min:0',
        ];
    }

    public function messages()
    {
        return (new HorarioStoreRequest())->messages();
    }
}
