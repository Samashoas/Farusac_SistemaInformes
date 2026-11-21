<?php

namespace Tests\Feature;

use App\Models\Curso;
use App\Models\Informe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InformeDocenteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Docente puede listar sus informes entregados.
     */
    public function test_docente_can_list_informes(): void
    {
        $docente = User::factory()->create();
        Informe::factory()->count(3)->create(['usuario_id' => $docente->id]);

        $response = $this->actingAs($docente)->get('/docente/informes');

        $response->assertStatus(200);
        $response->assertSee('Historial de Informes');
    }

    /**
     * Docente puede registrar un informe mensual válido con sus semanas.
     */
    public function test_docente_can_create_valid_informe(): void
    {
        $docente = User::factory()->create();
        $curso = Curso::factory()->create();
        $docente->cursos()->attach($curso->id);

        $payload = [
            'curso_id'                   => $curso->id,
            'periodo'                    => 'Primer Semestre 2026',
            'mes'                        => 'Marzo',
            'estudiantes_asignados'      => 35,
            'listado_asistencia_url'     => 'https://drive.google.com/asistencia',
            'enlace_evidencia_url'       => 'https://drive.google.com/evidencia',
            'enlace_meet_zoom_url'       => 'https://meet.google.com/abc-defg-hij',
            'enlace_classroom_drive_url' => 'https://classroom.google.com/c/123456',
            'estrategias_evaluacion'     => 'Evaluaciones continuas mediante rúbricas.',
            'semanas' => [
                [
                    'numero_semana'            => 1,
                    'actividad_realizada'      => 'Presentación del programa y primera clase magistral.',
                    'estudiantes_participaron' => 32,
                    'metodologias'             => 'Clase magistral participativa',
                    'medios_comunicacion'      => 'Google Meet y Classroom',
                ],
                [
                    'numero_semana'            => 2,
                    'actividad_realizada'      => 'Taller práctico de dibujo arquitectónico.',
                    'estudiantes_participaron' => 34,
                    'metodologias'             => 'Aprendizaje basado en proyectos',
                    'medios_comunicacion'      => 'Google Meet',
                ],
            ],
        ];

        $response = $this->actingAs($docente)->postJson('/docente/informes', $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('informes', [
            'usuario_id' => $docente->id,
            'curso_id'   => $curso->id,
            'periodo'    => 'Primer Semestre 2026',
            'mes'        => 'Marzo',
        ]);

        $this->assertDatabaseHas('informe_semanas', [
            'numero_semana'       => 1,
            'actividad_realizada' => 'Presentación del programa y primera clase magistral.',
        ]);
    }

    /**
     * El Form Request rechaza la petición si faltan campos obligatorios.
     */
    public function test_guardar_informe_validates_required_fields(): void
    {
        $docente = User::factory()->create();

        $response = $this->actingAs($docente)->postJson('/docente/informes', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['curso_id', 'periodo', 'mes', 'estudiantes_asignados', 'semanas']);
    }

    /**
     * El Form Request previene el registro de informes duplicados para el mismo curso, periodo y mes.
     */
    public function test_cannot_create_duplicate_informe_for_same_course_period_and_month(): void
    {
        $docente = User::factory()->create();
        $curso = Curso::factory()->create();

        // Primer informe registrado
        Informe::factory()->create([
            'usuario_id' => $docente->id,
            'curso_id'   => $curso->id,
            'periodo'    => 'Primer Semestre 2026',
            'mes'        => 'Abril',
        ]);

        // Intento de duplicado
        $payload = [
            'curso_id'              => $curso->id,
            'periodo'               => 'Primer Semestre 2026',
            'mes'                   => 'Abril',
            'estudiantes_asignados' => 25,
            'semanas' => [
                [
                    'numero_semana'       => 1,
                    'actividad_realizada' => 'Clase duplicada',
                ],
            ],
        ];

        $response = $this->actingAs($docente)->postJson('/docente/informes', $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['mes']);
    }

    /**
     * Protección IDOR: Un docente NO puede editar ni ver el informe de otro docente.
     */
    public function test_docente_cannot_access_other_docentes_informe(): void
    {
        $docenteA = User::factory()->create();
        $docenteB = User::factory()->create();

        $informeB = Informe::factory()->create(['usuario_id' => $docenteB->id]);

        // Intentar acceder a la vista de edición del informe de B
        $response = $this->actingAs($docenteA)->get("/docente/informes/{$informeB->id}/editar");
        $response->assertStatus(403);

        // Intentar ver el informe de B
        $responseVer = $this->actingAs($docenteA)->get("/docente/informes/{$informeB->id}/ver");
        $responseVer->assertStatus(403);

        // Intentar eliminar el informe de B
        $responseDel = $this->actingAs($docenteA)->deleteJson("/docente/informes/{$informeB->id}");
        $responseDel->assertStatus(403);
    }

    /**
     * Un informe bloqueado (plazo de 3 días vencido) no permite actualización.
     */
    public function test_docente_cannot_update_locked_informe(): void
    {
        $docente = User::factory()->create();
        $curso = Curso::factory()->create();

        $informeBloqueado = Informe::factory()->bloqueado()->create([
            'usuario_id' => $docente->id,
            'curso_id'   => $curso->id,
        ]);

        $payload = [
            'estudiantes_asignados' => 40,
            'semanas' => [
                [
                    'numero_semana'       => 1,
                    'actividad_realizada' => 'Intento de edición bloqueada',
                ],
            ],
        ];

        $response = $this->actingAs($docente)->putJson("/docente/informes/{$informeBloqueado->id}", $payload);

        $response->assertStatus(403);
    }

    /**
     * Docente puede eliminar su propio informe exitosamente.
     */
    public function test_docente_can_delete_own_informe(): void
    {
        $docente = User::factory()->create();
        $informe = Informe::factory()->create(['usuario_id' => $docente->id]);

        $response = $this->actingAs($docente)->deleteJson("/docente/informes/{$informe->id}");

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseMissing('informes', ['id' => $informe->id]);
    }
}
