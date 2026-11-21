<?php

namespace Tests\Feature;

use App\Models\Curso;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Administrador puede registrar un nuevo usuario con datos válidos.
     */
    public function test_admin_can_create_user_with_valid_data(): void
    {
        $admin = User::factory()->administrador()->create();

        $payload = [
            'nombre' => 'Carlos Mendoza',
            'correo' => 'carlos.mendoza@farusac.edu.gt',
            'numero' => '20150012',
            'rol'    => 'docente',
            'plaza'  => 'titular',
        ];

        $response = $this->actingAs($admin)->postJson('/admin/usuarios', $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('usuarios', [
            'correo' => 'carlos.mendoza@farusac.edu.gt',
            'rol'    => 'docente',
            'plaza'  => 'titular',
        ]);
    }

    /**
     * El endpoint de creación de usuario valida campos requeridos y unicidad del correo.
     */
    public function test_admin_create_user_validates_required_fields(): void
    {
        $admin = User::factory()->administrador()->create();

        $response = $this->actingAs($admin)->postJson('/admin/usuarios', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['nombre', 'correo', 'numero', 'rol', 'plaza']);
    }

    /**
     * Administrador puede editar los datos de un usuario existente.
     */
    public function test_admin_can_edit_user(): void
    {
        $admin = User::factory()->administrador()->create();
        $user = User::factory()->create([
            'numero' => '11111111',
            'rol'    => 'docente',
            'plaza'  => 'interino',
        ]);

        $payload = [
            'numero' => '99999999',
            'rol'    => 'coordinador',
            'plaza'  => 'titular',
        ];

        $response = $this->actingAs($admin)->putJson("/admin/usuarios/{$user->id}", $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('usuarios', [
            'id'     => $user->id,
            'numero' => '99999999',
            'rol'    => 'coordinador',
            'plaza'  => 'titular',
        ]);
    }

    /**
     * Administrador puede alternar el estado activo / inactivo de un usuario.
     */
    public function test_admin_can_toggle_user_status(): void
    {
        $admin = User::factory()->administrador()->create();
        $user = User::factory()->create(['estado' => 'activo']);

        $response = $this->actingAs($admin)->postJson("/admin/usuarios/{$user->id}/toggle-status");

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('usuarios', [
            'id'     => $user->id,
            'estado' => 'inactivo',
        ]);

        // Volver a activar
        $this->actingAs($admin)->postJson("/admin/usuarios/{$user->id}/toggle-status");

        $this->assertDatabaseHas('usuarios', [
            'id'     => $user->id,
            'estado' => 'activo',
        ]);
    }

    /**
     * Administrador puede crear y posteriormente eliminar un curso.
     */
    public function test_admin_can_create_and_delete_course(): void
    {
        $admin = User::factory()->administrador()->create();

        $payload = [
            'carrera'      => 'Arquitectura',
            'area'         => 'Diseño',
            'nombre_curso' => 'Teoría del Diseño 1',
            'codigo_curso' => '101',
            'seccion'      => 'A',
            'anio'         => 2026,
            'semestre'     => 'Primer Semestre',
        ];

        $responseCreate = $this->actingAs($admin)->postJson('/admin/cursos', $payload);

        $responseCreate->assertStatus(200);
        $responseCreate->assertJson(['success' => true]);

        $this->assertDatabaseHas('cursos', [
            'codigo_curso' => '101',
            'seccion'      => 'A',
        ]);

        $curso = Curso::where('codigo_curso', '101')->first();

        $responseDelete = $this->actingAs($admin)->deleteJson("/admin/cursos/{$curso->id}");

        $responseDelete->assertStatus(200);
        $responseDelete->assertJson(['success' => true]);

        $this->assertDatabaseMissing('cursos', ['id' => $curso->id]);
    }

    /**
     * Administrador puede importar usuarios masivamente mediante archivo CSV válido.
     */
    public function test_admin_can_import_usuarios_csv(): void
    {
        $admin = User::factory()->administrador()->create();

        $csvContent = "Nombre,Correo,Numero,Plaza\n"
                    . "Ana Gomez,ana.gomez@farusac.edu.gt,20180001,Titular\n"
                    . "Mario Ruiz,mario.ruiz@farusac.edu.gt,20180002,Interino\n";

        $file = UploadedFile::fake()->createWithContent('usuarios.csv', $csvContent);

        $response = $this->actingAs($admin)->postJson('/admin/importar-usuarios', [
            'archivo_csv' => $file,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('usuarios', [
            'correo' => 'ana.gomez@farusac.edu.gt',
            'plaza'  => 'Titular',
        ]);

        $this->assertDatabaseHas('usuarios', [
            'correo' => 'mario.ruiz@farusac.edu.gt',
            'plaza'  => 'Interino',
        ]);
    }

    /**
     * El endpoint de importación rechaza archivos con formato incorrecto.
     */
    public function test_import_usuarios_rejects_invalid_file_format(): void
    {
        $admin = User::factory()->administrador()->create();

        $fakePdf = UploadedFile::fake()->create('documento.pdf', 100, 'application/pdf');

        $response = $this->actingAs($admin)->postJson('/admin/importar-usuarios', [
            'archivo_csv' => $fakePdf,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['archivo_csv']);
    }

    /**
     * Administrador puede importar cursos masivamente mediante archivo CSV válido.
     */
    public function test_admin_can_import_cursos_csv(): void
    {
        $admin = User::factory()->administrador()->create();

        $csvContent = "Carrera,Area,NombreCurso,Codigo,Seccion,Anio,Semestre\n"
                    . "Arquitectura,Diseno,Geometria Descriptiva,102,B,2026,Primer Semestre\n";

        $file = UploadedFile::fake()->createWithContent('cursos.csv', $csvContent);

        $response = $this->actingAs($admin)->postJson('/admin/importar-cursos', [
            'archivo_csv' => $file,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('cursos', [
            'codigo_curso' => '102',
            'seccion'      => 'B',
        ]);
    }
}
