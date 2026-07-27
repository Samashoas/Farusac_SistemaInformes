<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller{

    public function ShowLogin(){
        return view('auth.login');
    }

    public function login (Request $request){
        $request ->validate([
            'correo' => 'required|email',
        ]);

        $user = User::where('correo', $request->correo) -> first();

        if($user){
            return $this-> redirectBasedOnRole($user -> rol);
        }

        return back() -> withErrors([
            'correo' => 'Correo electronico no encontrado',
        ]);
    }

    private function redirectBasedOnRole($role){
        switch ($role){
            case 'administrador':
                return redirect()->route('admin.dashboard');
            case 'jefe':
                return redirect()->route('jefe.dashboard');
            case 'docente':
                return redirect()->route('docente.dashboard');
            default:
                Auth::logout();
                return redirect('/')->withErrors(['correo' => 'Rol invalido']);
        }
    }

    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}