<?php

namespace App\Http\Requests\Registros\Almacen;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
class AlmacenAsignarProyecto extends FormRequest
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
            'proyecto' => [
                'nullable', // Permite que el campo sea opcional
                'exists:proyectos,id', // Verifica que el proyecto exista en la tabla proyectos
                function ($attribute, $value, $fail) {
                    if ($value) { // Solo realiza la verificación si el valor está presente
                        $proyecto = DB::table('proyectos')
                            ->where('id', $value)
                            ->where('estado', 'ANULADO')
                            ->first();

                        if ($proyecto) {
                            $fail('El proyecto está anulado y no puede ser seleccionado.');
                        }
                    }
                }
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'proyecto.exists' => 'El proyecto seleccionado no existe.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }

}
