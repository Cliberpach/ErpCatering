<?php

namespace App\Http\Requests\Registros\Horario;

use Illuminate\Foundation\Http\FormRequest;

class HorarioStoreRequest extends FormRequest
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
        return [
            'nombre_proyecto.required'    => 'El nombre del proyecto es obligatorio.',
            'hora_inicio.required'        => 'La hora de inicio es obligatoria.',
            'hora_inicio.date_format'     => 'El formato de la hora de inicio debe ser HH:MM.',
            'hora_final.required'         => 'La hora de finalización es obligatoria.',
            'hora_final.date_format'      => 'El formato de la hora final debe ser HH:MM.',
            'hora_final.after'            => 'La hora final debe ser después de la hora de inicio.',
            'descripcion.max'             => 'La descripción no puede exceder los 500 caracteres.',
            'minutos_tolerancia.required' => 'Los minutos de tolerancia son obligatorios.',
            'minutos_tolerancia.integer'  => 'Los minutos de tolerancia deben ser un número entero.',
            'minutos_tolerancia.min'      => 'Los minutos de tolerancia no pueden ser negativos.',
        ];
    }
}
