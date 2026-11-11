<?php

namespace App\Http\Controllers;

use App\Enums\Rol;
use App\Models\Curso;
use App\Models\Informe;
use App\Models\InformeCoordinacion;
use App\Models\User;
use App\Services\CsvImportService;
use App\Services\InformeExportService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Enum;

class AdminController extends Controller
{
    public function __construct(
        protected CsvImportService $csvService,
        protected InformeExportService $exportService
    ) {}

    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function cargaDatosView()
    {
        return view('admin.carga_datos');
    }

    public function usuariosView(Request $request)
    {
        $perPage  = $request->query('per_page', 25);
        $usuarios = User::orderBy('id', 'desc')->paginate($perPage)->withQueryString();
        return view('admin.usuarios', compact('usuarios'));
    }

    public function cursosView()
    {
        $cursos = Curso::all();
        return view('admin.cursos', compact('cursos'));
    }

    public function crearCurso(Request $request)
    {
        $request->validate([
            'carrera'      => 'required|string|max:100',
            'area'         => 'required|string|max:150',
            'nombre_curso' => 'required|string|max:150',
            'codigo_curso' => 'required|string|max:50',
            'seccion'      => 'required|string|max:10',
            'anio'         => 'required|integer',
            'semestre'     => 'required|string|max:30',
        ]);

        $existe = Curso::where('carrera', $request->carrera)
            ->where('codigo_curso', $request->codigo_curso)
            ->where('seccion', $request->seccion)
            ->where('anio', $request->anio)
            ->where('semestre', $request->semestre)
            ->first();

        if ($existe) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe un registro con la misma Carrera, Código, Sección, Año y Semestre.',
            ], 422);
        }

        $curso = Curso::create([
            'carrera'      => $request->carrera,
            'area'         => $request->area,
            'nombre_curso' => $request->nombre_curso,
            'codigo_curso' => $request->codigo_curso,
            'seccion'      => $request->seccion,
            'anio'         => $request->anio,
            'semestre'     => $request->semestre,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Curso creado exitosamente',
            'curso'   => $curso,
        ]);
    }

    public function eliminarCurso($id)
    {
        $curso = Curso::findOrFail($id);
        $curso->delete();

        return response()->json([
            'success' => true,
            'message' => 'Curso eliminado exitosamente',
        ]);
    }

    public function crearUsuario(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:150',
            'correo' => 'required|email|max:100|unique:usuarios,correo',
            'numero' => 'required|string|max:30',
            'rol'    => ['required', new Enum(Rol::class)],
            'plaza'  => 'required|in:titular + ampliacion,titular,interino',
        ]);

        $usuario = User::create([
            'nombre' => $request->nombre,
            'correo' => $request->correo,
            'numero' => $request->numero,
            'rol'    => Rol::from($request->rol),
            'plaza'  => $request->plaza,
            'estado' => 'activo',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Usuario creado exitosamente',
            'usuario' => $usuario,
        ]);
    }

    public function editarUsuario(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        $request->validate([
            'numero' => 'required|string|max:30',
            'rol'    => ['required', new Enum(Rol::class)],
            'plaza'  => 'required|in:titular + ampliacion,titular,interino',
        ]);

        $usuario->update([
            'numero' => $request->numero,
            'rol'    => Rol::from($request->rol),
            'plaza'  => $request->plaza,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Usuario actualizado exitosamente',
            'usuario' => $usuario,
        ]);
    }

    public function toggleEstadoUsuario($id)
    {
        $usuario = User::findOrFail($id);

        $nuevoEstado = ($usuario->estado === 'activo') ? 'inactivo' : 'activo';
        $usuario->update([
            'estado' => $nuevoEstado,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Estado del usuario modificado exitosamente',
            'usuario' => $usuario,
        ]);
    }

    // =========================================================================
    // IMPORTACIÓN MASIVA DE DATOS CSV (Delegada a CsvImportService)
    // =========================================================================

    public function cargaUsuariosCsv(Request $request)
    {
        $request->validate([
            'archivo_csv' => 'required|mimes:csv,txt|max:5120',
        ]);

        $result = $this->csvService->importarUsuarios($request->file('archivo_csv'));
        return response()->json($result);
    }

    public function cargaCursosCsv(Request $request)
    {
        $request->validate([
            'archivo_csv' => 'required|mimes:csv,txt|max:5120',
        ]);

        $result = $this->csvService->importarCursos($request->file('archivo_csv'));
        return response()->json($result);
    }

    public function cargaAcademicaCsv(Request $request)
    {
        $request->validate([
            'archivo_csv' => 'required|file|max:10240',
        ]);

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

    public function informesView()
    {
        $user = Auth::user();

        $informesDocentes = Informe::with(['usuario', 'curso', 'semanas'])
            ->orderBy('id', 'desc')
            ->get();

        $informesCoordinacion = InformeCoordinacion::with(['usuario', 'programas', 'asignaturas', 'avances', 'estudiantes'])
            ->orderBy('id', 'desc')
            ->get();

        $carreras = Curso::select('carrera')
            ->distinct()
            ->whereNotNull('carrera')
            ->where('carrera', '!=', '')
            ->pluck('carrera');

        $areas = Curso::select('area')
            ->distinct()
            ->whereNotNull('area')
            ->where('area', '!=', '')
            ->orderBy('area', 'asc')
            ->pluck('area');

        $periodosDocentes = Informe::select('periodo')->distinct()->whereNotNull('periodo')->pluck('periodo');
        $periodosCoord    = InformeCoordinacion::select('periodo')->distinct()->whereNotNull('periodo')->pluck('periodo');
        $periodos         = $periodosDocentes->merge($periodosCoord)->unique()->filter()->values();

        $aniosDocentes = Curso::select('anio')->distinct()->whereNotNull('anio')->pluck('anio');
        $aniosCoord    = InformeCoordinacion::select('anio')->distinct()->whereNotNull('anio')->pluck('anio');
        $anios         = $aniosDocentes->merge($aniosCoord)->unique()->filter()->sortDesc()->values();

        $stats = [
            'total'                => $informesDocentes->count() + $informesCoordinacion->count(),
            'docentes'             => $informesDocentes->count(),
            'coordinacion'         => $informesCoordinacion->count(),
            'docentes_unicos'      => $informesDocentes->pluck('usuario_id')->unique()->count(),
            'coordinadores_unicos' => $informesCoordinacion->pluck('usuario_id')->unique()->count(),
        ];

        return view('admin.informes', compact(
            'user',
            'informesDocentes',
            'informesCoordinacion',
            'carreras',
            'areas',
            'periodos',
            'anios',
            'stats'
        ));
    }

    public function verInformeDocente($id)
    {
        $user    = Auth::user();
        $informe = Informe::with(['curso', 'semanas', 'usuario'])->findOrFail($id);
        return view('docente.ver_informe_pdf', compact('user', 'informe'));
    }

    public function verInformeCoordinacion($id)
    {
        $user    = Auth::user();
        $informe = InformeCoordinacion::with(['programas', 'asignaturas', 'avances', 'estudiantes', 'usuario'])->findOrFail($id);
        return view('coordinador.ver_informe_coordinacion', compact('user', 'informe'));
    }

    public function obtenerDetalleInformeDocenteJson($id)
    {
        $informe = Informe::with(['curso', 'semanas', 'usuario'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'tipo'    => 'docente',
            'informe' => $this->buildInformeDocenteData($informe),
        ]);
    }

    public function obtenerDetalleInformeCoordinacionJson($id)
    {
        $informe = InformeCoordinacion::with(['programas', 'asignaturas', 'avances', 'estudiantes', 'usuario'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'tipo'    => 'coordinacion',
            'informe' => $this->buildInformeCoordinacionData($informe),
        ]);
    }

    public function eliminarInformeDocente($id)
    {
        try {
            $informe = Informe::findOrFail($id);
            $this->eliminarInformeDocenteModel($informe);

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

    public function eliminarInformeCoordinacion($id)
    {
        try {
            $informe = InformeCoordinacion::findOrFail($id);
            $this->eliminarInformeCoordinacionModel($informe);

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

    public function descargarInformesZip(Request $request)
    {
        $user = Auth::user();
        try {
            return $this->exportService->descargarInformesZip($request, $user);
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function descargarInformesCsv(Request $request)
    {
        return $this->exportService->descargarInformesCsv($request);
    }

    public function verConsolidadoPdf(Request $request)
    {
        $user      = Auth::user();
        $filtrados = $this->exportService->obtenerInformesFiltrados($request);

        $informesDocentes     = $filtrados['docentes'];
        $informesCoordinacion = $filtrados['coordinacion'];
        $mes                  = $filtrados['mes'];
        $anio                 = $filtrados['anio'];
        $totalCount           = $filtrados['total'];

        return view('admin.consolidado_informes_pdf', compact(
            'user',
            'informesDocentes',
            'informesCoordinacion',
            'mes',
            'anio',
            'totalCount'
        ));
    }

    public function descargarSeleccionadosZip(Request $request)
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

    public function verConsolidadoSeleccionadosPdf(Request $request)
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

    public function obtenerListaDescargaZipJson(Request $request)
    {
        $result = $this->exportService->obtenerListaDescargaZipJson($request);
        return response()->json($result);
    }

    public function obtenerListaSeleccionadosZipJson(Request $request)
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
