<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProyectoRegimenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'regimen_id' => 'nullable|exists:regimenes,id',
            'horario_id' => 'nullable|exists:horarios,id',
            'estado' => 'required|in:ACTIVO,INACTIVO',
        ];
    }
}
