@extends('layouts.app')

@section('title', 'FARUSAC - Panel del Coordinador')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
<div class="dashboard-top-section">
    <!-- Switch de Vistas (Informes Docente vs Informes de Coordinador) -->
    <div class="view-switch-container">
        <div class="view-switch">
            <button type="button" class="switch-btn active" id="btnSwitchDocente" onclick="switchView('docente')">
                <svg class="switch-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <span>Informes Docente</span>
            </button>
            <button type="button" class="switch-btn" id="btnSwitchCoordinador" onclick="switchView('coordinador')">
                <svg class="switch-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <span>Informes de Coordinador</span>
            </button>
        </div>
    </div>

    @php
        $areaActual = $user->area ?? session('coordinador_area');
        $carreraActual = session('coordinador_carrera');
        if (!$carreraActual && $areaActual && isset($areasPorCarrera)) {
            foreach ($areasPorCarrera as $carKey => $areasList) {
                if (in_array($areaActual, $areasList)) {
                    $carreraActual = $carKey;
                    break;
                }
            }
        }
    @endphp

    @if ($areaActual)
        <!-- Banner de Área a Cargo -->
        <div class="area-badge-banner">
            <div class="area-badge-info">
                <span class="area-label-title">Área a Cargo:</span>
                <span class="area-name-pill" id="currentAreaDisplay">{{ $areaActual }}</span>
                @if ($carreraActual)
                    <span class="carrera-name-pill" style="background: rgba(0, 45, 114, 0.1); color: var(--color-azul); padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 600;">{{ $carreraActual }}</span>
                @endif
            </div>
        </div>
    @endif
</div>

<!-- 1. VISTA: INFORMES DOCENTE (Cursos del coordinador) -->
<div id="docenteViewSection" style="display: flex; width: 100%; justify-content: center;">
    <div class="circles-dashboard-container" id="coursesGrid">
        @foreach ($cursosAsignados as $curso)
            <div class="circle-card-item" id="curso-item-{{ $curso->id }}" onclick="window.location.href='{{ route('coordinador.informes.crear') }}?curso_id={{ $curso->id }}'">
                <button type="button" class="btn-unassign-course" title="Remover curso" onclick="event.stopPropagation(); unassignCourse({{ $curso->id }}, '{{ addslashes($curso->nombre_curso) }}', '{{ $curso->seccion }}')">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
                <div class="circle-shape">
                    <svg class="circle-course-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <div class="circle-label">
                    {{ $curso->nombre_curso }} - {{ $curso->seccion }}
                </div>
            </div>
        @endforeach

        <div class="circle-card-item circle-card-add" id="btnOpenModal" onclick="openAddCourseModal()">
            <div class="circle-add-shape">
                <div class="circle-inner-dashed">
                    <svg class="circle-add-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                </div>
            </div>
            <div class="circle-label">
                Agregar nuevo Curso
            </div>
        </div>
    </div>
</div>

<!-- 2. VISTA: INFORMES DE COORDINADOR -->
<div id="coordinadorViewSection" class="coordinador-content-section">
    <div class="coord-reports-card">
        <div class="coord-reports-header">
            <div>
                <div class="coord-reports-title">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Informes Mensuales de Coordinación</span>
                </div>
                <p class="coord-reports-subtitle">
                    Registro y entrega oficial de informes mensuales del <strong>{{ $areaActual ?? 'Área Asignada' }}</strong>.
                </p>
            </div>
            <a href="{{ route('coordinador.informes-coordinacion.crear') }}" class="btn-create-coord-report">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                <span>Crear Informe de Coordinación</span>
            </a>
        </div>

        @if (isset($informesCoordinacion) && $informesCoordinacion->count() > 0)
            <div class="coord-table-container">
                <table class="coord-reports-table">
                    <thead>
                        <tr>
                            <th>Mes y Periodo</th>
                            <th>Área</th>
                            <th>Fecha de Creación</th>
                            <th>Estado</th>
                            <th style="text-align: center;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($informesCoordinacion as $inf)
                            @php
                                $isLocked = ($inf->bloqueado_en && \Carbon\Carbon::now()->greaterThan($inf->bloqueado_en));
                                $estadoClass = $isLocked ? 'bloqueado' : ($inf->estado == 'enviado' ? 'enviado' : 'borrador');
                                $estadoTexto = $isLocked ? 'Bloqueado (Plazo Vencido)' : ucfirst($inf->estado);
                            @endphp
                            <tr id="coord-report-row-{{ $inf->id }}">
                                <td>
                                    <strong style="color: var(--color-azul);">{{ $inf->mes }} {{ $inf->anio }}</strong>
                                    <div style="font-size: 12px; color: #64748b;">{{ $inf->periodo }}</div>
                                </td>
                                <td>{{ $inf->area }}</td>
                                <td>{{ $inf->created_at ? $inf->created_at->format('d/m/Y H:i') : '—' }}</td>
                                <td>
                                    <span class="badge-status {{ $estadoClass }}">{{ $estadoTexto }}</span>
                                </td>
                                <td style="text-align: center;">
                                    <div class="actions-btn-group" style="justify-content: center;">
                                        <button type="button" class="btn-table-action btn-table-view" title="Ver / Imprimir"
                                            onclick="openReportPreviewModal('coordinacion', {{ $inf->id }}, 'Área {{ addslashes($inf->area) }} - {{ addslashes($inf->mes) }}', '{{ addslashes($user->nombre) }} • {{ addslashes($inf->mes) }} ({{ addslashes($inf->periodo) }})', '{{ route('coordinador.informes-coordinacion.ver', $inf->id) }}')">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 4px;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                            Ver
                                        </button>
                                        @if (!$isLocked)
                                            <a href="{{ route('coordinador.informes-coordinacion.editar', $inf->id) }}" class="btn-table-action btn-table-edit" title="Editar">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 4px;"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                Editar
                                            </a>
                                        @endif
                                        <button type="button" class="btn-table-action btn-table-delete" title="Eliminar" onclick="confirmDeleteCoordReport({{ $inf->id }}, '{{ $inf->mes }} {{ $inf->anio }}')">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-coord-state">
                <div class="empty-coord-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h4 style="font-family: 'Outfit', sans-serif; font-size: 17px; font-weight: 700; color: var(--color-texto-principal); margin-bottom: 6px;">
                    No hay informes de coordinación registrados
                </h4>
                <p style="font-size: 13.5px; color: var(--color-texto-secundario); max-width: 480px; margin: 0 auto 20px auto;">
                    Aún no has creado ningún informe mensual para el área de <strong>{{ $areaActual ?? 'Coordinación' }}</strong>.
                </p>
                <a href="{{ route('coordinador.informes-coordinacion.crear') }}" class="btn-create-coord-report">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    <span>Crear Primer Informe de Coordinación</span>
                </a>
            </div>
        @endif
    </div>
</div>

<!-- MODAL AGREGAR CURSO -->
<div class="modal-overlay" id="addCourseModalOverlay" onclick="handleBackdropClick(event)">
    <div class="modal-card" id="modalCard">
        <div class="modal-header">
            <h3 class="modal-title">Agregar nuevo Curso</h3>
            <button type="button" class="modal-close-btn" onclick="closeAddCourseModal()" title="Cerrar">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <div class="modal-alert-error" id="modalErrorMessage"></div>

        <form id="addCourseForm" onsubmit="handleCourseSubmit(event)">
            <div class="form-group-row">
                <label for="selectCarrera" class="form-label-side">Carrera:</label>
                <div class="select-wrapper">
                    <select id="selectCarrera" class="custom-select" onchange="onCarreraChange()">
                        <option value="">Selecciona una carrera...</option>
                        @foreach ($carreras as $carrera)
                            <option value="{{ $carrera }}">{{ $carrera }}</option>
                        @endforeach
                    </select>
                    <svg class="select-arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>

            <div class="form-group-row">
                <label for="selectArea" class="form-label-side">Área:</label>
                <div class="select-wrapper">
                    <select id="selectArea" class="custom-select" disabled onchange="onAreaChange()">
                        <option value="">Selecciona primero una carrera...</option>
                    </select>
                    <svg class="select-arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>

            <div class="form-group-row">
                <label for="selectCurso" class="form-label-side">Curso:</label>
                <div class="select-wrapper">
                    <select id="selectCurso" class="custom-select" disabled onchange="onCursoChange()">
                        <option value="">Selecciona primero un área...</option>
                    </select>
                    <svg class="select-arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>

            <div class="form-group-row">
                <label for="selectSeccion" class="form-label-side">Sección:</label>
                <div class="select-wrapper">
                    <select id="selectSeccion" class="custom-select" disabled onchange="onSeccionChange()">
                        <option value="">Selecciona primero un curso...</option>
                    </select>
                    <svg class="select-arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>

            <button type="submit" class="modal-btn-submit" id="btnSubmitCourse" disabled>
                Agregar Curso
            </button>
        </form>
    </div>
</div>

@if (!$areaActual)
<!-- MODAL OBLIGATORIO: SELECCIÓN DE ÁREA -->
<div class="modal-overlay active" id="areaSelectionModalOverlay">
    <div class="modal-card">
        <div class="modal-header">
            <h3 class="modal-title">Selección de Área</h3>
        </div>

        <p style="font-size: 14px; color: var(--color-texto-secundario); margin-bottom: 20px; line-height: 1.5;">
            Para acceder y gestionar las funciones correspondientes a su coordinación, seleccione la carrera y el área de la cual es coordinador:
        </p>

        <div class="form-group-row">
            <label for="selectCarreraModal" class="form-label-side">Carrera:</label>
            <div class="select-wrapper">
                <select id="selectCarreraModal" class="custom-select" onchange="onCarreraModalChange()">
                    <option value="">Selecciona una carrera...</option>
                    @foreach ($carreras as $c)
                        <option value="{{ $c }}">{{ $c }}</option>
                    @endforeach
                </select>
                <svg class="select-arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </div>
        </div>

        <div class="form-group-row">
            <label for="selectAreaModal" class="form-label-side">Área:</label>
            <div class="select-wrapper">
                <select id="selectAreaModal" class="custom-select" onchange="onAreaModalChange()">
                    <option value="">Selecciona un área...</option>
                    @foreach ($areasDisponibles as $a)
                        <option value="{{ $a }}">{{ $a }}</option>
                    @endforeach
                </select>
                <svg class="select-arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </div>
        </div>

        <div class="modal-alert-error" id="areaModalError"></div>

        <button type="button" class="modal-btn-submit" id="btnConfirmArea" onclick="confirmarSeleccionArea()">
            Confirmar Área
        </button>
    </div>
</div>
@endif

<!-- Modal Confirmación para Remover Curso o Informe -->
<div class="modal-overlay" id="confirmDeleteModalOverlay" onclick="handleConfirmBackdropClick(event)">
    <div class="confirm-status-card" onclick="event.stopPropagation()">
        <div class="alert-content">
            <div class="warning-triangle">⚠</div>
            <h3 class="alert-title" id="confirmModalTitle">¿ESTÁ SEGURO?</h3>
            <p class="alert-desc" id="confirmModalDesc">Esta acción no se puede deshacer.</p>
        </div>
        <div class="modal-footer-btns">
            <button type="button" class="modal-btn btn-submit-delete" id="btnConfirmDelete">REMOVER</button>
            <button type="button" class="modal-btn btn-cancel-delete" onclick="closeConfirmDeleteModal()">CANCELAR</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    window.dashboardConfig = {
        rolePrefix: 'coordinador',
        catalogoCursos: @json($catalogoCursos),
        areasPorCarreraMap: @json($areasPorCarrera ?? []),
        todasLasAreasList: @json($areasDisponibles ?? []),
        currentSelectedArea: @json($areaActual ?? '')
    };
</script>
<script src="{{ asset('js/dashboard.js') }}"></script>
@endpush
