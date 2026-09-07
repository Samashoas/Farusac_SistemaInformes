<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>FARUSAC - Historial de Informes</title>
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

        .sidebar-link-perfil:hover {
            background-color: rgba(0, 45, 114, 0.05);
            color: var(--color-azul);
        }
        .sidebar-link-perfil:hover .sidebar-icon {
            color: var(--color-azul);
        }

        /* Módulo Activo Informes */
        .sidebar-link-informes {
            background-color: rgba(0, 45, 114, 0.08);
            color: var(--color-azul);
            font-weight: 600;
        }
        .sidebar-link-informes .sidebar-icon {
            color: var(--color-azul);
        }

        /* --- CONTENIDO PRINCIPAL --- */
        .main-content-area {
            flex-grow: 1;
            padding: 40px;
            transition: padding-left 0.3s ease;
            width: 100%;
        }

        /* --- VISTA DE GESTIÓN DE INFORMES --- */
        .content-card {
            background-color: #ffffff;
            border-radius: var(--border-radius-card);
            border: 1.5px solid #edf2f7;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.01);
            padding: 35px;
            width: 100%;
        }

        .content-header-title {
            font-family: 'Outfit', sans-serif;
            font-size: 20px;
            font-weight: 700;
            color: var(--color-texto-principal);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 30px;
            text-align: center;
            border-bottom: 1.5px solid #edf2f7;
            padding-bottom: 15px;
        }

        /* Barra de Herramientas (Filtros y Búsqueda) */
        .toolbar {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .filters-left {
            display: flex;
            align-items: flex-end;
            gap: 16px;
            flex: 1;
            flex-wrap: wrap;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group.search-group {
            flex: 1;
            min-width: 250px;
        }

        .label-with-info {
            display: flex;
            align-items: center;
            font-family: 'Outfit', sans-serif;
            font-size: 13.5px;
            font-weight: 700;
            color: var(--color-texto-secundario);
            gap: 8px;
        }

        /* Tooltip */
        .tooltip-container {
            position: relative;
            display: inline-block;
        }

        .tooltip-btn {
            background-color: #edf2f7;
            border: none;
            color: var(--color-texto-claro);
            width: 18px;
            height: 18px;
            border-radius: 50%;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .tooltip-btn:hover {
            background-color: var(--color-azul);
            color: #ffffff;
        }

        .tooltip-content {
            visibility: hidden;
            width: 220px;
            background-color: #1a202c;
            color: #ffffff;
            text-align: center;
            border-radius: 6px;
            padding: 8px 12px;
            position: absolute;
            z-index: 10;
            bottom: 130%;
            left: 50%;
            transform: translateX(-50%);
            opacity: 0;
            transition: opacity 0.3s;
            font-family: 'Inter', sans-serif;
            font-size: 11px;
            font-weight: 400;
            line-height: 1.4;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }

        .tooltip-content::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: #1a202c transparent transparent transparent;
        }

        .tooltip-container:hover .tooltip-content {
            visibility: visible;
            opacity: 1;
        }

        .input-text {
            height: 42px;
            border: 1.5px solid #cbd5e0;
            border-radius: var(--border-radius-input);
            padding: 0 15px;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            color: var(--color-texto-principal);
            width: 100%;
            outline: none;
            transition: all 0.2s ease;
        }

        .input-text:focus {
            border-color: var(--color-azul);
            box-shadow: 0 0 0 3px rgba(0, 45, 114, 0.08);
        }

        .select-filter {
            height: 42px;
            border: 1.5px solid #cbd5e0;
            border-radius: var(--border-radius-input);
            padding: 0 35px 0 15px;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            color: var(--color-texto-principal);
            outline: none;
            background-color: #ffffff;
            cursor: pointer;
            min-width: 130px;
            transition: all 0.2s ease;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%234a5568' stroke-width='2.5'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 14px;
        }

        .select-filter:focus {
            border-color: var(--color-azul);
            box-shadow: 0 0 0 3px rgba(0, 45, 114, 0.08);
        }

        /* --- TABLA DE INFORMES --- */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            border-radius: 12px;
            border: 1.5px solid #edf2f7;
            margin-top: 20px;
        }

        .courses-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 14px;
        }

        .courses-table th {
            background-color: #fcfcfc;
            color: var(--color-texto-secundario);
            font-family: 'Outfit', sans-serif;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 18px 24px;
            border-bottom: 2px solid #edf2f7;
        }

        .courses-table td {
            padding: 16px 24px;
            border-bottom: 1.5px solid #edf2f7;
            color: var(--color-texto-principal);
            vertical-align: middle;
        }

        .courses-table tr:last-child td {
            border-bottom: none;
        }

        .courses-table tr:hover td {
            background-color: rgba(0, 45, 114, 0.01);
        }

        .td-id {
            font-weight: 700 !important;
            color: var(--color-azul);
            width: 70px;
        }

        .td-curso {
            font-weight: 600;
            color: var(--color-texto-principal);
        }

        .td-codigo {
            font-family: monospace;
            font-size: 14.5px;
            font-weight: 600;
            letter-spacing: 0.02em;
        }

        .td-seccion {
            font-weight: 600;
            color: var(--color-texto-principal);
        }

        .no-records-row {
            text-align: center !important;
            padding: 40px !important;
            color: var(--color-texto-claro) !important;
            font-style: italic;
        }

        /* Acciones */
        .action-btn {
            background: none;
            border: none;
            cursor: pointer;
            padding: 6px;
            border-radius: 8px;
            color: var(--color-texto-claro);
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }

        .action-btn:hover {
            background-color: #f7fafc;
            color: var(--color-azul);
        }

        .action-btn.delete-btn:hover {
            background-color: #fff5f5;
            color: #e53e3e; /* Rojo papelera */
        }

        .action-btn.edit-btn:hover {
            background-color: #f7fafc;
            color: var(--color-azul);
        }

        .action-btn.view-btn:hover {
            background-color: #f0fdf4;
            color: #16a34a;
        }

        .action-btn svg {
            width: 20px;
            height: 20px;
        }

        /* --- MODAL DE CONFIRMACIÓN DE ELIMINACIÓN --- */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 45, 114, 0.25);
            backdrop-filter: blur(4px);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .modal-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        .confirm-status-card {
            background-color: #ffffff;
            border-radius: var(--border-radius-card);
            border: 2.5px solid #e53e3e; /* Borde rojo de alerta */
            padding: 35px 40px;
            max-width: 460px;
            width: 90%;
            box-shadow: 0 20px 45px rgba(229, 62, 62, 0.15);
            text-align: center;
            position: relative;
            transform: scale(0.9);
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .modal-overlay.active .confirm-status-card {
            transform: scale(1);
        }

        .warning-triangle {
            font-size: 52px;
            color: #d69e2e; /* Amarillo de advertencia */
            margin-bottom: 15px;
            line-height: 1;
        }

        .alert-title {
            font-family: 'Outfit', sans-serif;
            font-size: 15px;
            font-weight: 800;
            color: #e53e3e; /* Rojo cabecera */
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 12px;
            line-height: 1.4;
        }

        .alert-desc {
            font-size: 13px;
            color: #718096;
            line-height: 1.5;
            font-weight: 500;
        }

        .modal-footer {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
        }

        .modal-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 30px;
            border-radius: 25px;
            font-family: 'Outfit', sans-serif;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            cursor: pointer;
            transition: all 0.2s ease;
            outline: none;
            border: none;
        }

        .btn-submit {
            background-color: #ffffff;
            border: 2px solid #60a5fa;
            color: #2563eb;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .btn-submit:hover,
        .btn-submit:active {
            background-color: #93c5fd;
            color: var(--color-azul);
            border-color: #3b82f6;
            box-shadow: 0 4px 12px rgba(96, 165, 250, 0.25);
        }

        .btn-cancel {
            background-color: #ffffff;
            border: 2px solid #f87171;
            color: #dc2626;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .btn-cancel:hover,
        .btn-cancel:active {
            background-color: #fca5a5;
            color: #991b1b;
            border-color: #ef4444;
            box-shadow: 0 4px 12px rgba(248, 113, 113, 0.25);
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

        @media (max-width: 1024px) {
            .main-content-area {
                padding: 25px;
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
                padding: 15px;
            }
            .content-card {
                padding: 20px;
            }
            .toolbar {
                flex-direction: column;
                align-items: stretch;
            }
            .filters-left {
                flex-direction: column;
                align-items: stretch;
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

                <!-- Enlace Perfil -->
                <a href="#" class="sidebar-link sidebar-link-perfil">
                    <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Perfil</span>
                </a>

                <!-- Enlace Informes (Activo) -->
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

            <!-- Tarjeta de Contenido Principal -->
            <div class="content-card">
                <h2 class="content-header-title">Historial de Informes</h2>

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
                                        Buscar por nombre o código de curso
                                    </div>
                                </div>
                            </div>
                            <input type="text" id="searchInput" class="input-text"
                                placeholder="Ej. Fotografía, 30313...">
                        </div>

                        <!-- Filtro Carrera -->
                        <div class="form-group">
                            <label for="filterCarrera" class="label-with-info">Carrera</label>
                            <select id="filterCarrera" class="select-filter">
                                <option value="">Todos</option>
                                @foreach ($carreras as $carr)
                                    <option value="{{ $carr }}">{{ $carr }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filtro Curso -->
                        <div class="form-group">
                            <label for="filterCurso" class="label-with-info">Curso</label>
                            <select id="filterCurso" class="select-filter">
                                <option value="">Todos</option>
                                @foreach ($cursosDocente as $cur)
                                    <option value="{{ $cur }}">{{ $cur }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filtro Periodo -->
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

                        <!-- Filtro Año -->
                        <div class="form-group">
                            <label for="filterAnio" class="label-with-info">Año</label>
                            <select id="filterAnio" class="select-filter">
                                <option value="">Todos</option>
                                @foreach ($anios as $a)
                                    <option value="{{ $a }}">{{ $a }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filtro Mes -->
                        <div class="form-group">
                            <label for="filterMes" class="label-with-info">Mes</label>
                            <select id="filterMes" class="select-filter">
                                <option value="">Todos</option>
                                <option value="Enero">Enero</option>
                                <option value="Febrero">Febrero</option>
                                <option value="Marzo">Marzo</option>
                                <option value="Abril">Abril</option>
                                <option value="Mayo">Mayo</option>
                                <option value="Junio">Junio</option>
                                <option value="Julio">Julio</option>
                                <option value="Agosto">Agosto</option>
                                <option value="Septiembre">Septiembre</option>
                                <option value="Octubre">Octubre</option>
                                <option value="Noviembre">Noviembre</option>
                                <option value="Diciembre">Diciembre</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Informes -->
                <div class="table-responsive">
                    <table class="courses-table" id="informesTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Curso</th>
                                <th>Código</th>
                                <th>Sección</th>
                                <th>Año</th>
                                <th>Periodo</th>
                                <th>Mes</th>
                                <th style="text-align: right;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="informesTableBody">
                            @forelse ($informes as $index => $inf)
                                @php
                                    $cursoNombre = $inf->curso->nombre_curso ?? 'Sin asignar';
                                    $cursoCodigo = $inf->curso->codigo_curso ?? '—';
                                    $cursoSeccion = $inf->curso->seccion ?? '—';
                                    $cursoCarrera = $inf->curso->carrera ?? '';
                                    $anioVal = $inf->curso->anio ?? date('Y', strtotime($inf->created_at));
                                    
                                    // Limpiar periodo si trae año adjunto
                                    $periodoClean = trim(preg_replace('/\d{4}/', '', $inf->periodo));
                                    if (!$periodoClean) { $periodoClean = $inf->periodo; }
                                @endphp
                                <tr class="informe-row" 
                                    id="row-informe-{{ $inf->id }}"
                                    data-carrera="{{ strtolower($cursoCarrera) }}"
                                    data-curso="{{ strtolower($cursoNombre) }}"
                                    data-codigo="{{ strtolower($cursoCodigo) }}"
                                    data-periodo="{{ strtolower($periodoClean) }}"
                                    data-anio="{{ $anioVal }}"
                                    data-mes="{{ strtolower($inf->mes) }}">
                                    <td class="td-id">{{ $index + 1 }}.</td>
                                    <td class="td-curso">{{ $cursoNombre }}</td>
                                    <td class="td-codigo">{{ $cursoCodigo }}</td>
                                    <td class="td-seccion">{{ $cursoSeccion }}</td>
                                    <td>{{ $anioVal }}</td>
                                    <td>{{ $periodoClean }}</td>
                                    <td>{{ $inf->mes }}</td>
                                    <td style="text-align: right;">
                                        <div style="display: inline-flex; align-items: center; gap: 8px; justify-content: flex-end;">
                                            <!-- Visualizar PDF -->
                                            <a href="{{ route('docente.informes.ver', $inf->id) }}" target="_blank" class="action-btn view-btn" title="Visualizar informe">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>

                                            <!-- Editar -->
                                            <a href="{{ route('docente.informes.editar', $inf->id) }}" class="action-btn edit-btn" title="Editar informe">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </a>

                                            <!-- Eliminar -->
                                            <button type="button" class="action-btn delete-btn" onclick="openDeleteModal({{ $inf->id }}, '{{ addslashes($cursoNombre) }}')" title="Eliminar informe">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr id="noDataRow">
                                    <td colspan="8" class="no-records-row">
                                        No se han registrado informes todavía.
                                    </td>
                                </tr>
                            @endforelse
                            
                            <!-- Fila oculta para cuando la búsqueda no arroja resultados -->
                            <tr id="noResultsRow" style="display: none;">
                                <td colspan="8" class="no-records-row">
                                    No se encontraron informes coincidentes con los filtros seleccionados.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
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

    <!-- TOAST NOTIFICATION -->
    <div class="toast-notification" id="toastNotification">
        <span id="toastMessage"></span>
    </div>

    <!-- JAVASCRIPT: FILTRADO EN TIEMPO REAL, ELIMINACIÓN Y MENÚS -->
    <script>
        let informeToDeleteId = null;

        // 1. Filtrado en Tiempo Real (Buscador y Selectores)
        function applyFilters() {
            const searchVal = document.getElementById('searchInput').value.trim().toLowerCase();
            const carreraVal = document.getElementById('filterCarrera').value.trim().toLowerCase();
            const cursoVal = document.getElementById('filterCurso').value.trim().toLowerCase();
            const periodoVal = document.getElementById('filterPeriodo').value.trim().toLowerCase();
            const anioVal = document.getElementById('filterAnio').value.trim();
            const mesVal = document.getElementById('filterMes').value.trim().toLowerCase();

            const rows = document.querySelectorAll('#informesTableBody .informe-row');
            let visibleCount = 0;

            rows.forEach(row => {
                const rowCarrera = row.getAttribute('data-carrera') || '';
                const rowCurso = row.getAttribute('data-curso') || '';
                const rowCodigo = row.getAttribute('data-codigo') || '';
                const rowPeriodo = row.getAttribute('data-periodo') || '';
                const rowAnio = row.getAttribute('data-anio') || '';
                const rowMes = row.getAttribute('data-mes') || '';

                // Búsqueda por nombre o código de curso
                const matchesSearch = !searchVal || rowCurso.includes(searchVal) || rowCodigo.includes(searchVal);

                // Filtros dropdown
                const matchesCarrera = !carreraVal || rowCarrera.includes(carreraVal);
                const matchesCurso = !cursoVal || rowCurso.includes(cursoVal);
                const matchesPeriodo = !periodoVal || rowPeriodo.includes(periodoVal);
                const matchesAnio = !anioVal || rowAnio === anioVal;
                const matchesMes = !mesVal || rowMes.includes(mesVal);

                if (matchesSearch && matchesCarrera && matchesCurso && matchesPeriodo && matchesAnio && matchesMes) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            const noResultsRow = document.getElementById('noResultsRow');

            if (noResultsRow) {
                if (rows.length > 0 && visibleCount === 0) {
                    noResultsRow.style.display = '';
                } else {
                    noResultsRow.style.display = 'none';
                }
            }
        }

        // Listeners para filtros
        document.getElementById('searchInput').addEventListener('input', applyFilters);
        document.getElementById('filterCarrera').addEventListener('change', applyFilters);
        document.getElementById('filterCurso').addEventListener('change', applyFilters);
        document.getElementById('filterPeriodo').addEventListener('change', applyFilters);
        document.getElementById('filterAnio').addEventListener('change', applyFilters);
        document.getElementById('filterMes').addEventListener('change', applyFilters);

        // 2. Modal de Eliminación
        function openDeleteModal(id, cursoNombre) {
            informeToDeleteId = id;
            document.getElementById('deleteModalDesc').innerHTML = `Esta acción no se puede deshacer. Se eliminará permanentemente el informe de <b>${cursoNombre}</b> junto a todas sus semanas registradas.`;
            document.getElementById('deleteInformeModal').classList.add('active');
        }

        function closeDeleteModal() {
            informeToDeleteId = null;
            document.getElementById('deleteInformeModal').classList.remove('active');
        }

        function executeDeleteInforme() {
            if (!informeToDeleteId) return;

            const btn = document.getElementById('btnConfirmDelete');
            btn.disabled = true;
            btn.textContent = 'ELIMINANDO...';

            fetch(`/docente/informes/${informeToDeleteId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json().then(data => ({ status: res.status, body: data })))
            .then(({ status, body }) => {
                btn.disabled = false;
                btn.textContent = 'CONFIRMAR';
                closeDeleteModal();

                if (status === 200 && body.success) {
                    showToast(body.message, 'success');
                    const row = document.getElementById(`row-informe-${informeToDeleteId}`);
                    if (row) {
                        row.remove();
                    }
                    applyFilters();
                } else {
                    showToast(body.message || 'Error al eliminar el informe.', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                btn.disabled = false;
                btn.textContent = 'CONFIRMAR';
                closeDeleteModal();
                showToast('Error de conexión con el servidor.', 'error');
            });
        }

        // 3. Helper de Toast
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toastNotification');
            const toastMsg = document.getElementById('toastMessage');
            toastMsg.textContent = message;
            toast.className = `toast-notification ${type} show`;

            setTimeout(() => {
                toast.classList.remove('show');
            }, 3800);
        }

        // 4. Dropdowns y Sidebar
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
