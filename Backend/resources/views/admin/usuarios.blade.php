@extends('layouts.app')

@section('title', 'Gestión de Usuarios - Sistema de Informes')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endpush

@section('content')
    <div class="content-card">
        <h2 class="content-header-title">Gestión de Usuarios</h2>

        <!-- Barra de Herramientas (Filtros y Búsqueda) -->
        <div class="toolbar">
            <div class="filters-left">
                <!-- Entrada de Búsqueda -->
                <div class="form-group search-group">
                    <div class="label-with-info">
                        <span>Buscar</span>
                        <div class="tooltip-container">
                            <button type="button" class="tooltip-btn">?</button>
                            <div class="tooltip-content">
                                Se puede buscar por registro personal, correo electrónico o nombre de usuario por medio de
                                texto.
                            </div>
                        </div>
                    </div>
                    <input type="text" id="searchInput" class="input-text"
                        placeholder="Ej. Juan, juan@farusac.edu.gt, 20141721...">
                </div>

                <!-- Filtro de Rol -->
                <div class="form-group">
                    <label for="rolFilter" class="label-with-info">Rol</label>
                    <select id="rolFilter" class="select-filter">
                        <option value="todos">Todos</option>
                        <option value="administrador">Administrador</option>
                        <option value="coordinador">Coordinador</option>
                        <option value="docente">Docente</option>
                    </select>
                </div>

                <!-- Filtro de Estado -->
                <div class="form-group">
                    <label for="estadoFilter" class="label-with-info">Estado</label>
                    <select id="estadoFilter" class="select-filter">
                        <option value="todos">Todos</option>
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                    </select>
                </div>
            </div>

            <!-- Botón y Acciones de Agregar / Carga Masiva -->
            <div class="add-actions-container">
                <button type="button" class="add-user-trigger" id="addUserBtn" title="Agregar Usuario Individual">
                    <div class="add-user-avatar">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span class="plus-badge">+</span>
                    </div>
                </button>
                <a href="{{ route('admin.carga-datos') }}" class="bulk-load-link" title="Ir a Carga de Datos Masiva">carga
                    masiva</a>
            </div>
        </div>

        <!-- Tabla de Usuarios -->
        <div class="table-responsive">
            <table class="users-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Registro Personal</th>
                        <th>Rol</th>
                        <th>Plaza</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="usersTableBody">
                    <!-- Filas generadas dinámicamente -->
                </tbody>
            </table>
        </div>

        @if ($usuarios instanceof \Illuminate\Pagination\LengthAwarePaginator && $usuarios->hasPages())
            <div class="pagination-container"
                style="display: flex; justify-content: center; align-items: center; margin-top: 25px;">
                {{ $usuarios->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Emergente: Agregar Nuevo Usuario -->
    <div class="modal-overlay" id="addUserModal" onclick="closeAddUserModal()">
        <div class="add-user-modal" onclick="event.stopPropagation()">
            <button type="button" class="modal-close-btn" onclick="closeAddUserModal()">×</button>
            <h2 class="modal-title">Agregar Nuevo Usuario</h2>

            <form class="modal-form" id="addUserForm" onsubmit="handleCreateUser(event)">
                <div class="form-group">
                    <label for="newUserName" class="modal-form-label">Nombre</label>
                    <input type="text" id="newUserName" class="input-text" placeholder="Ej. Juan Pérez" required>
                </div>

                <div class="form-group">
                    <label for="newUserEmail" class="modal-form-label">Correo</label>
                    <input type="email" id="newUserEmail" class="input-text" placeholder="Ej. juan.perez@farusac.edu.gt"
                        required>
                </div>

                <div class="form-group">
                    <label for="newUserPhone" class="modal-form-label">Registro Personal</label>
                    <input type="text" id="newUserPhone" class="input-text" placeholder="Ej. 20141721" required>
                </div>

                <div class="form-group">
                    <label for="newUserRole" class="modal-form-label">Rol</label>
                    <select id="newUserRole" class="select-filter" style="width: 100%;" required>
                        <option value="docente">Docente</option>
                        <option value="coordinador">Coordinador</option>
                        <option value="administrador">Administrador</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="newUserPlaza" class="modal-form-label">Plaza</label>
                    <select id="newUserPlaza" class="select-filter" style="width: 100%;" required>
                        <option value="titular + ampliacion">Titular + Ampliacion</option>
                        <option value="titular">Titular</option>
                        <option value="interino">Interino</option>
                    </select>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="modal-btn btn-submit">Agregar</button>
                    <button type="button" class="modal-btn btn-cancel" onclick="closeAddUserModal()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Emergente: Editar Usuario -->
    <div class="modal-overlay" id="editUserModal" onclick="closeEditUserModal()">
        <div class="add-user-modal" onclick="event.stopPropagation()">
            <button type="button" class="modal-close-btn" onclick="closeEditUserModal()">×</button>
            <h2 class="modal-title">Editar Usuario</h2>

            <form class="modal-form" id="editUserForm" onsubmit="handleUpdateUser(event)">
                <input type="hidden" id="editUserId">

                <div class="form-group">
                    <label for="editUserName" class="modal-form-label">Nombre</label>
                    <input type="text" id="editUserName" class="input-text input-disabled" readonly>
                </div>

                <div class="form-group">
                    <label for="editUserEmail" class="modal-form-label">Correo</label>
                    <input type="email" id="editUserEmail" class="input-text input-disabled" readonly>
                </div>

                <div class="form-group">
                    <label for="editUserPhone" class="modal-form-label">Registro Personal</label>
                    <input type="text" id="editUserPhone" class="input-text" placeholder="Ej. 20141721" required>
                </div>

                <div class="form-group">
                    <label for="editUserRole" class="modal-form-label">Rol</label>
                    <select id="editUserRole" class="select-filter" style="width: 100%;" required>
                        <option value="docente">Docente</option>
                        <option value="coordinador">Coordinador</option>
                        <option value="administrador">Administrador</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="editUserPlaza" class="modal-form-label">Plaza</label>
                    <select id="editUserPlaza" class="select-filter" style="width: 100%;" required>
                        <option value="titular + ampliacion">Titular + Ampliacion</option>
                        <option value="titular">Titular</option>
                        <option value="interino">Interino</option>
                    </select>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="modal-btn btn-submit">Confirmar</button>
                    <button type="button" class="modal-btn btn-cancel" onclick="closeEditUserModal()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Emergente: Confirmación de Alerta de Cambio de Estado -->
    <div class="modal-overlay" id="confirmStatusModal" onclick="closeConfirmStatusModal()">
        <div class="confirm-status-card" onclick="event.stopPropagation()" id="confirmStatusCard">
            <div class="alert-content">
                <div class="warning-triangle" id="alertIcon">⚠</div>
                <h3 class="alert-title" id="alertTitle">¿ESTÁ SEGURO QUE QUIERE DESACTIVAR EL USUARIO?</h3>
                <p class="alert-desc" id="alertDesc">El usuario perderá acceso al sistema hasta que vuelva a ser habilitado
                </p>
            </div>
            <div class="modal-footer" style="margin-top: 25px;">
                <button type="button" class="modal-btn btn-submit" id="confirmStatusBtn"
                    onclick="executeToggleEstado()">Confirmar</button>
                <button type="button" class="modal-btn btn-cancel" onclick="closeConfirmStatusModal()">Cancelar</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/admin-usuarios.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            initAdminUsuarios(@json($usuarios));
        });
    </script>
@endpush