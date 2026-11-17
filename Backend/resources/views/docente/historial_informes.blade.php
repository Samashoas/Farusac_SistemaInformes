@extends('layouts.app')

@section('title', 'Historial de Informes - FARUSAC')

@php
    $rolePrefix = (isset($user) && $user->isCoordinador()) || Auth::user()->isCoordinador() ? 'coordinador' : 'docente';
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/informes.css') }}">
@endpush

@section('content')
<div class="content-card">
    <h2 class="content-header-title">Historial de Informes</h2>

    <!-- Barra de Herramientas (Filtros y Búsqueda) -->
    <div class="toolbar">
        <div class="filters-left">
            <div class="form-group search-group">
                <div class="label-with-info">
                    <span>Buscar</span>
                    <div class="tooltip-container">
                        <button type="button" class="tooltip-btn">?</button>
                        <div class="tooltip-content">
                            Buscar por nombre o código de curso
                        </div>
                    </div>
                </div>
                <input type="text" id="searchInput" class="input-text" placeholder="Ej. Fotografía, 30313...">
            </div>

            <div class="form-group">
                <label for="filterCarrera" class="label-with-info">Carrera</label>
                <select id="filterCarrera" class="select-filter">
                    <option value="">Todos</option>
                    @foreach ($carreras as $carr)
                        <option value="{{ $carr }}">{{ $carr }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="filterCurso" class="label-with-info">Curso</label>
                <select id="filterCurso" class="select-filter">
                    <option value="">Todos</option>
                    @foreach ($cursosDocente as $cur)
                        <option value="{{ $cur }}">{{ $cur }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="filterPeriodo" class="label-with-info">Periodo</label>
                <select id="filterPeriodo" class="select-filter">
                    <option value="">Todos</option>
                    <option value="Primer Semestre">Primer Semestre</option>
                    <option value="Segundo Semestre">Segundo Semestre</option>
                    <option value="Vacaciones Junio">Vacaciones Junio</option>
                    <option value="Vacaciones Diciembre">Vacaciones Diciembre</option>
                </select>
            </div>

            <div class="form-group">
                <label for="filterAnio" class="label-with-info">Año</label>
                <select id="filterAnio" class="select-filter">
                    <option value="">Todos</option>
                    @foreach ($anios as $a)
                        <option value="{{ $a }}">{{ $a }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="filterMes" class="label-with-info">Mes</label>
                <select id="filterMes" class="select-filter">
                    <option value="">Todos</option>
                    @foreach (['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'] as $m)
                        <option value="{{ $m }}">{{ $m }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Tabla de Informes con Componente Blade -->
    <x-informe-table :informes="$informes" :rolePrefix="$rolePrefix" />
</div>

<!-- MODAL DE CONFIRMACIÓN DE ELIMINACIÓN -->
<div class="modal-overlay" id="deleteInformeModal" onclick="closeDeleteModal()">
    <div class="confirm-status-card" onclick="event.stopPropagation()">
        <div class="alert-content">
            <div class="warning-triangle">⚠</div>
            <h3 class="alert-title">¿ESTÁ SEGURO QUE QUIERE ELIMINAR ESTE INFORME DE FORMA PERMANENTE?</h3>
            <p class="alert-desc" id="deleteModalDesc">
                Esta acción no se puede deshacer. Se eliminarán todas las semanas y registros asociados al informe del curso.
            </p>
        </div>
        <div class="modal-footer">
            <button type="button" class="modal-btn btn-submit" id="btnConfirmDelete" onclick="executeDeleteInforme()">CONFIRMAR</button>
            <button type="button" class="modal-btn btn-cancel" onclick="closeDeleteModal()">CANCELAR</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    window.historialRolePrefix = "{{ $rolePrefix }}";
</script>
<script src="{{ asset('js/historial-informes.js') }}"></script>
@endpush
