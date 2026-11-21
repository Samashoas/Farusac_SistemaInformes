<?php

namespace Database\Factories;

use App\Models\Curso;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Curso>
 */
class CursoFactory extends Factory
{
    protected $model = Curso::class;

    public function definition(): array
    {
        return [
            'carrera'      => 'Arquitectura',
            'area'         => 'Diseño Arquitectónico',
            'nombre_curso' => fake()->words(3, true),
            'codigo_curso' => 'CUR-' . fake()->unique()->numerify('####'),
            'seccion'      => fake()->randomElement(['A', 'B', 'C', 'M1', 'V1']),
            'anio'         => 2026,
            'semestre'     => 'Primer Semestre',
        ];
    }
}
