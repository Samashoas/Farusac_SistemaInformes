<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Curso;
use App\Models\User;
use App\Models\Informe;
use App\Models\InformeSemana;
use Carbon\Carbon;

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

    /**
     * Muestra la vista de creación de informe (Wizard de 2 fases).
     */
    public function crearInformeView(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        try {
            $cursosAsignados = $user->cursos()
                ->orderBy('nombre_curso', 'asc')
                ->orderBy('seccion', 'asc')
                ->get();
        } catch (\Exception $e) {
            $cursosAsignados = collect();
        }

        $selectedCursoId = $request->query('curso_id');
        $selectedCurso = null;
        if ($selectedCursoId) {
            $selectedCurso = $cursosAsignados->firstWhere('id', $selectedCursoId) ?? Curso::find($selectedCursoId);
        }

        return view('docente.crear_informe', compact('user', 'cursosAsignados', 'selectedCurso'));
    }

    /**
     * Guarda el nuevo informe con sus semanas registradas.
     */
    public function guardarInforme(Request $request)
    {
        $request->validate([
            'curso_id' => 'required|exists:cursos,id',
            'periodo' => 'required|string',
            'mes' => 'required|string',
            'estudiantes_asignados' => 'required|integer|min:0',
            'listado_asistencia_url' => 'nullable|string|max:255',
            'enlace_evidencia_url' => 'nullable|string|max:255',
            'enlace_meet_zoom_url' => 'nullable|string|max:255',
            'enlace_classroom_drive_url' => 'nullable|string|max:255',
            'estrategias_evaluacion' => 'nullable|string',
            'semanas' => 'required|array|min:1',
            'semanas.*.numero_semana' => 'required|integer',
            'semanas.*.actividad_realizada' => 'required|string',
            'semanas.*.estudiantes_participaron' => 'nullable|integer|min:0',
            'semanas.*.metodologias' => 'nullable|string',
            'semanas.*.medios_comunicacion' => 'nullable|string',
        ], [
            'curso_id.required' => 'Debe seleccionar un curso válido.',
            'periodo.required' => 'El periodo es obligatorio.',
            'mes.required' => 'El mes es obligatorio.',
            'semanas.required' => 'Debe registrar al menos una semana de actividades.',
            'semanas.*.actividad_realizada.required' => 'El contenido o actividad realizada es obligatorio en cada semana.',
        ]);

        /** @var User $user */
        $user = Auth::user();

        DB::beginTransaction();
        try {
            $informe = Informe::create([
                'usuario_id' => $user->id,
                'curso_id' => $request->input('curso_id'),
                'periodo' => $request->input('periodo'),
                'mes' => $request->input('mes'),
                'estudiantes_asignados' => $request->input('estudiantes_asignados', 0),
                'listado_asistencia_url' => $request->input('listado_asistencia_url'),
                'enlace_evidencia_url' => $request->input('enlace_evidencia_url'),
                'enlace_meet_zoom_url' => $request->input('enlace_meet_zoom_url'),
                'enlace_classroom_drive_url' => $request->input('enlace_classroom_drive_url'),
                'estrategias_evaluacion' => $request->input('estrategias_evaluacion'),
                'estado' => 'enviado',
                'bloqueado_en' => Carbon::now()->addDays(3),
            ]);

            foreach ($request->input('semanas') as $sem) {
                InformeSemana::create([
                    'informe_id' => $informe->id,
                    'numero_semana' => $sem['numero_semana'] ?? 1,
                    'actividad_realizada' => $sem['actividad_realizada'] ?? '',
                    'estudiantes_participaron' => $sem['estudiantes_participaron'] ?? 0,
                    'metodologias' => $sem['metodologias'] ?? null,
                    'medios_comunicacion' => $sem['medios_comunicacion'] ?? null,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'El informe ha sido creado y enviado exitosamente. Recuerda que tienes un máximo de 3 días para editar o modificar tu informe antes de que se bloquee para revisión.',
                'informe_id' => $informe->id,
                'redirect_url' => route('docente.dashboard')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar el informe: ' . $e->getMessage()
            ], 500);
        }
    }
}
