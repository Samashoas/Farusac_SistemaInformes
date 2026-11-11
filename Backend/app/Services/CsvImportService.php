<?php

namespace App\Services;

use App\Enums\Rol;
use App\Models\Curso;
use App\Models\User;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class CsvImportService
{
    /**
     * Procesa la carga masiva de usuarios desde un archivo CSV.
     * Formato esperado de columnas: [Nombre, Correo, Número/Registro, Plaza]
     */
    public function importarUsuarios(UploadedFile $archivo): array
    {
        $handle = fopen($archivo->getRealPath(), 'r');
        if ($handle === false) {
            throw new Exception('No se pudo abrir el archivo CSV de usuarios.');
        }

        // Leer fila de encabezados
        fgetcsv($handle, 1000, ',');

        $success_records = [];
        $error_lines = [];
        $line_number = 1;
        $plazas_validas = ['Titular', 'Titular+Ampliacion', 'Interino'];

        while (($fila = fgetcsv($handle, 1000, ',')) !== false) {
            $line_number++;
            if (count($fila) >= 4) {
                $nombre = trim($fila[0]);
                $correo = trim($fila[1]);
                $numero = trim($fila[2]);
                $plaza  = trim($fila[3]);

                $es_correo_valido = filter_var($correo, FILTER_VALIDATE_EMAIL) && str_ends_with(strtolower($correo), '@farusac.edu.gt');

                if (!empty($nombre) && $es_correo_valido && in_array($plaza, $plazas_validas)) {
                    try {
                        $usuario = User::updateOrCreate(
                            ['correo' => $correo],
                            [
                                'nombre' => $nombre,
                                'numero' => $numero,
                                'plaza'  => $plaza,
                                'rol'    => Rol::Docente,
                            ]
                        );

                        $success_records[] = [
                            'id'     => $usuario->id,
                            'nombre' => $usuario->nombre,
                            'correo' => $usuario->correo,
                            'numero' => $usuario->numero ?? 'N/D',
                            'rol'    => $usuario->rol instanceof Rol ? $usuario->rol->value : (string) $usuario->rol,
                            'plaza'  => $usuario->plaza ?? 'N/D',
                            'estado' => $usuario->estado,
                        ];
                    } catch (Exception $e) {
                        $error_lines[] = "Línea $line_number: " . implode(',', $fila) . " (Error en base de datos: " . $e->getMessage() . ")";
                    }
                } else {
                    $error_reason = '';
                    if (empty($nombre)) {
                        $error_reason = 'Nombre vacío';
                    } elseif (!$es_correo_valido) {
                        $error_reason = 'Correo inválido o no es @farusac.edu.gt';
                    } elseif (!in_array($plaza, $plazas_validas)) {
                        $error_reason = 'Plaza inválida (Debe ser: Titular, Titular+Ampliacion o Interino)';
                    }
                    $error_lines[] = "Línea $line_number: " . implode(',', $fila) . " ($error_reason)";
                }
            } else {
                $error_lines[] = "Línea $line_number: " . implode(',', $fila) . " (Columnas insuficientes, requiere 4)";
            }
        }

        fclose($handle);

        return [
            'success'         => true,
            'success_count'   => count($success_records),
            'error_count'     => count($error_lines),
            'success_records' => $success_records,
            'error_lines'     => $error_lines,
        ];
    }

    /**
     * Procesa la carga masiva de cursos desde un archivo CSV.
     * Formato esperado: [Carrera, Área, Nombre Curso, Código Curso, Sección, Año, Semestre]
     */
    public function importarCursos(UploadedFile $archivo): array
    {
        $handle = fopen($archivo->getRealPath(), 'r');
        if ($handle === false) {
            throw new Exception('No se pudo abrir el archivo CSV de cursos.');
        }

        // Leer fila de encabezados
        fgetcsv($handle, 1000, ',');

        $success_records = [];
        $error_lines = [];
        $line_number = 1;

        $carreras_validas  = ['Arquitectura', 'Diseño Gráfico'];
        $semestres_validos = ['Primer Semestre', 'Segundo Semestre', 'Vacaciones Junio', 'Vacaciones Diciembre'];

        while (($fila = fgetcsv($handle, 1000, ',')) !== false) {
            $line_number++;
            if (count($fila) >= 7) {
                $carrera      = trim($fila[0]);
                $area         = trim($fila[1]);
                $nombre_curso = trim($fila[2]);
                $codigo_curso = trim($fila[3]);
                $seccion      = trim($fila[4]);
                $anio         = intval(trim($fila[5]));
                $semestre     = trim($fila[6]);

                if (in_array($carrera, $carreras_validas) && !empty($nombre_curso) && !empty($seccion) && !empty($codigo_curso) && $anio > 0 && in_array($semestre, $semestres_validos)) {
                    try {
                        $curso = Curso::updateOrCreate(
                            [
                                'carrera'      => $carrera,
                                'codigo_curso' => $codigo_curso,
                                'seccion'      => $seccion,
                                'anio'         => $anio,
                                'semestre'     => $semestre,
                            ],
                            [
                                'area'         => $area,
                                'nombre_curso' => $nombre_curso,
                            ]
                        );

                        $success_records[] = [
                            'id'       => $curso->id,
                            'carrera'  => $curso->carrera,
                            'area'     => $curso->area,
                            'curso'    => $curso->nombre_curso,
                            'codigo'   => $curso->codigo_curso,
                            'seccion'  => $curso->seccion,
                            'jornada'  => (strtoupper($curso->seccion) === 'B') ? 'Vespertina' : 'Matutina',
                            'semestre' => $curso->semestre,
                            'anio'     => $curso->anio,
                        ];
                    } catch (Exception $e) {
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

        return [
            'success'         => true,
            'success_count'   => count($success_records),
            'error_count'     => count($error_lines),
            'success_records' => $success_records,
            'error_lines'     => $error_lines,
        ];
    }

    /**
     * Carga Masiva Integral de la Carga Académica (Docentes, Cursos y Asignaciones Directas).
     * Procesa el reporte oficial FARUSAC (ej. CA Detallada OCT2026.csv).
     * Aplica la regla de coexistencia:
     * - Semestre regular y vacaciones del mismo semestre pueden coexistir.
     * - Semestres diferentes no pueden coexistir (sus asignaciones y cursos no vinculados se limpian).
     */
    public function importarCargaAcademica(UploadedFile $archivo, ?string $carreraReq = null, ?int $anioReq = null, ?string $semestreReq = null): array
    {
        $rawContent = file_get_contents($archivo->getRealPath());
        if ($rawContent === false) {
            return [
                'success' => false,
                'message' => 'No se pudo leer el contenido del archivo de carga académica.',
                'status'  => 400,
            ];
        }

        // Limpiar BOM UTF-8 si existe
        if (str_starts_with($rawContent, "\xEF\xBB\xBF")) {
            $rawContent = substr($rawContent, 3);
        }

        // Convertir encoding a UTF-8 si viene en ISO-8859-1 o Windows-1252
        if (!mb_check_encoding($rawContent, 'UTF-8')) {
            $rawContent = mb_convert_encoding($rawContent, 'UTF-8', 'ISO-8859-1, Windows-1252, UTF-8');
        }

        // Dividir en líneas normalizadas
        $lineas = preg_split('/\r\n|\r|\n/', $rawContent);

        $headerRowIndex    = null;
        $columnIndices     = [];
        $carreraDetectada  = null;
        $semestreDetectado = null;
        $anioDetectado     = null;

        // Escanear las primeras 15 líneas para detectar metadatos y la fila de encabezados
        $maxScan = min(15, count($lineas));
        for ($i = 0; $i < $maxScan; $i++) {
            $lineaStr = trim($lineas[$i]);
            if (empty($lineaStr)) {
                continue;
            }

            // Detectar Carrera en las líneas previas al encabezado
            if ($headerRowIndex === null) {
                if (stripos($lineaStr, 'diseño gráfico') !== false || stripos($lineaStr, 'diseno grafico') !== false) {
                    $carreraDetectada = 'Diseño Gráfico';
                } elseif (stripos($lineaStr, 'arquitectura') !== false && !$carreraDetectada) {
                    $carreraDetectada = 'Arquitectura';
                }

                // Detectar Año (4 dígitos tipo 20XX)
                if (preg_match('/\b(20\d{2})\b/', $lineaStr, $matchesAnio)) {
                    $anioDetectado = intval($matchesAnio[1]);
                }

                // Detectar Semestre / Periodo
                if (preg_match('/vacaciones.*(?:junio|primer|1)/i', $lineaStr)) {
                    $semestreDetectado = 'Vacaciones Junio';
                } elseif (preg_match('/vacaciones.*(?:diciembre|segundo|2)/i', $lineaStr)) {
                    $semestreDetectado = 'Vacaciones Diciembre';
                } elseif (preg_match('/primer\s+semestre|1(?:er|er\.|ro)?\s+semestre/i', $lineaStr)) {
                    $semestreDetectado = 'Primer Semestre';
                } elseif (preg_match('/segundo\s+semestre|2(?:do|do\.)?\s+semestre/i', $lineaStr)) {
                    $semestreDetectado = 'Segundo Semestre';
                }
            }

            // Convertir línea a CSV para verificar si es la fila de encabezados
            $cols = str_getcsv($lineaStr, ',');
            $colsNormalizadas = array_map(function ($c) {
                $c = mb_strtolower(trim($c));
                return strtr($c, ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ñ' => 'n']);
            }, $cols);

            $tieneDocente = false;
            $tieneCurso   = false;
            $tieneCorreo  = false;
            foreach ($colsNormalizadas as $idx => $nombreCol) {
                if (str_contains($nombreCol, 'docente') || str_contains($nombreCol, 'profesor')) {
                    $tieneDocente = true;
                }
                if (str_contains($nombreCol, 'asignatura') || str_contains($nombreCol, 'curso')) {
                    $tieneCurso = true;
                }
                if (str_contains($nombreCol, 'correo') || str_contains($nombreCol, 'email')) {
                    $tieneCorreo = true;
                }
            }

            if ($tieneDocente && ($tieneCurso || $tieneCorreo)) {
                $headerRowIndex = $i;
                foreach ($colsNormalizadas as $idx => $nombreCol) {
                    if (str_contains($nombreCol, 'area')) {
                        $columnIndices['area'] = $idx;
                    } elseif (str_contains($nombreCol, 'ciclo')) {
                        $columnIndices['ciclo'] = $idx;
                    } elseif (str_contains($nombreCol, 'codigo')) {
                        $columnIndices['codigo'] = $idx;
                    } elseif (str_contains($nombreCol, 'asignatura') || str_contains($nombreCol, 'curso')) {
                        $columnIndices['asignatura'] = $idx;
                    } elseif (str_contains($nombreCol, 'seccion')) {
                        $columnIndices['seccion'] = $idx;
                    } elseif (str_contains($nombreCol, 'jornada')) {
                        $columnIndices['jornada'] = $idx;
                    } elseif (str_contains($nombreCol, 'docente') || str_contains($nombreCol, 'profesor')) {
                        $columnIndices['docente'] = $idx;
                    } elseif (str_contains($nombreCol, 'registro')) {
                        $columnIndices['registro'] = $idx;
                    } elseif (str_contains($nombreCol, 'correo') || str_contains($nombreCol, 'email')) {
                        $columnIndices['correo'] = $idx;
                    }
                }
                break;
            }
        }

        if ($headerRowIndex === null || !isset($columnIndices['asignatura']) || !isset($columnIndices['docente']) || !isset($columnIndices['correo'])) {
            return [
                'success' => false,
                'message' => 'No se encontraron las columnas requeridas (Asignatura, Docente, Correo) en el archivo CSV. Verifique que sea el formato oficial de Carga Académica.',
                'status'  => 422,
            ];
        }

        // Determinar Carrera, Año y Semestre finales (respetando parámetros si el admin los seleccionó manualmente)
        $carrera = (!empty($carreraReq) && $carreraReq !== 'detectar') ? $carreraReq : ($carreraDetectada ?: 'Arquitectura');
        $anio = (!empty($anioReq) && $anioReq !== 0) ? intval($anioReq) : ($anioDetectado ?: intval(date('Y')));
        $semestre = (!empty($semestreReq) && $semestreReq !== 'detectar') ? $semestreReq : ($semestreDetectado ?: 'Segundo Semestre');

        // Normalización exacta del semestre
        if (preg_match('/vacaciones.*(?:junio|primer|1)/i', $semestre)) {
            $semestre = 'Vacaciones Junio';
        } elseif (preg_match('/vacaciones.*(?:diciembre|segundo|2)/i', $semestre)) {
            $semestre = 'Vacaciones Diciembre';
        } elseif (preg_match('/primer\s+semestre|1/i', $semestre)) {
            $semestre = 'Primer Semestre';
        } elseif (preg_match('/segundo\s+semestre|2/i', $semestre)) {
            $semestre = 'Segundo Semestre';
        }

        // =========================================================================
        // REGLA DE COEXISTENCIA:
        // Ciclo 1: Primer Semestre (Enero) + Vacaciones Junio (Vacaciones 1er sem)
        // Ciclo 2: Segundo Semestre (Julio) + Vacaciones Diciembre (Vacaciones 2do sem)
        // Semestres de ciclos diferentes no pueden coexistir.
        // =========================================================================
        $allowedSemesters = [];
        if ($semestre === 'Primer Semestre' || $semestre === 'Vacaciones Junio') {
            $allowedSemesters = ['Primer Semestre', 'Vacaciones Junio'];
            $cicloNombre = 'Ciclo 1: Primer Semestre y Vacaciones de Junio';
        } else {
            $allowedSemesters = ['Segundo Semestre', 'Vacaciones Diciembre'];
            $cicloNombre = 'Ciclo 2: Segundo Semestre y Vacaciones de Diciembre';
        }

        // 1. Encontrar cursos que pertenecen a un ciclo incompatible o año diferente
        $incompatibleCourseIds = Curso::where('carrera', $carrera)
            ->where(function ($q) use ($anio, $allowedSemesters) {
                $q->where('anio', '!=', $anio)
                  ->orWhereNotIn('semestre', $allowedSemesters);
            })
            ->pluck('id')
            ->toArray();

        $cursosIncompatiblesDesasignados = 0;
        if (!empty($incompatibleCourseIds)) {
            // Desasignar a los docentes de los cursos incompatibles
            $cursosIncompatiblesDesasignados = DB::table('docente_cursos')
                ->whereIn('curso_id', $incompatibleCourseIds)
                ->delete();

            // Eliminar de cursos aquellos que no tengan informes históricos para mantener limpia la BD
            Curso::whereIn('id', $incompatibleCourseIds)
                ->whereDoesntHave('informes')
                ->delete();
        }

        // 2. Para el periodo específico actual ($carrera, $anio, $semestre):
        // Limpiar asignaciones previas para reflejar fielmente la nueva carga del CSV
        $currentPeriodCourseIds = Curso::where('carrera', $carrera)
            ->where('anio', $anio)
            ->where('semestre', $semestre)
            ->pluck('id')
            ->toArray();

        if (!empty($currentPeriodCourseIds)) {
            DB::table('docente_cursos')
                ->whereIn('curso_id', $currentPeriodCourseIds)
                ->delete();
        }

        $success_records = [];
        $error_lines = [];
        $docentesMap = [];
        $cursosMap = [];

        DB::beginTransaction();
        try {
            $totalLineas = count($lineas);
            for ($i = $headerRowIndex + 1; $i < $totalLineas; $i++) {
                $lineaStr = trim($lineas[$i]);
                if (empty($lineaStr)) {
                    continue;
                }

                $fila = str_getcsv($lineaStr, ',');
                $line_number = $i + 1;

                $asignatura       = isset($columnIndices['asignatura']) ? trim($fila[$columnIndices['asignatura']] ?? '') : '';
                $docenteNombre    = isset($columnIndices['docente']) ? trim($fila[$columnIndices['docente']] ?? '') : '';
                $correo           = isset($columnIndices['correo']) ? strtolower(trim($fila[$columnIndices['correo']] ?? '')) : '';
                $codigoCurso      = isset($columnIndices['codigo']) ? trim($fila[$columnIndices['codigo']] ?? '') : '';
                $seccion          = isset($columnIndices['seccion']) ? trim($fila[$columnIndices['seccion']] ?? '') : '';
                $area             = isset($columnIndices['area']) ? trim($fila[$columnIndices['area']] ?? '') : '';
                $registroPersonal = isset($columnIndices['registro']) ? trim($fila[$columnIndices['registro']] ?? '') : '';
                $jornadaCsv       = isset($columnIndices['jornada']) ? strtoupper(trim($fila[$columnIndices['jornada']] ?? '')) : '';

                // Validaciones por fila
                if (empty($docenteNombre)) {
                    $error_lines[] = "Línea $line_number: Nombre del docente ausente.";
                    continue;
                }

                if (empty($correo) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                    $error_lines[] = "Línea $line_number: Correo electrónico inválido o ausente ('$correo') para el docente $docenteNombre.";
                    continue;
                }

                if (empty($asignatura) || empty($codigoCurso) || empty($seccion)) {
                    $error_lines[] = "Línea $line_number: Datos incompletos del curso (Código: '$codigoCurso', Asignatura: '$asignatura', Sección: '$seccion').";
                    continue;
                }

                if (empty($area)) {
                    $area = 'Área General';
                }

                // Determinar jornada (Matutina / Vespertina)
                $jornada = 'Matutina';
                if ($jornadaCsv === 'V' || str_starts_with($jornadaCsv, 'VESP') ||
                    str_starts_with(strtoupper($seccion), 'V') ||
                    str_starts_with(strtoupper($seccion), 'F') ||
                    str_starts_with(strtoupper($seccion), 'G') ||
                    str_starts_with(strtoupper($seccion), 'PG') ||
                    strtoupper($seccion) === 'B') {
                    $jornada = 'Vespertina';
                }

                // 1. Obtener o crear Docente (sin sobreescribir roles especiales como coordinador o admin)
                if (!isset($docentesMap[$correo])) {
                    $usuario = User::where('correo', $correo)->first();
                    if (!$usuario) {
                        $usuario = User::create([
                            'nombre' => $docenteNombre,
                            'correo' => $correo,
                            'numero' => $registroPersonal ?: null,
                            'rol'    => Rol::Docente,
                            'plaza'  => 'Titular',
                            'estado' => 'activo',
                        ]);
                    } else {
                        $usuario->nombre = $docenteNombre;
                        if (!empty($registroPersonal) && empty($usuario->numero)) {
                            $usuario->numero = $registroPersonal;
                        }
                        $usuario->save();
                    }
                    $docentesMap[$correo] = $usuario;
                } else {
                    $usuario = $docentesMap[$correo];
                }

                // 2. Obtener o crear Curso
                $cursoKey = "$codigoCurso-$seccion";
                if (!isset($cursosMap[$cursoKey])) {
                    $curso = Curso::updateOrCreate(
                        [
                            'carrera'      => $carrera,
                            'codigo_curso' => $codigoCurso,
                            'seccion'      => $seccion,
                            'anio'         => $anio,
                            'semestre'     => $semestre,
                        ],
                        [
                            'area'         => $area,
                            'nombre_curso' => $asignatura,
                        ]
                    );
                    $cursosMap[$cursoKey] = $curso;
                } else {
                    $curso = $cursosMap[$cursoKey];
                }

                // 3. Vincular docente con curso en docente_cursos
                DB::table('docente_cursos')->updateOrInsert(
                    [
                        'usuario_id' => $usuario->id,
                        'curso_id'   => $curso->id,
                    ],
                    [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );

                // 4. Registro único para reporte
                $asignacionKey = $curso->id . '-' . $usuario->id;
                if (!isset($success_records[$asignacionKey])) {
                    $success_records[$asignacionKey] = [
                        'docente_id' => $usuario->id,
                        'docente'    => $usuario->nombre,
                        'correo'     => $usuario->correo,
                        'numero'     => $usuario->numero ?: 'N/D',
                        'curso_id'   => $curso->id,
                        'curso'      => $curso->nombre_curso,
                        'codigo'     => $curso->codigo_curso,
                        'seccion'    => $curso->seccion,
                        'jornada'    => $jornada,
                        'area'       => $curso->area,
                        'carrera'    => $curso->carrera,
                        'semestre'   => $curso->semestre,
                        'anio'       => $curso->anio,
                    ];
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Ocurrió un error al procesar la carga académica: ' . $e->getMessage(),
                'status'  => 500,
            ];
        }

        return [
            'success'           => true,
            'status'            => 200,
            'periodo'           => "$semestre $anio",
            'carrera'           => $carrera,
            'semestre'          => $semestre,
            'anio'              => $anio,
            'ciclo_nombre'      => $cicloNombre,
            'coexistencia_info' => "Periodo activo cargado: $semestre $anio ($carrera). Puede coexistir con: " . implode(' y ', $allowedSemesters) . ". Cursos de semestres diferentes desasignados: $cursosIncompatiblesDesasignados.",
            'total_docentes'    => count($docentesMap),
            'total_cursos'      => count($cursosMap),
            'success_count'     => count($success_records),
            'error_count'       => count($error_lines),
            'success_records'   => array_values($success_records),
            'error_lines'       => $error_lines,
        ];
    }
}

