<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActualizarInformeDocenteRequest;
use App\Http\Requests\ActualizarPerfilRequest;
use App\Http\Requests\AsignarCursoRequest;
use App\Http\Requests\GuardarInformeDocenteRequest;
use App\Http\Resources\CursoResource;
use App\Models\Curso;
use App\Models\Informe;
use App\Models\User;
use App\Services\CatalogoService;
use App\Services\CursoService;
use App\Services\InformeDocenteService;
use App\Services\UserService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DocenteController extends Controller
{
    public function __construct(
        protected InformeDocenteService $docenteService,
        protected CatalogoService $catalogoService,
        protected CursoService $cursoService,
        protected UserService $userService
    ) {}

    /**
     * Muestra el Dashboard principal del docente con sus cursos asignados y catálogo.
     */
    public function dashboard(): View
    {
        /** @var User $user */
        $user = Auth::user();

        try {
            $cursosAsignados = $user->cursos()
                ->orderBy('nombre_curso', 'asc')
                ->orderBy('seccion', 'asc')
                ->get();
        } catch (\Throwable $e) {
            Log::warning('No se pudieron cargar los cursos asignados del docente ID ' . ($user->id ?? '?') . ': ' . $e->getMessage());
            $cursosAsignados = collect();
        }

        $catalogoCursos = $this->catalogoService->getCatalogoCursos();
        $carreras       = $this->catalogoService->getCarreras();

        return view('docente.dashboard', compact('user', 'cursosAsignados', 'catalogoCursos', 'carreras'));
    }

    /**
     * Asigna un curso seleccionado al docente autenticado.
     */
    public function asignarCurso(AsignarCursoRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        try {
            $curso = $this->cursoService->asignarDocente($user, (int) $request->validated('curso_id'));

            return response()->json([
                'success' => true,
                'message' => 'Curso asignado exitosamente.',
                'curso'   => new CursoResource($curso),
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first(),
            ], 422);
        } catch (Exception $e) {
            Log::error('Error al asignar el curso: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado al asignar el curso.',
            ], 500);
        }
    }

    /**
     * Elimina la asignación de un curso del docente.
     */
    public function desasignarCurso(Curso $curso): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        try {
            $this->cursoService->desasignarDocente($user, $curso);

            return response()->json([
                'success' => true,
                'message' => 'El curso ha sido removido de tu panel exitosamente.',
            ]);
        } catch (Exception $e) {
            Log::error('Error al remover la asignación del curso: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al intentar remover el curso asignado.',
            ], 500);
        }
    }

    /**
     * Muestra el historial completo de informes entregados por el docente.
     */
    public function historialInformes(): View
    {
        /** @var User $user */
        $user = Auth::user();

        try {
            $informes = Informe::with(['curso', 'semanas'])
                ->where('usuario_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
        } catch (Exception $e) {
            $informes = collect();
        }

        $filtros = $this->catalogoService->getFiltrosDocente($informes, $user);
        $vista   = $user->isCoordinador() ? 'coordinador.historial_informes' : 'docente.historial_informes';

        return view($vista, array_merge(['user' => $user, 'informes' => $informes], $filtros));
    }

    /**
     * Muestra el formulario para crear un nuevo informe mensual de docente.
     */
    public function crearInformeView(Request $request): View
    {
        /** @var User $user */
        $user = Auth::user();

        try {
            $cursosAsignados = $user->cursos()
                ->orderBy('nombre_curso', 'asc')
                ->orderBy('seccion', 'asc')
                ->get();
        } catch (\Throwable $e) {
            Log::warning('No se pudieron cargar los cursos asignados del docente ID ' . ($user->id ?? '?') . ': ' . $e->getMessage());
            $cursosAsignados = collect();
        }

        $selectedCursoId = $request->query('curso_id');
        $selectedCurso   = null;
        if ($selectedCursoId) {
            $selectedCurso = $cursosAsignados->firstWhere('id', $selectedCursoId) ?? Curso::find($selectedCursoId);
        }

        $vista                  = $user->isCoordinador() ? 'coordinador.crear_informe' : 'docente.crear_informe';
        $cursoPreseleccionadoId = $selectedCursoId;

        $informesExistentes = Informe::where('usuario_id', $user->id)
            ->get(['id', 'curso_id', 'mes', 'periodo'])
            ->map(function ($inf) {
                preg_match('/\d{4}/', $inf->periodo, $m);
                return [
                    'id'       => $inf->id,
                    'curso_id' => (int) $inf->curso_id,
                    'mes'      => mb_strtolower(trim($inf->mes)),
                    'anio'     => $m[0] ?? null,
                    'periodo'  => $inf->periodo,
                ];
            });

        return view($vista, compact('user', 'cursosAsignados', 'selectedCurso', 'cursoPreseleccionadoId', 'informesExistentes'));
    }

    /**
     * Guarda el nuevo informe docente con sus semanas registradas delegando al servicio.
     */
    public function guardarInforme(GuardarInformeDocenteRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        try {
            $informe = $this->docenteService->guardarInforme($user, $request->validated());

            $redirectUrl = $user->isCoordinador()
                ? route('coordinador.dashboard')
                : route('docente.dashboard');

            $dias = (int) config('informes.dias_bloqueo', 3);

            return response()->json([
                'success'      => true,
                'message'      => "El informe ha sido creado y enviado exitosamente. Recuerda que tienes un máximo de {$dias} días para editar o modificar tu informe antes de que se bloquee para revisión.",
                'informe_id'   => $informe->id,
                'redirect_url' => $redirectUrl,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first(),
                'errors'  => $e->validator->errors(),
            ], 422);
        } catch (Exception $e) {
            Log::error('Error al guardar el informe docente: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al guardar el informe.',
            ], 500);
        }
    }

    /**
     * Muestra la vista de edición de informe cargando los datos previos.
     */
    public function editarInformeView(Informe $informe): View|RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user->isAdministrador() && $informe->usuario_id !== $user->id) {
            abort(403, 'No tienes autorización para editar este informe.');
        }

        if (!$user->isAdministrador() && $informe->isBloqueado()) {
            $dias = (int) config('informes.dias_bloqueo', 3);
            $redirectUrl = $user->isCoordinador() ? route('coordinador.informes') : route('docente.informes');
            return redirect($redirectUrl)->with('error', "Este informe ya no puede ser editado porque ha vencido el plazo límite de {$dias} días.");
        }

        $informe->loadMissing(['curso', 'semanas']);
        $estaBloqueado = $informe->isBloqueado();

        try {
            $cursosAsignados = $user->cursos()
                ->orderBy('nombre_curso', 'asc')
                ->orderBy('seccion', 'asc')
                ->get();
        } catch (\Throwable $e) {
            Log::warning('No se pudieron cargar los cursos asignados del docente ID ' . ($user->id ?? '?') . ': ' . $e->getMessage());
            $cursosAsignados = collect();
        }

        $selectedCurso = $informe->curso;
        $modoEdicion   = true;
        $vista         = $user->isCoordinador() ? 'coordinador.crear_informe' : 'docente.crear_informe';

        $informesExistentes = Informe::where('usuario_id', $user->id)
            ->where('id', '!=', $informe->id)
            ->get(['id', 'curso_id', 'mes', 'periodo'])
            ->map(function ($inf) {
                preg_match('/\d{4}/', $inf->periodo, $m);
                return [
                    'id'       => $inf->id,
                    'curso_id' => (int) $inf->curso_id,
                    'mes'      => mb_strtolower(trim($inf->mes)),
                    'anio'     => $m[0] ?? null,
                    'periodo'  => $inf->periodo,
                ];
            });

        return view($vista, compact('user', 'cursosAsignados', 'selectedCurso', 'informe', 'modoEdicion', 'estaBloqueado', 'informesExistentes'));
    }

    /**
     * Actualiza un informe existente y sus semanas delegando al servicio.
     */
    public function actualizarInforme(ActualizarInformeDocenteRequest $request, Informe $informe): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user->isAdministrador() && $informe->usuario_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes autorización para actualizar este informe.',
            ], 403);
        }

        if (!$user->isAdministrador() && $informe->isBloqueado()) {
            $dias = (int) config('informes.dias_bloqueo', 3);
            return response()->json([
                'success' => false,
                'message' => "Este informe ya no puede ser editado porque ha vencido el plazo límite de {$dias} días.",
            ], 422);
        }

        try {
            $this->docenteService->actualizarInforme($informe, $request->validated(), $user);

            $redirectUrl = $user->isCoordinador() ? route('coordinador.informes') : route('docente.informes');

            return response()->json([
                'success'      => true,
                'message'      => 'Informe actualizado exitosamente.',
                'redirect_url' => $redirectUrl,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first(),
                'errors'  => $e->validator->errors(),
            ], 422);
        } catch (Exception $e) {
            Log::error('Error al actualizar el informe docente: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al actualizar el informe.',
            ], 500);
        }
    }

    /**
     * Elimina permanentemente un informe del docente delegando al servicio.
     */
    public function eliminarInforme(Informe $informe): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user->isAdministrador() && $informe->usuario_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes autorización para eliminar este informe.',
            ], 403);
        }

        if (!$user->isAdministrador() && $informe->isBloqueado()) {
            $dias = (int) config('informes.dias_bloqueo', 3);
            return response()->json([
                'success' => false,
                'message' => "No se puede eliminar un informe cuyo plazo de {$dias} días ya ha vencido.",
            ], 422);
        }

        try {
            $this->docenteService->eliminarInformeDocente($informe, $user);

            return response()->json([
                'success' => true,
                'message' => 'El informe ha sido eliminado permanentemente.',
            ]);
        } catch (Exception $e) {
            Log::error('Error al eliminar el informe docente: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al eliminar el informe.',
            ], 500);
        }
    }

    /**
     * Genera la vista institucional del informe en formato lista para imprimir/PDF.
     */
    public function verInforme(Informe $informe): View
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user->isCoordinador() && !$user->isAdministrador() && $informe->usuario_id !== $user->id) {
            abort(403, 'No tienes autorización para ver este informe.');
        }

        $informe->loadMissing(['curso', 'semanas', 'usuario']);

        return view('docente.ver_informe_pdf', compact('user', 'informe'));
    }

    /**
     * Muestra la vista de perfil del docente.
     */
    public function perfilView(): View
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
    public function actualizarPerfil(ActualizarPerfilRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        try {
            $this->userService->actualizarPerfil($user, $request->validated('numero'));

            return response()->json([
                'success' => true,
                'message' => 'Perfil actualizado exitosamente.',
                'numero'  => $user->numero,
            ]);
        } catch (Exception $e) {
            Log::error('Error al actualizar el perfil del usuario: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al actualizar el perfil.',
            ], 500);
        }
    }
}
