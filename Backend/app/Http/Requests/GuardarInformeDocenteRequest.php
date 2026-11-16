<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class GuardarInformeDocenteRequest extends FormRequest
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
            'curso_id'                           => 'required|exists:cursos,id',
            'periodo'                            => 'required|string',
            'mes'                                => 'required|string',
            'estudiantes_asignados'              => 'required|integer|min:1',
            'listado_asistencia_url'             => 'nullable|string|max:255',
            'enlace_evidencia_url'               => 'nullable|string|max:255',
            'enlace_meet_zoom_url'               => 'nullable|string|max:255',
            'enlace_classroom_drive_url'         => 'nullable|string|max:255',
            'estrategias_evaluacion'             => 'nullable|string',
            'semanas'                            => 'required|array|min:1',
            'semanas.*.numero_semana'            => 'required|integer',
            'semanas.*.actividad_realizada'      => 'required|string',
            'semanas.*.estudiantes_participaron' => 'nullable|integer|min:0',
            'semanas.*.metodologias'             => 'nullable|string',
            'semanas.*.medios_comunicacion'      => 'nullable|string',
        ];
    }

    /**
     * Mensajes de error personalizados.
     */
    public function messages(): array
    {
        return [
            'curso_id.required'                     => 'Debe seleccionar un curso válido.',
            'curso_id.exists'                       => 'El curso seleccionado no existe.',
            'periodo.required'                      => 'El periodo es obligatorio.',
            'mes.required'                          => 'El mes es obligatorio.',
            'estudiantes_asignados.required'        => 'La cantidad de estudiantes asignados es obligatoria.',
            'estudiantes_asignados.min'             => 'La cantidad de estudiantes asignados debe ser mayor a 0.',
            'semanas.required'                      => 'Debe registrar al menos una semana de actividades.',
            'semanas.*.actividad_realizada.required' => 'El contenido o actividad realizada es obligatorio en cada semana.',
        ];
    }
}

