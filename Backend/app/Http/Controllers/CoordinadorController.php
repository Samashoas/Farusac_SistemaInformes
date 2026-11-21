<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActualizarInformeCoordinacionRequest;
use App\Http\Requests\GuardarInformeCoordinacionRequest;
use App\Http\Requests\SeleccionarAreaRequest;
use App\Http\Resources\InformeDocenteResource;
use App\Models\Informe;
use App\Models\InformeCoordinacion;
use App\Models\User;
use App\Services\CatalogoService;
use App\Services\InformeCoordinacionService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class CoordinadorController extends Controller
{
    public function __construct(
        protected InformeCoordinacionService $coordinacionService,
        protected CatalogoService $catalogoService
    ) {}

    /**
     * Dashboard principal del coordinador.
     */
    public function dashboard(): View
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

        $catalogoCursos   = $this->catalogoService->getCatalogoCursos();
        $carreras         = $this->catalogoService->getCarreras();
        $areasPorCarrera  = $this->catalogoService->getAreasPorCarrera();
        $areasDisponibles = $this->catalogoService->getAreasDisponibles();

        // Informes mensuales de coordinación generados por este usuario
        try {
            $informesCoordinacion = InformeCoordinacion::where('usuario_id', $user->id)
                ->orderBy('anio', 'desc')
                ->orderBy('id', 'desc')
                ->get();
        } catch (Exception $e) {
            $informesCoordinacion = collect();
        }

        return view('coordinador.dashboard', compact(
            'user',
            'cursosAsignados',
            'catalogoCursos',
            'carreras',
            'areasDisponibles',
            'areasPorCarrera',
            'informesCoordinacion'
        ));
    }

    /**
     * Guarda la selección obligatoria del área a cargo del coordinador.
     */
    public function seleccionarArea(SeleccionarAreaRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validated();
        $area      = $validated['area'];
        $carrera   = $validated['carrera'] ?? null;

        try {
            $user->update(['area' => $area]);
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
            Log::error('Error al guardar área en usuarios: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al guardar el área en la base de datos.',
            ], 500);
        }
    }

    /**
     * Retorna los datos completos de un informe de docente en formato JSON para el modal interactivo.
     */
    public function obtenerDetalleInformeDocenteJson(Informe $informe): JsonResponse
    {
        $informe->loadMissing(['curso', 'semanas', 'usuario']);

        return response()->json([
            'success' => true,
            'informe' => new InformeDocenteResource($informe),
        ]);
    }

    /**
     * Retorna los datos dinámicos consolidados de docentes y programas para el área en formato JSON.
     */
    public function obtenerDatosDocentesArea(Request $request): JsonResponse
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

    /**
     * Muestra el formulario para crear un Informe Mensual de Coordinación.
     */
    public function crearInformeCoordinacionView(Request $request): View|RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $area = $user->area ?? session('coordinador_area');

        if (!$area) {
            return redirect()->route('coordinador.dashboard')
                ->with('error', 'Debe seleccionar un área de coordinación primero.');
        }

        $cursosArea        = $this->coordinacionService->getCursosPorArea($area, session('coordinador_carrera'), $user);
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
    public function guardarInformeCoordinacion(GuardarInformeCoordinacionRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $area = $user->area ?? session('coordinador_area');

        try {
            $informe = $this->coordinacionService->guardarInforme($user, $request->validated(), $area);

            return response()->json([
                'success'    => true,
                'message'    => 'Informe de coordinación guardado exitosamente.',
                'informe_id' => $informe->id,
                'redirect'   => route('coordinador.dashboard'),
            ]);
        } catch (Exception $e) {
            Log::error('Error al guardar informe de coordinación: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al guardar el informe de coordinación.',
            ], 500);
        }
    }

    /**
     * Muestra el formulario para editar un informe de coordinación existente.
     */
    public function editarInformeCoordinacionView(InformeCoordinacion $informeCoordinacion): View
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user->isAdministrador() && $informeCoordinacion->usuario_id !== $user->id) {
            abort(403, 'No tienes autorización para editar este informe de coordinación.');
        }

        $informeCoordinacion->loadMissing(['programas', 'asignaturas', 'avances', 'estudiantes']);
        $informe = $informeCoordinacion;

        $area              = $informe->area;
        $cursosArea        = $this->coordinacionService->getCursosPorArea($area, session('coordinador_carrera'), $user);
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
    public function actualizarInformeCoordinacion(ActualizarInformeCoordinacionRequest $request, InformeCoordinacion $informeCoordinacion): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user->isAdministrador() && $informeCoordinacion->usuario_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes autorización para actualizar este informe de coordinación.',
            ], 403);
        }

        if ($informeCoordinacion->isBloqueado()) {
            $dias = (int) config('informes.dias_bloqueo', 3);
            return response()->json([
                'success' => false,
                'message' => "El informe está bloqueado para edición (plazo máximo de {$dias} días vencido).",
            ], 403);
        }

        try {
            $this->coordinacionService->actualizarInforme($informeCoordinacion, $request->validated());

            return response()->json([
                'success'  => true,
                'message'  => 'Informe de coordinación actualizado correctamente.',
                'redirect' => route('coordinador.dashboard'),
            ]);
        } catch (Exception $e) {
            Log::error('Error al actualizar informe de coordinación: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al actualizar el informe de coordinación.',
            ], 500);
        }
    }

    /**
     * Elimina un informe de coordinación permanentemente delegando al servicio.
     */
    public function eliminarInformeCoordinacion(InformeCoordinacion $informeCoordinacion): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user->isAdministrador() && $informeCoordinacion->usuario_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes autorización para eliminar este informe de coordinación.',
            ], 403);
        }

        try {
            $this->coordinacionService->eliminarInformeCoordinacion($informeCoordinacion, $user);

            return response()->json([
                'success' => true,
                'message' => 'El informe de coordinación ha sido eliminado permanentemente.',
            ]);
        } catch (Exception $e) {
            Log::error('Error al eliminar informe de coordinación: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al eliminar el informe de coordinación.',
            ], 500);
        }
    }

    /**
     * Genera la vista institucional del informe de coordinación para consultar / imprimir.
     */
    public function verInformeCoordinacion(InformeCoordinacion $informeCoordinacion): View
    {
        /** @var User $user */
        $user = Auth::user();

        $informeCoordinacion->loadMissing(['programas', 'asignaturas', 'avances', 'estudiantes']);
        $informe = $informeCoordinacion;

        return view('coordinador.ver_informe_coordinacion', compact('user', 'informe'));
    }
}
