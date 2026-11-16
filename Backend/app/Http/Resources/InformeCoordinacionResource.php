<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InformeCoordinacionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                              => $this->id,
            'coordinador_nombre'              => $this->usuario->nombre ?? 'Coordinador no asignado',
            'coordinador_correo'              => $this->usuario->correo ?? '',
            'area'                            => $this->area,
            'periodo'                         => $this->periodo,
            'mes'                             => $this->mes,
            'anio'                            => $this->anio,
            'herramientas_virtuales'          => $this->herramientas_virtuales,
            'enlace_actividades_coordinacion' => $this->enlace_actividades_coordinacion,
            'enlace_informe_auxiliares'       => $this->enlace_informe_auxiliares,
            'enlace_docentes_permisos'        => $this->enlace_docentes_permisos,
            'estado'                          => $this->estado,
            'created_at'                      => $this->created_at ? $this->created_at->format('d/m/Y H:i') : '',
            'programas'                       => $this->programas,
            'asignaturas'                     => $this->asignaturas,
            'avances'                         => $this->avances,
            'estudiantes'                     => $this->estudiantes,
        ];
    }
}

