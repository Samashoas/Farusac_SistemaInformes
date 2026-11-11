<?php

namespace App\Http\Controllers;

use App\Enums\Rol;
use App\Models\Curso;
use App\Models\Informe;
use App\Models\User;
use App\Services\InformeDocenteService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DocenteController extends Controller
{
    public function __construct(
        protected ?InformeDocenteService $docenteService = null
    ) {
        $this->docenteService = $docenteService ?? app(InformeDocenteService::class);
    }

    /**
     * Muestra el Dashboard principal del docente con sus cursos asignados
     * y el catálogo de cursos para la ventana modal.
     */
    public function dashboard(?Request $request = null)
    {
        /** @var User $user */
        $user = Auth::user();

        // Obtener los cursos asignados al docente
        try {
            $cursosAsignados = $user->cursos()
                ->orderBy('nombre_curso', 'asc')
                ->orderBy('seccion', 'asc')
                ->get();
        } catch (Exception $e) {
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
                ->whereNotNull('carrera')
                ->where('carrera', '!=', '')
                ->pluck('carrera');

            if ($carreras->isEmpty()) {
                $carreras = collect(['Arquitectura', 'Diseño Gráfico']);
            }
        } catch (Exception $e) {
            $catalogoCursos = collect();
            $carreras = collect(['Arquitectura', 'Diseño Gráfico']);
        }

        return view('docente.dashboard', compact('user', 'cursosAsignados', 'catalogoCursos', 'carreras'));
    }

    /**
     * Asigna un curso seleccionado al docente autenticado.
     */
    public function asignarCurso(Request $request)
    {
        $request->validate([
            'curso_id' => 'required|exists:cursos,id',
        ], [
            'curso_id.required' => 'Debes seleccionar una sección válida.',
            'curso_id.exists'   => 'El curso seleccionado no existe en el sistema.',
        ]);

        /** @var User $user */
        $user    = Auth::user();
        $cursoId = $request->input('curso_id');

        // Verificar si ya está asignado
        if ($user->cursos()->where('cursos.id', $cursoId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Este curso y sección ya se encuentran asignados a tu cuenta.',
            ], 422);
        }

        try {
            $user->cursos()->attach($cursoId);
            $curso = Curso::find($cursoId);

            return response()->json([
                'success' => true,
                'message' => 'Curso asignado exitosamente.',
                'curso'   => [
                    'id'           => $curso->id,
                    'nombre_curso' => $curso->nombre_curso,
                    'seccion'      => $curso->seccion,
                    'carrera'      => $curso->carrera,
                    'area'         => $curso->area,
                    'codigo_curso' => $curso->codigo_curso,
                    'anio'         => $curso->anio,
                    'semestre'     => $curso->semestre,
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al asignar el curso: ' . $e->getMessage(),
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
                'message' => 'El curso ha sido removido de tu panel exitosamente.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al remover la asignación: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Muestra el historial completo de informes entregados por el docente.
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

            // Extraer opciones únicas para filtros
            $carreras = $informes->pluck('curso.carrera')->filter()->unique()->values();
            $cursosDocente = $informes->pluck('curso.nombre_curso')->filter()->unique()->values();
            $periodos = $informes->pluck('periodo')->filter()->map(function ($p) {
                return trim(preg_replace('/\d{4}/', '', $p));
            })->filter()->unique()->values();
            $anios = $informes->pluck('curso.anio')->filter()->unique()->values();
            $meses = $informes->pluck('mes')->filter()->unique()->values();

            // Fallback para selectores si el docente aún no tiene informes registrados
            if ($carreras->isEmpty()) {
                $carreras = Curso::select('carrera')->distinct()->whereNotNull('carrera')->where('carrera', '!=', '')->pluck('carrera');
                if ($carreras->isEmpty()) {
                    $carreras = collect(['Arquitectura', 'Diseño Gráfico']);
                }
            }
            if ($cursosDocente->isEmpty()) {
                $cursosDocente = $user->cursos()->pluck('nombre_curso')->unique()->values();
            }
            if ($anios->isEmpty()) {
                $anios = collect([date('Y')]);
            }
        } catch (Exception $e) {
            $informes      = collect();
            $carreras      = collect(['Arquitectura', 'Diseño Gráfico']);
            $cursosDocente = collect();
            $periodos      = collect();
            $anios         = collect([date('Y')]);
            $meses         = collect();
        }

        $vista = $user->isCoordinador() ? 'coordinador.historial_informes' : 'docente.historial_informes';

        return view($vista, compact('user', 'informes', 'carreras', 'cursosDocente', 'periodos', 'anios', 'meses'));
    }

    /**
     * Muestra el formulario para crear un nuevo informe mensual.
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
        } catch (Exception $e) {
            $cursosAsignados = collect();
        }

        $selectedCursoId = $request->query('curso_id');
        $selectedCurso   = null;
        if ($selectedCursoId) {
            $selectedCurso = $cursosAsignados->firstWhere('id', $selectedCursoId) ?? Curso::find($selectedCursoId);
        }

        $vista                  = $user->isCoordinador() ? 'coordinador.crear_informe' : 'docente.crear_informe';
        $cursoPreseleccionadoId = $selectedCursoId;
        return view($vista, compact('user', 'cursosAsignados', 'selectedCurso', 'cursoPreseleccionadoId'));
    }

    /**
     * Guarda el nuevo informe con sus semanas registradas delegando al servicio.
     */
    public function guardarInforme(Request $request)
    {
        $request->validate([
            'curso_id'                           => 'required|exists:cursos,id',
            'periodo'                            => 'required|string',
            'mes'                                => 'required|string',
            'estudiantes_asignados'              => 'required|integer|min:1',
            'listado_asistencia_url'             => 'nullable|string|max:255',
            'enlace_evidencia_url'               => 'nullable|string|max:255',
            'enlace_meet_zoom_url'               => 'nullable|string|max:255',
            'enlace_classroom_drive_url'         => 'nullable|string|max:255',
            'estrategias_evaluacion'             => 'nullable|string',
            'semanas'                            => 'required|array|min:1',
            'semanas.*.numero_semana'            => 'required|integer',
            'semanas.*.actividad_realizada'      => 'required|string',
            'semanas.*.estudiantes_participaron' => 'nullable|integer|min:0',
            'semanas.*.metodologias'             => 'nullable|string',
            'semanas.*.medios_comunicacion'      => 'nullable|string',
        ], [
            'curso_id.required'                     => 'Debe seleccionar un curso válido.',
            'periodo.required'                      => 'El periodo es obligatorio.',
            'mes.required'                          => 'El mes es obligatorio.',
            'estudiantes_asignados.required'        => 'La cantidad de estudiantes asignados es obligatoria.',
            'estudiantes_asignados.min'             => 'La cantidad de estudiantes asignados debe ser mayor a 0.',
            'semanas.required'                      => 'Debe registrar al menos una semana de actividades.',
            'semanas.*.actividad_realizada.required' => 'El contenido o actividad realizada es obligatorio en cada semana.',
        ]);

        /** @var User $user */
        $user = Auth::user();

        try {
            $informe = $this->docenteService->guardarInforme($user, $request->all());

            $redirectUrl = $user->isCoordinador() ? route('coordinador.dashboard') : route('docente.dashboard');

            return response()->json([
                'success'      => true,
                'message'      => 'El informe ha sido creado y enviado exitosamente. Recuerda que tienes un máximo de 3 días para editar o modificar tu informe antes de que se bloquee para revisión.',
                'informe_id'   => $informe->id,
                'redirect_url' => $redirectUrl,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar el informe: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Muestra la vista de edición de informe cargando los datos previos.
     */
    public function editarInformeView($id)
    {
        /** @var User $user */
        $user = Auth::user();

        $informe = Informe::with(['curso', 'semanas'])
            ->where('usuario_id', $user->id)
            ->findOrFail($id);

        $estaBloqueado = false;
        if ($informe->bloqueado_en && Carbon::now()->greaterThan($informe->bloqueado_en)) {
            $estaBloqueado = true;
        }

        try {
            $cursosAsignados = $user->cursos()
                ->orderBy('nombre_curso', 'asc')
                ->orderBy('seccion', 'asc')
                ->get();
        } catch (Exception $e) {
            $cursosAsignados = collect();
        }

        $selectedCurso = $informe->curso;
        $modoEdicion   = true;

        $vista = $user->isCoordinador() ? 'coordinador.crear_informe' : 'docente.crear_informe';

        return view($vista, compact('user', 'cursosAsignados', 'selectedCurso', 'informe', 'modoEdicion', 'estaBloqueado'));
    }

    /**
     * Actualiza un informe existente y sus semanas delegando al servicio.
     */
    public function actualizarInforme(Request $request, $id)
    {
        /** @var User $user */
        $user = Auth::user();

        $informe = Informe::where('usuario_id', $user->id)->findOrFail($id);

        if ($informe->bloqueado_en && Carbon::now()->greaterThan($informe->bloqueado_en)) {
            return response()->json([
                'success' => false,
                'message' => 'Este informe ya no puede ser editado porque ha vencido el plazo límite de 3 días.',
            ], 422);
        }

        $request->validate([
            'curso_id'                           => 'required|exists:cursos,id',
            'periodo'                            => 'required|string',
            'mes'                                => 'required|string',
            'estudiantes_asignados'              => 'required|integer|min:1',
            'listado_asistencia_url'             => 'nullable|string|max:255',
            'enlace_evidencia_url'               => 'nullable|string|max:255',
            'enlace_meet_zoom_url'               => 'nullable|string|max:255',
            'enlace_classroom_drive_url'         => 'nullable|string|max:255',
            'estrategias_evaluacion'             => 'nullable|string',
            'semanas'                            => 'required|array|min:1',
            'semanas.*.numero_semana'            => 'required|integer',
            'semanas.*.actividad_realizada'      => 'required|string',
            'semanas.*.estudiantes_participaron' => 'nullable|integer|min:0',
            'semanas.*.metodologias'             => 'nullable|string',
            'semanas.*.medios_comunicacion'      => 'nullable|string',
        ], [
            'curso_id.required'                     => 'Debe seleccionar un curso válido.',
            'periodo.required'                      => 'El periodo es obligatorio.',
            'mes.required'                          => 'El mes es obligatorio.',
            'estudiantes_asignados.required'        => 'La cantidad de estudiantes asignados es obligatoria.',
            'estudiantes_asignados.min'             => 'La cantidad de estudiantes asignados debe ser mayor a 0.',
            'semanas.required'                      => 'Debe registrar al menos una semana de actividades.',
            'semanas.*.actividad_realizada.required' => 'El contenido o actividad realizada es obligatorio en cada semana.',
        ]);

        try {
            $this->docenteService->actualizarInforme($informe, $request->all());

            $redirectUrl = $user->isCoordinador() ? route('coordinador.informes') : route('docente.informes');

            return response()->json([
                'success'      => true,
                'message'      => 'Informe actualizado exitosamente.',
                'redirect_url' => $redirectUrl,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el informe: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Elimina permanentemente un informe del docente.
     */
    public function eliminarInforme($id)
    {
        /** @var User $user */
        $user = Auth::user();

        try {
            $informe = Informe::where('usuario_id', $user->id)->findOrFail($id);
            $this->eliminarInformeDocenteModel($informe);

            return response()->json([
                'success' => true,
                'message' => 'El informe ha sido eliminado permanentemente.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el informe: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Genera la vista institucional del informe en formato lista para imprimir/PDF.
     */
    public function verInforme($id)
    {
        /** @var User $user */
        $user = Auth::user();

        $query = Informe::with(['curso', 'semanas', 'usuario']);
        if (!$user->isCoordinador() && !$user->isAdministrador()) {
            $query->where('usuario_id', $user->id);
        }
        $informe = $query->findOrFail($id);

        return view('docente.ver_informe_pdf', compact('user', 'informe'));
    }

    /**
     * Muestra la vista de perfil del docente.
     */
    public function perfilView()
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->isCoordinador()) {
            if (!$user->area && session()->has('coordinador_area')) {
                $user->area = session()->get('coordinador_area');
            }
            return view('coordinador.perfil', compact('user'));
        }

        return view('docente.perfil', compact('user'));
    }

    /**
     * Actualiza la información editable del perfil del docente (Registro Personal).
     */
    public function actualizarPerfil(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'numero' => 'nullable|string|max:20',
        ], [
            'numero.max' => 'El registro personal no puede exceder los 20 caracteres.',
        ]);

        try {
            $user->numero = $request->input('numero');
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Perfil actualizado exitosamente.',
                'numero'  => $user->numero,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el perfil: ' . $e->getMessage(),
            ], 500);
        }
    }
}
