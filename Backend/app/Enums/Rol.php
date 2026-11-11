<?php

namespace App\Enums;

enum Rol: string
{
    case Docente       = 'docente';
    case Coordinador   = 'coordinador';
    case Administrador = 'administrador';

    /**
     * Retorna todos los valores como array de strings.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Retorna el nombre visible del rol.
     */
    public function label(): string
    {
        return match ($this) {
            self::Docente       => 'Docente',
            self::Coordinador   => 'Coordinador',
            self::Administrador => 'Administrador',
        };
    }
}
