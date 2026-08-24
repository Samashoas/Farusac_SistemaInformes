<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AdminController extends Controller
{
    public function dashboard(){
        return view('admin.dashboard');
    }

    public function cargaDatosView(){
        return view('admin.carga_datos');
    }

    public function usuariosView(){
        $usuarios = User::all();
        return view('admin.usuarios', compact('usuarios'));
    }

    public function crearUsuario(Request $request){
        $request->validate([
            'nombre' => 'required|string|max:150',
            'correo' => 'required|email|max:100|unique:usuarios,correo',
            'numero' => 'required|string|max:30',
            'rol' => 'required|in:docente,jefe,administrador',
            'plaza' => 'required|in:titular + ampliacion,titular,interino',
        ]);

        $usuario = User::create([
            'nombre' => $request->nombre,
            'correo' => $request->correo,
            'numero' => $request->numero,
            'rol' => $request->rol,
            'plaza' => $request->plaza,
            'estado' => 'activo',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Usuario creado exitosamente',
            'usuario' => $usuario
        ]);
    }

    public function editarUsuario(Request $request, $id){
        $usuario = User::findOrFail($id);

        $request->validate([
            'numero' => 'required|string|max:30',
            'rol' => 'required|in:docente,jefe,administrador',
            'plaza' => 'required|in:titular + ampliacion,titular,interino',
        ]);

        $usuario->update([
            'numero' => $request->numero,
            'rol' => $request->rol,
            'plaza' => $request->plaza,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Usuario actualizado exitosamente',
            'usuario' => $usuario
        ]);
    }

    public function toggleEstadoUsuario($id){
        $usuario = User::findOrFail($id);
        
        $nuevoEstado = ($usuario->estado === 'activo') ? 'inactivo' : 'activo';
        $usuario->update([
            'estado' => $nuevoEstado
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Estado del usuario modificado exitosamente',
            'usuario' => $usuario
        ]);
    }

    public function CargaUsuariosCsv(Request $request){
        $request -> validate([
            'archivo_csv' => 'required|mimes:csv,txt|max:5120',
        ]);

        $archivo = $request -> file('archivo_csv');
        $handle = fopen($archivo->getRealPath(), 'r');

        $encabezados = fgetcsv($handle, 1000, ',');

        $contador = 0;

        while(($fila = fgetcsv($handle,1000,',')) !== FALSE){
            if(count($fila) >= 3){
                $correo = trim($fila[1]);

                User::updateOrCreate(
                    ['correo' => $correo],
                    [
                        'nombre' => trim($fila[0]),
                        'rol' => strtolower(trim($fila[2])),
                    ]
                );
                $contador++;
            }
        }

        fclose($handle);

        return back()->with('exito', "Se han cargado $contador usuarios exitosamente.");
    }
}
