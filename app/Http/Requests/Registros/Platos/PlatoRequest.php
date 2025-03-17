<?php

namespace App\Http\Requests\Registros\Platos;

use Illuminate\Foundation\Http\FormRequest;

class PlatoRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para realizar esta solicitud.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Permite la solicitud de todos los usuarios (ajústalo si es necesario)
    }

    /**
     * Obtiene las reglas de validación que se aplican a la solicitud.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'nombre' => 'required|string|max:255',
            'calorias' => 'required|string|max:255',
            'proteinas' => 'required|string|max:255',
            'carbohidratos' => 'required|string|max:255',
            'grasas' => 'required|string|max:255',
            'peso' => 'required|string|max:255',
            'costo' => 'required|numeric',
        ];
    }

    /**
     * Obtener los mensajes de error personalizados para las reglas de validación.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'nombre.required' => 'El nombre del plato es obligatorio.',
            'calorias.required' => 'Las calorías son obligatorias.',
            'proteinas.required' => 'Las proteínas son obligatorias.',
            'carbohidratos.required' => 'Los carbohidratos son obligatorios.',
            'grasas.required' => 'Las grasas son obligatorias.',
            'peso.required' => 'El peso es obligatorio.',
            'costo.required' => 'El costo es obligatorio.',
            'costo.numeric' => 'El costo debe ser un número válido.',
        ];
    }
}
