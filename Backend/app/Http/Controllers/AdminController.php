<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AdminController extends Controller
{
    public function dashboard(){
        return view('admin.dashboard');
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
