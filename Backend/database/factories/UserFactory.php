<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre'    => fake()->name(),
            'correo'    => fake()->unique()->safeEmail(),
            'numero'    => fake()->optional()->numerify('########'),
            'rol'       => 'docente',
            'plaza'     => fake()->optional()->numerify('PL-####'),
            'area'      => 'Área Común',
            'estado'    => 'activo',
            'google_id' => null,
        ];
    }

    /**
     * Estado para usuario con rol de Administrador.
     */
    public function administrador(): static
    {
        return $this->state(fn (array $attributes) => [
            'rol' => 'administrador',
        ]);
    }

    /**
     * Estado para usuario con rol de Coordinador.
     */
    public function coordinador(?string $area = null): static
    {
        return $this->state(fn (array $attributes) => [
            'rol'  => 'coordinador',
            'area' => $area ?? 'Área de Diseño',
        ]);
    }

    /**
     * Estado para usuario inactivo.
     */
    public function inactivo(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'inactivo',
        ]);
    }
}
