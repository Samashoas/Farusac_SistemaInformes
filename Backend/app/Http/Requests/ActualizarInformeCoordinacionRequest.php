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
            'programas'                       => 'nullable|array',
            'programas.*.asignatura'          => 'nullable|string|max:200',
            'programas.*.enlace_programa'     => 'nullable|string|max:255',
            'asignaturas'                     => 'nullable|array',
            'asignaturas.*.curso_id'          => 'nullable',
            'asignaturas.*.docente_nombre'    => 'nullable|string|max:150',
            'asignaturas.*.asignatura'        => 'nullable|string|max:150',
            'asignaturas.*.seccion'           => 'nullable|string|max:50',
            'asignaturas.*.presento_informe'  => 'nullable',
            'asignaturas.*.tiene_sala_reuniones' => 'nullable',
            'asignaturas.*.funciona_enlace_virtual' => 'nullable',
            'asignaturas.*.funciona_enlace_evaluacion' => 'nullable',
            'asignaturas.*.evidencias_generales' => 'nullable',
            'asignaturas.*.observaciones'     => 'nullable|string',
            'avances'                         => 'nullable|array',
            'avances.*.curso_id'              => 'nullable',
            'avances.*.docente_nombre'        => 'nullable|string|max:150',
            'avances.*.asignatura'            => 'nullable|string|max:150',
            'avances.*.seccion'               => 'nullable|string|max:50',
            'avances.*.porcentaje_avance'     => 'nullable',
            'avances.*.observaciones'         => 'nullable|string',
            'estudiantes'                     => 'nullable|array',
            'estudiantes.*.curso_id'          => 'nullable',
            'estudiantes.*.docente_nombre'    => 'nullable|string|max:150',
            'estudiantes.*.asignatura'        => 'nullable|string|max:150',
            'estudiantes.*.seccion'           => 'nullable|string|max:50',
            'estudiantes.*.cantidad_estudiantes' => 'nullable',
            'estudiantes.*.carne_estudiantes' => 'nullable|string',
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

