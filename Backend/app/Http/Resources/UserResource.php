<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'     => $this->id,
            'nombre' => $this->nombre,
            'correo' => $this->correo,
            'numero' => $this->numero,
            'rol'    => $this->rol instanceof \App\Enums\Rol ? $this->rol->value : (string) $this->rol,
            'plaza'  => $this->plaza,
            'area'   => $this->area,
            'estado' => $this->estado,
        ];
    }
}

