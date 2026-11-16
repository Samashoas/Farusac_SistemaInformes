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

    public function cursosView(): View
    {
        $cursos = Curso::all();

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
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el curso: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function eliminarCurso(int|string $id): JsonResponse
    {
        try {
            $this->cursoService->eliminarCurso($id);

            return response()->json([
                'success' => true,
                'message' => 'Curso eliminado exitosamente',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el curso: ' . $e->getMessage(),
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
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el usuario: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function editarUsuario(EditarUsuarioRequest $request, int|string $id): JsonResponse
    {
        try {
            $usuario = $this->userService->editarUsuario($id, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Usuario actualizado exitosamente',
                'usuario' => new UserResource($usuario),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el usuario: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function toggleEstadoUsuario(int|string $id): JsonResponse
    {
        try {
            $usuario = $this->userService->toggleEstadoUsuario($id);

            return response()->json([
                'success' => true,
                'message' => 'Estado del usuario modificado exitosamente',
                'usuario' => new UserResource($usuario),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar estado del usuario: ' . $e->getMessage(),
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

    public function informesView(): View
    {
        $user = Auth::user();

        $informesDocentes = Informe::with(['usuario', 'curso', 'semanas'])
            ->orderBy('id', 'desc')
            ->get();

        $informesCoordinacion = InformeCoordinacion::with(['usuario', 'programas', 'asignaturas', 'avances', 'estudiantes'])
            ->orderBy('id', 'desc')
            ->get();

        $filtros = $this->catalogoService->getFiltrosAdmin($informesDocentes, $informesCoordinacion);

        return view('admin.informes', array_merge([
            'user'                 => $user,
            'informesDocentes'     => $informesDocentes,
            'informesCoordinacion' => $informesCoordinacion,
        ], $filtros));
    }

    public function verInformeDocente(int|string $id): View
    {
        $user    = Auth::user();
        $informe = Informe::with(['curso', 'semanas', 'usuario'])->findOrFail($id);

        return view('docente.ver_informe_pdf', compact('user', 'informe'));
    }

    public function verInformeCoordinacion(int|string $id): View
    {
        $user    = Auth::user();
        $informe = InformeCoordinacion::with(['programas', 'asignaturas', 'avances', 'estudiantes', 'usuario'])->findOrFail($id);

        return view('coordinador.ver_informe_coordinacion', compact('user', 'informe'));
    }

    public function obtenerDetalleInformeDocenteJson(int|string $id): JsonResponse
    {
        $informe = Informe::with(['curso', 'semanas', 'usuario'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'tipo'    => 'docente',
            'informe' => new InformeDocenteResource($informe),
        ]);
    }

    public function obtenerDetalleInformeCoordinacionJson(int|string $id): JsonResponse
    {
        $informe = InformeCoordinacion::with(['programas', 'asignaturas', 'avances', 'estudiantes', 'usuario'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'tipo'    => 'coordinacion',
            'informe' => new InformeCoordinacionResource($informe),
        ]);
    }

    public function eliminarInformeDocente(int|string $id): JsonResponse
    {
        try {
            $this->docenteService->eliminarInformeDocente($id);

            return response()->json([
                'success' => true,
                'message' => 'Informe de docente eliminado permanentemente.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el informe de docente: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function eliminarInformeCoordinacion(int|string $id): JsonResponse
    {
        try {
            $this->coordinacionService->eliminarInformeCoordinacion($id);

            return response()->json([
                'success' => true,
                'message' => 'Informe de coordinación eliminado permanentemente.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el informe de coordinación: ' . $e->getMessage(),
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
            return redirect()->back()->with('error', $e->getMessage());
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

    public function descargarSeleccionadosZip(Request $request): BinaryFileResponse|JsonResponse
    {
        $user       = Auth::user();
        $docenteIds = $request->input('docente_ids', []);
        $coordIds   = $request->input('coordinacion_ids', []);

        if (is_string($docenteIds)) {
            $docenteIds = explode(',', $docenteIds);
        }
        if (is_string($coordIds)) {
            $coordIds = explode(',', $coordIds);
        }

        try {
            return $this->exportService->descargarSeleccionadosZip($docenteIds, $coordIds, $user);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function verConsolidadoSeleccionadosPdf(Request $request): View
    {
        $user       = Auth::user();
        $docenteIds = $request->input('docente_ids', []);
        $coordIds   = $request->input('coordinacion_ids', []);

        if (is_string($docenteIds)) {
            $docenteIds = explode(',', $docenteIds);
        }
        if (is_string($coordIds)) {
            $coordIds = explode(',', $coordIds);
        }

        $informesDocentes     = !empty($docenteIds) ? Informe::with(['usuario', 'curso', 'semanas'])->whereIn('id', array_filter($docenteIds))->get() : collect();
        $informesCoordinacion = !empty($coordIds) ? InformeCoordinacion::with(['usuario', 'programas', 'asignaturas', 'avances', 'estudiantes'])->whereIn('id', array_filter($coordIds))->get() : collect();

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
        $docenteIds = $request->input('docente_ids', []);
        $coordIds   = $request->input('coordinacion_ids', []);

        if (is_string($docenteIds)) {
            $docenteIds = explode(',', $docenteIds);
        }
        if (is_string($coordIds)) {
            $coordIds = explode(',', $coordIds);
        }

        $result = $this->exportService->obtenerListaSeleccionadosZipJson($docenteIds, $coordIds);
        $status = empty($result['success']) ? 422 : 200;

        return response()->json($result, $status);
    }
}
