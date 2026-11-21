<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * La ruta raíz debe redirigir al login.
     */
    public function test_root_redirects_to_login(): void
    {
        $response = $this->get('/');

        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }

    /**
     * La pantalla de login debe renderizarse correctamente.
     */
    public function test_login_screen_renders_successfully(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Sistema de Informes');
        $response->assertSee('Acceder con Google');
    }

    /**
     * El endpoint de redirección a Google OAuth debe invocar el driver de Socialite.
     */
    public function test_google_redirect_generates_oauth_redirect(): void
    {
        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('with')
            ->once()
            ->andReturnSelf();
        $provider->shouldReceive('redirect')
            ->once()
            ->andReturn(redirect('https://accounts.google.com/o/oauth2/v2/auth'));

        Socialite::shouldReceive('driver')
            ->with('google')
            ->once()
            ->andReturn($provider);

        $response = $this->get('/auth/google');

        $response->assertStatus(302);
        $response->assertRedirect('https://accounts.google.com/o/oauth2/v2/auth');
    }

    /**
     * Callback de Google con dominio institucional válido autentica e inicia sesión.
     */
    public function test_google_callback_with_valid_institutional_domain_authenticates_user(): void
    {
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('google-unique-id-123');
        $socialiteUser->shouldReceive('getName')->andReturn('Docente Institucional');
        $socialiteUser->shouldReceive('getEmail')->andReturn('docente.test@farusac.edu.gt');

        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('setHttpClient')->once()->andReturnSelf();
        $provider->shouldReceive('user')->once()->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')
            ->with('google')
            ->once()
            ->andReturn($provider);

        $response = $this->get('/auth/google/callback');

        $response->assertStatus(302);
        $response->assertRedirect(route('docente.dashboard'));
        $this->assertAuthenticated();

        $this->assertDatabaseHas('usuarios', [
            'correo' => 'docente.test@farusac.edu.gt',
            'rol'    => 'docente',
        ]);
    }

    /**
     * Callback de Google rechaza correos que no pertenezcan al dominio institucional.
     */
    public function test_google_callback_with_invalid_domain_is_rejected(): void
    {
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('google-external-id');
        $socialiteUser->shouldReceive('getName')->andReturn('Usuario Externo');
        $socialiteUser->shouldReceive('getEmail')->andReturn('usuario@gmail.com');

        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('setHttpClient')->once()->andReturnSelf();
        $provider->shouldReceive('user')->once()->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')
            ->with('google')
            ->once()
            ->andReturn($provider);

        $response = $this->get('/auth/google/callback');

        $response->assertStatus(302);
        $response->assertRedirect('/');
        $response->assertSessionHasErrors(['correo']);
        $this->assertGuest();
    }

    /**
     * Usuario autenticado puede cerrar sesión correctamente.
     */
    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    /**
     * Rutas protegidas redirigen al login para usuarios invitados.
     */
    public function test_guests_cannot_access_protected_panels(): void
    {
        $this->get('/docente/panel')->assertRedirect(route('login'));
        $this->get('/coordinador/panel')->assertRedirect(route('login'));
        $this->get('/admin/panel')->assertRedirect(route('login'));
    }
}
