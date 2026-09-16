<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>FARUSAC - Perfil de Usuario</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        /* --- DISEÑO SISTEMA Y PALETA INSTITUCIONAL --- */
        :root {
            --color-azul: #002D72;       /* Pantone 288C */
            --color-oro: #AC8400;        /* Pantone 118C */
            --color-terracota: #B94700;  /* Pantone 1525C */
            --color-fondo: #f4f7fa;
            --color-tarjeta: #ffffff;
            --color-texto-principal: #1a202c;
            --color-texto-secundario: #4a5568;
            --color-texto-claro: #718096;
            --color-borde: #e2e8f0;
            --border-radius-card: 16px;
            --border-radius-input: 8px;
            --shadow-premium: 0 10px 30px rgba(0, 45, 114, 0.04);
            --transition-smooth: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--color-fondo);
            color: var(--color-texto-principal);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* --- HEADER / BARRA SUPERIOR --- */
        .admin-header {
            background-color: #ffffff;
            border-bottom: 2px solid transparent;
            background-image: linear-gradient(white, white),
                linear-gradient(90deg, var(--color-azul), var(--color-oro), var(--color-terracota));
            background-origin: border-box;
            background-clip: padding-box, border-box;
            height: 90px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 40px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .hamburger-btn {
            background: none;
            border: none;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            gap: 6px;
            padding: 5px;
            outline: none;
        }

        .hamburger-line {
            width: 24px;
            height: 2.5px;
            background-color: var(--color-azul);
            border-radius: 2px;
            transition: var(--transition-smooth);
        }

        .header-logo {
            height: 48px;
            width: auto;
            object-fit: contain;
        }

        .header-title {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            font-family: 'Outfit', sans-serif;
            font-size: 26px;
            font-weight: 700;
            color: var(--color-texto-principal);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin: 0;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 15px;
            cursor: pointer;
            position: relative;
            padding: 5px 10px;
            border-radius: 30px;
            transition: var(--transition-smooth);
        }

        .header-right:hover {
            background-color: #f7fafc;
        }

        .profile-info {
            text-align: right;
        }

        .profile-name {
            font-size: 14px;
            font-weight: 600;
            color: var(--color-texto-principal);
        }

        .profile-email {
            font-size: 11px;
            color: var(--color-texto-claro);
            margin-top: 1px;
        }

        .profile-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--color-azul), var(--color-oro));
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 18px;
            border: 2px solid #ffffff;
            box-shadow: 0 2px 8px rgba(0, 45, 114, 0.15);
        }

        .profile-arrow {
            width: 18px;
            height: 18px;
            color: var(--color-texto-claro);
            margin-left: 8px;
            transition: var(--transition-smooth);
        }

        /* Dropdown Perfil */
        .dropdown-menu {
            position: absolute;
            top: 65px;
            right: 0;
            background-color: #ffffff;
            border: 1.5px solid var(--color-borde);
            border-radius: 12px;
            width: 180px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            display: none;
            overflow: hidden;
            z-index: 110;
        }

        .dropdown-menu.active {
            display: block;
            animation: slideDown 0.2s ease;
        }

        .dropdown-item {
            width: 100%;
            padding: 12px 20px;
            font-size: 14px;
            color: var(--color-texto-secundario);
            background: none;
            border: none;
            text-align: left;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            transition: background-color 0.2s ease;
        }

        .dropdown-item:hover {
            background-color: #f7fafc;
            color: var(--color-azul);
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* --- APP CONTAINER (SIDEBAR + MAIN CONTENT) --- */
        .app-container {
            display: flex;
            min-height: calc(100vh - 90px);
            position: relative;
        }

        /* MENÚ LATERAL (SIDEBAR OVERLAY) */
        .admin-sidebar {
            width: 260px;
            background-color: #ffffff;
            border-right: 1.5px solid var(--color-borde);
            display: flex;
            flex-direction: column;
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s ease;
            flex-shrink: 0;
            position: fixed;
            top: 90px;
            left: 0;
            height: calc(100vh - 90px);
            z-index: 99;
            box-shadow: 10px 0 25px rgba(0, 45, 114, 0.08);
            transform: translateX(0);
            overflow-y: auto;
        }

        .admin-sidebar.collapsed {
            transform: translateX(-100%);
            box-shadow: none;
        }

        .sidebar-nav {
            padding: 30px 15px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 14px 20px;
            text-decoration: none;
            color: var(--color-texto-secundario);
            font-size: 14px;
            font-weight: 500;
            border-radius: 12px;
            transition: all 0.25s ease;
        }

        .sidebar-icon {
            width: 20px;
            height: 20px;
            color: var(--color-texto-claro);
            transition: all 0.25s ease;
        }

        .sidebar-link-inicio:hover {
            background-color: rgba(0, 45, 114, 0.05);
            color: var(--color-azul);
        }
        .sidebar-link-inicio:hover .sidebar-icon {
            color: var(--color-azul);
        }

        /* Módulo Activo Perfil */
        .sidebar-link-perfil {
            background-color: rgba(0, 45, 114, 0.08);
            color: var(--color-azul);
            font-weight: 600;
        }
        .sidebar-link-perfil .sidebar-icon {
            color: var(--color-azul);
        }

        .sidebar-link-informes:hover {
            background-color: rgba(0, 45, 114, 0.05);
            color: var(--color-azul);
        }
        .sidebar-link-informes:hover .sidebar-icon {
            color: var(--color-azul);
        }

        /* --- CONTENIDO PRINCIPAL --- */
        .main-content-area {
            flex-grow: 1;
            padding: 40px;
            transition: padding-left 0.3s ease;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        /* --- TARJETA DE PERFIL --- */
        .content-card {
            background-color: #ffffff;
            border-radius: var(--border-radius-card);
            border: 1.5px solid #edf2f7;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.01);
            padding: 45px 50px;
            width: 100%;
            max-width: 950px;
        }

        .content-header-title {
            font-family: 'Outfit', sans-serif;
            font-size: 22px;
            font-weight: 700;
            color: var(--color-texto-principal);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 35px;
            text-align: center;
            border-bottom: 1.5px solid #edf2f7;
            padding-bottom: 18px;
        }

        /* Layout de 2 Columnas (Avatar Izquierda - Formulario Derecha) */
        .profile-layout-container {
            display: grid;
            grid-template-columns: 240px 1fr;
            gap: 50px;
            align-items: center;
        }

        /* Columna Izquierda: Avatar Circular Grande */
        .profile-avatar-column {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 16px;
        }

        .big-avatar-circle {
            width: 190px;
            height: 190px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--color-azul) 0%, var(--color-oro) 100%);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Outfit', sans-serif;
            font-size: 82px;
            font-weight: 800;
            border: 5px solid #ffffff;
            box-shadow: 0 12px 30px rgba(0, 45, 114, 0.18);
            user-select: none;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .big-avatar-circle:hover {
            transform: scale(1.02);
            box-shadow: 0 16px 36px rgba(0, 45, 114, 0.22);
        }

        .profile-role-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 16px;
            border-radius: 20px;
            background-color: rgba(0, 45, 114, 0.08);
            color: var(--color-azul);
            font-family: 'Outfit', sans-serif;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .badge-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #10b981; /* Verde activo */
        }

        /* Columna Derecha: Formulario de Campos */
        .profile-form-column {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .form-row-2col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .field-label {
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            font-weight: 700;
            color: var(--color-texto-secundario);
            margin-bottom: 2px;
        }

        .input-text {
            height: 44px;
            border: 1.5px solid #cbd5e0;
            border-radius: var(--border-radius-input);
            padding: 0 16px;
            font-family: 'Inter', sans-serif;
            font-size: 14.5px;
            color: var(--color-texto-principal);
            width: 100%;
            outline: none;
            transition: all 0.2s ease;
        }

        .input-text:focus {
            border-color: var(--color-azul);
            box-shadow: 0 0 0 3px rgba(0, 45, 114, 0.08);
        }

        /* Campos No Editables / Bloqueados */
        .input-readonly {
            background-color: #f8fafc;
            border-color: #e2e8f0;
            color: #64748b;
            cursor: not-allowed;
            font-weight: 500;
            user-select: none;
        }

        .input-readonly:focus {
            border-color: #e2e8f0;
            box-shadow: none;
        }

        /* Campo Editable */
        .input-editable {
            background-color: #ffffff;
            border-color: #94a3b8;
            color: var(--color-texto-principal);
            font-weight: 500;
        }

        .input-editable:hover {
            border-color: var(--color-azul);
        }

        .input-editable:focus {
            border-color: var(--color-azul);
            box-shadow: 0 0 0 3px rgba(0, 45, 114, 0.1);
        }

        /* --- BOTONES DE ACCIÓN (CONFIRMAR / CANCELAR) --- */
        .profile-actions-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            margin-top: 35px;
            padding-top: 25px;
            border-top: 1.5px solid #edf2f7;
            opacity: 0;
            transform: translateY(12px);
            pointer-events: none;
            visibility: hidden;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .profile-actions-container.visible {
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
            visibility: visible;
        }

        .modal-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 36px;
            border-radius: 25px;
            font-family: 'Outfit', sans-serif;
            font-size: 13.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            outline: none;
            border: none;
        }

        .btn-submit {
            background-color: #ffffff;
            border: 2px solid #60a5fa;
            color: #2563eb;
            box-shadow: 0 2px 6px rgba(37, 99, 235, 0.08);
        }

        .btn-submit:hover,
        .btn-submit:active {
            background-color: #93c5fd;
            color: var(--color-azul);
            border-color: #3b82f6;
            box-shadow: 0 4px 14px rgba(96, 165, 250, 0.3);
            transform: translateY(-1px);
        }

        .btn-cancel {
            background-color: #ffffff;
            border: 2px solid #f87171;
            color: #dc2626;
            box-shadow: 0 2px 6px rgba(220, 38, 38, 0.08);
        }

        .btn-cancel:hover,
        .btn-cancel:active {
            background-color: #fca5a5;
            color: #991b1b;
            border-color: #ef4444;
            box-shadow: 0 4px 14px rgba(248, 113, 113, 0.3);
            transform: translateY(-1px);
        }

        .modal-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }

        /* --- TOAST NOTIFICATION --- */
        .toast-notification {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background-color: #1e293b;
            color: #ffffff;
            padding: 14px 24px;
            border-radius: 12px;
            font-size: 13.5px;
            font-weight: 500;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            gap: 12px;
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            z-index: 1100;
        }

        .toast-notification.show {
            transform: translateY(0);
            opacity: 1;
        }

        .toast-notification.success {
            border-left: 4px solid #38a169;
        }

        .toast-notification.error {
            border-left: 4px solid #e53e3e;
        }

        @media (max-width: 860px) {
            .profile-layout-container {
                grid-template-columns: 1fr;
                gap: 35px;
            }
            .content-card {
                padding: 30px 25px;
            }
            .form-row-2col {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .admin-header {
                padding: 0 20px;
            }
            .header-title {
                font-size: 18px;
            }
            .main-content-area {
                padding: 20px 15px;
            }
        }
    </style>
</head>

<body>

    <!-- Header / Barra Superior -->
    <header class="admin-header">
        <div class="header-left">
            <button type="button" class="hamburger-btn" id="hamburgerBtn" title="Menú">
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
            </button>
            <a href="{{ route('docente.dashboard') }}">
                <img src="{{ asset('images/FarusacLogo.png') }}" class="header-logo" alt="Logo FARUSAC">
            </a>
        </div>

        <h1 class="header-title">Sistema de Informes</h1>

        <div class="header-right" id="profileToggle">
            <div class="profile-info">
                <div class="profile-name">{{ $user->nombre }}</div>
                <div class="profile-email">{{ $user->correo }}</div>
            </div>
            <div class="profile-avatar">
                {{ strtoupper(substr($user->nombre, 0, 1)) }}
            </div>
            <svg class="profile-arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 12 15 18 9"></polyline>
            </svg>

            <div class="dropdown-menu" id="profileDropdown">
                <form action="{{ route('logout') }}" method="POST" id="logout-form" style="display: none;">
                    @csrf
                </form>
                <button type="button"
                    onclick="event.stopPropagation(); document.getElementById('logout-form').submit();"
                    class="dropdown-item">
                    Cerrar sesión
                </button>
            </div>
        </div>
    </header>

    <!-- Contenedor general (Sidebar + Contenido) -->
    <div class="app-container">

        <!-- Menú Lateral (Sidebar Overlay) -->
        <aside class="admin-sidebar collapsed" id="docenteSidebar">
            <nav class="sidebar-nav">
                <!-- Enlace Inicio -->
                <a href="{{ route('docente.dashboard') }}" class="sidebar-link sidebar-link-inicio">
                    <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Inicio</span>
                </a>

                <!-- Enlace Perfil (Activo) -->
                <a href="{{ route('docente.perfil') }}" class="sidebar-link sidebar-link-perfil">
                    <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Perfil</span>
                </a>

                <!-- Enlace Informes -->
                <a href="{{ route('docente.informes') }}" class="sidebar-link sidebar-link-informes">
                    <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Informes</span>
                </a>
            </nav>
        </aside>

        <!-- Area de Contenido Principal -->
        <main class="main-content-area" id="mainContent">

            <!-- Tarjeta de Perfil -->
            <div class="content-card">
                <h2 class="content-header-title">Perfil de Usuario</h2>

                <div class="profile-layout-container">
                    <!-- Columna Izquierda: Avatar Circular Grande -->
                    <div class="profile-avatar-column">
                        <div class="big-avatar-circle" title="{{ $user->nombre }}">
                            {{ strtoupper(substr($user->nombre, 0, 1)) }}
                        </div>
                        <div class="profile-role-badge">
                            <span class="badge-dot"></span>
                            <span>{{ ucfirst($user->rol) }}</span>
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

                        <!-- Campo Teléfono -->
                        <div class="form-group">
                            <label for="inputTelefono" class="field-label">Teléfono:</label>
                            <input type="text" id="inputTelefono" class="input-text input-editable"
                                value="{{ $user->numero ?? '' }}"
                                placeholder="Ej. 12345678"
                                autocomplete="off">
                        </div>

                        <!-- Fila 2 Columnas: Cargo y Plaza -->
                        <div class="form-row-2col">
                            <!-- Campo Cargo (Rol) -->
                            <div class="form-group">
                                <label class="field-label">Cargo:</label>
                                <input type="text" class="input-text input-readonly" value="{{ ucfirst($user->rol) }}" readonly disabled>
                            </div>

                            <!-- Campo Plaza -->
                            <div class="form-group">
                                <label class="field-label">Plaza:</label>
                                <input type="text" class="input-text input-readonly" value="{{ $user->plaza ?? 'Sin asignar' }}" readonly disabled>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción (Confirmar y Cancelar) - Visibles únicamente al detectar cambios -->
                <div class="profile-actions-container" id="profileActionsContainer">
                    <button type="button" class="modal-btn btn-submit" id="btnConfirmar" onclick="guardarPerfil()">Confirmar</button>
                    <button type="button" class="modal-btn btn-cancel" id="btnCancelar" onclick="cancelarEdicion()">Cancelar</button>
                </div>
            </div>

        </main>
    </div>

    <!-- TOAST NOTIFICATION -->
    <div class="toast-notification" id="toastNotification">
        <span id="toastMessage"></span>
    </div>

    <!-- JAVASCRIPT: DETECCIÓN DE CAMBIOS, AJAX Y MENÚS -->
    <script>
        // Valor inicial del teléfono cargado desde la BD
        let initialTelefono = @json($user->numero ?? '');
        const inputTelefono = document.getElementById('inputTelefono');
        const actionsContainer = document.getElementById('profileActionsContainer');
        const btnConfirmar = document.getElementById('btnConfirmar');
        const btnCancelar = document.getElementById('btnCancelar');

        // 1. Detección en tiempo real de cambios en el campo editable
        function checkChanges() {
            const currentVal = inputTelefono.value.trim();
            const storedVal = (initialTelefono || '').trim();

            if (currentVal !== storedVal) {
                actionsContainer.classList.add('visible');
            } else {
                actionsContainer.classList.remove('visible');
            }
        }

        inputTelefono.addEventListener('input', checkChanges);

        // 2. Cancelar edición: restaura el valor original y oculta los botones
        function cancelarEdicion() {
            inputTelefono.value = initialTelefono || '';
            actionsContainer.classList.remove('visible');
        }

        // 3. Confirmar / Guardar cambios vía AJAX
        async function guardarPerfil() {
            const nuevoTelefono = inputTelefono.value.trim();

            btnConfirmar.disabled = true;
            btnCancelar.disabled = true;
            btnConfirmar.textContent = 'GUARDANDO...';

            try {
                const response = await fetch(`{{ route('docente.perfil.actualizar') }}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        numero: nuevoTelefono
                    })
                });

                const data = await response.json();

                btnConfirmar.disabled = false;
                btnCancelar.disabled = false;
                btnConfirmar.textContent = 'CONFIRMAR';

                if (response.ok && data.success) {
                    initialTelefono = nuevoTelefono;
                    inputTelefono.value = nuevoTelefono;
                    actionsContainer.classList.remove('visible');
                    showToast(data.message || 'Perfil actualizado exitosamente.', 'success');
                } else {
                    showToast(data.message || 'Error al actualizar el perfil.', 'error');
                }
            } catch (err) {
                console.error('Error al actualizar perfil:', err);
                btnConfirmar.disabled = false;
                btnCancelar.disabled = false;
                btnConfirmar.textContent = 'CONFIRMAR';
                showToast('Error de conexión con el servidor.', 'error');
            }
        }

        // 4. Helper para Toast Notifications
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toastNotification');
            const toastMsg = document.getElementById('toastMessage');
            toastMsg.textContent = message;
            toast.className = `toast-notification ${type} show`;

            setTimeout(() => {
                toast.classList.remove('show');
            }, 3500);
        }

        // 5. Toggle de Header y Sidebar
        document.addEventListener('DOMContentLoaded', () => {
            const profileToggle = document.getElementById('profileToggle');
            const profileDropdown = document.getElementById('profileDropdown');
            const hamburgerBtn = document.getElementById('hamburgerBtn');
            const docenteSidebar = document.getElementById('docenteSidebar');

            profileToggle.addEventListener('click', (e) => {
                e.stopPropagation();
                profileDropdown.classList.toggle('active');
            });

            document.addEventListener('click', () => {
                profileDropdown.classList.remove('active');
            });

            hamburgerBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                docenteSidebar.classList.toggle('collapsed');
            });

            document.addEventListener('click', (e) => {
                if (!docenteSidebar.contains(e.target) && !hamburgerBtn.contains(e.target)) {
                    docenteSidebar.classList.add('collapsed');
                }
            });

            docenteSidebar.addEventListener('click', (e) => {
                e.stopPropagation();
            });
        });
    </script>
</body>

</html>
