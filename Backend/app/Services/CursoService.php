<?php

namespace App\Services;

use App\Models\Curso;
use App\Models\User;
use Exception;
use Illuminate\Validation\ValidationException;

class CursoService
{
    /**
     * Crea un nuevo curso verificando que no exista duplicado por carrera, código, sección, año y semestre.
     *
     * @throws ValidationException
     */
    public function crearCurso(array $data): Curso
    {
        $existe = Curso::where('carrera', $data['carrera'])
            ->where('codigo_curso', $data['codigo_curso'])
            ->where('seccion', $data['seccion'])
            ->where('anio', $data['anio'])
            ->where('semestre', $data['semestre'])
            ->exists();

        if ($existe) {
            throw ValidationException::withMessages([
                'duplicado' => 'Ya existe un registro con la misma Carrera, Código, Sección, Año y Semestre.',
            ]);
        }

        return Curso::create([
            'carrera'      => $data['carrera'],
            'area'         => $data['area'],
            'nombre_curso' => $data['nombre_curso'],
            'codigo_curso' => $data['codigo_curso'],
            'seccion'      => $data['seccion'],
            'anio'         => $data['anio'],
            'semestre'     => $data['semestre'],
        ]);
    }

    /**
     * Elimina un curso del sistema por su identificador.
     */
    public function eliminarCurso(int|string $id): bool
    {
        $curso = Curso::findOrFail($id);
        return (bool) $curso->delete();
    }

    /**
     * Asigna un curso a un usuario docente.
     *
     * @throws ValidationException
     */
    public function asignarDocente(User $user, int $cursoId): Curso
    {
        if ($user->cursos()->where('cursos.id', $cursoId)->exists()) {
            throw ValidationException::withMessages([
                'curso_id' => 'Este curso y sección ya se encuentran asignados a tu cuenta.',
            ]);
        }

        $user->cursos()->attach($cursoId);
        return Curso::findOrFail($cursoId);
    }

    /**
     * Remueve la asignación de un curso del usuario docente.
     */
    public function desasignarDocente(User $user, int $cursoId): void
    {
        $user->cursos()->detach($cursoId);
    }
}

