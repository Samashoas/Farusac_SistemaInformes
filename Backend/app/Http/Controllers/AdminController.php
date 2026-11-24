<?php

namespace App\Http\Controllers;

use App\Http\Requests\CrearCursoRequest;
use App\Http\Requests\CrearUsuarioRequest;
use App\Http\Requests\EditarUsuarioRequest;
use App\Http\Requests\ImportCargaAcademicaRequest;
use App\Http\Requests\ImportCsvRequest;
use App\Http\Resources\CursoResource;
use App\Http\Resources\InformeCoordinacionResource;
use App\Http\Resources\InformeDocenteResource;
use App\Http\Resources\UserResource;
use App\Models\Curso;
use App\Models\Informe;
use App\Models\InformeCoordinacion;
use App\Models\User;
use App\Services\CatalogoService;
use App\Services\CsvImportService;
use App\Services\CursoService;
use App\Services\InformeCoordinacionService;
use App\Services\InformeDocenteService;
use App\Services\InformeExportService;
use App\Services\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
    public function __construct(
        protected CsvImportService $csvService,
        protected InformeExportService $exportService,
        protected CursoService $cursoService,
        protected UserService $userService,
        protected CatalogoService $catalogoService,
        protected InformeDocenteService $docenteService,
        protected InformeCoordinacionService $coordinacionService
    ) {}

    public function dashboard(): View
    {
        return view('admin.dashboard');
    }

    public function cargaDatosView(): View
    {
        return view('admin.carga_datos');
    }

    public function usuariosView(Request $request): View
    {
        $perPage  = $request->query('per_page', 25);
        $usuarios = User::orderBy('id', 'desc')->paginate($perPage)->withQueryString();

        return view('admin.usuarios', compact('usuarios'));
    }

    public function cursosView(Request $request): View
    {
        $perPage = $request->query('per_page', 25);
        $cursos  = Curso::orderBy('id', 'desc')->paginate($perPage)->withQueryString();

        return view('admin.cursos', compact('cursos'));
    }

    public function crearCurso(CrearCursoRequest $request): JsonResponse
    {
        try {
            $curso = $this->cursoService->crearCurso($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Curso creado exitosamente',
                'curso'   => new CursoResource($curso),
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first(),
            ], 422);
        } catch (Exception $e) {
            Log::error('Error al crear el curso: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado al crear el curso.',
            ], 500);
        }
    }

    public function eliminarCurso(Curso $curso): JsonResponse
    {
        try {
            $this->cursoService->eliminarCurso($curso);

            return response()->json([
                'success' => true,
                'message' => 'Curso eliminado exitosamente',
            ]);
        } catch (Exception $e) {
            Log::error('Error al eliminar el curso: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al intentar eliminar el curso.',
            ], 500);
        }
    }

    public function crearUsuario(CrearUsuarioRequest $request): JsonResponse
    {
        try {
            $usuario = $this->userService->crearUsuario($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Usuario creado exitosamente',
                'usuario' => new UserResource($usuario),
            ]);
        } catch (Exception $e) {
            Log::error('Error al crear el usuario: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado al registrar el usuario.',
            ], 500);
        }
    }

    public function editarUsuario(EditarUsuarioRequest $request, User $usuario): JsonResponse
    {
        try {
            $updatedUsuario = $this->userService->editarUsuario($usuario, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Usuario actualizado exitosamente',
                'usuario' => new UserResource($updatedUsuario),
            ]);
        } catch (Exception $e) {
            Log::error('Error al actualizar el usuario: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado al actualizar el usuario.',
            ], 500);
        }
    }

    public function toggleEstadoUsuario(User $usuario): JsonResponse
    {
        try {
            $updatedUsuario = $this->userService->toggleEstadoUsuario($usuario);

            return response()->json([
                'success' => true,
                'message' => 'Estado del usuario modificado exitosamente',
                'usuario' => new UserResource($updatedUsuario),
            ]);
        } catch (Exception $e) {
            Log::error('Error al cambiar estado del usuario: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al modificar el estado del usuario.',
            ], 500);
        }
    }

    // =========================================================================
    // IMPORTACIÓN MASIVA DE DATOS CSV (Delegada a CsvImportService)
    // =========================================================================

    public function cargaUsuariosCsv(ImportCsvRequest $request): JsonResponse
    {
        $result = $this->csvService->importarUsuarios($request->file('archivo_csv'));

        return response()->json($result);
    }

    public function cargaCursosCsv(ImportCsvRequest $request): JsonResponse
    {
        $result = $this->csvService->importarCursos($request->file('archivo_csv'));

        return response()->json($result);
    }

    public function cargaAcademicaCsv(ImportCargaAcademicaRequest $request): JsonResponse
    {
        $result = $this->csvService->importarCargaAcademica(
            $request->file('archivo_csv'),
            $request->input('carrera'),
            $request->input('anio') ? intval($request->input('anio')) : null,
            $request->input('semestre')
        );

        return response()->json($result, $result['status'] ?? 200);
    }

    // =========================================================================
    // GESTIÓN Y VISUALIZACIÓN DE INFORMES
    // =========================================================================

    public function informesView(Request $request): View
    {
        $user    = Auth::user();
        $perPage = $request->query('per_page', 25);

        $informesDocentes = Informe::with(['usuario', 'curso', 'semanas'])
            ->orderBy('id', 'desc')
            ->paginate($perPage, ['*'], 'page_docentes')
            ->withQueryString();

        $informesCoordinacion = InformeCoordinacion::with(['usuario', 'programas', 'asignaturas', 'avances', 'estudiantes'])
            ->orderBy('id', 'desc')
            ->paginate($perPage, ['*'], 'page_coordinacion')
            ->withQueryString();

        $filtros = $this->catalogoService->getFiltrosAdmin($informesDocentes, $informesCoordinacion);

        return view('admin.informes', array_merge([
            'user'                 => $user,
            'informesDocentes'     => $informesDocentes,
            'informesCoordinacion' => $informesCoordinacion,
        ], $filtros));
    }

    public function verInformeDocente(Informe $informe): View
    {
        $user = Auth::user();
        $informe->loadMissing(['curso', 'semanas', 'usuario']);

        return view('docente.ver_informe_pdf', compact('user', 'informe'));
    }

    public function verInformeCoordinacion(InformeCoordinacion $informeCoordinacion): View
    {
        $user = Auth::user();
        $informeCoordinacion->loadMissing(['programas', 'asignaturas', 'avances', 'estudiantes', 'usuario']);

        // Si el informe carece de asignaturas registradas, autoreparar con los datos consolidados del área
        if ($informeCoordinacion->asignaturas->isEmpty()) {
            $res = $this->coordinacionService->obtenerDatosDocentesArea(
                $informeCoordinacion->area,
                null,
                $informeCoordinacion->periodo,
                $informeCoordinacion->mes,
                $informeCoordinacion->usuario ?? $user
            );
            if (!empty($res['datos']) && $res['datos']->isNotEmpty()) {
                $payload = [
                    'periodo'                         => $informeCoordinacion->periodo,
                    'mes'                             => $informeCoordinacion->mes,
                    'anio'                            => $informeCoordinacion->anio,
                    'herramientas_virtuales'          => $informeCoordinacion->herramientas_virtuales ?? 'Google Classroom, Google Meet, Zoom, Campus Virtual FARUSAC',
                    'enlace_actividades_coordinacion' => $informeCoordinacion->enlace_actividades_coordinacion,
                    'enlace_informe_auxiliares'       => $informeCoordinacion->enlace_informe_auxiliares,
                    'enlace_docentes_permisos'        => $informeCoordinacion->enlace_docentes_permisos,
                    'estado'                          => $informeCoordinacion->estado,
                    'programas'                       => $res['programas']->toArray(),
                    'asignaturas'                     => $res['datos']->toArray(),
                    'avances'                         => $res['datos']->toArray(),
                    'estudiantes'                     => [],
                ];
                try {
                    $this->coordinacionService->actualizarInforme($informeCoordinacion, $payload);
                    $informeCoordinacion->load(['programas', 'asignaturas', 'avances', 'estudiantes', 'usuario']);
                } catch (\Throwable $e) {
                    Log::warning('No se pudo autoreparar el informe de coordinación en admin: ' . $e->getMessage());
                }
            }
        }

        $informe = $informeCoordinacion;

        return view('coordinador.ver_informe_coordinacion', compact('user', 'informe'));
    }

    public function obtenerDetalleInformeDocenteJson(Informe $informe): JsonResponse
    {
        $informe->loadMissing(['curso', 'semanas', 'usuario']);

        return response()->json([
            'success' => true,
            'tipo'    => 'docente',
            'informe' => new InformeDocenteResource($informe),
        ]);
    }

    public function obtenerDetalleInformeCoordinacionJson(InformeCoordinacion $informeCoordinacion): JsonResponse
    {
        $informeCoordinacion->loadMissing(['programas', 'asignaturas', 'avances', 'estudiantes', 'usuario']);

        if ($informeCoordinacion->asignaturas->isEmpty()) {
            $res = $this->coordinacionService->obtenerDatosDocentesArea(
                $informeCoordinacion->area,
                null,
                $informeCoordinacion->periodo,
                $informeCoordinacion->mes,
                $informeCoordinacion->usuario
            );
            if (!empty($res['datos']) && $res['datos']->isNotEmpty()) {
                $payload = [
                    'periodo'                         => $informeCoordinacion->periodo,
                    'mes'                             => $informeCoordinacion->mes,
                    'anio'                            => $informeCoordinacion->anio,
                    'herramientas_virtuales'          => $informeCoordinacion->herramientas_virtuales ?? 'Google Classroom, Google Meet, Zoom, Campus Virtual FARUSAC',
                    'enlace_actividades_coordinacion' => $informeCoordinacion->enlace_actividades_coordinacion,
                    'enlace_informe_auxiliares'       => $informeCoordinacion->enlace_informe_auxiliares,
                    'enlace_docentes_permisos'        => $informeCoordinacion->enlace_docentes_permisos,
                    'estado'                          => $informeCoordinacion->estado,
                    'programas'                       => $res['programas']->toArray(),
                    'asignaturas'                     => $res['datos']->toArray(),
                    'avances'                         => $res['datos']->toArray(),
                    'estudiantes'                     => [],
                ];
                try {
                    $this->coordinacionService->actualizarInforme($informeCoordinacion, $payload);
                    $informeCoordinacion->load(['programas', 'asignaturas', 'avances', 'estudiantes', 'usuario']);
                } catch (\Throwable $e) {
                    Log::warning('No se pudo autoreparar el informe en detalle-json: ' . $e->getMessage());
                }
            }
        }

        return response()->json([
            'success' => true,
            'tipo'    => 'coordinacion',
            'informe' => new InformeCoordinacionResource($informeCoordinacion),
        ]);
    }

    public function eliminarInformeDocente(Informe $informe): JsonResponse
    {
        try {
            $this->docenteService->eliminarInformeDocente($informe);

            return response()->json([
                'success' => true,
                'message' => 'Informe de docente eliminado permanentemente.',
            ]);
        } catch (Exception $e) {
            Log::error('Error al eliminar el informe de docente: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al eliminar el informe de docente.',
            ], 500);
        }
    }

    public function eliminarInformeCoordinacion(InformeCoordinacion $informeCoordinacion): JsonResponse
    {
        try {
            $this->coordinacionService->eliminarInformeCoordinacion($informeCoordinacion);

            return response()->json([
                'success' => true,
                'message' => 'Informe de coordinación eliminado permanentemente.',
            ]);
        } catch (Exception $e) {
            Log::error('Error al eliminar el informe de coordinación: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al eliminar el informe de coordinación.',
            ], 500);
        }
    }

    // =========================================================================
    // EXPORTACIÓN Y REPORTES (Delegada a InformeExportService)
    // =========================================================================

    public function descargarInformesZip(Request $request): BinaryFileResponse|RedirectResponse
    {
        $user = Auth::user();
        try {
            return $this->exportService->descargarInformesZip($request, $user);
        } catch (Exception $e) {
            Log::error('Error al descargar ZIP de informes: ' . $e->getMessage(), ['exception' => $e]);

            return redirect()->back()->with('error', $e->getMessage() ?: 'Ocurrió un error al generar la descarga comprimida de los informes.');
        }
    }

    public function descargarInformesCsv(Request $request): StreamedResponse
    {
        return $this->exportService->descargarInformesCsv($request);
    }

    public function verConsolidadoPdf(Request $request): View
    {
        $user      = Auth::user();
        $filtrados = $this->exportService->obtenerInformesFiltrados($request);

        return view('admin.consolidado_informes_pdf', [
            'user'                 => $user,
            'informesDocentes'     => $filtrados['docentes'],
            'informesCoordinacion' => $filtrados['coordinacion'],
            'mes'                  => $filtrados['mes'],
            'anio'                 => $filtrados['anio'],
            'totalCount'           => $filtrados['total'],
        ]);
    }

    /**
     * Sanitiza y castea una lista de IDs (como array o string delimitado por comas) a enteros positivos.
     *
     * @return int[]
     */
    protected function sanitizarIds(mixed $ids): array
    {
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }

        if (!is_array($ids)) {
            return [];
        }

        return array_values(array_filter(
            array_map('intval', $ids),
            fn (int $id) => $id > 0
        ));
    }

    public function descargarSeleccionadosZip(Request $request): BinaryFileResponse|RedirectResponse|JsonResponse
    {
        $user       = Auth::user();
        $docenteIds = $this->sanitizarIds($request->input('docente_ids', []));
        $coordIds   = $this->sanitizarIds($request->input('coordinacion_ids', []));

        try {
            return $this->exportService->descargarSeleccionadosZip($docenteIds, $coordIds, $user);
        } catch (Exception $e) {
            Log::error('Error al descargar ZIP de seleccionados: ' . $e->getMessage(), ['exception' => $e]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?: 'Ocurrió un error al procesar la descarga comprimida de los informes seleccionados.',
                ], 422);
            }

            return redirect()->back()->with('error', $e->getMessage() ?: 'Ocurrió un error al procesar la descarga comprimida de los informes seleccionados.');
        }
    }

    public function verConsolidadoSeleccionadosPdf(Request $request): View
    {
        $user       = Auth::user();
        $docenteIds = $this->sanitizarIds($request->input('docente_ids', []));
        $coordIds   = $this->sanitizarIds($request->input('coordinacion_ids', []));

        $informesDocentes     = !empty($docenteIds) ? Informe::with(['usuario', 'curso', 'semanas'])->whereIn('id', $docenteIds)->get() : collect();
        $informesCoordinacion = !empty($coordIds) ? InformeCoordinacion::with(['usuario', 'programas', 'asignaturas', 'avances', 'estudiantes'])->whereIn('id', $coordIds)->get() : collect();

        $mes        = 'Seleccionados';
        $anio       = date('Y');
        $totalCount = $informesDocentes->count() + $informesCoordinacion->count();

        return view('admin.consolidado_informes_pdf', compact(
            'user',
            'informesDocentes',
            'informesCoordinacion',
            'mes',
            'anio',
            'totalCount'
        ));
    }

    public function obtenerListaDescargaZipJson(Request $request): JsonResponse
    {
        $result = $this->exportService->obtenerListaDescargaZipJson($request);

        return response()->json($result);
    }

    public function obtenerListaSeleccionadosZipJson(Request $request): JsonResponse
    {
        $docenteIds = $this->sanitizarIds($request->input('docente_ids', []));
        $coordIds   = $this->sanitizarIds($request->input('coordinacion_ids', []));

        $result = $this->exportService->obtenerListaSeleccionadosZipJson($docenteIds, $coordIds);
        $status = empty($result['success']) ? 422 : 200;

        return response()->json($result, $status);
    }
}
