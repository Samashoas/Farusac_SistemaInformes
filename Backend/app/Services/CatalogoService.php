<?php

namespace App\Services;

use App\Models\Curso;
use App\Models\Informe;
use App\Models\InformeCoordinacion;
use App\Models\User;
use Exception;
use Illuminate\Support\Collection;

class CatalogoService
{
    /**
     * Fallback institucional de carreras predeterminadas.
     */
    protected array $carrerasPorDefecto = ['Arquitectura', 'Diseño Gráfico'];

    /**
     * Fallback institucional de áreas por carrera cuando aún no existen registros cargados.
     */
    protected array $areasPorDefecto = [
        'Arquitectura' => [
            'Diseño Arquitectónico',
            'Medios de Expresión',
            'Historia y Teoría',
            'Sistemas Estructurales',
            'Ambiente y Urbanismo',
            'Digital',
            'Sistemas Constructivos',
            'Investigación y Graduación',
        ],
        'Diseño Gráfico' => [
            'Área de Diseño',
            'Área de Comunicación',
            'Área de Tecnología y Expresión',
            'Área de Teoría e Historia',
            'Área de Gestión y Práctica Profesional',
            'Área de Graduación',
        ],
    ];

    /**
     * Obtiene el catálogo completo de cursos ordenado institucionalmente.
     */
    public function getCatalogoCursos(): Collection
    {
        try {
            return Curso::orderBy('carrera', 'asc')
                ->orderBy('nombre_curso', 'asc')
                ->orderBy('seccion', 'asc')
                ->get();
        } catch (Exception $e) {
            return collect();
        }
    }

    /**
     * Obtiene la lista de carreras activas registradas en el sistema.
     */
    public function getCarreras(): Collection
    {
        try {
            $carrerasDb = Curso::select('carrera')
                ->distinct()
                ->whereNotNull('carrera')
                ->where('carrera', '!=', '')
                ->pluck('carrera');

            return $carrerasDb->isNotEmpty() ? $carrerasDb : collect($this->carrerasPorDefecto);
        } catch (Exception $e) {
            return collect($this->carrerasPorDefecto);
        }
    }

    /**
     * Obtiene las áreas agrupadas por carrera con fallback institucional.
     */
    public function getAreasPorCarrera(): array
    {
        $areasPorCarrera = [];

        try {
            $cursosCarreraArea = Curso::select('carrera', 'area')
                ->distinct()
                ->whereNotNull('carrera')
                ->where('carrera', '!=', '')
                ->whereNotNull('area')
                ->where('area', '!=', '')
                ->orderBy('area', 'asc')
                ->get();

            foreach ($cursosCarreraArea as $item) {
                $areasPorCarrera[$item->carrera][] = $item->area;
            }

            foreach ($areasPorCarrera as $car => $ars) {
                $areasPorCarrera[$car] = array_values(array_unique($ars));
            }
        } catch (Exception $e) {
            // Continúa con fallbacks institucionales
        }

        foreach ($this->areasPorDefecto as $carrera => $areas) {
            if (empty($areasPorCarrera[$carrera])) {
                $areasPorCarrera[$carrera] = $areas;
            }
        }

        return $areasPorCarrera;
    }

    /**
     * Obtiene todas las áreas académicas únicas disponibles.
     */
    public function getAreasDisponibles(): Collection
    {
        try {
            $areas = Curso::select('area')
                ->distinct()
                ->whereNotNull('area')
                ->where('area', '!=', '')
                ->orderBy('area', 'asc')
                ->pluck('area');

            if ($areas->isNotEmpty()) {
                return $areas;
            }
        } catch (Exception $e) {
            // Usa fallback
        }

        $allFallback = array_merge(
            $this->areasPorDefecto['Arquitectura'],
            $this->areasPorDefecto['Diseño Gráfico']
        );

        return collect(array_values(array_unique($allFallback)));
    }

    /**
     * Extrae y normaliza las opciones de filtrado para el historial de informes docentes.
     */
    public function getFiltrosDocente(Collection $informes, User $user): array
    {
        $carreras = $informes->pluck('curso.carrera')->filter()->unique()->values();
        $cursosDocente = $informes->pluck('curso.nombre_curso')->filter()->unique()->values();
        $periodos = $informes->pluck('periodo')->filter()->map(function ($p) {
            return trim(preg_replace('/\d{4}/', '', (string) $p));
        })->filter()->unique()->values();
        $anios = $informes->pluck('curso.anio')->filter()->unique()->values();
        $meses = $informes->pluck('mes')->filter()->unique()->values();

        if ($carreras->isEmpty()) {
            $carreras = $this->getCarreras();
        }

        if ($cursosDocente->isEmpty()) {
            try {
                $cursosDocente = $user->cursos()->pluck('nombre_curso')->unique()->values();
            } catch (Exception $e) {
                $cursosDocente = collect();
            }
        }

        if ($anios->isEmpty()) {
            $anios = collect([date('Y')]);
        }

        return compact('carreras', 'cursosDocente', 'periodos', 'anios', 'meses');
    }

    /**
     * Extrae opciones de filtros y estadísticas para la vista de informes del Administrador.
     */
    public function getFiltrosAdmin(Collection $informesDocentes, Collection $informesCoordinacion): array
    {
        $carreras = $this->getCarreras();
        $areas    = $this->getAreasDisponibles();

        $periodosDocentes = Informe::select('periodo')->distinct()->whereNotNull('periodo')->pluck('periodo');
        $periodosCoord    = InformeCoordinacion::select('periodo')->distinct()->whereNotNull('periodo')->pluck('periodo');
        $periodos         = $periodosDocentes->merge($periodosCoord)->unique()->filter()->values();

        $aniosDocentes = Curso::select('anio')->distinct()->whereNotNull('anio')->pluck('anio');
        $aniosCoord    = InformeCoordinacion::select('anio')->distinct()->whereNotNull('anio')->pluck('anio');
        $anios         = $aniosDocentes->merge($aniosCoord)->unique()->filter()->sortDesc()->values();

        $stats = [
            'total'                => $informesDocentes->count() + $informesCoordinacion->count(),
            'docentes'             => $informesDocentes->count(),
            'coordinacion'         => $informesCoordinacion->count(),
            'docentes_unicos'      => $informesDocentes->pluck('usuario_id')->unique()->count(),
            'coordinadores_unicos' => $informesCoordinacion->pluck('usuario_id')->unique()->count(),
        ];

        return compact('carreras', 'areas', 'periodos', 'anios', 'stats');
    }
}

