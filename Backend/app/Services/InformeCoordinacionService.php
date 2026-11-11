<?php

namespace App\Services;

use App\Models\Curso;
use App\Models\Informe;
use App\Models\InformeCoordinacion;
use App\Models\InformeCoordinacionAsignatura;
use App\Models\InformeCoordinacionAvance;
use App\Models\InformeCoordinacionEstudiante;
use App\Models\InformeCoordinacionPrograma;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class InformeCoordinacionService
{
    /**
     * Obtiene los cursos asociados a un área (y opcionalmente carrera).
     */
    public function getCursosPorArea(string $area, ?string $carrera = null, ?User $user = null): Collection
    {
        if (empty($area)) {
            return collect();
        }

        $cleanArea = trim(str_ireplace(['Área de ', 'Area de ', 'Área ', 'Area '], '', $area));

        $palabras = array_filter(explode(' ', $cleanArea), function ($p) {
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

        if (!empty($carrera)) {
            $cleanCarrera = trim($carrera);
            $query->where(function ($q) use ($cleanCarrera) {
                $q->where('carrera', $cleanCarrera)
                  ->orWhere('carrera', 'LIKE', '%' . $cleanCarrera . '%');
                if (stripos($cleanCarrera, 'Diseño') !== false) {
                    $q->orWhere('carrera', 'LIKE', '%Diseño%');
                }
            });
        }

        $cursos = $query->orderBy('nombre_curso', 'asc')
            ->orderBy('seccion', 'asc')
            ->get();

        if ($cursos->isEmpty() && $user) {
            $cursos = $user->cursos()->orderBy('nombre_curso', 'asc')->orderBy('seccion', 'asc')->get();
        }

        return $cursos;
    }

    /**
     * Obtiene los datos consolidados de docentes y programas para el área especificada.
     */
    public function obtenerDatosDocentesArea(string $area, ?string $carrera = null, ?string $periodo = null, ?string $mes = null, ?User $user = null): array
    {
        $cursosArea = $this->getCursosPorArea($area, $carrera, $user);
        $cursoIds   = $cursosArea->pluck('id')->toArray();

        $asignaciones = DB::table('docente_cursos')
            ->join('usuarios', 'usuarios.id', '=', 'docente_cursos.usuario_id')
            ->whereIn('docente_cursos.curso_id', $cursoIds)
            ->select('docente_cursos.curso_id', 'usuarios.nombre as docente_nombre', 'usuarios.correo as docente_correo')
            ->get()
            ->keyBy('curso_id');

        $docenteInformesQuery = Informe::with(['usuario', 'semanas'])
            ->whereIn('curso_id', $cursoIds)
            ->whereIn('estado', ['enviado', 'aprobado', 'bloqueado']);

        if (!empty($mes)) {
            $docenteInformesQuery->whereRaw('LOWER(TRIM(mes)) = ?', [mb_strtolower(trim($mes))]);
        }

        if (!empty($periodo)) {
            $cleanPeriodo = trim(preg_replace('/\d{4}/', '', $periodo));
            $docenteInformesQuery->where(function ($q) use ($periodo, $cleanPeriodo) {
                $q->where('periodo', trim($periodo))
                  ->orWhere('periodo', 'LIKE', '%' . $cleanPeriodo . '%');
            });
        }

        $docenteInformes   = $docenteInformesQuery->get()->keyBy('curso_id');
        $asignaturasUnicas = $cursosArea->pluck('nombre_curso')->unique()->values();

        $programas = $asignaturasUnicas->map(function ($nombreCurso) use ($cursosArea, $docenteInformes) {
            $subCursoIds = $cursosArea->where('nombre_curso', $nombreCurso)->pluck('id');
            $enlaceProg  = null;
            foreach ($subCursoIds as $cId) {
                $inf = $docenteInformes->get($cId);
                if ($inf && !empty($inf->enlace_classroom_drive_url)) {
                    $enlaceProg = $inf->enlace_classroom_drive_url;
                    break;
                }
            }
            return [
                'asignatura'      => $nombreCurso,
                'enlace_programa' => $enlaceProg,
            ];
        });

        $datos = $cursosArea->map(function ($curso) use ($asignaciones, $docenteInformes) {
            $docente = $asignaciones->get($curso->id);
            $inf     = $docenteInformes->get($curso->id);

            $presento = (bool) $inf;
            $sala     = $inf && !empty($inf->enlace_meet_zoom_url);
            $virtual  = $inf && !empty($inf->enlace_classroom_drive_url);
            $eval     = $inf && !empty($inf->estrategias_evaluacion);
            $evid     = $inf && (!empty($inf->enlace_evidencia_url) || !empty($inf->listado_asistencia_url));

            return [
                'curso_id'                   => $curso->id,
                'docente_nombre'             => $docente ? $docente->docente_nombre : 'Sin docente asignado',
                'asignatura'                 => $curso->nombre_curso,
                'seccion'                    => $curso->seccion,
                'presento_informe'           => $presento,
                'tiene_sala_reuniones'       => $sala,
                'funciona_enlace_virtual'    => $virtual,
                'funciona_enlace_evaluacion' => $eval,
                'evidencias_generales'       => $evid,
                'observaciones'              => '',
                'porcentaje_avance'          => 100,
                'docente_informe_id'         => $inf ? $inf->id : null,
                'informe_detalles'           => $inf ? [
                    'id'                         => $inf->id,
                    'estudiantes_asignados'      => $inf->estudiantes_asignados,
                    'listado_asistencia_url'     => $inf->listado_asistencia_url,
                    'enlace_evidencia_url'       => $inf->enlace_evidencia_url,
                    'enlace_meet_zoom_url'       => $inf->enlace_meet_zoom_url,
                    'enlace_classroom_drive_url' => $inf->enlace_classroom_drive_url,
                    'estrategias_evaluacion'     => $inf->estrategias_evaluacion,
                    'semanas_registradas'        => $inf->semanas->count(),
                ] : null,
            ];
        });

        return [
            'datos'     => $datos,
            'programas' => $programas,
        ];
    }

    /**
     * Guarda un nuevo informe de coordinación con todas sus sub-entidades de forma atómica.
     */
    public function guardarInforme(User $user, array $data, ?string $area): InformeCoordinacion
    {
        $estado      = $data['estado'] ?? 'enviado';
        $bloqueadoEn = ($estado === 'enviado') ? Carbon::now()->addDays(3) : null;

        DB::beginTransaction();
        try {
            $informe = InformeCoordinacion::create([
                'usuario_id'                      => $user->id,
                'area'                            => $area,
                'periodo'                         => $data['periodo'],
                'mes'                             => $data['mes'],
                'anio'                            => $data['anio'],
                'herramientas_virtuales'          => $data['herramientas_virtuales'] ?? null,
                'enlace_actividades_coordinacion' => $data['enlace_actividades_coordinacion'] ?? null,
                'enlace_informe_auxiliares'       => $data['enlace_informe_auxiliares'] ?? null,
                'enlace_docentes_permisos'        => $data['enlace_docentes_permisos'] ?? null,
                'estado'                          => $estado,
                'bloqueado_en'                    => $bloqueadoEn,
            ]);

            $this->sincronizarSubEntidades($informe, $data);

            DB::commit();
            return $informe;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Actualiza un informe de coordinación existente y sus sub-entidades de forma atómica.
     */
    public function actualizarInforme(InformeCoordinacion $informe, array $data): InformeCoordinacion
    {
        $estado = $data['estado'] ?? $informe->estado;

        DB::beginTransaction();
        try {
            $informe->update([
                'periodo'                         => $data['periodo'],
                'mes'                             => $data['mes'],
                'anio'                            => $data['anio'],
                'herramientas_virtuales'          => $data['herramientas_virtuales'] ?? null,
                'enlace_actividades_coordinacion' => $data['enlace_actividades_coordinacion'] ?? null,
                'enlace_informe_auxiliares'       => $data['enlace_informe_auxiliares'] ?? null,
                'enlace_docentes_permisos'        => $data['enlace_docentes_permisos'] ?? null,
                'estado'                          => $estado,
            ]);

            $informe->programas()->delete();
            $informe->asignaturas()->delete();
            $informe->avances()->delete();
            $informe->estudiantes()->delete();

            $this->sincronizarSubEntidades($informe, $data);

            DB::commit();
            return $informe;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Sincroniza los programas, asignaturas, avances y estadísticas de estudiantes.
     */
    protected function sincronizarSubEntidades(InformeCoordinacion $informe, array $data): void
    {
        // 1. Programas de asignaturas
        $programas = $data['programas'] ?? [];
        if (is_array($programas)) {
            foreach ($programas as $prog) {
                if (!empty($prog['asignatura'])) {
                    InformeCoordinacionPrograma::create([
                        'informe_coordinacion_id' => $informe->id,
                        'asignatura'              => $prog['asignatura'],
                        'enlace_programa'         => $prog['enlace_programa'] ?? null,
                    ]);
                }
            }
        }

        // 2. Info general de asignaturas
        $asignaturas = $data['asignaturas'] ?? [];
        if (is_array($asignaturas)) {
            foreach ($asignaturas as $asig) {
                if (!empty($asig['asignatura'])) {
                    InformeCoordinacionAsignatura::create([
                        'informe_coordinacion_id'    => $informe->id,
                        'curso_id'                   => !empty($asig['curso_id']) ? $asig['curso_id'] : null,
                        'docente_nombre'             => $asig['docente_nombre'] ?? '—',
                        'asignatura'                 => $asig['asignatura'],
                        'seccion'                    => $asig['seccion'] ?? '—',
                        'presento_informe'           => filter_var($asig['presento_informe'] ?? false, FILTER_VALIDATE_BOOLEAN),
                        'tiene_sala_reuniones'       => filter_var($asig['tiene_sala_reuniones'] ?? false, FILTER_VALIDATE_BOOLEAN),
                        'funciona_enlace_virtual'    => filter_var($asig['funciona_enlace_virtual'] ?? false, FILTER_VALIDATE_BOOLEAN),
                        'funciona_enlace_evaluacion' => filter_var($asig['funciona_enlace_evaluacion'] ?? false, FILTER_VALIDATE_BOOLEAN),
                        'evidencias_generales'       => filter_var($asig['evidencias_generales'] ?? false, FILTER_VALIDATE_BOOLEAN),
                        'observaciones'              => $asig['observaciones'] ?? null,
                    ]);
                }
            }
        }

        // 3. Avances de contenido
        $avances = $data['avances'] ?? [];
        if (is_array($avances)) {
            foreach ($avances as $av) {
                if (!empty($av['asignatura'])) {
                    InformeCoordinacionAvance::create([
                        'informe_coordinacion_id' => $informe->id,
                        'curso_id'                => !empty($av['curso_id']) ? $av['curso_id'] : null,
                        'docente_nombre'          => $av['docente_nombre'] ?? '—',
                        'asignatura'              => $av['asignatura'],
                        'seccion'                 => $av['seccion'] ?? '—',
                        'porcentaje_avance'       => intval($av['porcentaje_avance'] ?? 0),
                        'observaciones'           => $av['observaciones'] ?? null,
                    ]);
                }
            }
        }

        // 4. Estadísticas de estudiantes
        $estudiantes = $data['estudiantes'] ?? [];
        if (is_array($estudiantes)) {
            foreach ($estudiantes as $est) {
                if (!empty($est['asignatura'])) {
                    InformeCoordinacionEstudiante::create([
                        'informe_coordinacion_id' => $informe->id,
                        'curso_id'                => !empty($est['curso_id']) ? $est['curso_id'] : null,
                        'docente_nombre'          => $est['docente_nombre'] ?? '—',
                        'asignatura'              => $est['asignatura'],
                        'seccion'                 => $est['seccion'] ?? '—',
                        'estudiantes_asignados'   => intval($est['estudiantes_asignados'] ?? 0),
                        'estudiantes_atendidos'   => intval($est['estudiantes_atendidos'] ?? 0),
                        'observaciones'           => $est['observaciones'] ?? null,
                    ]);
                }
            }
        }
    }
}

