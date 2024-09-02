<?php

namespace App\Http\Requests\Registros\Proyecto;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
class ProyectoAsignarSupervisorRequest extends FormRequest
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
            'supervisor' => [
                'nullable', // Permite que el campo sea opcional
                'exists:users,id', // Verifica que el usuario exista en la tabla users
                function ($attribute, $value, $fail) {
                    if ($value) { // Solo realiza la verificación si el valor está presente
                        $usuario = DB::table('users')
                            ->where('id', $value)
                            ->where('estado', 'ANULADO')
                            ->first();

                        if ($usuario) {
                            $fail('El supervisor está anulado y no puede ser seleccionado.');
                        }
                    }
                }
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'supervisor.exists' => 'El supervisor seleccionado no existe.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
