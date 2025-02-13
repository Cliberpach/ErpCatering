<?php

namespace App\Http\Requests\Registros\MotivoDescanso;

use Illuminate\Foundation\Http\FormRequest;

class Motivo_DescansoUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'descripcion' => 'required|string|max:255|unique:motivo_descansos,descripcion,' . $this->route('id'),
        ];
    }

    public function messages()
    {
        return (new Motivo_DescansoStoreRequest())->messages();
    }
}
