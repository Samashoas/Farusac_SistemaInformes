@extends('layouts.app')

@section('title', 'FARUSAC - Perfil de Usuario')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/docente/perfil.css') }}">
@endpush

@section('content')
    <div class="profile-card-wrapper">
        <div class="content-card">
            <h2 class="content-header-title">{{ $user->isCoordinador() ? 'Perfil de Coordinador' : 'Perfil de Usuario' }}
            </h2>

            <div class="profile-layout-container">
                <!-- Columna Izquierda: Avatar Circular Grande -->
                <div class="profile-avatar-column">
                    <div class="big-avatar-circle" title="{{ $user->nombre }}">
                        {{ strtoupper(substr($user->nombre, 0, 1)) }}
                    </div>
                    <div class="profile-role-badge">
                        <span class="badge-dot"></span>
                        <span>{{ $user->rol_nombre }}</span>
                    </div>
                </div>

                <!-- Columna Derecha: Formulario de Datos -->
                <div class="profile-form-column">
                    <!-- Campo Nombre -->
                    <div class="form-group">
                        <label class="field-label">Nombre:</label>
                        <input type="text" class="input-text input-readonly" value="{{ $user->nombre }}" readonly disabled>
                    </div>

                    <!-- Campo Correo -->
                    <div class="form-group">
                        <label class="field-label">Correo:</label>
                        <input type="email" class="input-text input-readonly" value="{{ $user->correo }}" readonly disabled>
                    </div>

                    <!-- Campo Registro Personal (Editable) -->
                    <div class="form-group">
                        <label class="field-label">Registro Personal:</label>
                        <input type="text" class="input-text input-readonly" value="{{ $user->numero ?? '' }}" readonly
                            disabled>
                    </div>

                    <!-- Fila 2 Columnas: Cargo y Plaza -->
                    <div class="form-row-2col">
                        <div class="form-group">
                            <label class="field-label">Cargo:</label>
                            <input type="text" class="input-text input-readonly" value="{{ $user->rol_nombre }}" readonly
                                disabled>
                        </div>

                        <div class="form-group">
                            <label class="field-label">Plaza:</label>
                            <input type="text" class="input-text input-readonly"
                                value="{{ ucfirst($user->plaza ?? 'No asignada') }}" readonly disabled>
                        </div>
                    </div>

                    @if ($user->isCoordinador())
                        <!-- Campo Área a cargo (Exclusivo Coordinador) -->
                        <div class="form-group">
                            <label class="field-label">Área a cargo:</label>
                            <input type="text" class="input-text input-readonly"
                                value="{{ ($user->area ?? session('coordinador_area')) ? (($user->area ?? session('coordinador_area')) . (session('coordinador_carrera') ? ' (' . session('coordinador_carrera') . ')' : '')) : 'Sin área asignada' }}"
                                readonly disabled>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Contenedor Botones de Acción -->
            <div class="profile-actions-container" id="profileActionsContainer">
                <button type="button" class="modal-btn modal-btn-cancel" id="btnCancelar" onclick="cancelarEdicion()">
                    Cancelar
                </button>
                <button type="button" class="modal-btn modal-btn-confirm" id="btnConfirmar" onclick="guardarPerfil()">
                    Confirmar
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/perfil.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            initPerfil(@json($user->numero ?? ''), "{{ route('docente.perfil.actualizar') }}");
        });
    </script>
@endpush