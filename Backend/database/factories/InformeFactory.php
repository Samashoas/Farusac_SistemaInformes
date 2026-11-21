<?php

namespace Database\Factories;

use App\Models\Curso;
use App\Models\Informe;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Informe>
 */
class InformeFactory extends Factory
{
    protected $model = Informe::class;

    public function definition(): array
    {
        return [
            'usuario_id'            => User::factory(),
            'curso_id'              => Curso::factory(),
            'periodo'               => 'Primer Semestre',
            'mes'                   => 'Febrero',
            'estudiantes_asignados' => fake()->numberBetween(15, 60),
            'listado_asistencia_url'=> fake()->url(),
            'enlace_evidencia_url'  => fake()->url(),
            'enlace_meet_zoom_url'  => fake()->url(),
            'enlace_classroom_drive_url' => fake()->url(),
            'estrategias_evaluacion'=> fake()->paragraph(),
            'estado'                => 'enviado',
            'bloqueado_en'          => null,
        ];
    }

    /**
     * Estado para informe bloqueado.
     */
    public function bloqueado(): static
    {
        return $this->state(fn () => [
            'estado'       => 'bloqueado',
            'bloqueado_en' => now()->subDays(5),
        ]);
    }
}
