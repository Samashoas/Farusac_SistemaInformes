<?php

namespace App\Services;

use App\Models\Curso;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Cache;
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

        $curso = Curso::create([
            'carrera'      => $data['carrera'],
            'area'         => $data['area'],
            'nombre_curso' => $data['nombre_curso'],
            'codigo_curso' => $data['codigo_curso'],
            'seccion'      => $data['seccion'],
            'anio'         => $data['anio'],
            'semestre'     => $data['semestre'],
        ]);

        Cache::forget('catalogo_cursos_completo');

        return $curso;
    }

    /**
     * Elimina un curso del sistema por su identificador o instancia del modelo.
     */
    public function eliminarCurso(Curso|int|string $curso): bool
    {
        $target = $curso instanceof Curso ? $curso : Curso::findOrFail($curso);
        $deleted = (bool) $target->delete();
        if ($deleted) {
            Cache::forget('catalogo_cursos_completo');
        }
        return $deleted;
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
    public function desasignarDocente(User $user, Curso|int|string $curso): void
    {
        $cursoId = $curso instanceof Curso ? $curso->id : (int) $curso;
        $user->cursos()->detach($cursoId);
    }
}

