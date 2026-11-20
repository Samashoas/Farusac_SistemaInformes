<?php

namespace App\Services;

use App\Models\Informe;
use App\Models\InformeSemana;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InformeDocenteService
{
    /**
     * Guarda un nuevo informe docente y sus semanas de forma atómica.
     */
    public function guardarInforme(User $user, array $data): Informe
    {
        $cursoId = $data['curso_id'];
        $mes     = trim($data['mes'] ?? '');
        $periodo = trim($data['periodo'] ?? '');

        preg_match('/\d{4}/', $periodo, $matches);
        $anio = $matches[0] ?? null;

        $query = Informe::where('curso_id', $cursoId)
            ->whereRaw('LOWER(TRIM(mes)) = ?', [mb_strtolower($mes)]);

        if ($anio) {
            $query->where('periodo', 'LIKE', "%{$anio}%");
        } else {
            $query->where('periodo', $periodo);
        }

        if ($query->exists()) {
            $periodoDesc = $anio ? " ({$anio})" : " ({$periodo})";
            throw ValidationException::withMessages([
                'mes' => ["Ya existe un informe registrado para este curso en el mes de {$mes}{$periodoDesc}. No se permite crear más de un informe para el mismo curso y mes."],
            ]);
        }

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
                'bloqueado_en'               => Carbon::now()->addDays((int) config('informes.dias_bloqueo', 3)),
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
    public function actualizarInforme(Informe $informe, array $data, ?User $user = null): Informe
    {
        if ($user && !$user->isAdministrador() && $informe->isBloqueado()) {
            $dias = (int) config('informes.dias_bloqueo', 3);
            throw new Exception("Este informe ya no puede ser editado porque ha vencido el plazo límite de {$dias} días.");
        }

        $cursoId = $data['curso_id'] ?? $informe->curso_id;
        $mes     = trim($data['mes'] ?? $informe->mes);
        $periodo = trim($data['periodo'] ?? $informe->periodo);

        preg_match('/\d{4}/', $periodo, $matches);
        $anio = $matches[0] ?? null;

        $query = Informe::where('curso_id', $cursoId)
            ->where('id', '!=', $informe->id)
            ->whereRaw('LOWER(TRIM(mes)) = ?', [mb_strtolower($mes)]);

        if ($anio) {
            $query->where('periodo', 'LIKE', "%{$anio}%");
        } else {
            $query->where('periodo', $periodo);
        }

        if ($query->exists()) {
            $periodoDesc = $anio ? " ({$anio})" : " ({$periodo})";
            throw ValidationException::withMessages([
                'mes' => ["Ya existe otro informe registrado para este curso en el mes de {$mes}{$periodoDesc}. No se permite duplicar informes para el mismo curso y mes."],
            ]);
        }

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

    /**
     * Elimina atómicamente un informe docente y sus semanas asociadas.
     */
    public function eliminarInformeDocente(Informe|int|string $informe, ?User $user = null): void
    {
        $target = $informe instanceof Informe ? $informe : Informe::findOrFail($informe);

        if ($user && !$user->isAdministrador()) {
            if ($target->usuario_id !== $user->id) {
                abort(403, 'No tienes permiso para eliminar este informe.');
            }

            if ($target->isBloqueado()) {
                $dias = (int) config('informes.dias_bloqueo', 3);
                abort(403, "No se puede eliminar un informe cuyo plazo de {$dias} días ya ha vencido.");
            }
        }

        DB::beginTransaction();
        try {
            $target->semanas()->delete();
            $target->delete();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

