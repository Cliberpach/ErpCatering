<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProyectoRegimenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'proyecto_id' => 'required|exists:proyectos,id',
            'colaborador_id' => 'required|exists:colaboradores,id',
            'regimen_id' => 'nullable|exists:regimenes,id',
            'horario_id' => 'nullable|exists:horarios,id',
            'estado' => 'required|in:ACTIVO,INACTIVO',
        ];
    }
}
