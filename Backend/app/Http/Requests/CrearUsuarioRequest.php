<?php

namespace App\Http\Requests;

use App\Enums\Rol;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class CrearUsuarioRequest extends FormRequest
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
            'nombre' => 'required|string|max:150',
            'correo' => 'required|email|max:100|unique:usuarios,correo',
            'numero' => 'required|string|max:30',
            'rol'    => ['required', new Enum(Rol::class)],
            'plaza'  => 'required|in:titular + ampliacion,titular,interino',
        ];
    }

    /**
     * Mensajes de error personalizados.
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del usuario es obligatorio.',
            'correo.required' => 'El correo electrónico es obligatorio.',
            'correo.email'    => 'El correo debe ser una dirección válida.',
            'correo.unique'   => 'Este correo electrónico ya está registrado.',
            'numero.required' => 'El registro de personal/número es obligatorio.',
            'rol.required'    => 'El rol institucional es obligatorio.',
            'plaza.required'  => 'La plaza docente o de personal es obligatoria.',
        ];
    }
}

