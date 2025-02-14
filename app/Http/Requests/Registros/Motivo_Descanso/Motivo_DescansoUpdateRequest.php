<?php

namespace App\Http\Requests\Registros\Motivo_Descanso;

use App\Http\Requests\Registros\Motivo_Descanso\Motivo_DescansoStoreRequest;
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
            'descripcion' => 'required|string|max:255|unique:motivo_descanso,descripcion,' . $this->route('id'),
        ];
    }

    public function messages()
    {
        return (new Motivo_DescansoStoreRequest())->messages();
    }
}
