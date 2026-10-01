<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Curso;
use App\Models\Informe;
use App\Models\InformeCoordinacion;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function dashboard(){
        return view('admin.dashboard');
    }

    public function cargaDatosView(){
        return view('admin.carga_datos');
    }

    public function usuariosView(){
        $usuarios = User::all();
        return view('admin.usuarios', compact('usuarios'));
    }

    public function cursosView(){
        $cursos = Curso::all();
        return view('admin.cursos', compact('cursos'));
    }

    public function crearCurso(Request $request){
        $request->validate([
            'carrera' => 'required|string|max:100',
            'area' => 'required|string|max:150',
            'nombre_curso' => 'required|string|max:150',
            'codigo_curso' => 'required|integer',
            'seccion' => 'required|string|max:10',
            'anio' => 'required|integer',
            'semestre' => 'required|string|max:30',
        ]);

        // Evitar duplicados basados en UNIQUE KEY uq_curso_periodo (carrera, codigo_curso, seccion, anio, semestre)
        $existe = Curso::where('carrera', $request->carrera)
            ->where('codigo_curso', $request->codigo_curso)
            ->where('seccion', $request->seccion)
            ->where('anio', $request->anio)
            ->where('semestre', $request->semestre)
            ->first();

        if ($existe) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe un registro con la misma Carrera, Código, Sección, Año y Semestre.'
            ], 422);
        }

        $curso = Curso::create([
            'carrera' => $request->carrera,
            'area' => $request->area,
            'nombre_curso' => $request->nombre_curso,
            'codigo_curso' => $request->codigo_curso,
            'seccion' => $request->seccion,
            'anio' => $request->anio,
            'semestre' => $request->semestre,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Curso creado exitosamente',
            'curso' => $curso
        ]);
    }

    public function eliminarCurso($id){
        $curso = Curso::findOrFail($id);
        $curso->delete();

        return response()->json([
            'success' => true,
            'message' => 'Curso eliminado exitosamente'
        ]);
    }

    public function crearUsuario(Request $request){
        $request->validate([
            'nombre' => 'required|string|max:150',
            'correo' => 'required|email|max:100|unique:usuarios,correo',
            'numero' => 'required|string|max:30',
            'rol' => 'required|in:docente,coordinador,administrador',
            'plaza' => 'required|in:titular + ampliacion,titular,interino',
        ]);

        $usuario = User::create([
            'nombre' => $request->nombre,
            'correo' => $request->correo,
            'numero' => $request->numero,
            'rol' => $request->rol,
            'plaza' => $request->plaza,
            'estado' => 'activo',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Usuario creado exitosamente',
            'usuario' => $usuario
        ]);
    }

    public function editarUsuario(Request $request, $id){
        $usuario = User::findOrFail($id);

        $request->validate([
            'numero' => 'required|string|max:30',
            'rol' => 'required|in:docente,coordinador,administrador',
            'plaza' => 'required|in:titular + ampliacion,titular,interino',
        ]);

        $usuario->update([
            'numero' => $request->numero,
            'rol' => $request->rol,
            'plaza' => $request->plaza,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Usuario actualizado exitosamente',
            'usuario' => $usuario
        ]);
    }

    public function toggleEstadoUsuario($id){
        $usuario = User::findOrFail($id);
        
        $nuevoEstado = ($usuario->estado === 'activo') ? 'inactivo' : 'activo';
        $usuario->update([
            'estado' => $nuevoEstado
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Estado del usuario modificado exitosamente',
            'usuario' => $usuario
        ]);
    }

    public function CargaUsuariosCsv(Request $request){
        $request->validate([
            'archivo_csv' => 'required|mimes:csv,txt|max:5120',
        ]);

        $archivo = $request->file('archivo_csv');
        $handle = fopen($archivo->getRealPath(), 'r');

        $encabezados = fgetcsv($handle, 1000, ',');

        $success_records = [];
        $error_lines = [];
        $line_number = 1;

        while(($fila = fgetcsv($handle, 1000, ',')) !== FALSE){
            $line_number++;
            if(count($fila) >= 4){
                $nombre = trim($fila[0]);
                $correo = trim($fila[1]);
                $numero = trim($fila[2]);
                $plaza = trim($fila[3]);

                $es_correo_valido = filter_var($correo, FILTER_VALIDATE_EMAIL) && str_ends_with(strtolower($correo), '@farusac.edu.gt');
                $plazas_validas = ['Titular', 'Titular+Ampliacion', 'Interino'];

                if(!empty($nombre) && $es_correo_valido && in_array($plaza, $plazas_validas)){
                    try {
                        $usuario = User::updateOrCreate(
                            ['correo' => $correo],
                            [
                                'nombre' => $nombre,
                                'numero' => $numero,
                                'plaza' => $plaza,
                                'rol' => 'docente' // Por defecto
                            ]
                        );
                        $success_records[] = [
                            'id' => $usuario->id,
                            'nombre' => $usuario->nombre,
                            'correo' => $usuario->correo,
                            'numero' => $usuario->numero ?? 'N/D',
                            'rol' => $usuario->rol,
                            'plaza' => $usuario->plaza ?? 'N/D',
                            'estado' => $usuario->estado,
                        ];
                    } catch (\Exception $e) {
                        $error_lines[] = "Línea $line_number: " . implode(',', $fila) . " (Error en base de datos: " . $e->getMessage() . ")";
                    }
                } else {
                    $error_reason = "";
                    if (empty($nombre)) $error_reason = "Nombre vacío";
                    elseif (!$es_correo_valido) $error_reason = "Correo inválido o no es @farusac.edu.gt";
                    elseif (!in_array($plaza, $plazas_validas)) $error_reason = "Plaza inválida (Debe ser: Titular, Titular+Ampliacion o Interino)";
                    $error_lines[] = "Línea $line_number: " . implode(',', $fila) . " ($error_reason)";
                }
            } else {
                $error_lines[] = "Línea $line_number: " . implode(',', $fila) . " (Columnas insuficientes, requiere 4)";
            }
        }

        fclose($handle);

        return response()->json([
            'success' => true,
            'success_count' => count($success_records),
            'error_count' => count($error_lines),
            'success_records' => $success_records,
            'error_lines' => $error_lines
        ]);
    }

    public function CargaCursosCsv(Request $request){
        $request->validate([
            'archivo_csv' => 'required|mimes:csv,txt|max:5120',
        ]);

        $archivo = $request->file('archivo_csv');
        $handle = fopen($archivo->getRealPath(), 'r');

        $encabezados = fgetcsv($handle, 1000, ',');

        $success_records = [];
        $error_lines = [];
        $line_number = 1;

        while(($fila = fgetcsv($handle, 1000, ',')) !== FALSE){
            $line_number++;
            if(count($fila) >= 7){
                $carrera = trim($fila[0]);
                $area = trim($fila[1]);
                $nombre_curso = trim($fila[2]);
                $codigo_curso = intval(trim($fila[3]));
                $seccion = trim($fila[4]);
                $anio = intval(trim($fila[5]));
                $semestre = trim($fila[6]);

                $carreras_validas = ['Arquitectura', 'Diseño Gráfico'];
                $semestres_validos = ['Primer Semestre', 'Segundo Semestre', 'Vacaciones Junio', 'Vacaciones Diciembre'];

                if (in_array($carrera, $carreras_validas) && !empty($nombre_curso) && !empty($seccion) && $codigo_curso > 0 && $anio > 0 && in_array($semestre, $semestres_validos)) {
                    try {
                        $curso = Curso::updateOrCreate(
                            [
                                'carrera' => $carrera,
                                'codigo_curso' => $codigo_curso,
                                'seccion' => $seccion,
                                'anio' => $anio,
                                'semestre' => $semestre
                            ],
                            [
                                'area' => $area,
                                'nombre_curso' => $nombre_curso
                            ]
                        );
                        $success_records[] = [
                            'id' => $curso->id,
                            'carrera' => $curso->carrera,
                            'area' => $curso->area,
                            'curso' => $curso->nombre_curso,
                            'codigo' => $curso->codigo_curso,
                            'seccion' => $curso->seccion,
                            'jornada' => ($curso->seccion === 'B' || $curso->seccion === 'b') ? 'Vespertina' : 'Matutina',
                            'semestre' => $curso->semestre,
                            'anio' => $curso->anio
                        ];
                    } catch (\Exception $e) {
                        $error_lines[] = "Línea $line_number: " . implode(',', $fila) . " (Error en base de datos: " . $e->getMessage() . ")";
                    }
                } else {
                    $error_lines[] = "Línea $line_number: " . implode(',', $fila) . " (Datos inválidos)";
                }
            } else {
                $error_lines[] = "Línea $line_number: " . implode(',', $fila) . " (Columnas insuficientes)";
            }
        }

        fclose($handle);

        return response()->json([
            'success' => true,
            'success_count' => count($success_records),
            'error_count' => count($error_lines),
            'success_records' => $success_records,
            'error_lines' => $error_lines
        ]);
    }

    /**
     * Muestra el historial consolidado de informes para el Administrador (Docentes y Coordinadores).
     */
    public function informesView()
    {
        $user = Auth::user();

        // 1. Informes de Docentes
        $informesDocentes = Informe::with(['usuario', 'curso', 'semanas'])
            ->orderBy('id', 'desc')
            ->get();

        // 2. Informes de Coordinación
        $informesCoordinacion = InformeCoordinacion::with(['usuario', 'programas', 'asignaturas', 'avances', 'estudiantes'])
            ->orderBy('id', 'desc')
            ->get();

        // 3. Catálogos para filtros
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
        $periodosCoord = InformeCoordinacion::select('periodo')->distinct()->whereNotNull('periodo')->pluck('periodo');
        $periodos = $periodosDocentes->merge($periodosCoord)->unique()->filter()->values();

        $aniosDocentes = Curso::select('anio')->distinct()->whereNotNull('anio')->pluck('anio');
        $aniosCoord = InformeCoordinacion::select('anio')->distinct()->whereNotNull('anio')->pluck('anio');
        $anios = $aniosDocentes->merge($aniosCoord)->unique()->filter()->sortDesc()->values();

        $stats = [
            'total' => $informesDocentes->count() + $informesCoordinacion->count(),
            'docentes' => $informesDocentes->count(),
            'coordinacion' => $informesCoordinacion->count(),
            'docentes_unicos' => $informesDocentes->pluck('usuario_id')->unique()->count(),
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

    /**
     * Muestra la vista oficial imprimible/PDF del informe de docente.
     */
    public function verInformeDocente($id)
    {
        $user = Auth::user();
        $informe = Informe::with(['curso', 'semanas', 'usuario'])->findOrFail($id);
        return view('docente.ver_informe_pdf', compact('user', 'informe'));
    }

    /**
     * Muestra la vista oficial imprimible/PDF del informe de coordinación.
     */
    public function verInformeCoordinacion($id)
    {
        $user = Auth::user();
        $informe = InformeCoordinacion::with(['programas', 'asignaturas', 'avances', 'estudiantes', 'usuario'])->findOrFail($id);
        return view('coordinador.ver_informe_coordinacion', compact('user', 'informe'));
    }

    /**
     * Retorna los datos completos de un informe de docente en JSON para modal interactivo.
     */
    public function obtenerDetalleInformeDocenteJson($id)
    {
        $informe = Informe::with(['curso', 'semanas', 'usuario'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'tipo' => 'docente',
            'informe' => [
                'id' => $informe->id,
                'docente_nombre' => $informe->usuario->nombre ?? 'Docente no asignado',
                'docente_correo' => $informe->usuario->correo ?? '',
                'asignatura' => $informe->curso->nombre_curso ?? 'Curso',
                'carrera' => $informe->curso->carrera ?? '',
                'area' => $informe->curso->area ?? '',
                'codigo' => $informe->curso->codigo_curso ?? '',
                'seccion' => $informe->curso->seccion ?? '',
                'anio' => $informe->curso->anio ?? date('Y', strtotime($informe->created_at)),
                'periodo' => $informe->periodo,
                'mes' => $informe->mes,
                'estudiantes_asignados' => $informe->estudiantes_asignados,
                'listado_asistencia_url' => $informe->listado_asistencia_url,
                'enlace_evidencia_url' => $informe->enlace_evidencia_url,
                'enlace_meet_zoom_url' => $informe->enlace_meet_zoom_url,
                'enlace_classroom_drive_url' => $informe->enlace_classroom_drive_url,
                'estrategias_evaluacion' => $informe->estrategias_evaluacion,
                'estado' => $informe->estado,
                'created_at' => $informe->created_at ? $informe->created_at->format('d/m/Y H:i') : '',
                'semanas' => $informe->semanas->map(function ($s) {
                    return [
                        'numero_semana' => $s->numero_semana,
                        'tema_planificado' => $s->tema_planificado,
                        'actividades_desarrolladas' => $s->actividades_desarrolladas,
                        'cumplimiento' => $s->cumplimiento,
                        'observaciones' => $s->observaciones,
                    ];
                }),
            ]
        ]);
    }

    /**
     * Retorna los datos completos de un informe de coordinación en JSON para modal interactivo.
     */
    public function obtenerDetalleInformeCoordinacionJson($id)
    {
        $informe = InformeCoordinacion::with(['programas', 'asignaturas', 'avances', 'estudiantes', 'usuario'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'tipo' => 'coordinacion',
            'informe' => [
                'id' => $informe->id,
                'coordinador_nombre' => $informe->usuario->nombre ?? 'Coordinador no asignado',
                'coordinador_correo' => $informe->usuario->correo ?? '',
                'area' => $informe->area,
                'periodo' => $informe->periodo,
                'mes' => $informe->mes,
                'anio' => $informe->anio,
                'herramientas_virtuales' => $informe->herramientas_virtuales,
                'enlace_actividades_coordinacion' => $informe->enlace_actividades_coordinacion,
                'enlace_informe_auxiliares' => $informe->enlace_informe_auxiliares,
                'enlace_docentes_permisos' => $informe->enlace_docentes_permisos,
                'estado' => $informe->estado,
                'created_at' => $informe->created_at ? $informe->created_at->format('d/m/Y H:i') : '',
                'programas' => $informe->programas,
                'asignaturas' => $informe->asignaturas,
                'avances' => $informe->avances,
                'estudiantes' => $informe->estudiantes,
            ]
        ]);
    }

    /**
     * Elimina permanentemente un informe de docente.
     */
    public function eliminarInformeDocente($id)
    {
        DB::beginTransaction();
        try {
            $informe = Informe::findOrFail($id);
            $informe->semanas()->delete();
            $informe->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Informe de docente eliminado permanentemente.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el informe de docente: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Elimina permanentemente un informe de coordinación.
     */
    public function eliminarInformeCoordinacion($id)
    {
        DB::beginTransaction();
        try {
            $informe = InformeCoordinacion::findOrFail($id);
            $informe->programas()->delete();
            $informe->asignaturas()->delete();
            $informe->avances()->delete();
            $informe->estudiantes()->delete();
            $informe->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Informe de coordinación eliminado permanentemente.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el informe de coordinación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper para filtrar informes según los parámetros recibidos.
     */
    protected function obtenerInformesFiltrados(Request $request)
    {
        $mes = $request->input('mes');
        $anio = $request->input('anio');
        $tipo = $request->input('tipo', 'todos');
        $carrera = $request->input('carrera');
        $area = $request->input('area');

        $docentesQuery = Informe::with(['usuario', 'curso', 'semanas']);
        $coordinacionQuery = InformeCoordinacion::with(['usuario', 'programas', 'asignaturas', 'avances', 'estudiantes']);

        if ($mes && strtolower($mes) !== 'todos' && strtolower($mes) !== 'general') {
            $docentesQuery->where(function($q) use ($mes) {
                $q->whereRaw('LOWER(TRIM(mes)) = ?', [strtolower(trim($mes))])
                  ->orWhere('mes', 'LIKE', "%{$mes}%");
            });
            $coordinacionQuery->where(function($q) use ($mes) {
                $q->whereRaw('LOWER(TRIM(mes)) = ?', [strtolower(trim($mes))])
                  ->orWhere('mes', 'LIKE', "%{$mes}%");
            });
        }

        if ($anio && strtolower($anio) !== 'todos') {
            $docentesQuery->where(function ($query) use ($anio) {
                $query->whereHas('curso', function ($q) use ($anio) {
                    $q->where('anio', $anio);
                })
                ->orWhere('periodo', 'LIKE', "%{$anio}%")
                ->orWhereYear('created_at', $anio);
            });
            $coordinacionQuery->where(function ($query) use ($anio) {
                $query->where('anio', $anio)
                      ->orWhere('periodo', 'LIKE', "%{$anio}%")
                      ->orWhereYear('created_at', $anio);
            });
        }

        if ($carrera && strtolower($carrera) !== 'todas' && $carrera !== '') {
            $docentesQuery->whereHas('curso', function ($q) use ($carrera) {
                $q->where('carrera', $carrera);
            });
        }

        if ($area && strtolower($area) !== 'todas' && $area !== '') {
            $docentesQuery->whereHas('curso', function ($q) use ($area) {
                $q->where('area', $area);
            });
            $coordinacionQuery->where('area', $area);
        }

        $informesDocentes = ($tipo === 'coordinacion') ? collect() : $docentesQuery->orderBy('id', 'desc')->get();
        $informesCoordinacion = ($tipo === 'docente') ? collect() : $coordinacionQuery->orderBy('id', 'desc')->get();

        return [
            'docentes' => $informesDocentes,
            'coordinacion' => $informesCoordinacion,
            'mes' => $mes,
            'anio' => $anio,
            'total' => $informesDocentes->count() + $informesCoordinacion->count()
        ];
    }

    /**
     * Limpia nombres para archivos y carpetas en disco.
     */
    protected function limpiarNombreArchivo($string)
    {
        if (empty($string)) {
            return '';
        }
        $string = Str::ascii($string);
        $string = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $string);
        $string = preg_replace('/_+/', '_', $string);
        return trim($string, '_');
    }

    /**
     * Convierte imágenes a data-URI base64 para que el HTML y Dompdf carguen las imágenes sin problemas de rutas o encoding.
     */
    protected function embeberImagenesBase64($html)
    {
        $logos = [
            'logos-usac-farusac.png' => public_path('images/InformePDF/logos-usac-farusac.png'),
            'LOGOS ACREDITADORAS 2026 HCERES (1).png' => public_path('images/InformePDF/LOGOS ACREDITADORAS 2026 HCERES (1).png'),
            'LOGOS ACREDITADORAS 2026 CCA (1).png' => public_path('images/InformePDF/LOGOS ACREDITADORAS 2026 CCA (1).png'),
            'LOGOS ACREDITADORAS 2026 CEAI (1).png' => public_path('images/InformePDF/LOGOS ACREDITADORAS 2026 CEAI (1).png'),
            'FarusacLogo.png' => public_path('images/FarusacLogo.png'),
        ];

        foreach ($logos as $filename => $fullPath) {
            if (file_exists($fullPath)) {
                $type = pathinfo($fullPath, PATHINFO_EXTENSION);
                $data = file_get_contents($fullPath);
                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

                $variants = [
                    asset('images/InformePDF/' . $filename),
                    url('images/InformePDF/' . $filename),
                    'images/InformePDF/' . $filename,
                    asset('images/InformePDF/' . rawurlencode($filename)),
                    url('images/InformePDF/' . rawurlencode($filename)),
                    'images/InformePDF/' . rawurlencode($filename),
                    asset('images/' . $filename),
                    url('images/' . $filename),
                    'images/' . $filename,
                ];

                foreach ($variants as $v) {
                    $html = str_replace($v, $base64, $html);
                }
            }
        }

        return $html;
    }

    /**
     * Busca la ruta del ejecutable de Microsoft Edge o Google Chrome en el sistema operativo.
     */
    protected function getChromiumBinary()
    {
        $candidates = [
            'C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe',
            'C:\\Program Files\\Microsoft\\Edge\\Application\\msedge.exe',
            'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
            'C:\\Program Files (x86)\\Google\\Chrome\\Application\\chrome.exe',
            getenv('LOCALAPPDATA') . '\\Microsoft\\Edge\\Application\\msedge.exe',
            getenv('LOCALAPPDATA') . '\\Google\\Chrome\\Application\\chrome.exe',
            getenv('ProgramFiles') . '\\Microsoft\\Edge\\Application\\msedge.exe',
            getenv('ProgramFiles(x86)') . '\\Microsoft\\Edge\\Application\\msedge.exe',
            getenv('ProgramFiles') . '\\Google\\Chrome\\Application\\chrome.exe',
            getenv('ProgramFiles(x86)') . '\\Google\\Chrome\\Application\\chrome.exe',
        ];

        foreach ($candidates as $path) {
            if (!empty($path) && file_exists($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Renderiza una vista Blade en PDF vectorizado de alta fidelidad usando el motor nativo de Edge/Chrome.
     */
    protected function renderHtmlToPdfChromium($html)
    {
        $chromiumPath = $this->getChromiumBinary();
        if (!$chromiumPath) {
            throw new \Exception('No se encontró el ejecutable de Microsoft Edge o Google Chrome para renderizar el PDF.');
        }

        // Embeber imágenes institucionales en Base64 para carga instantánea offline
        $html = $this->embeberImagenesBase64($html);

        // Inyectar regla CSS para pantalla completa de hoja (margin 0) y pie de página adherido al borde inferior
        $printOptimizationsCss = '<style>
            @page { size: letter portrait; margin: 0 !important; }
            html, body { margin: 0 !important; padding: 0 !important; width: 100% !important; max-width: 100% !important; background-color: #ffffff !important; overflow: visible !important; }
            .no-print-bar { display: none !important; }
            .document-page { width: 100% !important; max-width: 100% !important; margin: 0 !important; padding: 0 !important; border: none !important; box-shadow: none !important; border-radius: 0 !important; }
            .report-table-layout { width: 100% !important; max-width: 100% !important; table-layout: fixed !important; border-collapse: collapse !important; }
            .report-table-layout>thead { display: table-header-group !important; }
            .report-table-layout>tfoot { display: table-footer-group !important; }
            .report-table-layout>tbody { display: table-row-group !important; }
            .report-header-wrapper { padding: 10mm 15mm 0 15mm !important; width: 100% !important; box-sizing: border-box !important; }
            .report-body-wrapper { padding: 4mm 15mm 8mm 15mm !important; width: 100% !important; box-sizing: border-box !important; }
            .report-footer-spacer { display: block !important; height: 50px !important; width: 100% !important; visibility: hidden !important; }
            .report-footer-wrapper { position: fixed !important; bottom: 0 !important; left: 0 !important; right: 0 !important; width: 100% !important; padding: 0 !important; margin: 0 !important; z-index: 9999 !important; background-color: #ffffff !important; }
            .footer-gold-bar { height: 2.5px !important; background-color: #f3b228 !important; width: 100% !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            .footer-navy-bar { background-color: #0b2341 !important; color: #ffffff !important; padding: 5px 20px !important; width: 100% !important; box-sizing: border-box !important; display: flex !important; align-items: center !important; justify-content: space-between !important; flex-wrap: nowrap !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        </style>';
        $html = str_replace('</head>', $printOptimizationsCss . '</head>', $html);

        $tempHtml = tempnam(sys_get_temp_dir(), 'farusac_html_') . '.html';
        $tempPdf = tempnam(sys_get_temp_dir(), 'farusac_pdf_') . '.pdf';

        file_put_contents($tempHtml, $html);

        $cmd = sprintf(
            '"%s" --headless --disable-gpu --allow-file-access-from-files --no-pdf-header-footer --run-all-compositor-stages-before-draw --virtual-time-budget=2500 --print-to-pdf="%s" "%s"',
            $chromiumPath,
            $tempPdf,
            $tempHtml
        );

        exec($cmd);

        $pdfBinary = null;
        if (file_exists($tempPdf) && filesize($tempPdf) > 0) {
            $pdfBinary = file_get_contents($tempPdf);
        }

        @unlink($tempHtml);
        @unlink($tempPdf);

        if (!$pdfBinary) {
            throw new \Exception('No se pudo generar el archivo PDF con el motor de Chromium.');
        }

        return $pdfBinary;
    }

    /**
     * Genera el contenido binario PDF de un informe de docente reutilizando la vista oficial institucional.
     */
    protected function generarPdfDocente($informe, $user)
    {
        $html = view('docente.ver_informe_pdf', [
            'user' => $user,
            'informe' => $informe,
        ])->render();

        return $this->renderHtmlToPdfChromium($html);
    }

    /**
     * Genera el contenido binario PDF de un informe de coordinación reutilizando la vista oficial institucional.
     */
    protected function generarPdfCoordinacion($informe, $user)
    {
        $html = view('coordinador.ver_informe_coordinacion', [
            'user' => $user,
            'informe' => $informe,
        ])->render();

        return $this->renderHtmlToPdfChromium($html);
    }

    /**
     * Descarga masiva de informes en formato PDF dentro de un archivo ZIP estructurado:
     * Año -> Mes -> Docente / Coordinador -> Informes_Docente_Nombre_Mes.pdf / Informes_Coordinador_Nombre_Mes.pdf
     */
    public function descargarInformesZip(Request $request)
    {
        $user = Auth::user();
        $filtrados = $this->obtenerInformesFiltrados($request);

        $informesDocentes = $filtrados['docentes'];
        $informesCoordinacion = $filtrados['coordinacion'];
        $mesFiltro = $filtrados['mes'] ? ucfirst(strtolower($filtrados['mes'])) : 'General';
        $anioFiltro = $filtrados['anio'] ?? date('Y');

        if ($informesDocentes->isEmpty() && $informesCoordinacion->isEmpty()) {
            return redirect()->back()->with('error', 'No se encontraron informes para los filtros seleccionados.');
        }

        if (!class_exists('ZipArchive')) {
            return response()->json(['error' => 'La extensión ZipArchive de PHP no está habilitada en el servidor.'], 500);
        }

        $zip = new \ZipArchive();
        $zipFileName = tempnam(sys_get_temp_dir(), 'farusac_pdf_zip_');

        if ($zip->open($zipFileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== TRUE) {
            return response()->json(['error' => 'No se pudo crear el archivo ZIP temporal.'], 500);
        }

        $usedDocentePaths = [];

        // 1. Agregar Informes de Docentes en PDF
        // Estructura: Año -> Mes -> Docente -> Informes_Docente_Nombre_Mes.pdf
        foreach ($informesDocentes as $inf) {
            $docenteNombre = $this->limpiarNombreArchivo($inf->usuario->nombre ?? 'Docente');
            $cursoNombre = $this->limpiarNombreArchivo($inf->curso->nombre_curso ?? 'Curso');
            $seccion = $this->limpiarNombreArchivo($inf->curso->seccion ?? 'A');
            $mesLimpio = $this->limpiarNombreArchivo(ucfirst(strtolower($inf->mes ?? $mesFiltro)));
            $anioLimpio = $this->limpiarNombreArchivo($inf->curso->anio ?? $anioFiltro);

            $dirPath = "{$anioLimpio}/{$mesLimpio}/Docente";
            $fileName = "Informes_Docente_{$docenteNombre}_{$mesLimpio}.pdf";
            $fullPath = "{$dirPath}/{$fileName}";

            // Si el mismo docente tiene varios cursos en el mismo mes, añadimos curso/sección para no sobreescribir
            if (isset($usedDocentePaths[$fullPath])) {
                $fullPath = "{$dirPath}/Informes_Docente_{$docenteNombre}_{$cursoNombre}_Sec{$seccion}_{$mesLimpio}.pdf";
            }
            $usedDocentePaths[$fullPath] = true;

            $pdfBinary = $this->generarPdfDocente($inf, $user);
            $zip->addFromString($fullPath, $pdfBinary);
        }

        $usedCoordPaths = [];

        // 2. Agregar Informes de Coordinación en PDF
        // Estructura: Año -> Mes -> Coordinador -> Informes_Coordinador_Nombre_Mes.pdf
        foreach ($informesCoordinacion as $infC) {
            $coordNombre = $this->limpiarNombreArchivo($infC->usuario->nombre ?? 'Coordinador');
            $area = $this->limpiarNombreArchivo($infC->area ?? 'Area');
            $mesLimpio = $this->limpiarNombreArchivo(ucfirst(strtolower($infC->mes ?? $mesFiltro)));
            $anioLimpio = $this->limpiarNombreArchivo($infC->anio ?? $anioFiltro);

            $dirPath = "{$anioLimpio}/{$mesLimpio}/Coordinador";
            $fileName = "Informes_Coordinador_{$coordNombre}_{$mesLimpio}.pdf";
            $fullPath = "{$dirPath}/{$fileName}";

            if (isset($usedCoordPaths[$fullPath])) {
                $fullPath = "{$dirPath}/Informes_Coordinador_{$coordNombre}_{$area}_{$mesLimpio}.pdf";
            }
            $usedCoordPaths[$fullPath] = true;

            $pdfBinary = $this->generarPdfCoordinacion($infC, $user);
            $zip->addFromString($fullPath, $pdfBinary);
        }

        $zip->close();

        $downloadName = "Informes_FARUSAC_{$mesFiltro}_{$anioFiltro}.zip";
        return response()->download($zipFileName, $downloadName)->deleteFileAfterSend(true);
    }

    /**
     * Descarga el resumen consolidado en formato CSV.
     */
    public function descargarInformesCsv(Request $request)
    {
        $filtrados = $this->obtenerInformesFiltrados($request);
        $informesDocentes = $filtrados['docentes'];
        $informesCoordinacion = $filtrados['coordinacion'];
        $mes = $filtrados['mes'] ?? 'General';
        $anio = $filtrados['anio'] ?? date('Y');

        $csvContent = "\xEF\xBB\xBF";
        $csvContent .= "Tipo;Autor;Correo;Curso/Area;Codigo;Seccion;Carrera;Periodo;Mes;Anio;Estudiantes;Fecha Entrega;Listado Asistencia;Evidencias Drive;Meet/Virtual;Classroom\n";

        foreach ($informesDocentes as $inf) {
            $csvContent .= sprintf(
                "\"Docente\";\"%s\";\"%s\";\"%s\";\"%s\";\"%s\";\"%s\";\"%s\";\"%s\";\"%s\";\"%s\";\"%s\";\"%s\";\"%s\";\"%s\";\"%s\"\n",
                addslashes($inf->usuario->nombre ?? ''),
                addslashes($inf->usuario->correo ?? ''),
                addslashes($inf->curso->nombre_curso ?? ''),
                addslashes($inf->curso->codigo_curso ?? ''),
                addslashes($inf->curso->seccion ?? ''),
                addslashes($inf->curso->carrera ?? ''),
                addslashes($inf->periodo ?? ''),
                addslashes($inf->mes ?? ''),
                addslashes($inf->curso->anio ?? ''),
                $inf->estudiantes_asignados ?? 0,
                $inf->created_at ? $inf->created_at->format('d/m/Y H:i') : '',
                addslashes($inf->listado_asistencia_url ?? ''),
                addslashes($inf->enlace_evidencia_url ?? ''),
                addslashes($inf->enlace_meet_zoom_url ?? ''),
                addslashes($inf->enlace_classroom_drive_url ?? '')
            );
        }

        foreach ($informesCoordinacion as $infC) {
            $csvContent .= sprintf(
                "\"Coordinador\";\"%s\";\"%s\";\"%s\";\"—\";\"—\";\"—\";\"%s\";\"%s\";\"%s\";\"—\";\"%s\";\"—\";\"%s\";\"—\";\"—\"\n",
                addslashes($infC->usuario->nombre ?? ''),
                addslashes($infC->usuario->correo ?? ''),
                addslashes($infC->area ?? ''),
                addslashes($infC->periodo ?? ''),
                addslashes($infC->mes ?? ''),
                addslashes($infC->anio ?? ''),
                $infC->created_at ? $infC->created_at->format('d/m/Y H:i') : '',
                addslashes($infC->enlace_actividades_coordinacion ?? '')
            );
        }

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"Resumen_Entregas_FARUSAC_{$mes}_{$anio}.csv\"",
        ];

        return response($csvContent, 200, $headers);
    }

    /**
     * Muestra la vista continua de todo el mes para imprimir o exportar en un solo PDF.
     */
    public function verConsolidadoPdf(Request $request)
    {
        $user = Auth::user();
        $filtrados = $this->obtenerInformesFiltrados($request);

        $informesDocentes = $filtrados['docentes'];
        $informesCoordinacion = $filtrados['coordinacion'];
        $mes = $filtrados['mes'];
        $anio = $filtrados['anio'];
        $totalCount = $filtrados['total'];

        return view('admin.consolidado_informes_pdf', compact(
            'user',
            'informesDocentes',
            'informesCoordinacion',
            'mes',
            'anio',
            'totalCount'
        ));
    }

    /**
     * Descarga ZIP con archivos PDF para elementos seleccionados específicamente por checkbox.
     * Estructura: Año -> Mes -> Docente / Coordinador -> Informe_Docente_Nombre_Mes.pdf / Informe_Coordinador_Nombre_Mes.pdf
     */
    public function descargarSeleccionadosZip(Request $request)
    {
        $user = Auth::user();
        $docenteIds = $request->input('docente_ids', []);
        $coordIds = $request->input('coordinacion_ids', []);

        if (is_string($docenteIds)) {
            $docenteIds = explode(',', $docenteIds);
        }
        if (is_string($coordIds)) {
            $coordIds = explode(',', $coordIds);
        }

        $informesDocentes = !empty($docenteIds) ? Informe::with(['usuario', 'curso', 'semanas'])->whereIn('id', array_filter($docenteIds))->get() : collect();
        $informesCoordinacion = !empty($coordIds) ? InformeCoordinacion::with(['usuario', 'programas', 'asignaturas', 'avances', 'estudiantes'])->whereIn('id', array_filter($coordIds))->get() : collect();

        if ($informesDocentes->isEmpty() && $informesCoordinacion->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No se seleccionó ningún informe.'], 422);
        }

        if (!class_exists('ZipArchive')) {
            return response()->json(['success' => false, 'message' => 'La extensión ZipArchive no está habilitada.'], 500);
        }

        $zip = new \ZipArchive();
        $zipFileName = tempnam(sys_get_temp_dir(), 'farusac_sel_pdf_zip_');

        if ($zip->open($zipFileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== TRUE) {
            return response()->json(['success' => false, 'message' => 'Error al crear archivo ZIP.'], 500);
        }

        $usedDocentePaths = [];
        foreach ($informesDocentes as $inf) {
            $docenteNombre = $this->limpiarNombreArchivo($inf->usuario->nombre ?? 'Docente');
            $cursoNombre = $this->limpiarNombreArchivo($inf->curso->nombre_curso ?? 'Curso');
            $seccion = $this->limpiarNombreArchivo($inf->curso->seccion ?? 'A');
            $mes = $this->limpiarNombreArchivo(ucfirst(strtolower($inf->mes ?? 'Mes')));
            $anio = $this->limpiarNombreArchivo($inf->curso->anio ?? date('Y'));

            $dirPath = "{$anio}/{$mes}/Docente";
            $fileName = "Informes_Docente_{$docenteNombre}_{$mes}.pdf";
            $fullPath = "{$dirPath}/{$fileName}";

            if (isset($usedDocentePaths[$fullPath])) {
                $fullPath = "{$dirPath}/Informes_Docente_{$docenteNombre}_{$cursoNombre}_Sec{$seccion}_{$mes}.pdf";
            }
            $usedDocentePaths[$fullPath] = true;

            $pdfBinary = $this->generarPdfDocente($inf, $user);
            $zip->addFromString($fullPath, $pdfBinary);
        }

        $usedCoordPaths = [];
        foreach ($informesCoordinacion as $infC) {
            $coordNombre = $this->limpiarNombreArchivo($infC->usuario->nombre ?? 'Coordinador');
            $area = $this->limpiarNombreArchivo($infC->area ?? 'Area');
            $mes = $this->limpiarNombreArchivo(ucfirst(strtolower($infC->mes ?? 'Mes')));
            $anio = $this->limpiarNombreArchivo($infC->anio ?? date('Y'));

            $dirPath = "{$anio}/{$mes}/Coordinador";
            $fileName = "Informes_Coordinador_{$coordNombre}_{$mes}.pdf";
            $fullPath = "{$dirPath}/{$fileName}";

            if (isset($usedCoordPaths[$fullPath])) {
                $fullPath = "{$dirPath}/Informes_Coordinador_{$coordNombre}_{$area}_{$mes}.pdf";
            }
            $usedCoordPaths[$fullPath] = true;

            $pdfBinary = $this->generarPdfCoordinacion($infC, $user);
            $zip->addFromString($fullPath, $pdfBinary);
        }

        $zip->close();

        return response()->download($zipFileName, "Informes_Seleccionados_FARUSAC.zip")->deleteFileAfterSend(true);
    }

    /**
     * Muestra el consolidado PDF para elementos seleccionados por checkbox.
     */
    public function verConsolidadoSeleccionadosPdf(Request $request)
    {
        $user = Auth::user();
        $docenteIds = $request->input('docente_ids', []);
        $coordIds = $request->input('coordinacion_ids', []);

        if (is_string($docenteIds)) {
            $docenteIds = explode(',', $docenteIds);
        }
        if (is_string($coordIds)) {
            $coordIds = explode(',', $coordIds);
        }

        $informesDocentes = !empty($docenteIds) ? Informe::with(['usuario', 'curso', 'semanas'])->whereIn('id', array_filter($docenteIds))->get() : collect();
        $informesCoordinacion = !empty($coordIds) ? InformeCoordinacion::with(['usuario', 'programas', 'asignaturas', 'avances', 'estudiantes'])->whereIn('id', array_filter($coordIds))->get() : collect();

        $mes = 'Seleccionados';
        $anio = date('Y');
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

    /**
     * Retorna la lista de URLs y metadatos de archivos para generación de ZIP en cliente.
     */
    public function obtenerListaDescargaZipJson(Request $request)
    {
        $filtrados = $this->obtenerInformesFiltrados($request);

        $informesDocentes = $filtrados['docentes'];
        $informesCoordinacion = $filtrados['coordinacion'];
        $mesFiltro = $filtrados['mes'] ? ucfirst(strtolower($filtrados['mes'])) : 'General';
        $anioFiltro = $filtrados['anio'] ?? date('Y');

        if ($informesDocentes->isEmpty() && $informesCoordinacion->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontraron informes para los filtros seleccionados.',
                'items' => []
            ]);
        }

        $items = [];
        $usedDocentePaths = [];

        foreach ($informesDocentes as $inf) {
            $docenteNombre = $this->limpiarNombreArchivo($inf->usuario->nombre ?? 'Docente');
            $cursoNombre = $this->limpiarNombreArchivo($inf->curso->nombre_curso ?? 'Curso');
            $seccion = $this->limpiarNombreArchivo($inf->curso->seccion ?? 'A');
            $mesLimpio = $this->limpiarNombreArchivo(ucfirst(strtolower($inf->mes ?? $mesFiltro)));
            $anioLimpio = $this->limpiarNombreArchivo($inf->curso->anio ?? $anioFiltro);

            $dirPath = "{$anioLimpio}/{$mesLimpio}/Docente";
            $fileName = "Informes_Docente_{$docenteNombre}_{$mesLimpio}.pdf";
            $fullPath = "{$dirPath}/{$fileName}";

            if (isset($usedDocentePaths[$fullPath])) {
                $fileName = "Informes_Docente_{$docenteNombre}_{$cursoNombre}_Sec{$seccion}_{$mesLimpio}.pdf";
                $fullPath = "{$dirPath}/{$fileName}";
            }
            $usedDocentePaths[$fullPath] = true;

            $items[] = [
                'id' => $inf->id,
                'tipo' => 'docente',
                'autor' => $inf->usuario->nombre ?? 'Docente',
                'curso_area' => $inf->curso->nombre_curso ?? 'Curso',
                'seccion' => $inf->curso->seccion ?? '',
                'mes' => $mesLimpio,
                'anio' => $anioLimpio,
                'url' => route('admin.informes.docente.ver', ['id' => $inf->id, 'embed' => 1]),
                'fileName' => $fileName,
                'fullPath' => $fullPath,
            ];
        }

        $usedCoordPaths = [];
        foreach ($informesCoordinacion as $infC) {
            $coordNombre = $this->limpiarNombreArchivo($infC->usuario->nombre ?? 'Coordinador');
            $area = $this->limpiarNombreArchivo($infC->area ?? 'Area');
            $mesLimpio = $this->limpiarNombreArchivo(ucfirst(strtolower($infC->mes ?? $mesFiltro)));
            $anioLimpio = $this->limpiarNombreArchivo($infC->anio ?? $anioFiltro);

            $dirPath = "{$anioLimpio}/{$mesLimpio}/Coordinador";
            $fileName = "Informes_Coordinador_{$coordNombre}_{$mesLimpio}.pdf";
            $fullPath = "{$dirPath}/{$fileName}";

            if (isset($usedCoordPaths[$fullPath])) {
                $fileName = "Informes_Coordinador_{$coordNombre}_{$area}_{$mesLimpio}.pdf";
                $fullPath = "{$dirPath}/{$fileName}";
            }
            $usedCoordPaths[$fullPath] = true;

            $items[] = [
                'id' => $infC->id,
                'tipo' => 'coordinacion',
                'autor' => $infC->usuario->nombre ?? 'Coordinador',
                'curso_area' => $infC->area ?? 'Área',
                'seccion' => '',
                'mes' => $mesLimpio,
                'anio' => $anioLimpio,
                'url' => route('admin.informes.coordinacion.ver', ['id' => $infC->id, 'embed' => 1]),
                'fileName' => $fileName,
                'fullPath' => $fullPath,
            ];
        }

        return response()->json([
            'success' => true,
            'zipName' => "Informes_FARUSAC_{$mesFiltro}_{$anioFiltro}.zip",
            'total' => count($items),
            'items' => $items,
        ]);
    }

    /**
     * Retorna la lista de URLs y metadatos para informes seleccionados por checkbox para el cliente JSZip.
     */
    public function obtenerListaSeleccionadosZipJson(Request $request)
    {
        $docenteIds = $request->input('docente_ids', []);
        $coordIds = $request->input('coordinacion_ids', []);

        if (is_string($docenteIds)) {
            $docenteIds = explode(',', $docenteIds);
        }
        if (is_string($coordIds)) {
            $coordIds = explode(',', $coordIds);
        }

        $informesDocentes = !empty($docenteIds) ? Informe::with(['usuario', 'curso', 'semanas'])->whereIn('id', array_filter($docenteIds))->get() : collect();
        $informesCoordinacion = !empty($coordIds) ? InformeCoordinacion::with(['usuario', 'programas', 'asignaturas', 'avances', 'estudiantes'])->whereIn('id', array_filter($coordIds))->get() : collect();

        if ($informesDocentes->isEmpty() && $informesCoordinacion->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No se seleccionó ningún informe.',
                'items' => []
            ], 422);
        }

        $items = [];
        $usedDocentePaths = [];
        foreach ($informesDocentes as $inf) {
            $docenteNombre = $this->limpiarNombreArchivo($inf->usuario->nombre ?? 'Docente');
            $cursoNombre = $this->limpiarNombreArchivo($inf->curso->nombre_curso ?? 'Curso');
            $seccion = $this->limpiarNombreArchivo($inf->curso->seccion ?? 'A');
            $mes = $this->limpiarNombreArchivo(ucfirst(strtolower($inf->mes ?? 'Mes')));
            $anio = $this->limpiarNombreArchivo($inf->curso->anio ?? date('Y'));

            $dirPath = "{$anio}/{$mes}/Docente";
            $fileName = "Informes_Docente_{$docenteNombre}_{$mes}.pdf";
            $fullPath = "{$dirPath}/{$fileName}";

            if (isset($usedDocentePaths[$fullPath])) {
                $fileName = "Informes_Docente_{$docenteNombre}_{$cursoNombre}_Sec{$seccion}_{$mes}.pdf";
                $fullPath = "{$dirPath}/{$fileName}";
            }
            $usedDocentePaths[$fullPath] = true;

            $items[] = [
                'id' => $inf->id,
                'tipo' => 'docente',
                'autor' => $inf->usuario->nombre ?? 'Docente',
                'curso_area' => $inf->curso->nombre_curso ?? 'Curso',
                'seccion' => $inf->curso->seccion ?? '',
                'mes' => $mes,
                'anio' => $anio,
                'url' => route('admin.informes.docente.ver', ['id' => $inf->id, 'embed' => 1]),
                'fileName' => $fileName,
                'fullPath' => $fullPath,
            ];
        }

        $usedCoordPaths = [];
        foreach ($informesCoordinacion as $infC) {
            $coordNombre = $this->limpiarNombreArchivo($infC->usuario->nombre ?? 'Coordinador');
            $area = $this->limpiarNombreArchivo($infC->area ?? 'Area');
            $mes = $this->limpiarNombreArchivo(ucfirst(strtolower($infC->mes ?? 'Mes')));
            $anio = $this->limpiarNombreArchivo($infC->anio ?? date('Y'));

            $dirPath = "{$anio}/{$mes}/Coordinador";
            $fileName = "Informes_Coordinador_{$coordNombre}_{$mes}.pdf";
            $fullPath = "{$dirPath}/{$fileName}";

            if (isset($usedCoordPaths[$fullPath])) {
                $fileName = "Informes_Coordinador_{$coordNombre}_{$area}_{$mes}.pdf";
                $fullPath = "{$dirPath}/{$fileName}";
            }
            $usedCoordPaths[$fullPath] = true;

            $items[] = [
                'id' => $infC->id,
                'tipo' => 'coordinacion',
                'autor' => $infC->usuario->nombre ?? 'Coordinador',
                'curso_area' => $infC->area ?? 'Área',
                'seccion' => '',
                'mes' => $mes,
                'anio' => $anio,
                'url' => route('admin.informes.coordinacion.ver', ['id' => $infC->id, 'embed' => 1]),
                'fileName' => $fileName,
                'fullPath' => $fullPath,
            ];
        }

        return response()->json([
            'success' => true,
            'zipName' => "Informes_Seleccionados_FARUSAC.zip",
            'total' => count($items),
            'items' => $items,
        ]);
    }
}
