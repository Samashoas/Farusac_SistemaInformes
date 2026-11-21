<?php

namespace Tests\Unit;

use App\Enums\Rol;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Verificar métodos de comprobación de roles.
     */
    public function test_user_role_helper_methods(): void
    {
        $admin = User::factory()->administrador()->make();
        $this->assertTrue($admin->isAdministrador());
        $this->assertFalse($admin->isDocente());
        $this->assertFalse($admin->isCoordinador());

        $docente = User::factory()->make(['rol' => Rol::Docente]);
        $this->assertTrue($docente->isDocente());
        $this->assertFalse($docente->isAdministrador());
        $this->assertFalse($docente->isCoordinador());

        $coordinador = User::factory()->coordinador()->make();
        $this->assertTrue($coordinador->isCoordinador());
        $this->assertFalse($coordinador->isAdministrador());
    }

    /**
     * Verificar que el atributo rol se castea al Enum Rol.
     */
    public function test_user_role_casts_to_enum(): void
    {
        $user = User::factory()->create(['rol' => 'coordinador']);

        $this->assertInstanceOf(Rol::class, $user->rol);
        $this->assertEquals(Rol::Coordinador, $user->rol);
    }
}
