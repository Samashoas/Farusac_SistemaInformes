<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InformeDocenteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                         => $this->id,
            'docente_nombre'             => $this->usuario->nombre ?? 'Docente no asignado',
            'docente_correo'             => $this->usuario->correo ?? '',
            'asignatura'                 => $this->curso->nombre_curso ?? 'Curso',
            'carrera'                    => $this->curso->carrera ?? '',
            'area'                       => $this->curso->area ?? '',
            'codigo'                     => $this->curso->codigo_curso ?? '',
            'seccion'                    => $this->curso->seccion ?? '',
            'anio'                       => $this->curso->anio ?? ($this->created_at ? $this->created_at->format('Y') : date('Y')),
            'periodo'                    => $this->periodo,
            'mes'                        => $this->mes,
            'estudiantes_asignados'      => $this->estudiantes_asignados,
            'listado_asistencia_url'     => $this->listado_asistencia_url,
            'enlace_evidencia_url'       => $this->enlace_evidencia_url,
            'enlace_meet_zoom_url'       => $this->enlace_meet_zoom_url,
            'enlace_classroom_drive_url' => $this->enlace_classroom_drive_url,
            'estrategias_evaluacion'     => $this->estrategias_evaluacion,
            'estado'                     => $this->estado,
            'created_at'                 => $this->created_at ? $this->created_at->format('d/m/Y H:i') : '',
            'semanas'                    => $this->semanas ? $this->semanas->map(function ($s) {
                return [
                    'numero_semana'            => $s->numero_semana,
                    'actividad_realizada'      => $s->actividad_realizada,
                    'estudiantes_participaron' => $s->estudiantes_participaron,
                    'metodologias'             => $s->metodologias,
                    'medios_comunicacion'      => $s->medios_comunicacion,
                ];
            }) : [],
        ];
    }
}

