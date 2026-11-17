@extends('layouts.app')

@section('title', 'Gestión de Cursos - FARUSAC')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endpush

@section('content')
<div class="content-card">
    <h2 class="content-header-title">Gestión de Cursos</h2>

    <!-- Barra de Herramientas (Filtros y Búsqueda) -->
    <div class="toolbar">
        <div class="filters-left">
            <!-- Filtro Buscar por texto -->
            <div class="form-group search-group">
                <div class="label-with-info">
                    <span>Buscar</span>
                    <div class="tooltip-container">
                        <button type="button" class="tooltip-btn">?</button>
                        <div class="tooltip-content">
                            Indicar que se puede buscar por nombre o código de curso
                        </div>
                    </div>
                </div>
                <input type="text" id="searchInput" class="input-text" style="width: 100%;" placeholder="Ej. Fotografía, 30313...">
            </div>

            <!-- Filtro Carrera -->
            <div class="form-group">
                <label for="carreraFilter" class="label-with-info">Carrera</label>
                <select id="carreraFilter" class="select-filter">
                    <option value="todos">Todos</option>
                    <option value="Arquitectura">Arquitectura</option>
                    <option value="Diseño Gráfico">Diseño Gráfico</option>
                </select>
            </div>

            <!-- Filtro Semestre -->
            <div class="form-group">
                <label for="semestreFilter" class="label-with-info">Semestre</label>
                <select id="semestreFilter" class="select-filter">
                    <option value="todos">Todos</option>
                    <option value="Primer Semestre">Primer Semestre</option>
                    <option value="Segundo Semestre">Segundo Semestre</option>
                    <option value="Vacaciones Junio">Vacaciones Junio</option>
                    <option value="Vacaciones Diciembre">Vacaciones Diciembre</option>
                </select>
            </div>

            <!-- Filtro Año -->
            <div class="form-group">
                <label for="anioFilter" class="label-with-info">Año</label>
                <select id="anioFilter" class="select-filter">
                    <option value="todos">Todos</option>
                </select>
            </div>
        </div>

        <!-- Botón Agregar Curso y Carga Masiva -->
        <div class="add-actions-container">
            <button type="button" class="add-course-trigger" id="addCourseBtn" title="Agregar Curso Individual">
                <div class="add-course-avatar">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span class="plus-badge">+</span>
                </div>
            </button>
            <a href="{{ route('admin.carga-datos') }}" class="bulk-load-link" title="Ir a Carga de Datos Masiva">carga masiva</a>
        </div>
    </div>

    <!-- Tabla de Cursos -->
    <div class="table-responsive">
        <table class="courses-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Carrera</th>
                    <th>Área</th>
                    <th>Curso</th>
                    <th>Código</th>
                    <th>Sección</th>
                    <th>Jornada</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="coursesTableBody">
                <!-- Las filas se inyectan con JavaScript -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Emergente: Agregar Curso -->
<div class="modal-overlay" id="addCourseModal" onclick="closeAddCourseModal()">
    <div class="modal-card" onclick="event.stopPropagation()">
        <button type="button" class="modal-close-btn" onclick="closeAddCourseModal()">×</button>
        <h2 class="modal-title">Agregar Nuevo Curso</h2>
        <form class="modal-form" id="addCourseForm" onsubmit="handleCreateCourse(event)">
            <div class="form-group">
                <label for="newCarrera" class="modal-form-label">Carrera</label>
                <select id="newCarrera" class="select-filter" style="width: 100%;" required>
                    <option value="Arquitectura">Arquitectura</option>
                    <option value="Diseño Gráfico">Diseño Gráfico</option>
                </select>
            </div>
            <div class="form-group" style="position: relative;">
                <label for="newArea" class="modal-form-label">Área</label>
                <input type="text" id="newArea" class="input-text" style="width: 100%;" placeholder="Ej. Área Tecnología y Expresión" autocomplete="off" required>
                <div id="areaSuggestions" class="suggestions-container"></div>
            </div>
            <div class="form-group" style="position: relative;">
                <label for="newCurso" class="modal-form-label">Nombre del Curso</label>
                <input type="text" id="newCurso" class="input-text" style="width: 100%;" placeholder="Ej. Fotografía" autocomplete="off" required>
                <div id="cursosSuggestions" class="suggestions-container"></div>
            </div>
            <div class="form-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <label for="newCodigo" class="modal-form-label">Código de Curso</label>
                    <input type="text" id="newCodigo" class="input-text" style="width: 100%;" placeholder="Ej. 30313" required>
                </div>
                <div style="position: relative;">
                    <label for="newSeccion" class="modal-form-label">Sección</label>
                    <input type="text" id="newSeccion" class="input-text" style="width: 100%;" placeholder="Ej. A" autocomplete="off" required>
                    <div id="seccionSuggestions" class="suggestions-container"></div>
                </div>
            </div>
            <div class="form-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <label for="newJornada" class="modal-form-label">Jornada</label>
                    <select id="newJornada" class="select-filter" style="width: 100%;" required>
                        <option value="Matutina">Matutina</option>
                        <option value="Vespertina">Vespertina</option>
                    </select>
                </div>
                <div>
                    <label for="newSemestre" class="modal-form-label">Semestre</label>
                    <select id="newSemestre" class="select-filter" style="width: 100%;" required>
                        <option value="Primer Semestre">Primer Semestre</option>
                        <option value="Segundo Semestre">Segundo Semestre</option>
                        <option value="Vacaciones Junio">Vacaciones Junio</option>
                        <option value="Vacaciones Diciembre">Vacaciones Diciembre</option>
                    </select>
                </div>
            </div>
            <div class="form-group" style="position: relative;">
                <label for="newAnio" class="modal-form-label">Año</label>
                <input type="text" id="newAnio" class="input-text" style="width: 100%;" placeholder="Ej. 2026" maxlength="4" autocomplete="off" required>
                <div id="anioSuggestions" class="suggestions-container"></div>
            </div>

            <div class="modal-footer" style="margin-top: 15px;">
                <button type="submit" class="modal-btn btn-submit">CONFIRMAR</button>
                <button type="button" class="modal-btn btn-cancel" onclick="closeAddCourseModal()">CANCELAR</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Emergente: Confirmación de Eliminación de Curso -->
<div class="modal-overlay" id="confirmDeleteModal" onclick="closeConfirmDeleteModal()">
    <div class="confirm-status-card" onclick="event.stopPropagation()">
        <div class="alert-content">
            <div class="warning-triangle">⚠</div>
            <h3 class="alert-title">¿ESTÁ SEGURO QUE QUIERE ELIMINAR ESTE CURSO DE FORMA PERMANENTE?</h3>
            <p class="alert-desc">Esta acción no se puede deshacer y el curso será removido del sistema.</p>
        </div>
        <div class="modal-footer" style="margin-top: 25px;">
            <button type="button" class="modal-btn btn-submit" onclick="executeDeleteCourse()">CONFIRMAR</button>
            <button type="button" class="modal-btn btn-cancel" onclick="closeConfirmDeleteModal()">CANCELAR</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/admin-cursos.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            initAdminCursos(@json($cursos));
        });
    </script>
@endpush
