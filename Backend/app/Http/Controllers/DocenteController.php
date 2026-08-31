<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Curso;
use App\Models\User;

class DocenteController extends Controller
{
    /**
     * Muestra el Dashboard principal del docente con sus cursos asignados
     * y el catálogo de cursos para la ventana modal.
     */
    public function dashboard()
    {
        /** @var User $user */
        $user = Auth::user();

        // Obtener los cursos asignados al docente
        try {
            $cursosAsignados = $user->cursos()
                ->orderBy('nombre_curso', 'asc')
                ->orderBy('seccion', 'asc')
                ->get();
        } catch (\Exception $e) {
            $cursosAsignados = collect();
        }

        // Obtener el catálogo completo de cursos para alimentar los selectores en cascada
        try {
            $catalogoCursos = Curso::orderBy('carrera', 'asc')
                ->orderBy('nombre_curso', 'asc')
                ->orderBy('seccion', 'asc')
                ->get();

            $carreras = Curso::select('carrera')
                ->distinct()
                ->pluck('carrera');
        } catch (\Exception $e) {
            $catalogoCursos = collect();
            $carreras = collect();
        }

        return view('docente.dashboard', compact('user', 'cursosAsignados', 'catalogoCursos', 'carreras'));
    }

    /**
     * Asigna un curso seleccionado al docente autenticado.
     */
    public function asignarCurso(Request $request)
    {
        $request->validate([
            'curso_id' => 'required|exists:cursos,id'
        ], [
            'curso_id.required' => 'Debes seleccionar una sección válida.',
            'curso_id.exists' => 'El curso seleccionado no existe en el sistema.'
        ]);

        /** @var User $user */
        $user = Auth::user();
        $cursoId = $request->input('curso_id');

        // Verificar si ya está asignado
        if ($user->cursos()->where('cursos.id', $cursoId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Este curso y sección ya se encuentran asignados a tu cuenta.'
            ], 422);
        }

        try {
            $user->cursos()->attach($cursoId);
            $curso = Curso::find($cursoId);

            return response()->json([
                'success' => true,
                'message' => 'Curso asignado exitosamente.',
                'curso' => [
                    'id' => $curso->id,
                    'nombre_curso' => $curso->nombre_curso,
                    'seccion' => $curso->seccion,
                    'carrera' => $curso->carrera,
                    'area' => $curso->area,
                    'codigo_curso' => $curso->codigo_curso,
                    'anio' => $curso->anio,
                    'semestre' => $curso->semestre
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al asignar el curso: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Elimina la asignación de un curso del docente.
     */
    public function desasignarCurso($id)
    {
        /** @var User $user */
        $user = Auth::user();

        try {
            $user->cursos()->detach($id);

            return response()->json([
                'success' => true,
                'message' => 'El curso ha sido removido de tu panel exitosamente.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al remover la asignación: ' . $e->getMessage()
            ], 500);
        }
    }
}
