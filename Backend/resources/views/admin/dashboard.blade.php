@extends('layouts.app')

@section('title', 'Panel Administrador - Sistema de Informes')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endpush

@section('content')
<div class="cards-grid">
    <!-- Fila Superior: 3 Columnas (Gestiones) -->
    <div class="top-row">
        <!-- Gestión de Usuarios -->
        <div class="dashboard-card card-usuarios">
            <div class="card-header-bar">
                <h3 class="card-title">Gestión de Usuarios</h3>
                <button type="button" class="info-btn" onclick="showInfo('usuarios')">?</button>
            </div>
            <div class="card-icon">
                <img src="{{ asset('images/Admin/GestionUsuarios.png') }}" alt="Gestión de Usuarios">
            </div>
            <a href="{{ route('admin.usuarios') }}" class="card-btn">Ir a Gestión de Usuarios</a>
        </div>

        <!-- Gestión de Cursos -->
        <div class="dashboard-card card-cursos">
            <div class="card-header-bar">
                <h3 class="card-title">Gestión de Cursos</h3>
                <button type="button" class="info-btn" onclick="showInfo('cursos')">?</button>
            </div>
            <div class="card-icon">
                <img src="{{ asset('images/Admin/GestionCursos.png') }}" alt="Gestión de Cursos">
            </div>
            <a href="{{ route('admin.cursos') }}" class="card-btn">Ir a Gestión de Cursos</a>
        </div>

        <!-- Gestión de Informes -->
        <div class="dashboard-card card-informes">
            <div class="card-header-bar">
                <h3 class="card-title">Gestión de Informes</h3>
                <button type="button" class="info-btn" onclick="showInfo('informes')">?</button>
            </div>
            <div class="card-icon">
                <img src="{{ asset('images/Admin/GestionInformes.png') }}" alt="Gestión de Informes">
            </div>
            <a href="{{ route('admin.informes') }}" class="card-btn">Ir a Gestión de Informes</a>
        </div>
    </div>

    <!-- Fila Inferior: Carga de Datos (Centrada y Destacada) -->
    <div class="bottom-row">
        <div class="dashboard-card featured card-carga">
            <div class="card-header-bar">
                <h3 class="card-title">Carga de Datos</h3>
                <button type="button" class="info-btn" onclick="showInfo('carga')">?</button>
            </div>
            <div class="card-icon">
                <img src="{{ asset('images/Admin/CargaDatos.png') }}" alt="Carga de Datos">
            </div>
            <a href="{{ route('admin.carga-datos') }}" class="card-btn">Ir a Carga de Datos</a>
        </div>
    </div>
</div>

<!-- Modal de Información Emergente -->
<div class="info-modal-overlay" id="infoModal" onclick="closeInfoModal()">
    <div class="modal-card" onclick="event.stopPropagation()">
        <button type="button" class="modal-close-btn" onclick="closeInfoModal()" aria-label="Cerrar modal">×</button>
        <div class="modal-header-icon">
            <img id="modalIcon" src="" alt="">
        </div>
        <h2 class="modal-title" id="modalTitle">Título</h2>
        <div class="modal-divider"></div>
        <p class="modal-description" id="modalDescription">Descripción</p>
        <button type="button" class="modal-action-btn" onclick="closeInfoModal()">Entendido</button>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/admin-dashboard.js') }}"></script>
@endpush
