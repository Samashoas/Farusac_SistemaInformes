<?php

namespace App\Services;

use App\Models\Informe;
use App\Models\InformeCoordinacion;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use ZipArchive;

class InformeExportService
{
    public function __construct(
        protected ChromiumPdfService $pdfService
    ) {}

    /**
     * Limpia nombres para archivos y carpetas en disco / dentro del ZIP.
     */
    public function limpiarNombreArchivo(?string $string): string
    {
        if (empty($string)) {
            return '';
        }
        $string = Str::ascii($string);
        $string = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $string);
        $string = preg_replace('/_+/', '_', $string);
        return trim($string, '_');
    }

    /**
     * Helper para filtrar informes según los parámetros recibidos.
     *
     * @param  Request|array  $filtros
     * @return array{docentes: \Illuminate\Support\Collection, coordinacion: \Illuminate\Support\Collection, mes: ?string, anio: ?string, total: int}
     */
    public function obtenerInformesFiltrados(Request|array $filtros): array
    {
        $get = fn(string $key, $default = null) => $filtros instanceof Request ? $filtros->input($key, $default) : ($filtros[$key] ?? $default);

        $mes     = $get('mes');
        $anio    = $get('anio');
        $tipo    = $get('tipo', 'todos');
        $carrera = $get('carrera');
        $area    = $get('area');

        $docentesQuery     = Informe::with(['usuario', 'curso', 'semanas']);
        $coordinacionQuery = InformeCoordinacion::with(['usuario', 'programas', 'asignaturas', 'avances', 'estudiantes']);

        if ($mes && strtolower($mes) !== 'todos' && strtolower($mes) !== 'general') {
            $docentesQuery->where(function ($q) use ($mes) {
                $q->whereRaw('LOWER(TRIM(mes)) = ?', [strtolower(trim($mes))])
                  ->orWhere('mes', 'LIKE', "%{$mes}%");
            });
            $coordinacionQuery->where(function ($q) use ($mes) {
                $q->whereRaw('LOWER(TRIM(mes)) = ?', [strtolower(trim($mes))])
                  ->orWhere('mes', 'LIKE', "%{$mes}%");
            });
        }

        if ($anio && strtolower($anio) !== 'todos') {
            $docentesQuery->where(function ($query) use ($anio) {
                $query->whereHas('curso', function ($q) use ($anio) {
                    $q->where('anio', $anio);
                })
                ->orWhere('periodo', 'LIKE', "%{$anio}%")
                ->orWhereYear('created_at', $anio);
            });
            $coordinacionQuery->where(function ($query) use ($anio) {
                $query->where('anio', $anio)
                      ->orWhere('periodo', 'LIKE', "%{$anio}%")
                      ->orWhereYear('created_at', $anio);
            });
        }

        if ($carrera && strtolower($carrera) !== 'todas' && $carrera !== '') {
            $docentesQuery->whereHas('curso', function ($q) use ($carrera) {
                $q->where('carrera', $carrera);
            });
        }

        if ($area && strtolower($area) !== 'todas' && $area !== '') {
            $docentesQuery->whereHas('curso', function ($q) use ($area) {
                $q->where('area', $area);
            });
            $coordinacionQuery->where('area', $area);
        }

        $informesDocentes     = ($tipo === 'coordinacion') ? collect() : $docentesQuery->orderBy('id', 'desc')->get();
        $informesCoordinacion = ($tipo === 'docente') ? collect() : $coordinacionQuery->orderBy('id', 'desc')->get();

        return [
            'docentes'     => $informesDocentes,
            'coordinacion' => $informesCoordinacion,
            'mes'          => $mes,
            'anio'         => $anio,
            'total'        => $informesDocentes->count() + $informesCoordinacion->count(),
        ];
    }

    /**
     * Resuelve la ruta única dentro del ZIP para un informe de docente.
     * Evita colisiones cuando un docente tiene múltiples cursos en el mismo mes.
     *
     * @param  array  $usedPaths  Referencia al mapa de rutas ya usadas (se modifica in-place)
     * @return array{dirPath: string, fileName: string, fullPath: string, mes: string, anio: string, docenteNombre: string}
     */
    public function resolveDocenteZipPath(Informe $informe, string $mesFallback, string $anioFallback, array &$usedPaths): array
    {
        $docenteNombre = $this->limpiarNombreArchivo($informe->usuario->nombre ?? 'Docente');
        $cursoNombre   = $this->limpiarNombreArchivo($informe->curso->nombre_curso ?? 'Curso');
        $seccion       = $this->limpiarNombreArchivo($informe->curso->seccion ?? 'A');
        $mes           = $this->limpiarNombreArchivo(ucfirst(strtolower($informe->mes ?? $mesFallback)));
        $anio          = $this->limpiarNombreArchivo($informe->curso->anio ?? $anioFallback);

        $dirPath  = "{$anio}/{$mes}/Docente";
        $fileName = "Informes_Docente_{$docenteNombre}_{$mes}.pdf";
        $fullPath = "{$dirPath}/{$fileName}";

        if (isset($usedPaths[$fullPath])) {
            $fileName = "Informes_Docente_{$docenteNombre}_{$cursoNombre}_Sec{$seccion}_{$mes}.pdf";
            $fullPath = "{$dirPath}/{$fileName}";
        }
        $usedPaths[$fullPath] = true;

        return compact('dirPath', 'fileName', 'fullPath', 'mes', 'anio', 'docenteNombre');
    }

    /**
     * Resuelve la ruta única dentro del ZIP para un informe de coordinación.
     * Evita colisiones cuando un coordinador tiene múltiples informes en el mismo mes.
     *
     * @param  array  $usedPaths  Referencia al mapa de rutas ya usadas (se modifica in-place)
     * @return array{dirPath: string, fileName: string, fullPath: string, mes: string, anio: string, coordNombre: string}
     */
    public function resolveCoordZipPath(InformeCoordinacion $informe, string $mesFallback, string $anioFallback, array &$usedPaths): array
    {
        $coordNombre = $this->limpiarNombreArchivo($informe->usuario->nombre ?? 'Coordinador');
        $area        = $this->limpiarNombreArchivo($informe->area ?? 'Area');
        $mes         = $this->limpiarNombreArchivo(ucfirst(strtolower($informe->mes ?? $mesFallback)));
        $anio        = $this->limpiarNombreArchivo($informe->anio ?? $anioFallback);

        $dirPath  = "{$anio}/{$mes}/Coordinador";
        $fileName = "Informes_Coordinador_{$coordNombre}_{$mes}.pdf";
        $fullPath = "{$dirPath}/{$fileName}";

        if (isset($usedPaths[$fullPath])) {
            $fileName = "Informes_Coordinador_{$coordNombre}_{$area}_{$mes}.pdf";
            $fullPath = "{$dirPath}/{$fileName}";
        }
        $usedPaths[$fullPath] = true;

        return compact('dirPath', 'fileName', 'fullPath', 'mes', 'anio', 'coordNombre');
    }

    /**
     * Descarga masiva de informes en formato PDF dentro de un archivo ZIP estructurado:
     * Año -> Mes -> Docente / Coordinador -> Informes_Docente_Nombre_Mes.pdf / Informes_Coordinador_Nombre_Mes.pdf
     */
    public function descargarInformesZip(Request|array $filtros, User $user): BinaryFileResponse
    {
        $filtrados = $this->obtenerInformesFiltrados($filtros);

        $informesDocentes     = $filtrados['docentes'];
        $informesCoordinacion = $filtrados['coordinacion'];
        $mesFiltro            = $filtrados['mes'] ? ucfirst(strtolower($filtrados['mes'])) : 'General';
        $anioFiltro           = $filtrados['anio'] ?? date('Y');

        if ($informesDocentes->isEmpty() && $informesCoordinacion->isEmpty()) {
            throw new Exception('No se encontraron informes para los filtros seleccionados.');
        }

        if (!class_exists('ZipArchive')) {
            throw new Exception('La extensión ZipArchive de PHP no está habilitada en el servidor.');
        }

        $zip = new ZipArchive();
        $zipFileName = tempnam(sys_get_temp_dir(), 'farusac_pdf_zip_');

        if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new Exception('No se pudo crear el archivo ZIP temporal.');
        }

        $usedDocentePaths = [];
        foreach ($informesDocentes as $inf) {
            $pathInfo  = $this->resolveDocenteZipPath($inf, $mesFiltro, (string) $anioFiltro, $usedDocentePaths);
            $pdfBinary = $this->pdfService->generarPdfDocente($inf, $user);
            $zip->addFromString($pathInfo['fullPath'], $pdfBinary);
        }

        $usedCoordPaths = [];
        foreach ($informesCoordinacion as $infC) {
            $pathInfo  = $this->resolveCoordZipPath($infC, $mesFiltro, (string) $anioFiltro, $usedCoordPaths);
            $pdfBinary = $this->pdfService->generarPdfCoordinacion($infC, $user);
            $zip->addFromString($pathInfo['fullPath'], $pdfBinary);
        }

        $zip->close();

        $downloadName = "Informes_FARUSAC_{$mesFiltro}_{$anioFiltro}.zip";
        return response()->download($zipFileName, $downloadName)->deleteFileAfterSend(true);
    }

    /**
     * Descarga ZIP con archivos PDF para elementos seleccionados específicamente por IDs.
     */
    public function descargarSeleccionadosZip(array $docenteIds, array $coordIds, User $user): BinaryFileResponse
    {
        $informesDocentes = !empty($docenteIds)
            ? Informe::with(['usuario', 'curso', 'semanas'])->whereIn('id', array_filter($docenteIds))->get()
            : collect();

        $informesCoordinacion = !empty($coordIds)
            ? InformeCoordinacion::with(['programas', 'asignaturas', 'avances', 'estudiantes', 'usuario'])->whereIn('id', array_filter($coordIds))->get()
            : collect();

        if ($informesDocentes->isEmpty() && $informesCoordinacion->isEmpty()) {
            throw new Exception('No se seleccionó ningún informe.');
        }

        if (!class_exists('ZipArchive')) {
            throw new Exception('La extensión ZipArchive no está habilitada en el servidor.');
        }

        $zip = new ZipArchive();
        $zipFileName = tempnam(sys_get_temp_dir(), 'farusac_sel_pdf_zip_');

        if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new Exception('Error al crear archivo ZIP temporal.');
        }

        $usedDocentePaths = [];
        foreach ($informesDocentes as $inf) {
            $pathInfo  = $this->resolveDocenteZipPath($inf, 'Mes', date('Y'), $usedDocentePaths);
            $pdfBinary = $this->pdfService->generarPdfDocente($inf, $user);
            $zip->addFromString($pathInfo['fullPath'], $pdfBinary);
        }

        $usedCoordPaths = [];
        foreach ($informesCoordinacion as $infC) {
            $pathInfo  = $this->resolveCoordZipPath($infC, 'Mes', date('Y'), $usedCoordPaths);
            $pdfBinary = $this->pdfService->generarPdfCoordinacion($infC, $user);
            $zip->addFromString($pathInfo['fullPath'], $pdfBinary);
        }

        $zip->close();

        return response()->download($zipFileName, 'Informes_Seleccionados_FARUSAC.zip')->deleteFileAfterSend(true);
    }

    /**
     * Genera la descarga del resumen consolidado en formato CSV.
     */
    public function descargarInformesCsv(Request|array $filtros): Response
    {
        $filtrados            = $this->obtenerInformesFiltrados($filtros);
        $informesDocentes     = $filtrados['docentes'];
        $informesCoordinacion = $filtrados['coordinacion'];
        $mes                  = $filtrados['mes'] ?? 'General';
        $anio                 = $filtrados['anio'] ?? date('Y');

        $csvContent  = "\xEF\xBB\xBF";
        $csvContent .= "Tipo;Autor;Correo;Curso/Area;Codigo;Seccion;Carrera;Periodo;Mes;Anio;Estudiantes;Fecha Entrega;Listado Asistencia;Evidencias Drive;Meet/Virtual;Classroom\n";

        foreach ($informesDocentes as $inf) {
            $csvContent .= sprintf(
                "\"Docente\";\"%s\";\"%s\";\"%s\";\"%s\";\"%s\";\"%s\";\"%s\";\"%s\";\"%s\";\"%s\";\"%s\";\"%s\";\"%s\";\"%s\";\"%s\"\n",
                addslashes($inf->usuario->nombre ?? ''),
                addslashes($inf->usuario->correo ?? ''),
                addslashes($inf->curso->nombre_curso ?? ''),
                addslashes($inf->curso->codigo_curso ?? ''),
                addslashes($inf->curso->seccion ?? ''),
                addslashes($inf->curso->carrera ?? ''),
                addslashes($inf->periodo ?? ''),
                addslashes($inf->mes ?? ''),
                addslashes($inf->curso->anio ?? ''),
                $inf->estudiantes_asignados ?? 0,
                $inf->created_at ? $inf->created_at->format('d/m/Y H:i') : '',
                addslashes($inf->listado_asistencia_url ?? ''),
                addslashes($inf->enlace_evidencia_url ?? ''),
                addslashes($inf->enlace_meet_zoom_url ?? ''),
                addslashes($inf->enlace_classroom_drive_url ?? '')
            );
        }

        foreach ($informesCoordinacion as $infC) {
            $csvContent .= sprintf(
                "\"Coordinador\";\"%s\";\"%s\";\"%s\";\"—\";\"—\";\"—\";\"%s\";\"%s\";\"%s\";\"—\";\"%s\";\"—\";\"%s\";\"—\";\"—\"\n",
                addslashes($infC->usuario->nombre ?? ''),
                addslashes($infC->usuario->correo ?? ''),
                addslashes($infC->area ?? ''),
                addslashes($infC->periodo ?? ''),
                addslashes($infC->mes ?? ''),
                addslashes($infC->anio ?? ''),
                $infC->created_at ? $infC->created_at->format('d/m/Y H:i') : '',
                addslashes($infC->enlace_actividades_coordinacion ?? '')
            );
        }

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"Resumen_Entregas_FARUSAC_{$mes}_{$anio}.csv\"",
        ];

        return response($csvContent, 200, $headers);
    }

    /**
     * Retorna la lista de URLs y metadatos de archivos para generación de ZIP en cliente (JSZip).
     */
    public function obtenerListaDescargaZipJson(Request|array $filtros): array
    {
        $filtrados = $this->obtenerInformesFiltrados($filtros);

        $informesDocentes     = $filtrados['docentes'];
        $informesCoordinacion = $filtrados['coordinacion'];
        $mesFiltro            = $filtrados['mes'] ? ucfirst(strtolower($filtrados['mes'])) : 'General';
        $anioFiltro           = $filtrados['anio'] ?? date('Y');

        if ($informesDocentes->isEmpty() && $informesCoordinacion->isEmpty()) {
            return [
                'success' => false,
                'message' => 'No se encontraron informes para los filtros seleccionados.',
                'items'   => [],
            ];
        }

        $items = [];
        $usedDocentePaths = [];
        foreach ($informesDocentes as $inf) {
            $pathInfo = $this->resolveDocenteZipPath($inf, $mesFiltro, (string) $anioFiltro, $usedDocentePaths);

            $items[] = [
                'id'         => $inf->id,
                'tipo'       => 'docente',
                'autor'      => $inf->usuario->nombre ?? 'Docente',
                'curso_area' => $inf->curso->nombre_curso ?? 'Curso',
                'seccion'    => $inf->curso->seccion ?? '',
                'mes'        => $pathInfo['mes'],
                'anio'       => $pathInfo['anio'],
                'url'        => route('admin.informes.docente.ver', ['id' => $inf->id, 'embed' => 1]),
                'fileName'   => $pathInfo['fileName'],
                'fullPath'   => $pathInfo['fullPath'],
            ];
        }

        $usedCoordPaths = [];
        foreach ($informesCoordinacion as $infC) {
            $pathInfo = $this->resolveCoordZipPath($infC, $mesFiltro, (string) $anioFiltro, $usedCoordPaths);

            $items[] = [
                'id'         => $infC->id,
                'tipo'       => 'coordinacion',
                'autor'      => $infC->usuario->nombre ?? 'Coordinador',
                'curso_area' => $infC->area ?? 'Área',
                'seccion'    => '',
                'mes'        => $pathInfo['mes'],
                'anio'       => $pathInfo['anio'],
                'url'        => route('admin.informes.coordinacion.ver', ['id' => $infC->id, 'embed' => 1]),
                'fileName'   => $pathInfo['fileName'],
                'fullPath'   => $pathInfo['fullPath'],
            ];
        }

        return [
            'success' => true,
            'zipName' => "Informes_FARUSAC_{$mesFiltro}_{$anioFiltro}.zip",
            'total'   => count($items),
            'items'   => $items,
        ];
    }

    /**
     * Retorna la lista de URLs y metadatos para informes seleccionados por checkbox para el cliente JSZip.
     */
    public function obtenerListaSeleccionadosZipJson(array $docenteIds, array $coordIds): array
    {
        $informesDocentes = !empty($docenteIds)
            ? Informe::with(['usuario', 'curso', 'semanas'])->whereIn('id', array_filter($docenteIds))->get()
            : collect();

        $informesCoordinacion = !empty($coordIds)
            ? InformeCoordinacion::with(['programas', 'asignaturas', 'avances', 'estudiantes', 'usuario'])->whereIn('id', array_filter($coordIds))->get()
            : collect();

        if ($informesDocentes->isEmpty() && $informesCoordinacion->isEmpty()) {
            return [
                'success' => false,
                'message' => 'No se seleccionó ningún informe.',
                'items'   => [],
            ];
        }

        $items = [];
        $usedDocentePaths = [];
        foreach ($informesDocentes as $inf) {
            $pathInfo = $this->resolveDocenteZipPath($inf, 'Mes', date('Y'), $usedDocentePaths);

            $items[] = [
                'id'         => $inf->id,
                'tipo'       => 'docente',
                'autor'      => $inf->usuario->nombre ?? 'Docente',
                'curso_area' => $inf->curso->nombre_curso ?? 'Curso',
                'seccion'    => $inf->curso->seccion ?? '',
                'mes'        => $pathInfo['mes'],
                'anio'       => $pathInfo['anio'],
                'url'        => route('admin.informes.docente.ver', ['id' => $inf->id, 'embed' => 1]),
                'fileName'   => $pathInfo['fileName'],
                'fullPath'   => $pathInfo['fullPath'],
            ];
        }

        $usedCoordPaths = [];
        foreach ($informesCoordinacion as $infC) {
            $pathInfo = $this->resolveCoordZipPath($infC, 'Mes', date('Y'), $usedCoordPaths);

            $items[] = [
                'id'         => $infC->id,
                'tipo'       => 'coordinacion',
                'autor'      => $infC->usuario->nombre ?? 'Coordinador',
                'curso_area' => $infC->area ?? 'Área',
                'seccion'    => '',
                'mes'        => $pathInfo['mes'],
                'anio'       => $pathInfo['anio'],
                'url'        => route('admin.informes.coordinacion.ver', ['id' => $infC->id, 'embed' => 1]),
                'fileName'   => $pathInfo['fileName'],
                'fullPath'   => $pathInfo['fullPath'],
            ];
        }

        return [
            'success' => true,
            'zipName' => 'Informes_Seleccionados_FARUSAC.zip',
            'total'   => count($items),
            'items'   => $items,
        ];
    }
}

