<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class AsignarCursoRequest extends FormRequest
{
    /**
     * Determina si el usuario autenticado está autorizado a realizar esta solicitud.
     */
    public function authorize(): bool
    {
        /** @var User|null $user */
        $user = $this->user();
        return $user !== null && ($user->isDocente() || $user->isCoordinador());
    }

    /**
     * Reglas de validación que se aplican a la solicitud.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'curso_id' => 'required|exists:cursos,id',
        ];
    }

    /**
     * Mensajes de error personalizados.
     */
    public function messages(): array
    {
        return [
            'curso_id.required' => 'Debes seleccionar una sección válida.',
            'curso_id.exists'   => 'El curso seleccionado no existe en el sistema.',
        ];
    }
}

