<?php

namespace App\Http\Requests\PlanProyecto\Tarea;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
class TareaUpdateRequest extends FormRequest
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
            'tarea_nombre'          => 'required|max:150',
            'tarea_fecha_inicio'    => 'required|date|before_or_equal:tarea_fecha_fin',
            'tarea_fecha_fin'       => 'required|date|after_or_equal:tarea_fecha_inicio',
            'tarea_observacion'     => 'nullable|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'tarea_nombre.required' => 'El nombre de la tarea es obligatorio.',
            'tarea_nombre.max'      => 'El nombre de la tarea no debe superar los 150 caracteres.',
            
            'tarea_fecha_inicio.required'           => 'La fecha de inicio es obligatoria.',
            'tarea_fecha_inicio.date'               => 'La fecha de inicio debe ser una fecha válida.',
            'tarea_fecha_inicio.before_or_equal'    => 'La fecha de inicio no puede ser posterior a la fecha de fin.',
            
            'tarea_fecha_fin.required'          => 'La fecha de fin es obligatoria.',
            'tarea_fecha_fin.date'              => 'La fecha de fin debe ser una fecha válida.',
            'tarea_fecha_fin.after_or_equal'    => 'La fecha de fin no puede ser anterior a la fecha de inicio.',
            
            'tarea_observacion.max' => 'La observación no debe superar los 300 caracteres.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
