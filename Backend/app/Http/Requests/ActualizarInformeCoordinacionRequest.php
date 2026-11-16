<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class ActualizarInformeCoordinacionRequest extends FormRequest
{
    /**
     * Determina si el usuario autenticado está autorizado a realizar esta solicitud.
     */
    public function authorize(): bool
    {
        /** @var User|null $user */
        $user = $this->user();
        return $user !== null && $user->isCoordinador();
    }

    /**
     * Reglas de validación que se aplican a la solicitud.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'periodo'                         => 'required|string|max:100',
            'mes'                             => 'required|string|max:50',
            'anio'                            => 'required|integer',
            'herramientas_virtuales'          => 'nullable|string',
            'enlace_actividades_coordinacion' => 'nullable|string|max:255',
            'enlace_informe_auxiliares'       => 'nullable|string|max:255',
            'enlace_docentes_permisos'        => 'nullable|string|max:255',
            'estado'                          => 'nullable|in:borrador,enviado',
        ];
    }

    /**
     * Mensajes de error personalizados.
     */
    public function messages(): array
    {
        return [
            'periodo.required' => 'El periodo académico es obligatorio.',
            'mes.required'     => 'El mes del informe es obligatorio.',
            'anio.required'    => 'El año es obligatorio.',
        ];
    }
}

