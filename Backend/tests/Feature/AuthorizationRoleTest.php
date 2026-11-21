<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationRoleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Docente autenticado puede acceder a su panel y rutas de docencia.
     */
    public function test_docente_can_access_docente_routes(): void
    {
        $docente = User::factory()->create(['rol' => 'docente']);

        $this->actingAs($docente)->get('/docente/panel')->assertStatus(200);
        $this->actingAs($docente)->get('/docente/perfil')->assertStatus(200);
        $this->actingAs($docente)->get('/docente/informes')->assertStatus(200);
        $this->actingAs($docente)->get('/docente/informes/crear')->assertStatus(200);
    }

    /**
     * Docente NO puede acceder a las rutas exclusivas del Administrador (HTTP 403).
     */
    public function test_docente_cannot_access_admin_routes(): void
    {
        $docente = User::factory()->create(['rol' => 'docente']);

        $this->actingAs($docente)->get('/admin/panel')->assertStatus(403);
        $this->actingAs($docente)->get('/admin/usuarios')->assertStatus(403);
        $this->actingAs($docente)->get('/admin/cursos')->assertStatus(403);
        $this->actingAs($docente)->get('/admin/carga-datos')->assertStatus(403);
        $this->actingAs($docente)->get('/admin/informes')->assertStatus(403);
    }

    /**
     * Docente NO puede acceder al panel exclusivo del Coordinador (HTTP 403).
     */
    public function test_docente_cannot_access_coordinador_panel(): void
    {
        $docente = User::factory()->create(['rol' => 'docente']);

        $this->actingAs($docente)->get('/coordinador/panel')->assertStatus(403);
    }

    /**
     * Coordinador puede acceder a su panel exclusivo de Coordinación.
     */
    public function test_coordinador_can_access_coordinador_panel(): void
    {
        $coordinador = User::factory()->coordinador()->create();

        $this->actingAs($coordinador)->get('/coordinador/panel')->assertStatus(200);
        $this->actingAs($coordinador)->get('/coordinador/informes-coordinacion/crear')->assertStatus(200);
    }

    /**
     * Coordinador puede acceder a las rutas compartidas de docente (Numeral 3.1).
     */
    public function test_coordinador_can_access_shared_docente_routes(): void
    {
        $coordinador = User::factory()->coordinador()->create();

        $this->actingAs($coordinador)->get('/docente/perfil')->assertStatus(200);
        $this->actingAs($coordinador)->get('/docente/informes')->assertStatus(200);
        $this->actingAs($coordinador)->get('/docente/informes/crear')->assertStatus(200);
    }

    /**
     * Coordinador NO puede acceder al panel del Administrador (HTTP 403).
     */
    public function test_coordinador_cannot_access_admin_panel(): void
    {
        $coordinador = User::factory()->coordinador()->create();

        $this->actingAs($coordinador)->get('/admin/panel')->assertStatus(403);
        $this->actingAs($coordinador)->get('/admin/usuarios')->assertStatus(403);
    }

    /**
     * Administrador puede acceder a todos los módulos administrativos.
     */
    public function test_administrador_can_access_admin_routes(): void
    {
        $admin = User::factory()->administrador()->create();

        $this->actingAs($admin)->get('/admin/panel')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/usuarios')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/cursos')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/carga-datos')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/informes')->assertStatus(200);
    }
}
