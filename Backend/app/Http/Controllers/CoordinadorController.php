<?php

namespace App\Http\Controllers;

use App\Http\Requests\SeleccionarAreaRequest;
use App\Models\Curso;
use App\Models\Informe;
use App\Models\InformeCoordinacion;
use App\Models\User;
use App\Services\InformeCoordinacionService;
use App\Services\InformeDocenteService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class CoordinadorController extends DocenteController
{
    public function __construct(
        protected InformeCoordinacionService $coordinacionService,
        ?InformeDocenteService $docenteService = null
    ) {
        parent::__construct($docenteService);
    }

    /**
     * Dashboard principal del coordinador.
     */
    public function dashboard(?Request $request = null)
    {
        /** @var User $user */
        $user = Auth::user();

        // Cursos asignados al coordinador (como docente)
        try {
            $cursosAsignados = $user->cursos()
                ->orderBy('nombre_curso', 'asc')
                ->orderBy('seccion', 'asc')
                ->get();
        } catch (Exception $e) {
            $cursosAsignados = collect();
        }

        // Catálogo de cursos y áreas disponibles registradas en la BD
        try {
            $catalogoCursos = Curso::orderBy('carrera', 'asc')
                ->orderBy('nombre_curso', 'asc')
                ->orderBy('seccion', 'asc')
                ->get();

            $carrerasDb = Curso::select('carrera')
                ->distinct()
                ->whereNotNull('carrera')
                ->where('carrera', '!=', '')
                ->pluck('carrera');

            // Asegurar disponibilidad de las carreras institucionales
            $carreras = $carrerasDb->isNotEmpty()
                ? $carrerasDb
                : collect(['Arquitectura', 'Diseño Gráfico']);

            // Mapeo dinámico de áreas por carrera
            $areasPorCarrera = [];
            $cursosCarreraArea = Curso::select('carrera', 'area')
                ->distinct()
                ->whereNotNull('carrera')
                ->where('carrera', '!=', '')
                ->whereNotNull('area')
                ->where('area', '!=', '')
                ->orderBy('area', 'asc')
                ->get();

            foreach ($cursosCarreraArea as $item) {
                $areasPorCarrera[$item->carrera][] = $item->area;
            }

            // Normalizar arrays únicos de áreas
            foreach ($areasPorCarrera as $car => $ars) {
                $areasPorCarrera[$car] = array_values(array_unique($ars));
            }

            // Áreas institucionales por defecto si la base de datos aún no tiene cursos cargados
            if (empty($areasPorCarrera['Arquitectura'])) {
                $areasPorCarrera['Arquitectura'] = [
                    'Diseño Arquitectónico',
                    'Medios de Expresión',
                    'Historia y Teoría',
                    'Sistemas Estructurales',
                    'Ambiente y Urbanismo',
                    'Digital',
                    'Sistemas Constructivos',
                    'Investigación y Graduación',
                ];
            }
            if (empty($areasPorCarrera['Diseño Gráfico'])) {
                $areasPorCarrera['Diseño Gráfico'] = [
                    'Área de Diseño',
                    'Área de Comunicación',
                    'Área de Tecnología y Expresión',
                    'Área de Teoría e Historia',
                    'Área de Gestión y Práctica Profesional',
                    'Área de Graduación',
                ];
            }

            $areasDisponibles = Curso::select('area')
                ->distinct()
                ->whereNotNull('area')
                ->where('area', '!=', '')
                ->orderBy('area', 'asc')
                ->pluck('area');

            if ($areasDisponibles->isEmpty()) {
                $allFallback = array_merge($areasPorCarrera['Arquitectura'], $areasPorCarrera['Diseño Gráfico']);
                $areasDisponibles = collect(array_values(array_unique($allFallback)));
            }
        } catch (Exception $e) {
            $catalogoCursos = collect();
            $carreras = collect(['Arquitectura', 'Diseño Gráfico']);
            $areasPorCarrera = [
                'Arquitectura' => [
                    'Diseño Arquitectónico',
                    'Medios de Expresión',
                    'Historia y Teoría',
                    'Sistemas Estructurales',
                    'Ambiente y Urbanismo',
                    'Digital',
                    'Sistemas Constructivos',
                    'Investigación y Graduación',
                ],
                'Diseño Gráfico' => [
                    'Área de Diseño',
                    'Área de Comunicación',
                    'Área de Tecnología y Expresión',
                    'Área de Teoría e Historia',
                    'Área de Gestión y Práctica Profesional',
                    'Área de Graduación',
                ],
            ];
            $allFallback = array_merge($areasPorCarrera['Arquitectura'], $areasPorCarrera['Diseño Gráfico']);
            $areasDisponibles = collect(array_values(array_unique($allFallback)));
        }

        // Informes mensuales de coordinación generados por este usuario
        try {
            $informesCoordinacion = InformeCoordinacion::where('usuario_id', $user->id)
                ->orderBy('anio', 'desc')
                ->orderBy('id', 'desc')
                ->get();
        } catch (Exception $e) {
            $informesCoordinacion = collect();
        }

        return view('coordinador.dashboard', compact('user', 'cursosAsignados', 'catalogoCursos', 'carreras', 'areasDisponibles', 'areasPorCarrera', 'informesCoordinacion'));
    }

    /**
     * Guarda la selección obligatoria del área a cargo del coordinador.
     * La autorización y validación se delegan a SeleccionarAreaRequest.
     */
    public function seleccionarArea(SeleccionarAreaRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validated();
        $area      = $validated['area'];
        $carrera   = $validated['carrera'] ?? null;

        try {
            $updateData = ['area' => $area];
            if (Schema::hasColumn('usuarios', 'carrera') && !empty($carrera)) {
                $updateData['carrera'] = $carrera;
            }

            DB::table('usuarios')->where('id', $user->id)->update($updateData);
            $user->area = $area;
            $request->session()->put('coordinador_area', $area);
            if (!empty($carrera)) {
                $request->session()->put('coordinador_carrera', $carrera);
            }

            return response()->json([
                'success' => true,
                'message' => 'Área y carrera asignadas y guardadas exitosamente.',
                'area'    => $area,
                'carrera' => $carrera,
            ]);
        } catch (Exception $e) {
            Log::error('Error al guardar área en usuarios: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al guardar en la base de datos: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Retorna los datos completos de un informe de docente en formato JSON para el modal interactivo.
     */
    public function obtenerDetalleInformeDocenteJson($id)
    {
        $informe = Informe::with(['curso', 'semanas', 'usuario'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'informe' => $this->buildInformeDocenteData($informe),
        ]);
    }

    /**
     * Obtiene todos los cursos asociados a un área de coordinación delegando al servicio.
     */
    private function getCursosPorArea($area, $carrera = null)
    {
        return $this->coordinacionService->getCursosPorArea(
            $area,
            $carrera ?: session('coordinador_carrera'),
            Auth::user()
        );
    }

    /**
     * Retorna los datos dinámicos consolidados de docentes y programas para el área en formato JSON.
     */
    public function obtenerDatosDocentesArea(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $area = $user->area ?? session('coordinador_area');

        if (!$area) {
            return response()->json(['success' => false, 'message' => 'Sin área asignada'], 400);
        }

        $res = $this->coordinacionService->obtenerDatosDocentesArea(
            $area,
            session('coordinador_carrera'),
            $request->query('periodo'),
            $request->query('mes'),
            $user
        );

        return response()->json([
            'success'   => true,
            'datos'     => $res['datos'],
            'programas' => $res['programas'],
        ]);
    }

    // =========================================================================
    // MÓDULO DE INFORMES MENSUALES DE COORDINACIÓN (OFICIAL FARUSAC)
    // =========================================================================

    /**
     * Muestra el formulario para crear un Informe Mensual de Coordinación.
     */
    public function crearInformeCoordinacionView(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $area = $user->area ?? session('coordinador_area');

        if (!$area) {
            return redirect()->route('coordinador.dashboard')
                ->with('error', 'Debe seleccionar un área de coordinación primero.');
        }

        $cursosArea        = $this->getCursosPorArea($area);
        $asignaturasUnicas = $cursosArea->pluck('nombre_curso')->unique()->values();

        $mesDefecto = $request->query('mes') ?? ucfirst(Carbon::now()->locale('es')->translatedFormat('F'));
        $currentMonthNum = intval(date('n'));
        $periodoSugerido = ($currentMonthNum >= 7 && $currentMonthNum <= 11)
            ? ('Segundo Semestre ' . date('Y'))
            : (($currentMonthNum == 6) ? ('Vacaciones Junio ' . date('Y')) : (($currentMonthNum == 12) ? ('Vacaciones Diciembre ' . date('Y')) : ('Primer Semestre ' . date('Y'))));
        $periodoDefecto = $request->query('periodo', $periodoSugerido);

        $res = $this->coordinacionService->obtenerDatosDocentesArea(
            $area,
            session('coordinador_carrera'),
            $periodoDefecto,
            $mesDefecto,
            $user
        );

        $filasAsignaturas   = $res['datos'];
        $programasIniciales = $res['programas'];

        $docenteInformesQuery = Informe::with(['usuario', 'semanas'])
            ->whereIn('curso_id', $cursosArea->pluck('id')->toArray())
            ->whereIn('estado', ['enviado', 'aprobado', 'bloqueado']);

        if (!empty($mesDefecto)) {
            $docenteInformesQuery->whereRaw('LOWER(TRIM(mes)) = ?', [mb_strtolower(trim($mesDefecto))]);
        }

        $docenteInformes = $docenteInformesQuery->get()->keyBy('curso_id');

        return view('coordinador.crear_informe_coordinacion', compact(
            'user',
            'area',
            'cursosArea',
            'asignaturasUnicas',
            'filasAsignaturas',
            'docenteInformes',
            'programasIniciales',
            'periodoDefecto',
            'mesDefecto'
        ));
    }

    /**
     * Guarda un nuevo Informe Mensual de Coordinación en la base de datos delegando al servicio.
     */
    public function guardarInformeCoordinacion(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $area = $user->area ?? session('coordinador_area');

        $request->validate([
            'periodo'                         => 'required|string|max:100',
            'mes'                             => 'required|string|max:50',
            'anio'                            => 'required|integer',
            'herramientas_virtuales'          => 'nullable|string',
            'enlace_actividades_coordinacion' => 'nullable|string|max:255',
            'enlace_informe_auxiliares'       => 'nullable|string|max:255',
            'enlace_docentes_permisos'        => 'nullable|string|max:255',
            'estado'                          => 'nullable|in:borrador,enviado',
        ]);

        try {
            $informe = $this->coordinacionService->guardarInforme($user, $request->all(), $area);

            return response()->json([
                'success'    => true,
                'message'    => 'Informe de coordinación guardado exitosamente.',
                'informe_id' => $informe->id,
                'redirect'   => route('coordinador.dashboard'),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar el informe: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Muestra el formulario para editar un informe de coordinación existente.
     */
    public function editarInformeCoordinacionView($id)
    {
        /** @var User $user */
        $user = Auth::user();

        $informe = InformeCoordinacion::with(['programas', 'asignaturas', 'avances', 'estudiantes'])
            ->where('usuario_id', $user->id)
            ->findOrFail($id);

        $area              = $informe->area;
        $cursosArea        = $this->getCursosPorArea($area);
        $cursoIds          = $cursosArea->pluck('id')->toArray();
        $asignaturasUnicas = $cursosArea->pluck('nombre_curso')->unique()->values();

        $docenteInformes = Informe::with(['usuario', 'semanas'])
            ->whereIn('curso_id', $cursoIds)
            ->where('periodo', $informe->periodo)
            ->where('mes', $informe->mes)
            ->whereIn('estado', ['enviado', 'aprobado', 'bloqueado'])
            ->get()
            ->keyBy('curso_id');

        $programasGuardados = $informe->programas->keyBy('asignatura');
        $programasCompletos = $asignaturasUnicas->map(function ($nombreCurso) use ($programasGuardados, $cursosArea, $docenteInformes) {
            $p      = $programasGuardados->get($nombreCurso);
            $enlace = $p ? $p->enlace_programa : null;
            if (!$enlace) {
                $subCursoIds = $cursosArea->where('nombre_curso', $nombreCurso)->pluck('id');
                foreach ($subCursoIds as $cId) {
                    $inf = $docenteInformes->get($cId);
                    if ($inf && !empty($inf->enlace_classroom_drive_url)) {
                        $enlace = $inf->enlace_classroom_drive_url;
                        break;
                    }
                }
            }
            return [
                'asignatura'      => $nombreCurso,
                'enlace_programa' => $enlace,
            ];
        });

        $isLocked = ($informe->bloqueado_en && Carbon::now()->greaterThan($informe->bloqueado_en));

        return view('coordinador.crear_informe_coordinacion', compact(
            'user',
            'area',
            'informe',
            'cursosArea',
            'asignaturasUnicas',
            'isLocked',
            'docenteInformes',
            'programasCompletos'
        ));
    }

    /**
     * Actualiza un informe de coordinación existente delegando al servicio.
     */
    public function actualizarInformeCoordinacion(Request $request, $id)
    {
        /** @var User $user */
        $user = Auth::user();

        $informe = InformeCoordinacion::where('usuario_id', $user->id)->findOrFail($id);

        // Validar si está bloqueado por el límite de 3 días
        if ($informe->bloqueado_en && Carbon::now()->greaterThan($informe->bloqueado_en)) {
            return response()->json([
                'success' => false,
                'message' => 'El informe está bloqueado para edición (plazo máximo de 3 días vencido).',
            ], 403);
        }

        $request->validate([
            'periodo'                         => 'required|string|max:100',
            'mes'                             => 'required|string|max:50',
            'anio'                            => 'required|integer',
            'herramientas_virtuales'          => 'nullable|string',
            'enlace_actividades_coordinacion' => 'nullable|string|max:255',
            'enlace_informe_auxiliares'       => 'nullable|string|max:255',
            'enlace_docentes_permisos'        => 'nullable|string|max:255',
            'estado'                          => 'nullable|in:borrador,enviado',
        ]);

        try {
            $this->coordinacionService->actualizarInforme($informe, $request->all());

            return response()->json([
                'success'  => true,
                'message'  => 'Informe de coordinación actualizado correctamente.',
                'redirect' => route('coordinador.dashboard'),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el informe: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Elimina un informe de coordinación permanentemente.
     */
    public function eliminarInformeCoordinacion($id)
    {
        /** @var User $user */
        $user = Auth::user();

        try {
            $informe = InformeCoordinacion::where('usuario_id', $user->id)->findOrFail($id);
            $this->eliminarInformeCoordinacionModel($informe);

            return response()->json([
                'success' => true,
                'message' => 'El informe de coordinación ha sido eliminado permanentemente.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el informe de coordinación: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Genera la vista institucional del informe de coordinación para consultar / imprimir.
     */
    public function verInformeCoordinacion($id)
    {
        /** @var User $user */
        $user = Auth::user();

        $informe = InformeCoordinacion::with(['programas', 'asignaturas', 'avances', 'estudiantes'])
            ->where('usuario_id', $user->id)
            ->findOrFail($id);

        return view('coordinador.ver_informe_coordinacion', compact('user', 'informe'));
    }
}
