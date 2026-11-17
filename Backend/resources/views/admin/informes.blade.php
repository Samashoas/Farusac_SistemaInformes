@extends('layouts.app')

@section('title', 'Historial y Descarga de Informes - Panel Administrador')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endpush

@section('content')
<!-- Fila de Tarjetas KPI / Métricas -->
<div class="kpi-grid">
    <!-- Total Informes -->
    <div class="kpi-card kpi-total">
        <div class="kpi-info">
            <span class="kpi-label">Total Informes</span>
            <span class="kpi-value">{{ $stats['total'] }}</span>
            <span class="kpi-subtext">Registros globales</span>
        </div>
        <div class="kpi-icon-wrap">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        </div>
    </div>

    <!-- Informes Docentes -->
    <div class="kpi-card kpi-docentes">
        <div class="kpi-info">
            <span class="kpi-label">De Docentes</span>
            <span class="kpi-value">{{ $stats['docentes'] }}</span>
            <span class="kpi-subtext">{{ $stats['docentes_unicos'] }} docentes activos</span>
        </div>
        <div class="kpi-icon-wrap">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
            </svg>
        </div>
    </div>

    <!-- Informes Coordinación -->
    <div class="kpi-card kpi-coordinacion">
        <div class="kpi-info">
            <span class="kpi-label">De Coordinación</span>
            <span class="kpi-value">{{ $stats['coordinacion'] }}</span>
            <span class="kpi-subtext">{{ $stats['coordinadores_unicos'] }} coordinadores</span>
        </div>
        <div class="kpi-icon-wrap">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
        </div>
    </div>

    <!-- Usuarios Participantes -->
    <div class="kpi-card kpi-participantes">
        <div class="kpi-info">
            <span class="kpi-label">Cuentas Activas</span>
            <span class="kpi-value">{{ $stats['docentes_unicos'] + $stats['coordinadores_unicos'] }}</span>
            <span class="kpi-subtext">Con informes entregados</span>
        </div>
        <div class="kpi-icon-wrap">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
        </div>
    </div>
</div>

<!-- Tarjeta Contenedora Principal -->
<div class="content-card">
    <div class="content-header-row">
        <div class="content-title-block">
            <h2 class="content-header-title">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Historial y Descarga de Informes
            </h2>
            <p class="content-header-subtitle">
                Visualización, consulta y descarga masiva de informes de Docentes y Coordinadores para resguardo en Drive.
            </p>
        </div>

        <button type="button" class="btn-bulk-download" onclick="openBulkDownloadModal()">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            <span>Descargar Informes</span>
        </button>
    </div>

    <!-- Pestañas de Selección (Tabs) -->
    <div class="tabs-container">
        <button type="button" class="tab-btn active" id="tabBtnDocentes" onclick="switchTab('docentes')">
            <span>Informes de Docentes</span>
            <span class="tab-badge" id="tabCountDocentes">{{ $informesDocentes->count() }}</span>
        </button>
        <button type="button" class="tab-btn" id="tabBtnCoordinacion" onclick="switchTab('coordinacion')">
            <span>Informes de Coordinación de Área</span>
            <span class="tab-badge" id="tabCountCoordinacion">{{ $informesCoordinacion->count() }}</span>
        </button>
    </div>

    <!-- Barra de Filtros Interactiva -->
    <div class="filters-container">
        <div class="filters-top-row">
            <div class="search-box-wrapper">
                <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" id="searchInput" class="search-input" placeholder="Buscar por docente, coordinador, curso, código o correo..." oninput="applyFilters()">
            </div>
        </div>

        <div class="dropdown-filters-grid">
            <select id="filterCarrera" class="filter-select" onchange="applyFilters()">
                <option value="">Todas las Carreras</option>
                @foreach ($carreras as $carr)
                    <option value="{{ $carr }}">{{ $carr }}</option>
                @endforeach
            </select>

            <select id="filterArea" class="filter-select" onchange="applyFilters()">
                <option value="">Todas las Áreas</option>
                @foreach ($areas as $a)
                    <option value="{{ $a }}">{{ $a }}</option>
                @endforeach
            </select>

            <select id="filterPeriodo" class="filter-select" onchange="applyFilters()">
                <option value="">Todos los Periodos</option>
                @foreach ($periodos as $per)
                    <option value="{{ $per }}">{{ $per }}</option>
                @endforeach
            </select>

            <select id="filterAnio" class="filter-select" onchange="applyFilters()">
                <option value="">Todos los Años</option>
                @foreach ($anios as $yr)
                    <option value="{{ $yr }}">{{ $yr }}</option>
                @endforeach
            </select>

            <select id="filterMes" class="filter-select" onchange="applyFilters()">
                <option value="">Todos los Meses</option>
                @foreach (['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'] as $m)
                    <option value="{{ $m }}">{{ $m }}</option>
                @endforeach
            </select>
        </div>

        <div class="filters-status-row">
            <span class="results-counter" id="resultsCounterText">Cargando informes...</span>
            <button type="button" class="btn-clear-filters" onclick="clearFilters()">Limpiar filtros</button>
        </div>
    </div>

    <!-- 1. TABLA: INFORMES DE DOCENTES -->
    <div id="sectionDocentes" class="tab-content-section">
        <div class="table-responsive">
            <table class="custom-table" id="tablaDocentes">
                <thead>
                    <tr>
                        <th class="chk-cell">
                            <input type="checkbox" id="chkSelectAllDocentes" class="custom-checkbox" onchange="toggleSelectAll('docente')">
                        </th>
                        <th style="width: 45px;">No.</th>
                        <th>Docente</th>
                        <th>Curso & Sección</th>
                        <th>Carrera & Área</th>
                        <th>Año / Periodo</th>
                        <th>Mes</th>
                        <th style="text-align: center;">Estudiantes</th>
                        <th style="text-align: right;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbodyDocentes">
                    @forelse ($informesDocentes as $index => $inf)
                        @php
                            $docenteNombre = $inf->usuario->nombre ?? 'Docente no asignado';
                            $docenteCorreo = $inf->usuario->correo ?? '';
                            $cursoNombre = $inf->curso->nombre_curso ?? 'Sin curso';
                            $cursoCodigo = $inf->curso->codigo_curso ?? '—';
                            $cursoSeccion = $inf->curso->seccion ?? '—';
                            $cursoCarrera = $inf->curso->carrera ?? '';
                            $cursoArea = $inf->curso->area ?? '';
                            $anioVal = $inf->curso->anio ?? date('Y', strtotime($inf->created_at));

                            $periodoClean = trim(preg_replace('/\d{4}/', '', $inf->periodo));
                            if (!$periodoClean) {
                                $periodoClean = $inf->periodo;
                            }
                        @endphp
                        <tr class="informe-docente-row" id="row-docente-{{ $inf->id }}" data-tipo="docente"
                            data-nombre="{{ strtolower($docenteNombre) }}"
                            data-correo="{{ strtolower($docenteCorreo) }}"
                            data-curso="{{ strtolower($cursoNombre) }}"
                            data-codigo="{{ strtolower($cursoCodigo) }}"
                            data-carrera="{{ strtolower($cursoCarrera) }}"
                            data-area="{{ strtolower($cursoArea) }}"
                            data-periodo="{{ strtolower($periodoClean) }}" data-anio="{{ $anioVal }}"
                            data-mes="{{ strtolower($inf->mes) }}">

                            <td class="chk-cell">
                                <input type="checkbox" class="custom-checkbox chk-item chk-docente" value="{{ $inf->id }}" onchange="updateSelectionState()">
                            </td>
                            <td class="td-num seq-num">{{ $index + 1 }}.</td>

                            <td>
                                <div class="user-cell-block">
                                    <div class="user-mini-avatar avatar-docente">
                                        {{ strtoupper(substr($docenteNombre, 0, 1)) }}
                                    </div>
                                    <div class="user-info-text">
                                        <span class="user-name">{{ $docenteNombre }}</span>
                                        <span class="user-email">{{ $docenteCorreo }}</span>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <div style="display: flex; flex-direction: column; gap: 3px;">
                                    <span style="font-weight: 600; color: var(--color-texto-oscuro);">{{ $cursoNombre }}</span>
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        <span style="font-size: 11px; font-family: monospace; color: var(--color-texto-claro);">Cód: {{ $cursoCodigo }}</span>
                                        <span class="badge badge-seccion">Sec. {{ $cursoSeccion }}</span>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <div style="display: flex; flex-direction: column; gap: 4px; align-items: flex-start;">
                                    @if($cursoCarrera)
                                        <span class="badge badge-carrera">{{ $cursoCarrera }}</span>
                                    @endif
                                    @if($cursoArea)
                                        <span class="badge badge-area">{{ $cursoArea }}</span>
                                    @endif
                                </div>
                            </td>

                            <td>
                                <div style="display: flex; flex-direction: column; gap: 2px;">
                                    <span style="font-weight: 700; color: var(--color-azul);">{{ $anioVal }}</span>
                                    <span style="font-size: 12px; color: var(--color-texto-claro);">{{ $periodoClean }}</span>
                                </div>
                            </td>

                            <td>
                                <span style="font-weight: 600;">{{ $inf->mes }}</span>
                            </td>

                            <td style="text-align: center;">
                                <span class="badge badge-count">{{ $inf->estudiantes_asignados ?? 0 }}</span>
                            </td>

                            <td>
                                <div class="actions-cell">
                                    <button type="button" class="action-btn view-btn" title="Visualizar informe oficial"
                                        onclick="openReportPreviewModal('docente', {{ $inf->id }}, '{{ addslashes($cursoNombre) }} - Sec. {{ addslashes($cursoSeccion) }}', '{{ addslashes($docenteNombre) }} • {{ addslashes($inf->mes) }} {{ addslashes($anioVal) }}')">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>

                                    <button type="button" class="action-btn delete-btn" title="Eliminar informe"
                                        onclick="openDeleteModal('docente', {{ $inf->id }}, '{{ addslashes($cursoNombre) }} - {{ addslashes($docenteNombre) }}')">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr id="emptyDocentesRow">
                            <td colspan="9" class="no-records-row">No se han registrado informes de docentes todavía.</td>
                        </tr>
                    @endforelse

                    <tr id="noResultsDocentesRow" style="display: none;">
                        <td colspan="9" class="no-records-row">No se encontraron informes de docentes con los filtros aplicados.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 2. TABLA: INFORMES DE COORDINACIÓN -->
    <div id="sectionCoordinacion" class="tab-content-section" style="display: none;">
        <div class="table-responsive">
            <table class="custom-table" id="tablaCoordinacion">
                <thead>
                    <tr>
                        <th class="chk-cell">
                            <input type="checkbox" id="chkSelectAllCoord" class="custom-checkbox" onchange="toggleSelectAll('coordinacion')">
                        </th>
                        <th style="width: 45px;">No.</th>
                        <th>Coordinador(a)</th>
                        <th>Área de Coordinación</th>
                        <th>Año / Periodo</th>
                        <th>Mes</th>
                        <th>Resumen de Supervisión</th>
                        <th style="text-align: right;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbodyCoordinacion">
                    @forelse ($informesCoordinacion as $index => $infC)
                        @php
                            $coordNombre = $infC->usuario->nombre ?? 'Coordinador no asignado';
                            $coordCorreo = $infC->usuario->correo ?? '';
                            $areaCoord = $infC->area ?? 'General';
                            $anioVal = $infC->anio ?? date('Y', strtotime($infC->created_at));

                            $periodoClean = trim(preg_replace('/\d{4}/', '', $infC->periodo));
                            if (!$periodoClean) {
                                $periodoClean = $infC->periodo;
                            }

                            $countProg = $infC->programas ? $infC->programas->count() : 0;
                            $countAsig = $infC->asignaturas ? $infC->asignaturas->count() : 0;
                            $countAvance = $infC->avances ? $infC->avances->count() : 0;
                            $countEst = $infC->estudiantes ? $infC->estudiantes->count() : 0;
                        @endphp
                        <tr class="informe-coordinacion-row" id="row-coordinacion-{{ $infC->id }}"
                            data-tipo="coordinacion" data-nombre="{{ strtolower($coordNombre) }}"
                            data-correo="{{ strtolower($coordCorreo) }}"
                            data-area="{{ strtolower($areaCoord) }}"
                            data-periodo="{{ strtolower($periodoClean) }}" data-anio="{{ $anioVal }}"
                            data-mes="{{ strtolower($infC->mes) }}" data-carrera="">

                            <td class="chk-cell">
                                <input type="checkbox" class="custom-checkbox chk-item chk-coord" value="{{ $infC->id }}" onchange="updateSelectionState()">
                            </td>
                            <td class="td-num seq-num">{{ $index + 1 }}.</td>

                            <td>
                                <div class="user-cell-block">
                                    <div class="user-mini-avatar avatar-coord">
                                        {{ strtoupper(substr($coordNombre, 0, 1)) }}
                                    </div>
                                    <div class="user-info-text">
                                        <span class="user-name">{{ $coordNombre }}</span>
                                        <span class="user-email">{{ $coordCorreo }}</span>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <span class="badge badge-area" style="font-size: 12.5px; padding: 4px 10px;">{{ $areaCoord }}</span>
                            </td>

                            <td>
                                <div style="display: flex; flex-direction: column; gap: 2px;">
                                    <span style="font-weight: 700; color: var(--color-terracota);">{{ $anioVal }}</span>
                                    <span style="font-size: 12px; color: var(--color-texto-claro);">{{ $periodoClean }}</span>
                                </div>
                            </td>

                            <td>
                                <span style="font-weight: 600;">{{ $infC->mes }}</span>
                            </td>

                            <td>
                                <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                                    @if($countProg > 0)
                                        <span class="badge badge-carrera" title="Programas entregados">{{ $countProg }} prog.</span>
                                    @endif
                                    <span class="badge badge-type-docente" title="Asignaturas revisadas">{{ $countAsig }} asig.</span>
                                    <span class="badge badge-type-coord" title="Cursos con avance">{{ $countAvance }} avances</span>
                                    @if($countEst > 0)
                                        <span class="badge" style="background-color: #fef3c7; color: #92400e;" title="Cursos con estudiantes con problemas">{{ $countEst }} alertas est.</span>
                                    @endif
                                </div>
                            </td>

                            <td>
                                <div class="actions-cell">
                                    <button type="button" class="action-btn view-btn" title="Visualizar informe oficial"
                                        onclick="openReportPreviewModal('coordinacion', {{ $infC->id }}, 'Área {{ addslashes($areaCoord) }} - {{ addslashes($infC->mes) }} {{ addslashes($anioVal) }}', '{{ addslashes($coordNombre) }} • {{ addslashes($infC->mes) }} {{ addslashes($anioVal) }}')">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>

                                    <button type="button" class="action-btn delete-btn" title="Eliminar informe"
                                        onclick="openDeleteModal('coordinacion', {{ $infC->id }}, 'Coordinación {{ addslashes($areaCoord) }} - {{ addslashes($coordNombre) }}')">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr id="emptyCoordinacionRow">
                            <td colspan="8" class="no-records-row">No se han registrado informes de coordinación todavía.</td>
                        </tr>
                    @endforelse

                    <tr id="noResultsCoordinacionRow" style="display: none;">
                        <td colspan="8" class="no-records-row">No se encontraron informes de coordinación con los filtros aplicados.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- BARRA FLOTANTE DE SELECCIÓN MÚLTIPLE -->
<div class="selection-toolbar" id="selectionToolbar">
    <span class="selection-count-badge">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <polyline points="20 6 9 17 4 12"></polyline>
        </svg>
        <span id="selectedCountText">0 seleccionados</span>
    </span>
    <button type="button" class="btn-toolbar-action btn-toolbar-zip" onclick="downloadSelectedZip()">Descargar ZIP</button>
    <button type="button" class="btn-toolbar-action btn-toolbar-pdf" onclick="viewSelectedPdf()">Ver PDF Consolidado</button>
    <button type="button" class="btn-toolbar-clear" onclick="clearSelection()">Deseleccionar</button>
</div>

<!-- MODAL DE DESCARGA MASIVA POR MES / FILTROS -->
<div class="modal-overlay" id="bulkDownloadModal" onclick="closeBulkDownloadModal()">
    <div class="bulk-download-card" onclick="event.stopPropagation()">
        <div class="bulk-modal-header">
            <div class="bulk-modal-title">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Descarga Masiva de Informes
            </div>
            <button type="button" class="modal-close-icon-btn" onclick="closeBulkDownloadModal()" title="Cerrar ventana (Esc)">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <div class="bulk-modal-body">
            <div class="bulk-info-banner">
                Selecciona el <strong>mes y año</strong> para empaquetar y descargar los informes en un archivo <strong>ZIP</strong> con sus archivos en formato <strong>PDF</strong> organizados.
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label" for="bulkMes">Mes a Descargar</label>
                    <select id="bulkMes" class="form-control" onchange="updateBulkPreviewCount()">
                        <option value="">Todos los Meses</option>
                        @foreach (['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'] as $m)
                            <option value="{{ $m }}" {{ $m === 'Septiembre' ? 'selected' : '' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="bulkAnio">Año</label>
                    <select id="bulkAnio" class="form-control" onchange="updateBulkPreviewCount()">
                        <option value="">Todos los Años</option>
                        @foreach ($anios as $yr)
                            <option value="{{ $yr }}" {{ $yr == date('Y') ? 'selected' : '' }}>{{ $yr }}</option>
                        @endforeach
                        @if($anios->isEmpty())
                            <option value="{{ date('Y') }}" selected>{{ date('Y') }}</option>
                        @endif
                    </select>
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label" for="bulkTipo">Tipo de Informes</label>
                    <select id="bulkTipo" class="form-control" onchange="updateBulkPreviewCount()">
                        <option value="todos">Todos (Docentes y Coordinación)</option>
                        <option value="docente">Solo Informes de Docentes</option>
                        <option value="coordinacion">Solo Informes de Coordinación</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="bulkCarrera">Carrera (Opcional)</label>
                    <select id="bulkCarrera" class="form-control" onchange="updateBulkPreviewCount()">
                        <option value="">Todas las Carreras</option>
                        @foreach ($carreras as $carr)
                            <option value="{{ $carr }}">{{ $carr }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="bulk-actions-group">
                <a href="#" id="btnBulkZip" class="btn-export-option opt-zip" style="border-color: var(--color-terracota); background-color: rgba(185, 71, 0, 0.04);" onclick="triggerBulkZip(event)">
                    <div class="export-opt-left">
                        <div class="export-opt-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                        </div>
                        <div class="export-opt-text">
                            <span class="export-opt-title" style="color: var(--color-terracota);">Descargar ZIP con Archivos PDF</span>
                            <span class="export-opt-desc">Carpetas estructuradas: <code>Año/Mes/Docente</code> o <code>Año/Mes/Coordinador</code> con PDFs listos para Drive</span>
                        </div>
                    </div>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>

                <a href="#" id="btnBulkPdf" target="_blank" class="btn-export-option opt-pdf" onclick="triggerBulkPdf(event)">
                    <div class="export-opt-left">
                        <div class="export-opt-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                        </div>
                        <div class="export-opt-text">
                            <span class="export-opt-title">Abrir Todo el Mes en PDF Consolidado</span>
                            <span class="export-opt-desc">Vista continua lista para guardar/imprimir todos los informes del mes en un único archivo PDF</span>
                        </div>
                    </div>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>

                <a href="#" id="btnBulkCsv" class="btn-export-option opt-csv" onclick="triggerBulkCsv(event)">
                    <div class="export-opt-left">
                        <div class="export-opt-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="export-opt-text">
                            <span class="export-opt-title">Descargar Planilla de Control (.csv)</span>
                            <span class="export-opt-desc">Matriz con todos los docentes, entregas, estudiantes y enlaces de Drive y Meet</span>
                        </div>
                    </div>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- MODAL DE CONFIRMACIÓN DE ELIMINACIÓN -->
<div class="modal-overlay" id="deleteConfirmModal" onclick="closeDeleteModal()">
    <div class="confirm-delete-card" onclick="event.stopPropagation()">
        <div class="warning-icon">⚠</div>
        <h3 class="alert-title">¿ESTÁ SEGURO DE ELIMINAR ESTE INFORME?</h3>
        <p class="alert-desc" id="deleteModalMessage">
            Esta acción es permanente y eliminará todas las actividades y registros asociados al informe seleccionado.
        </p>
        <div class="confirm-actions-row">
            <button type="button" class="btn-confirm-delete" id="btnExecuteDelete" onclick="executeDelete()">CONFIRMAR ELIMINACIÓN</button>
            <button type="button" class="btn-cancel-delete" onclick="closeDeleteModal()">CANCELAR</button>
        </div>
    </div>
</div>

<!-- MODAL SUPERPUESTO DE VISUALIZACIÓN DE INFORME OFICIAL (LIGHTBOX IFRAME) -->
<div class="report-modal-overlay" id="reportPreviewModal" onclick="closeReportPreviewModal()">
    <div class="report-modal-window" onclick="event.stopPropagation()">
        <div class="report-modal-header">
            <div class="report-modal-header-left">
                <span class="report-badge-type report-badge-docente" id="previewModalBadge">Docente</span>
                <div>
                    <h3 class="report-modal-title" id="previewModalTitle">Cargando informe...</h3>
                    <p class="report-modal-subtitle" id="previewModalSubtitle">FARUSAC • Control Académico</p>
                </div>
            </div>
            <div class="report-modal-header-actions">
                <button type="button" class="report-btn-tool btn-tool-print" onclick="printReportIframe()" title="Imprimir o Exportar PDF">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <polyline points="6 9 6 2 18 2 18 9"></polyline>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                        <rect x="6" y="14" width="12" height="8"></rect>
                    </svg>
                    <span>Imprimir / PDF</span>
                </button>
                <a href="#" id="previewModalFullTabBtn" target="_blank" class="report-btn-tool btn-tool-tab" title="Abrir en pestaña completa del navegador">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                        <polyline points="15 3 21 3 21 9"></polyline>
                        <line x1="10" y1="14" x2="21" y2="3"></line>
                    </svg>
                    <span>Pestaña</span>
                </a>
                <button type="button" class="report-btn-close" onclick="closeReportPreviewModal()" title="Cerrar ventana (Esc)">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
        </div>

        <div class="report-modal-body">
            <div class="report-modal-loader" id="previewIframeLoader">
                <div class="report-loader-spinner"></div>
                <span class="report-loader-text">Cargando informe institucional oficial...</span>
            </div>
            <iframe id="reportPreviewIframe" class="report-preview-iframe" src="about:blank" onload="hidePreviewLoader()"></iframe>
        </div>
    </div>
</div>

<form id="formSeleccionadosPost" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="docente_ids" id="postDocenteIds">
    <input type="hidden" name="coordinacion_ids" id="postCoordIds">
</form>
@endsection

@push('scripts')
    <script src="{{ asset('js/admin-informes.js') }}"></script>
@endpush
