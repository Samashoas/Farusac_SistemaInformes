<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Enums\Rol;
use Exception;

class AuthController extends Controller{

    public function showLogin(){
        return view('auth.login');
    }

    public function redirect(){
        /** @var \Laravel\Socialite\Two\AbstractProvider $driver */
        $driver = Socialite::driver('google');
        return $driver
            ->with([
                'hd'     => config('services.google.allowed_domain'),
                'prompt' => 'select_account',
            ])
            ->redirect();
    }

    public function callback(){
        try{
            /** @var \Laravel\Socialite\Two\AbstractProvider $driver */
            $driver = Socialite::driver('google');
            $driver->setHttpClient(new \GuzzleHttp\Client([
                'verify' => config('services.google.ssl_cert_path'),
            ]));
            $googleUser = $driver->user();
        }catch (Exception $ex){
            Log::error('Error al iniciar sesión: ' . $ex->getMessage());
            return redirect('/')->withErrors(['correo' => 'Error al iniciar sesión. Intente nuevamente.']);
        }

        //Validar Acceso al dominio
        $allowedDomain = strtolower(config('services.google.allowed_domain'));
        $domain = explode('@', $googleUser->getEmail())[1] ?? '';

        if(strtolower($domain) !== $allowedDomain){
            return redirect('/')->withErrors(['correo' => 'Acceso denegado solo se permiten personas que pertenezcan a la institucion']);
        }

        //Buscar si el usuario está cargado en la base de datos
        $user = User::where('correo', $googleUser->getEmail())->first();

        if(!$user){
            // Registrar al usuario automáticamente con el rol de docente
            $user = User::create([
                'nombre' => $googleUser->getName(),
                'correo' => $googleUser->getEmail(),
                'rol' => Rol::Docente->value,
                'google_id' => $googleUser->getId(),
            ]);
        } else {
            // Vincular google_id si es el primer inicio de sesión del usuario
            if (!$user->google_id) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                ]);
            }
        }

        //Iniciar Sesion si todo está correcto y redirigir al panle correspondiente
        Auth::login($user);
        return $this->redirectBasedOnRole($user->rol);
    }
    
    private function redirectBasedOnRole($rol){
        $rolEnum = $rol instanceof Rol ? $rol : Rol::tryFrom(strtolower(trim((string) $rol)));
        return match($rolEnum){
            Rol::Administrador => redirect()->route('admin.dashboard'),
            Rol::Coordinador   => redirect()->route('coordinador.dashboard'),
            Rol::Docente       => redirect()->route('docente.dashboard'),
            default            => (function() use ($rol) {
                Auth::logout();
                $val = $rol instanceof Rol ? $rol->value : (string) $rol;
                return redirect('/')->withErrors(['correo' => 'Rol invalido (' . $val . ')']);
            })(),
        };
    }
    
    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}