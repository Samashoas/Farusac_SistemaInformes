<?php

namespace App\Services;

use App\Models\Informe;
use App\Models\InformeSemana;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class InformeDocenteService
{
    /**
     * Guarda un nuevo informe docente y sus semanas de forma atómica.
     */
    public function guardarInforme(User $user, array $data): Informe
    {
        DB::beginTransaction();
        try {
            $informe = Informe::create([
                'usuario_id'                 => $user->id,
                'curso_id'                   => $data['curso_id'],
                'periodo'                    => $data['periodo'],
                'mes'                        => $data['mes'],
                'estudiantes_asignados'      => $data['estudiantes_asignados'] ?? 0,
                'listado_asistencia_url'     => $data['listado_asistencia_url'] ?? null,
                'enlace_evidencia_url'       => $data['enlace_evidencia_url'] ?? null,
                'enlace_meet_zoom_url'       => $data['enlace_meet_zoom_url'] ?? null,
                'enlace_classroom_drive_url' => $data['enlace_classroom_drive_url'] ?? null,
                'estrategias_evaluacion'     => $data['estrategias_evaluacion'] ?? null,
                'estado'                     => 'enviado',
                'bloqueado_en'               => Carbon::now()->addDays(3),
            ]);

            $this->sincronizarSemanas($informe, $data['semanas'] ?? []);

            DB::commit();
            return $informe;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Actualiza un informe docente existente y sus semanas de forma atómica.
     */
    public function actualizarInforme(Informe $informe, array $data): Informe
    {
        DB::beginTransaction();
        try {
            $informe->update([
                'curso_id'                   => $data['curso_id'],
                'periodo'                    => $data['periodo'],
                'mes'                        => $data['mes'],
                'estudiantes_asignados'      => $data['estudiantes_asignados'] ?? $informe->estudiantes_asignados,
                'listado_asistencia_url'     => $data['listado_asistencia_url'] ?? null,
                'enlace_evidencia_url'       => $data['enlace_evidencia_url'] ?? null,
                'enlace_meet_zoom_url'       => $data['enlace_meet_zoom_url'] ?? null,
                'enlace_classroom_drive_url' => $data['enlace_classroom_drive_url'] ?? null,
                'estrategias_evaluacion'     => $data['estrategias_evaluacion'] ?? null,
            ]);

            $informe->semanas()->delete();
            $this->sincronizarSemanas($informe, $data['semanas'] ?? []);

            DB::commit();
            return $informe;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Registra las semanas de actividades para un informe docente.
     */
    protected function sincronizarSemanas(Informe $informe, array $semanas): void
    {
        foreach ($semanas as $sem) {
            InformeSemana::create([
                'informe_id'               => $informe->id,
                'numero_semana'            => $sem['numero_semana'] ?? 1,
                'actividad_realizada'      => $sem['actividad_realizada'] ?? '',
                'estudiantes_participaron' => $sem['estudiantes_participaron'] ?? 0,
                'metodologias'             => $sem['metodologias'] ?? null,
                'medios_comunicacion'      => $sem['medios_comunicacion'] ?? null,
            ]);
        }
    }
}

