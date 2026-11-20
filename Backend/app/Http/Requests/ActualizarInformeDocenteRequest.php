<?php

namespace App\Http\Requests;

use App\Models\Informe;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class ActualizarInformeDocenteRequest extends FormRequest
{
    /**
     * Determina si el usuario autenticado está autorizado a realizar esta solicitud.
     */
    public function authorize(): bool
    {
        /** @var User|null $user */
        $user = $this->user();
        if ($user === null || (!$user->isDocente() && !$user->isCoordinador() && !$user->isAdministrador())) {
            return false;
        }

        /** @var Informe|null $informe */
        $informe = $this->route('informe');
        if ($informe instanceof Informe && !$user->isAdministrador()) {
            if ($informe->usuario_id !== $user->id) {
                return false;
            }

            if ($informe->isBloqueado()) {
                return false;
            }
        }

        return true;
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
     * Validación personalizada posterior para impedir informes duplicados del mismo curso y mes al actualizar.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $cursoId = $this->input('curso_id');
            $mes = trim($this->input('mes', ''));
            $periodo = trim($this->input('periodo', ''));
            $informe = $this->route('informe');
            $informeId = $informe instanceof Informe ? $informe->id : (int) $informe;

            if ($cursoId && $mes && $periodo) {
                preg_match('/\d{4}/', $periodo, $matches);
                $anio = $matches[0] ?? null;

                $query = Informe::where('curso_id', $cursoId)
                    ->where('id', '!=', $informeId)
                    ->whereRaw('LOWER(TRIM(mes)) = ?', [mb_strtolower($mes)]);

                if ($anio) {
                    $query->where('periodo', 'LIKE', "%{$anio}%");
                } else {
                    $query->where('periodo', $periodo);
                }

                if ($query->exists()) {
                    $periodoDesc = $anio ? " ({$anio})" : " ({$periodo})";
                    $validator->errors()->add(
                        'mes',
                        "Ya existe otro informe registrado para este curso en el mes de {$mes}{$periodoDesc}. No se permite duplicar informes para el mismo curso y mes."
                    );
                }
            }
        });
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

