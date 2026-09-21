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

class CoordinadorController extends Controller
{
    /**
     * Dashboard principal del coordinador.
     */
    public function dashboard(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        // Cursos asignados al coordinador
        try {
            $cursosAsignados = $user->cursos()
                ->orderBy('nombre_curso', 'asc')
                ->orderBy('seccion', 'asc')
                ->get();
        } catch (\Exception $e) {
            $cursosAsignados = collect();
        }

        // Catálogo de cursos y áreas disponibles registradas en la BD
        try {
            $catalogoCursos = Curso::orderBy('carrera', 'asc')
                ->orderBy('nombre_curso', 'asc')
                ->orderBy('seccion', 'asc')
                ->get();

            $carreras = Curso::select('carrera')
                ->distinct()
                ->whereNotNull('carrera')
                ->where('carrera', '!=', '')
                ->pluck('carrera');

            $areasDisponibles = Curso::select('area')
                ->distinct()
                ->whereNotNull('area')
                ->where('area', '!=', '')
                ->orderBy('area', 'asc')
                ->pluck('area');
        } catch (\Exception $e) {
            $catalogoCursos = collect();
            $carreras = collect();
            $areasDisponibles = collect();
        }

        return view('coordinador.dashboard', compact('user', 'cursosAsignados', 'catalogoCursos', 'carreras', 'areasDisponibles'));
    }

    /**
     * Guarda la selección obligatoria del área a cargo del coordinador.
     */
    public function seleccionarArea(Request $request)
    {
        $request->validate([
            'area' => 'required|string|max:150',
        ], [
            'area.required' => 'Debe seleccionar un área de la lista para continuar.',
        ]);

        /** @var User $user */
        $user = Auth::user();
        $area = $request->input('area');

        try {
            // Guardar permanentemente en la base de datos
            DB::table('usuarios')->where('id', $user->id)->update(['area' => $area]);
            $user->area = $area;
            $request->session()->put('coordinador_area', $area);

            return response()->json([
                'success' => true,
                'message' => 'Área asignada y guardada permanentemente en la base de datos.',
                'area' => $area
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error al guardar área en usuarios: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al guardar en la base de datos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Muestra la vista de perfil del coordinador.
     */
    public function perfilView()
    {
        /** @var User $user */
        $user = Auth::user();

        // Obtener área desde el usuario o la sesión
        if (!$user->area && session()->has('coordinador_area')) {
            $user->area = session()->get('coordinador_area');
        }

        return view('coordinador.perfil', compact('user'));
    }

    /**
     * Actualiza los datos editables del perfil del coordinador (Teléfono).
     */
    public function actualizarPerfil(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'numero' => 'nullable|string|max:20',
        ], [
            'numero.max' => 'El número de teléfono no puede exceder los 20 caracteres.',
        ]);

        try {
            $user->numero = $request->input('numero');
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Perfil actualizado exitosamente.',
                'numero' => $user->numero
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el perfil: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Asigna un curso seleccionado al coordinador.
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
     * Elimina la asignación de un curso del coordinador.
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
     * Muestra el Historial de Informes del coordinador.
     */
    public function historialInformes(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        try {
            $informes = Informe::with(['curso', 'semanas'])
                ->where('usuario_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();

            $carreras = $informes->pluck('curso.carrera')->filter()->unique()->values();
            $cursosDocente = $informes->pluck('curso.nombre_curso')->filter()->unique()->values();
            $periodos = $informes->pluck('periodo')->filter()->map(function($p) {
                return trim(preg_replace('/\d{4}/', '', $p));
            })->filter()->unique()->values();
            $anios = $informes->pluck('curso.anio')->filter()->unique()->values();
            $meses = $informes->pluck('mes')->filter()->unique()->values();

        } catch (\Exception $e) {
            $informes = collect();
            $carreras = collect();
            $cursosDocente = collect();
            $periodos = collect();
            $anios = collect();
            $meses = collect();
        }

        return view('coordinador.historial_informes', compact('user', 'informes', 'carreras', 'cursosDocente', 'periodos', 'anios', 'meses'));
    }

    /**
     * Muestra el formulario para crear un nuevo informe mensual.
     */
    public function crearInformeView(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $cursosAsignados = $user->cursos()
            ->orderBy('nombre_curso', 'asc')
            ->orderBy('seccion', 'asc')
            ->get();

        $cursoPreseleccionadoId = $request->query('curso_id');

        return view('coordinador.crear_informe', compact('user', 'cursosAsignados', 'cursoPreseleccionadoId'));
    }

    /**
     * Guarda un nuevo informe mensual en la base de datos.
     */
    public function guardarInforme(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'curso_id' => 'required|exists:cursos,id',
            'periodo' => 'required|string',
            'mes' => 'required|string',
            'estudiantes_asignados' => 'required|integer|min:1',
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
            'estudiantes_asignados.required' => 'La cantidad de estudiantes asignados es obligatoria.',
            'estudiantes_asignados.min' => 'La cantidad de estudiantes asignados debe ser mayor a 0.',
            'semanas.required' => 'Debe registrar al menos una semana de actividades.',
            'semanas.*.actividad_realizada.required' => 'El contenido o actividad realizada es obligatorio en cada semana.',
        ]);

        DB::beginTransaction();
        try {
            $bloqueadoEn = Carbon::now()->addDays(3);

            $informe = Informe::create([
                'usuario_id' => $user->id,
                'curso_id' => $request->input('curso_id'),
                'periodo' => $request->input('periodo'),
                'mes' => $request->input('mes'),
                'estudiantes_asignados' => $request->input('estudiantes_asignados'),
                'listado_asistencia_url' => $request->input('listado_asistencia_url'),
                'enlace_evidencia_url' => $request->input('enlace_evidencia_url'),
                'enlace_meet_zoom_url' => $request->input('enlace_meet_zoom_url'),
                'enlace_classroom_drive_url' => $request->input('enlace_classroom_drive_url'),
                'estrategias_evaluacion' => $request->input('estrategias_evaluacion'),
                'estado' => 'entregado',
                'bloqueado_en' => $bloqueadoEn,
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
                'message' => 'Informe guardado exitosamente.',
                'redirect_url' => route('coordinador.informes')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar el informe: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Muestra la vista de edición de un informe existente.
     */
    public function editarInformeView($id)
    {
        /** @var User $user */
        $user = Auth::user();

        $informe = Informe::with(['curso', 'semanas'])
            ->where('usuario_id', $user->id)
            ->findOrFail($id);

        if ($informe->bloqueado_en && Carbon::now()->greaterThan($informe->bloqueado_en)) {
            return redirect()->route('coordinador.informes')->with('error', 'El plazo de edición de 3 días para este informe ha finalizado.');
        }

        $cursosAsignados = $user->cursos()
            ->orderBy('nombre_curso', 'asc')
            ->orderBy('seccion', 'asc')
            ->get();

        return view('coordinador.crear_informe', compact('user', 'informe', 'cursosAsignados'));
    }

    /**
     * Actualiza un informe mensual.
     */
    public function actualizarInforme(Request $request, $id)
    {
        /** @var User $user */
        $user = Auth::user();

        $informe = Informe::where('usuario_id', $user->id)->findOrFail($id);

        if ($informe->bloqueado_en && Carbon::now()->greaterThan($informe->bloqueado_en)) {
            return response()->json([
                'success' => false,
                'message' => 'Este informe ya no puede ser editado porque ha vencido el plazo límite de 3 días.'
            ], 422);
        }

        $request->validate([
            'curso_id' => 'required|exists:cursos,id',
            'periodo' => 'required|string',
            'mes' => 'required|string',
            'estudiantes_asignados' => 'required|integer|min:1',
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
            'estudiantes_asignados.required' => 'La cantidad de estudiantes asignados es obligatoria.',
            'estudiantes_asignados.min' => 'La cantidad de estudiantes asignados debe ser mayor a 0.',
            'semanas.required' => 'Debe registrar al menos una semana de actividades.',
            'semanas.*.actividad_realizada.required' => 'El contenido o actividad realizada es obligatorio en cada semana.',
        ]);

        DB::beginTransaction();
        try {
            $informe->update([
                'curso_id' => $request->input('curso_id'),
                'periodo' => $request->input('periodo'),
                'mes' => $request->input('mes'),
                'estudiantes_asignados' => $request->input('estudiantes_asignados'),
                'listado_asistencia_url' => $request->input('listado_asistencia_url'),
                'enlace_evidencia_url' => $request->input('enlace_evidencia_url'),
                'enlace_meet_zoom_url' => $request->input('enlace_meet_zoom_url'),
                'enlace_classroom_drive_url' => $request->input('enlace_classroom_drive_url'),
                'estrategias_evaluacion' => $request->input('estrategias_evaluacion'),
            ]);

            $informe->semanas()->delete();
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
                'message' => 'Informe actualizado exitosamente.',
                'redirect_url' => route('coordinador.informes')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el informe: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Elimina permanentemente un informe del coordinador.
     */
    public function eliminarInforme($id)
    {
        /** @var User $user */
        $user = Auth::user();

        DB::beginTransaction();
        try {
            $informe = Informe::where('usuario_id', $user->id)->findOrFail($id);
            $informe->semanas()->delete();
            $informe->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'El informe ha sido eliminado permanentemente.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el informe: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Genera la vista institucional del informe para imprimir / PDF.
     */
    public function verInforme($id)
    {
        /** @var User $user */
        $user = Auth::user();

        $informe = Informe::with(['curso', 'semanas'])
            ->where('usuario_id', $user->id)
            ->findOrFail($id);

        return view('docente.ver_informe_pdf', compact('user', 'informe'));
    }
}
