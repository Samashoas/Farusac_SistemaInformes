<?php

namespace App\Traits;

use App\Models\Informe;
use App\Models\InformeCoordinacion;
use Illuminate\Support\Facades\DB;
use Exception;

trait HandlesInformeData
{
    /**
     * Construye el array normalizado de datos de un informe docente para respuestas JSON.
     * Compartido entre DocenteController, CoordinadorController y AdminController.
     */
    protected function buildInformeDocenteData(Informe $informe): array
    {
        return [
            'id'                         => $informe->id,
            'docente_nombre'             => $informe->usuario->nombre ?? 'Docente no asignado',
            'docente_correo'             => $informe->usuario->correo ?? '',
            'asignatura'                 => $informe->curso->nombre_curso ?? 'Curso',
            'carrera'                    => $informe->curso->carrera ?? '',
            'area'                       => $informe->curso->area ?? '',
            'codigo'                     => $informe->curso->codigo_curso ?? '',
            'seccion'                    => $informe->curso->seccion ?? '',
            'anio'                       => $informe->curso->anio ?? ($informe->created_at ? $informe->created_at->format('Y') : date('Y')),
            'periodo'                    => $informe->periodo,
            'mes'                        => $informe->mes,
            'estudiantes_asignados'      => $informe->estudiantes_asignados,
            'listado_asistencia_url'     => $informe->listado_asistencia_url,
            'enlace_evidencia_url'       => $informe->enlace_evidencia_url,
            'enlace_meet_zoom_url'       => $informe->enlace_meet_zoom_url,
            'enlace_classroom_drive_url' => $informe->enlace_classroom_drive_url,
            'estrategias_evaluacion'     => $informe->estrategias_evaluacion,
            'estado'                     => $informe->estado,
            'created_at'                 => $informe->created_at ? $informe->created_at->format('d/m/Y H:i') : '',
            'semanas'                    => $informe->semanas->map(function ($s) {
                return [
                    'numero_semana'            => $s->numero_semana,
                    'actividad_realizada'      => $s->actividad_realizada,
                    'estudiantes_participaron' => $s->estudiantes_participaron,
                    'metodologias'             => $s->metodologias,
                    'medios_comunicacion'      => $s->medios_comunicacion,
                ];
            }),
        ];
    }

    /**
     * Construye el array normalizado de datos de un informe de coordinación para respuestas JSON.
     * Compartido entre CoordinadorController y AdminController.
     */
    protected function buildInformeCoordinacionData(InformeCoordinacion $informe): array
    {
        return [
            'id'                             => $informe->id,
            'coordinador_nombre'             => $informe->usuario->nombre ?? 'Coordinador no asignado',
            'coordinador_correo'             => $informe->usuario->correo ?? '',
            'area'                           => $informe->area,
            'periodo'                        => $informe->periodo,
            'mes'                            => $informe->mes,
            'anio'                           => $informe->anio,
            'herramientas_virtuales'         => $informe->herramientas_virtuales,
            'enlace_actividades_coordinacion'=> $informe->enlace_actividades_coordinacion,
            'enlace_informe_auxiliares'      => $informe->enlace_informe_auxiliares,
            'enlace_docentes_permisos'       => $informe->enlace_docentes_permisos,
            'estado'                         => $informe->estado,
            'created_at'                     => $informe->created_at ? $informe->created_at->format('d/m/Y H:i') : '',
            'programas'                      => $informe->programas,
            'asignaturas'                    => $informe->asignaturas,
            'avances'                        => $informe->avances,
            'estudiantes'                    => $informe->estudiantes,
        ];
    }

    /**
     * Elimina atómicamente un informe docente y sus registros asociados (semanas).
     */
    protected function eliminarInformeDocenteModel(Informe $informe): void
    {
        DB::beginTransaction();
        try {
            $informe->semanas()->delete();
            $informe->delete();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Elimina atómicamente un informe de coordinación y todas sus tablas hijas.
     */
    protected function eliminarInformeCoordinacionModel(InformeCoordinacion $informe): void
    {
        DB::beginTransaction();
        try {
            $informe->programas()->delete();
            $informe->asignaturas()->delete();
            $informe->avances()->delete();
            $informe->estudiantes()->delete();
            $informe->delete();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

