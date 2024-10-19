<?php

namespace App\Http\Requests\Logistica\GuiaRemision;

use Illuminate\Foundation\Http\FormRequest;

class GuiaRemisionStoreRequest extends FormRequest
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
        $rules = [
            'codigo_motivo_traslado'    => 'required|in:04', // Obligatorio y debe ser 04
            'serie'                     => 'nullable|string', // No es obligatorio
            'fecha_emision'             => 'required|date|after_or_equal:today', // Obligatorio y debe ser igual o posterior a la fecha actual
            'fecha_traslado'            => 'required|date|after_or_equal:today', // Obligatorio y debe ser igual o posterior a la fecha actual
            'unidad_medida_total'       => 'required|in:KGM,TNE', // Obligatorio y debe ser KGM o TNE
            'peso_total'                => 'required|numeric', // Obligatorio y debe ser numérico (entero o decimal)
            'nro_bultos'                => 'required|integer', // Obligatorio y debe ser un entero
            'codigo_modo_traslado'      => 'required|in:02', // Obligatorio y debe ser 02
            'conductor'                 => 'required|exists:conductores,id,estado,ACTIVO', // Obligatorio, debe existir en la tabla conductores con estado ACTIVO
            'vehiculo'                  => 'required|exists:vehiculos,id,estado,ACTIVO', // Obligatorio, debe existir en la tabla vehiculos con estado ACTIVO
        ];

        return $rules;
    }

    public function messages(): array
    {
        $messages = [
            'codigo_motivo_traslado.required'   => 'El código de motivo de traslado es obligatorio.',
            'codigo_motivo_traslado.in'         => 'El código de motivo de traslado debe ser "04".',
            
            'fecha_emision.required'            => 'La fecha de emisión es obligatoria.',
            'fecha_emision.date'                => 'La fecha de emisión debe ser una fecha válida.',
            'fecha_emision.after_or_equal'      => 'La fecha de emisión debe ser hoy o una fecha futura.',
            
            'fecha_traslado.required'           => 'La fecha de traslado es obligatoria.',
            'fecha_traslado.date'               => 'La fecha de traslado debe ser una fecha válida.',
            'fecha_traslado.after_or_equal'     => 'La fecha de traslado debe ser hoy o una fecha futura.',
            
            'unidad_medida_total.required'      => 'La unidad de medida total es obligatoria.',
            'unidad_medida_total.in'            => 'La unidad de medida total debe ser "KGM" o "TNE".',
            
            'peso_total.required'               => 'El peso total es obligatorio.',
            'peso_total.numeric'                => 'El peso total debe ser un número válido.',
            
            'nro_bultos.required'               => 'El número de bultos es obligatorio.',
            'nro_bultos.integer'                => 'El número de bultos debe ser un número entero.',
            
            'codigo_modo_traslado.required'     => 'El código de modo de traslado es obligatorio.',
            'codigo_modo_traslado.in'           => 'El código de modo de traslado debe ser "02".',
            
            'conductor.required'                => 'El conductor es obligatorio.',
            'conductor.exists'                  => 'El conductor debe existir y estar activo en la base de datos.',
            
            'vehiculo.required'                 => 'El vehículo es obligatorio.',
            'vehiculo.exists'                   => 'El vehículo debe existir y estar activo en la base de datos.',
        ];

        return $messages;

    }
}
