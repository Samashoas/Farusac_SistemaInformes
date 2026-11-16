<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CursoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'nombre_curso' => $this->nombre_curso,
            'seccion'      => $this->seccion,
            'carrera'      => $this->carrera,
            'area'         => $this->area,
            'codigo_curso' => $this->codigo_curso,
            'anio'         => $this->anio,
            'semestre'     => $this->semestre,
        ];
    }
}

