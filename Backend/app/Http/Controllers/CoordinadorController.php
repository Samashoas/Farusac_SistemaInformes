<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Curso;
use App\Models\User;
use App\Models\Informe;
use App\Models\InformeSemana;
use App\Models\InformeCoordinacion;
use App\Models\InformeCoordinacionPrograma;
use App\Models\InformeCoordinacionAsignatura;
use App\Models\InformeCoordinacionAvance;
use App\Models\InformeCoordinacionEstudiante;
use Carbon\Carbon;

class CoordinadorController extends DocenteController
{
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

        // Informes mensuales de coordinación generados por este usuario
        try {
            $informesCoordinacion = InformeCoordinacion::where('usuario_id', $user->id)
                ->orderBy('anio', 'desc')
                ->orderBy('id', 'desc')
                ->get();
        } catch (\Exception $e) {
            $informesCoordinacion = collect();
        }

        return view('coordinador.dashboard', compact('user', 'cursosAsignados', 'catalogoCursos', 'carreras', 'areasDisponibles', 'informesCoordinacion'));
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
     * Retorna los datos completos de un informe de docente en formato JSON para el modal interactivo.
     */
    public function obtenerDetalleInformeDocenteJson($id)
    {
        /** @var User $user */
        $user = Auth::user();

        $informe = Informe::with(['curso', 'semanas', 'usuario'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'informe' => [
                'id' => $informe->id,
                'docente_nombre' => $informe->usuario->nombre ?? 'Docente no asignado',
                'docente_correo' => $informe->usuario->correo ?? '',
                'asignatura' => $informe->curso->nombre_curso ?? 'Curso',
                'seccion' => $informe->curso->seccion ?? '',
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
                        'actividad_realizada' => $s->actividad_realizada,
                        'estudiantes_participaron' => $s->estudiantes_participaron,
                        'metodologias' => $s->metodologias,
                        'medios_comunicacion' => $s->medios_comunicacion,
                    ];
                }),
            ]
        ]);
    }

    /**
     * Obtiene todos los cursos asociados a un área de coordinación de manera flexible.
     */
    private function getCursosPorArea($area)
    {
        if (empty($area)) {
            return collect();
        }

        $cleanArea = trim(str_ireplace(['Área de ', 'Area de ', 'Área ', 'Area '], '', $area));

        // Separar palabras significativas (de más de 2 caracteres)
        $palabras = array_filter(explode(' ', $cleanArea), function($p) {
            $p = trim($p);
            return mb_strlen($p) > 2 && !in_array(mb_strtolower($p), ['para', 'sobre', 'con', 'del', 'las', 'los', 'por', 'que', 'una', 'uno', 'y', 'de', 'la', 'el']);
        });

        $query = Curso::where(function ($q) use ($area, $cleanArea, $palabras) {
            $q->where('area', $area)
              ->orWhere('area', trim($area))
              ->orWhere('area', 'LIKE', '%' . $cleanArea . '%')
              ->orWhere('area', 'LIKE', '%' . $area . '%');

            foreach ($palabras as $palabra) {
                $q->orWhere('area', 'LIKE', '%' . $palabra . '%');
            }
        });

        $cursos = $query->orderBy('nombre_curso', 'asc')
            ->orderBy('seccion', 'asc')
            ->get();

        // Si por alguna razón no encuentra cursos con esa área, buscar también en los cursos asignados al usuario
        if ($cursos->isEmpty()) {
            /** @var User $user */
            $user = Auth::user();
            if ($user) {
                $cursos = $user->cursos()->orderBy('nombre_curso', 'asc')->orderBy('seccion', 'asc')->get();
            }
        }

        return $cursos;
    }

    /**
     * Obtiene dinámicamente los informes entregados por los docentes del área para un periodo y mes.
     */
    public function obtenerDatosDocentesArea(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $area = $user->area ?? session('coordinador_area');

        $periodo = $request->query('periodo');
        $mes = $request->query('mes');

        if (!$area) {
            return response()->json(['success' => false, 'message' => 'Sin área asignada'], 400);
        }

        $cursosArea = $this->getCursosPorArea($area);
        $cursoIds = $cursosArea->pluck('id')->toArray();

        $asignaciones = DB::table('docente_cursos')
            ->join('usuarios', 'usuarios.id', '=', 'docente_cursos.usuario_id')
            ->whereIn('docente_cursos.curso_id', $cursoIds)
            ->select('docente_cursos.curso_id', 'usuarios.nombre as docente_nombre', 'usuarios.correo as docente_correo')
            ->get()
            ->keyBy('curso_id');

        $docenteInformesQuery = Informe::with(['usuario', 'semanas'])
            ->whereIn('curso_id', $cursoIds)
            ->whereIn('estado', ['enviado', 'aprobado', 'bloqueado']);

        // El mes es el filtro principal y estricto
        if (!empty($mes)) {
            $docenteInformesQuery->whereRaw('LOWER(TRIM(mes)) = ?', [mb_strtolower(trim($mes))]);
        }

        // Si se proporciona periodo, se filtra también de forma flexible
        if (!empty($periodo)) {
            $cleanPeriodo = trim(preg_replace('/\d{4}/', '', $periodo));
            $docenteInformesQuery->where(function ($q) use ($periodo, $cleanPeriodo) {
                $q->where('periodo', trim($periodo))
                  ->orWhere('periodo', 'LIKE', '%' . $cleanPeriodo . '%');
            });
        }

        $docenteInformes = $docenteInformesQuery->get()->keyBy('curso_id');

        $asignaturasUnicas = $cursosArea->pluck('nombre_curso')->unique()->values();

        $programas = $asignaturasUnicas->map(function ($nombreCurso) use ($cursosArea, $docenteInformes) {
            $cursoIds = $cursosArea->where('nombre_curso', $nombreCurso)->pluck('id');
            $enlaceProg = null;
            foreach ($cursoIds as $cId) {
                $inf = $docenteInformes->get($cId);
                if ($inf && !empty($inf->enlace_classroom_drive_url)) {
                    $enlaceProg = $inf->enlace_classroom_drive_url;
                    break;
                }
            }
            return [
                'asignatura' => $nombreCurso,
                'enlace_programa' => $enlaceProg,
            ];
        });

        $datos = $cursosArea->map(function ($curso) use ($asignaciones, $docenteInformes) {
            $docente = $asignaciones->get($curso->id);
            $inf = $docenteInformes->get($curso->id);

            $presento = $inf ? true : false;
            $sala = $inf && !empty($inf->enlace_meet_zoom_url);
            $virtual = $inf && !empty($inf->enlace_classroom_drive_url);
            $eval = $inf && !empty($inf->estrategias_evaluacion);
            $evid = $inf && (!empty($inf->enlace_evidencia_url) || !empty($inf->listado_asistencia_url));

            return [
                'curso_id' => $curso->id,
                'docente_nombre' => $docente ? $docente->docente_nombre : 'Sin docente asignado',
                'asignatura' => $curso->nombre_curso,
                'seccion' => $curso->seccion,
                'presento_informe' => $presento,
                'tiene_sala_reuniones' => $sala,
                'funciona_enlace_virtual' => $virtual,
                'funciona_enlace_evaluacion' => $eval,
                'evidencias_generales' => $evid,
                'observaciones' => '',
                'porcentaje_avance' => 100,
                'docente_informe_id' => $inf ? $inf->id : null,
                'informe_detalles' => $inf ? [
                    'id' => $inf->id,
                    'estudiantes_asignados' => $inf->estudiantes_asignados,
                    'listado_asistencia_url' => $inf->listado_asistencia_url,
                    'enlace_evidencia_url' => $inf->enlace_evidencia_url,
                    'enlace_meet_zoom_url' => $inf->enlace_meet_zoom_url,
                    'enlace_classroom_drive_url' => $inf->enlace_classroom_drive_url,
                    'estrategias_evaluacion' => $inf->estrategias_evaluacion,
                    'semanas_registradas' => $inf->semanas->count(),
                ] : null,
            ];
        });

        return response()->json([
            'success' => true,
            'datos' => $datos,
            'programas' => $programas,
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

        // Obtener cursos del área asignada
        $cursosArea = $this->getCursosPorArea($area);

        // Obtener docentes asignados a estos cursos
        $cursoIds = $cursosArea->pluck('id')->toArray();
        $asignaciones = DB::table('docente_cursos')
            ->join('usuarios', 'usuarios.id', '=', 'docente_cursos.usuario_id')
            ->whereIn('docente_cursos.curso_id', $cursoIds)
            ->select('docente_cursos.curso_id', 'usuarios.nombre as docente_nombre', 'usuarios.correo as docente_correo')
            ->get()
            ->keyBy('curso_id');

        // Nombres únicos de asignaturas del área (para Inciso 1 - Programas)
        $asignaturasUnicas = $cursosArea->pluck('nombre_curso')->unique()->values();

        // Determinar mes y periodo sugerido según la fecha actual o query params
        $mesDefecto = $request->query('mes') ?? ucfirst(\Carbon\Carbon::now()->locale('es')->translatedFormat('F'));
        $currentMonthNum = intval(date('n'));
        $periodoSugerido = ($currentMonthNum >= 7 && $currentMonthNum <= 11)
            ? ('Segundo Semestre ' . date('Y'))
            : (($currentMonthNum == 6) ? ('Vacaciones Junio ' . date('Y')) : (($currentMonthNum == 12) ? ('Vacaciones Diciembre ' . date('Y')) : ('Primer Semestre ' . date('Y'))));
        $periodoDefecto = $request->query('periodo', $periodoSugerido);

        // Buscar informes entregados por docentes para los cursos del área en el mes especificado
        $docenteInformesQuery = Informe::with(['usuario', 'semanas'])
            ->whereIn('curso_id', $cursoIds)
            ->whereIn('estado', ['enviado', 'aprobado', 'bloqueado']);

        if (!empty($mesDefecto)) {
            $docenteInformesQuery->whereRaw('LOWER(TRIM(mes)) = ?', [mb_strtolower(trim($mesDefecto))]);
        }

        if (!empty($periodoDefecto)) {
            $cleanPeriodo = trim(preg_replace('/\d{4}/', '', $periodoDefecto));
            $docenteInformesQuery->where(function ($q) use ($periodoDefecto, $cleanPeriodo) {
                $q->where('periodo', trim($periodoDefecto))
                  ->orWhere('periodo', 'LIKE', '%' . $cleanPeriodo . '%');
            });
        }

        $docenteInformes = $docenteInformesQuery->get()->keyBy('curso_id');

        // Mapear programas iniciales de forma automática con todos los cursos únicos del área
        $programasIniciales = $asignaturasUnicas->map(function ($nombreCurso) use ($cursosArea, $docenteInformes) {
            $cursoIds = $cursosArea->where('nombre_curso', $nombreCurso)->pluck('id');
            $enlaceProg = null;
            foreach ($cursoIds as $cId) {
                $inf = $docenteInformes->get($cId);
                if ($inf && !empty($inf->enlace_classroom_drive_url)) {
                    $enlaceProg = $inf->enlace_classroom_drive_url;
                    break;
                }
            }
            return [
                'asignatura' => $nombreCurso,
                'enlace_programa' => $enlaceProg,
            ];
        });

        // Mapear filas iniciales para Inciso 2 e Inciso 3.1
        $filasAsignaturas = $cursosArea->map(function ($curso) use ($asignaciones, $docenteInformes) {
            $docente = $asignaciones->get($curso->id);
            $inf = $docenteInformes->get($curso->id);

            $presento = $inf ? true : false;
            $sala = $inf && !empty($inf->enlace_meet_zoom_url);
            $virtual = $inf && !empty($inf->enlace_classroom_drive_url);
            $eval = $inf && !empty($inf->estrategias_evaluacion);
            $evid = $inf && (!empty($inf->enlace_evidencia_url) || !empty($inf->listado_asistencia_url));

            return [
                'curso_id' => $curso->id,
                'docente_nombre' => $docente ? $docente->docente_nombre : 'Sin docente asignado',
                'asignatura' => $curso->nombre_curso,
                'seccion' => $curso->seccion,
                'presento_informe' => $presento,
                'tiene_sala_reuniones' => $sala,
                'funciona_enlace_virtual' => $virtual,
                'funciona_enlace_evaluacion' => $eval,
                'evidencias_generales' => $evid,
                'observaciones' => '',
                'porcentaje_avance' => 100,
                'docente_informe_id' => $inf ? $inf->id : null,
                'informe_detalles' => $inf ? [
                    'id' => $inf->id,
                    'estudiantes_asignados' => $inf->estudiantes_asignados,
                    'listado_asistencia_url' => $inf->listado_asistencia_url,
                    'enlace_evidencia_url' => $inf->enlace_evidencia_url,
                    'enlace_meet_zoom_url' => $inf->enlace_meet_zoom_url,
                    'enlace_classroom_drive_url' => $inf->enlace_classroom_drive_url,
                    'estrategias_evaluacion' => $inf->estrategias_evaluacion,
                    'semanas_registradas' => $inf->semanas->count(),
                ] : null,
            ];
        });

        return view('coordinador.crear_informe_coordinacion', compact('user', 'area', 'cursosArea', 'asignaturasUnicas', 'filasAsignaturas', 'docenteInformes', 'programasIniciales', 'periodoDefecto', 'mesDefecto'));
    }

    /**
     * Guarda un nuevo Informe Mensual de Coordinación en la base de datos.
     */
    public function guardarInformeCoordinacion(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $area = $user->area ?? session('coordinador_area');

        $request->validate([
            'periodo' => 'required|string|max:100',
            'mes' => 'required|string|max:50',
            'anio' => 'required|integer',
            'herramientas_virtuales' => 'nullable|string',
            'enlace_actividades_coordinacion' => 'nullable|string|max:255',
            'enlace_informe_auxiliares' => 'nullable|string|max:255',
            'enlace_docentes_permisos' => 'nullable|string|max:255',
            'estado' => 'nullable|in:borrador,enviado',
        ]);

        $estado = $request->input('estado', 'enviado');
        $bloqueadoEn = ($estado === 'enviado') ? Carbon::now()->addDays(3) : null;

        DB::beginTransaction();
        try {
            $informe = InformeCoordinacion::create([
                'usuario_id' => $user->id,
                'area' => $area,
                'periodo' => $request->input('periodo'),
                'mes' => $request->input('mes'),
                'anio' => $request->input('anio'),
                'herramientas_virtuales' => $request->input('herramientas_virtuales'),
                'enlace_actividades_coordinacion' => $request->input('enlace_actividades_coordinacion'),
                'enlace_informe_auxiliares' => $request->input('enlace_informe_auxiliares'),
                'enlace_docentes_permisos' => $request->input('enlace_docentes_permisos'),
                'estado' => $estado,
                'bloqueado_en' => $bloqueadoEn,
            ]);

            // 1. Programas de asignaturas (Inciso 1)
            $programas = $request->input('programas', []);
            if (is_array($programas)) {
                foreach ($programas as $prog) {
                    if (!empty($prog['asignatura'])) {
                        InformeCoordinacionPrograma::create([
                            'informe_coordinacion_id' => $informe->id,
                            'asignatura' => $prog['asignatura'],
                            'enlace_programa' => $prog['enlace_programa'] ?? null,
                        ]);
                    }
                }
            }

            // 2. Info general de asignaturas (Inciso 2)
            $asignaturas = $request->input('asignaturas', []);
            if (is_array($asignaturas)) {
                foreach ($asignaturas as $asig) {
                    if (!empty($asig['asignatura'])) {
                        InformeCoordinacionAsignatura::create([
                            'informe_coordinacion_id' => $informe->id,
                            'curso_id' => !empty($asig['curso_id']) ? $asig['curso_id'] : null,
                            'docente_nombre' => $asig['docente_nombre'] ?? '—',
                            'asignatura' => $asig['asignatura'],
                            'seccion' => $asig['seccion'] ?? '—',
                            'presento_informe' => filter_var($asig['presento_informe'] ?? false, FILTER_VALIDATE_BOOLEAN),
                            'tiene_sala_reuniones' => filter_var($asig['tiene_sala_reuniones'] ?? false, FILTER_VALIDATE_BOOLEAN),
                            'funciona_enlace_virtual' => filter_var($asig['funciona_enlace_virtual'] ?? false, FILTER_VALIDATE_BOOLEAN),
                            'funciona_enlace_evaluacion' => filter_var($asig['funciona_enlace_evaluacion'] ?? false, FILTER_VALIDATE_BOOLEAN),
                            'evidencias_generales' => filter_var($asig['evidencias_generales'] ?? false, FILTER_VALIDATE_BOOLEAN),
                            'observaciones' => $asig['observaciones'] ?? null,
                        ]);
                    }
                }
            }

            // 3. Avance de contenidos (Inciso 3.1)
            $avances = $request->input('avances', []);
            if (is_array($avances)) {
                foreach ($avances as $av) {
                    if (!empty($av['asignatura'])) {
                        InformeCoordinacionAvance::create([
                            'informe_coordinacion_id' => $informe->id,
                            'curso_id' => !empty($av['curso_id']) ? $av['curso_id'] : null,
                            'docente_nombre' => $av['docente_nombre'] ?? '—',
                            'asignatura' => $av['asignatura'],
                            'seccion' => $av['seccion'] ?? '—',
                            'porcentaje_avance' => intval($av['porcentaje_avance'] ?? 0),
                            'observaciones' => $av['observaciones'] ?? null,
                        ]);
                    }
                }
            }

            // 4. Reporte de estudiantes con problemas (Inciso 3.2)
            $estudiantes = $request->input('estudiantes', []);
            if (is_array($estudiantes)) {
                foreach ($estudiantes as $est) {
                    if (!empty($est['asignatura'])) {
                        InformeCoordinacionEstudiante::create([
                            'informe_coordinacion_id' => $informe->id,
                            'asignatura' => $est['asignatura'],
                            'seccion' => $est['seccion'] ?? '—',
                            'cantidad_estudiantes' => intval($est['cantidad_estudiantes'] ?? 0),
                            'carne_estudiantes' => $est['carne_estudiantes'] ?? null,
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Informe mensual de coordinación guardado exitosamente.',
                'redirect_url' => route('coordinador.dashboard')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar el informe de coordinación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Muestra la vista para editar un informe de coordinación existente.
     */
    public function editarInformeCoordinacionView($id)
    {
        /** @var User $user */
        $user = Auth::user();

        $informe = InformeCoordinacion::with(['programas', 'asignaturas', 'avances', 'estudiantes'])
            ->where('usuario_id', $user->id)
            ->findOrFail($id);

        $area = $informe->area;

        // Cursos del área
        $cursosArea = $this->getCursosPorArea($area);

        $cursoIds = $cursosArea->pluck('id')->toArray();
        $asignaturasUnicas = $cursosArea->pluck('nombre_curso')->unique()->values();

        // Buscar informes entregados por docentes para los cursos del área en este periodo y mes
        $docenteInformes = Informe::with(['usuario', 'semanas'])
            ->whereIn('curso_id', $cursoIds)
            ->where('periodo', $informe->periodo)
            ->where('mes', $informe->mes)
            ->whereIn('estado', ['enviado', 'aprobado', 'bloqueado'])
            ->get()
            ->keyBy('curso_id');

        // Combinar programas guardados con todos los cursos del área para asegurar que aparezcan automáticamente
        $programasGuardados = $informe->programas->keyBy('asignatura');
        $programasCompletos = $asignaturasUnicas->map(function ($nombreCurso) use ($programasGuardados, $cursosArea, $docenteInformes) {
            $p = $programasGuardados->get($nombreCurso);
            $enlace = $p ? $p->enlace_programa : null;
            if (!$enlace) {
                $cursoIds = $cursosArea->where('nombre_curso', $nombreCurso)->pluck('id');
                foreach ($cursoIds as $cId) {
                    $inf = $docenteInformes->get($cId);
                    if ($inf && !empty($inf->enlace_classroom_drive_url)) {
                        $enlace = $inf->enlace_classroom_drive_url;
                        break;
                    }
                }
            }
            return [
                'asignatura' => $nombreCurso,
                'enlace_programa' => $enlace,
            ];
        });

        $isLocked = ($informe->bloqueado_en && Carbon::now()->greaterThan($informe->bloqueado_en));

        return view('coordinador.crear_informe_coordinacion', compact('user', 'area', 'informe', 'cursosArea', 'asignaturasUnicas', 'isLocked', 'docenteInformes', 'programasCompletos'));
    }

    /**
     * Actualiza un informe de coordinación existente.
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
                'message' => 'El informe está bloqueado para edición (plazo máximo de 3 días vencido).'
            ], 403);
        }

        $request->validate([
            'periodo' => 'required|string|max:100',
            'mes' => 'required|string|max:50',
            'anio' => 'required|integer',
            'herramientas_virtuales' => 'nullable|string',
            'enlace_actividades_coordinacion' => 'nullable|string|max:255',
            'enlace_informe_auxiliares' => 'nullable|string|max:255',
            'enlace_docentes_permisos' => 'nullable|string|max:255',
            'estado' => 'nullable|in:borrador,enviado',
        ]);

        $estado = $request->input('estado', $informe->estado);

        DB::beginTransaction();
        try {
            $informe->update([
                'periodo' => $request->input('periodo'),
                'mes' => $request->input('mes'),
                'anio' => $request->input('anio'),
                'herramientas_virtuales' => $request->input('herramientas_virtuales'),
                'enlace_actividades_coordinacion' => $request->input('enlace_actividades_coordinacion'),
                'enlace_informe_auxiliares' => $request->input('enlace_informe_auxiliares'),
                'enlace_docentes_permisos' => $request->input('enlace_docentes_permisos'),
                'estado' => $estado,
            ]);

            // Reemplazar programas (Inciso 1)
            $informe->programas()->delete();
            $programas = $request->input('programas', []);
            if (is_array($programas)) {
                foreach ($programas as $prog) {
                    if (!empty($prog['asignatura'])) {
                        InformeCoordinacionPrograma::create([
                            'informe_coordinacion_id' => $informe->id,
                            'asignatura' => $prog['asignatura'],
                            'enlace_programa' => $prog['enlace_programa'] ?? null,
                        ]);
                    }
                }
            }

            // Reemplazar asignaturas (Inciso 2)
            $informe->asignaturas()->delete();
            $asignaturas = $request->input('asignaturas', []);
            if (is_array($asignaturas)) {
                foreach ($asignaturas as $asig) {
                    if (!empty($asig['asignatura'])) {
                        InformeCoordinacionAsignatura::create([
                            'informe_coordinacion_id' => $informe->id,
                            'curso_id' => !empty($asig['curso_id']) ? $asig['curso_id'] : null,
                            'docente_nombre' => $asig['docente_nombre'] ?? '—',
                            'asignatura' => $asig['asignatura'],
                            'seccion' => $asig['seccion'] ?? '—',
                            'presento_informe' => filter_var($asig['presento_informe'] ?? false, FILTER_VALIDATE_BOOLEAN),
                            'tiene_sala_reuniones' => filter_var($asig['tiene_sala_reuniones'] ?? false, FILTER_VALIDATE_BOOLEAN),
                            'funciona_enlace_virtual' => filter_var($asig['funciona_enlace_virtual'] ?? false, FILTER_VALIDATE_BOOLEAN),
                            'funciona_enlace_evaluacion' => filter_var($asig['funciona_enlace_evaluacion'] ?? false, FILTER_VALIDATE_BOOLEAN),
                            'evidencias_generales' => filter_var($asig['evidencias_generales'] ?? false, FILTER_VALIDATE_BOOLEAN),
                            'observaciones' => $asig['observaciones'] ?? null,
                        ]);
                    }
                }
            }

            // Reemplazar avances (Inciso 3.1)
            $informe->avances()->delete();
            $avances = $request->input('avances', []);
            if (is_array($avances)) {
                foreach ($avances as $av) {
                    if (!empty($av['asignatura'])) {
                        InformeCoordinacionAvance::create([
                            'informe_coordinacion_id' => $informe->id,
                            'curso_id' => !empty($av['curso_id']) ? $av['curso_id'] : null,
                            'docente_nombre' => $av['docente_nombre'] ?? '—',
                            'asignatura' => $av['asignatura'],
                            'seccion' => $av['seccion'] ?? '—',
                            'porcentaje_avance' => intval($av['porcentaje_avance'] ?? 0),
                            'observaciones' => $av['observaciones'] ?? null,
                        ]);
                    }
                }
            }

            // Reemplazar estudiantes (Inciso 3.2)
            $informe->estudiantes()->delete();
            $estudiantes = $request->input('estudiantes', []);
            if (is_array($estudiantes)) {
                foreach ($estudiantes as $est) {
                    if (!empty($est['asignatura'])) {
                        InformeCoordinacionEstudiante::create([
                            'informe_coordinacion_id' => $informe->id,
                            'asignatura' => $est['asignatura'],
                            'seccion' => $est['seccion'] ?? '—',
                            'cantidad_estudiantes' => intval($est['cantidad_estudiantes'] ?? 0),
                            'carne_estudiantes' => $est['carne_estudiantes'] ?? null,
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Informe de coordinación actualizado correctamente.',
                'redirect_url' => route('coordinador.dashboard')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el informe de coordinación: ' . $e->getMessage()
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

        DB::beginTransaction();
        try {
            $informe = InformeCoordinacion::where('usuario_id', $user->id)->findOrFail($id);
            $informe->programas()->delete();
            $informe->asignaturas()->delete();
            $informe->avances()->delete();
            $informe->estudiantes()->delete();
            $informe->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'El informe de coordinación ha sido eliminado permanentemente.'
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
