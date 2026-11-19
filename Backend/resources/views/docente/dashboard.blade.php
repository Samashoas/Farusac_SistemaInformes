@extends('layouts.app')

@section('title', 'FARUSAC - Panel del Docente')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/docente/dashboard.css') }}">
@endpush

@section('content')
<div class="circles-dashboard-container" id="coursesGrid">
    @foreach ($cursosAsignados as $curso)
        <div class="circle-card-item" id="curso-item-{{ $curso->id }}" onclick="window.location.href='{{ route('docente.informes.crear') }}?curso_id={{ $curso->id }}'">
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

    <!-- Botón Agregar nuevo Curso -->
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

<!-- Modal Agregar Curso -->
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

<!-- Modal Confirmación para Remover Curso -->
<div class="modal-overlay" id="confirmDeleteModalOverlay" onclick="closeConfirmDeleteModal()">
    <div class="confirm-status-card" onclick="event.stopPropagation()">
        <div class="alert-content">
            <div class="warning-triangle">⚠</div>
            <h3 class="alert-title" id="confirmModalTitle">¿ESTÁ SEGURO QUE DESEA REMOVER EL CURSO?</h3>
            <p class="alert-desc" id="confirmModalDesc">El curso será eliminado de su lista de cursos asignados</p>
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
        rolePrefix: 'docente',
        catalogoCursos: @json($catalogoCursos)
    };
</script>
<script src="{{ asset('js/dashboard.js') }}"></script>
@endpush
