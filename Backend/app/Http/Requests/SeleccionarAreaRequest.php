<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;

class SeleccionarAreaRequest extends FormRequest
{
    /**
     * Determina si el usuario autenticado está autorizado a seleccionar o fijar el área.
     * Centraliza la autorización para evitar lógica duplicada en el controlador o middleware.
     */
    public function authorize(): bool
    {
        /** @var User|null $user */
        $user = $this->user();

        // Debe estar autenticado y tener el rol de Coordinador
        if (!$user || !$user->isCoordinador()) {
            return false;
        }

        // Si ya tiene un área asignada en la base de datos, no se permite su modificación
        if (!empty($user->area)) {
            throw new AuthorizationException(
                'El área de coordinación ya ha sido asignada previamente y no puede ser modificada.'
            );
        }

        return true;
    }

    /**
     * Reglas de validación para la solicitud.
     */
    public function rules(): array
    {
        return [
            'area'    => 'required|string|max:150',
            'carrera' => 'nullable|string|max:100',
        ];
    }

    /**
     * Mensajes personalizados para las reglas de validación.
     */
    public function messages(): array
    {
        return [
            'area.required' => 'Debe seleccionar un área de la lista para continuar.',
        ];
    }
}

