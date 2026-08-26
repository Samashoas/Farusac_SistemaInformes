<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Curso;

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

    public function cursosView(){
        $cursos = Curso::all();
        return view('admin.cursos', compact('cursos'));
    }

    public function crearCurso(Request $request){
        $request->validate([
            'carrera' => 'required|string|max:100',
            'area' => 'required|string|max:150',
            'nombre_curso' => 'required|string|max:150',
            'codigo_curso' => 'required|integer',
            'seccion' => 'required|string|max:10',
            'anio' => 'required|integer',
            'semestre' => 'required|string|max:30',
        ]);

        // Evitar duplicados basados en UNIQUE KEY uq_curso_periodo (carrera, codigo_curso, seccion, anio, semestre)
        $existe = Curso::where('carrera', $request->carrera)
            ->where('codigo_curso', $request->codigo_curso)
            ->where('seccion', $request->seccion)
            ->where('anio', $request->anio)
            ->where('semestre', $request->semestre)
            ->first();

        if ($existe) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe un registro con la misma Carrera, Código, Sección, Año y Semestre.'
            ], 422);
        }

        $curso = Curso::create([
            'carrera' => $request->carrera,
            'area' => $request->area,
            'nombre_curso' => $request->nombre_curso,
            'codigo_curso' => $request->codigo_curso,
            'seccion' => $request->seccion,
            'anio' => $request->anio,
            'semestre' => $request->semestre,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Curso creado exitosamente',
            'curso' => $curso
        ]);
    }

    public function eliminarCurso($id){
        $curso = Curso::findOrFail($id);
        $curso->delete();

        return response()->json([
            'success' => true,
            'message' => 'Curso eliminado exitosamente'
        ]);
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
        $request->validate([
            'archivo_csv' => 'required|mimes:csv,txt|max:5120',
        ]);

        $archivo = $request->file('archivo_csv');
        $handle = fopen($archivo->getRealPath(), 'r');

        $encabezados = fgetcsv($handle, 1000, ',');

        $success_records = [];
        $error_lines = [];
        $line_number = 1;

        while(($fila = fgetcsv($handle, 1000, ',')) !== FALSE){
            $line_number++;
            if(count($fila) >= 4){
                $nombre = trim($fila[0]);
                $correo = trim($fila[1]);
                $numero = trim($fila[2]);
                $plaza = trim($fila[3]);

                $es_correo_valido = filter_var($correo, FILTER_VALIDATE_EMAIL) && str_ends_with(strtolower($correo), '@farusac.edu.gt');
                $plazas_validas = ['Titular', 'Titular+Ampliacion', 'Interino'];

                if(!empty($nombre) && $es_correo_valido && in_array($plaza, $plazas_validas)){
                    try {
                        $usuario = User::updateOrCreate(
                            ['correo' => $correo],
                            [
                                'nombre' => $nombre,
                                'numero' => $numero,
                                'plaza' => $plaza,
                                'rol' => 'docente' // Por defecto
                            ]
                        );
                        $success_records[] = [
                            'id' => $usuario->id,
                            'nombre' => $usuario->nombre,
                            'correo' => $usuario->correo,
                            'numero' => $usuario->numero ?? 'N/D',
                            'rol' => $usuario->rol,
                            'plaza' => $usuario->plaza ?? 'N/D',
                            'estado' => $usuario->estado,
                        ];
                    } catch (\Exception $e) {
                        $error_lines[] = "Línea $line_number: " . implode(',', $fila) . " (Error en base de datos: " . $e->getMessage() . ")";
                    }
                } else {
                    $error_reason = "";
                    if (empty($nombre)) $error_reason = "Nombre vacío";
                    elseif (!$es_correo_valido) $error_reason = "Correo inválido o no es @farusac.edu.gt";
                    elseif (!in_array($plaza, $plazas_validas)) $error_reason = "Plaza inválida (Debe ser: Titular, Titular+Ampliacion o Interino)";
                    $error_lines[] = "Línea $line_number: " . implode(',', $fila) . " ($error_reason)";
                }
            } else {
                $error_lines[] = "Línea $line_number: " . implode(',', $fila) . " (Columnas insuficientes, requiere 4)";
            }
        }

        fclose($handle);

        return response()->json([
            'success' => true,
            'success_count' => count($success_records),
            'error_count' => count($error_lines),
            'success_records' => $success_records,
            'error_lines' => $error_lines
        ]);
    }

    public function CargaCursosCsv(Request $request){
        $request->validate([
            'archivo_csv' => 'required|mimes:csv,txt|max:5120',
        ]);

        $archivo = $request->file('archivo_csv');
        $handle = fopen($archivo->getRealPath(), 'r');

        $encabezados = fgetcsv($handle, 1000, ',');

        $success_records = [];
        $error_lines = [];
        $line_number = 1;

        while(($fila = fgetcsv($handle, 1000, ',')) !== FALSE){
            $line_number++;
            if(count($fila) >= 7){
                $carrera = trim($fila[0]);
                $area = trim($fila[1]);
                $nombre_curso = trim($fila[2]);
                $codigo_curso = intval(trim($fila[3]));
                $seccion = trim($fila[4]);
                $anio = intval(trim($fila[5]));
                $semestre = trim($fila[6]);

                $carreras_validas = ['Arquitectura', 'Diseño Gráfico'];
                $semestres_validos = ['Primer Semestre', 'Segundo Semestre', 'Vacaciones Junio', 'Vacaciones Diciembre'];

                if (in_array($carrera, $carreras_validas) && !empty($nombre_curso) && !empty($seccion) && $codigo_curso > 0 && $anio > 0 && in_array($semestre, $semestres_validos)) {
                    try {
                        $curso = Curso::updateOrCreate(
                            [
                                'carrera' => $carrera,
                                'codigo_curso' => $codigo_curso,
                                'seccion' => $seccion,
                                'anio' => $anio,
                                'semestre' => $semestre
                            ],
                            [
                                'area' => $area,
                                'nombre_curso' => $nombre_curso
                            ]
                        );
                        $success_records[] = [
                            'id' => $curso->id,
                            'carrera' => $curso->carrera,
                            'area' => $curso->area,
                            'curso' => $curso->nombre_curso,
                            'codigo' => $curso->codigo_curso,
                            'seccion' => $curso->seccion,
                            'jornada' => ($curso->seccion === 'B' || $curso->seccion === 'b') ? 'Vespertina' : 'Matutina',
                            'semestre' => $curso->semestre,
                            'anio' => $curso->anio
                        ];
                    } catch (\Exception $e) {
                        $error_lines[] = "Línea $line_number: " . implode(',', $fila) . " (Error en base de datos: " . $e->getMessage() . ")";
                    }
                } else {
                    $error_lines[] = "Línea $line_number: " . implode(',', $fila) . " (Datos inválidos)";
                }
            } else {
                $error_lines[] = "Línea $line_number: " . implode(',', $fila) . " (Columnas insuficientes)";
            }
        }

        fclose($handle);

        return response()->json([
            'success' => true,
            'success_count' => count($success_records),
            'error_count' => count($error_lines),
            'success_records' => $success_records,
            'error_lines' => $error_lines
        ]);
    }
}
