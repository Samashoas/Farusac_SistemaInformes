<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class CrearCursoRequest extends FormRequest
{
    /**
     * Determina si el usuario autenticado está autorizado a realizar esta solicitud.
     */
    public function authorize(): bool
    {
        /** @var User|null $user */
        $user = $this->user();
        return $user !== null && $user->isAdministrador();
    }

    /**
     * Reglas de validación que se aplican a la solicitud.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'carrera'      => 'required|string|max:100',
            'area'         => 'required|string|max:150',
            'nombre_curso' => 'required|string|max:150',
            'codigo_curso' => 'required|string|max:50',
            'seccion'      => 'required|string|max:10',
            'anio'         => 'required|integer',
            'semestre'     => 'required|string|max:30',
        ];
    }

    /**
     * Mensajes de error personalizados.
     */
    public function messages(): array
    {
        return [
            'carrera.required'      => 'La carrera es obligatoria.',
            'area.required'         => 'El área académica es obligatoria.',
            'nombre_curso.required' => 'El nombre del curso es obligatorio.',
            'codigo_curso.required' => 'El código del curso es obligatorio.',
            'seccion.required'      => 'La sección es obligatoria.',
            'anio.required'         => 'El año es obligatorio.',
            'semestre.required'     => 'El semestre es obligatorio.',
        ];
    }
}

