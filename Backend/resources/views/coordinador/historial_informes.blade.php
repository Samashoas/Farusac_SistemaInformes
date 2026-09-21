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
        }

        .dropdown-menu {
            position: absolute;
            top: 60px;
            right: 0;
            background-color: #ffffff;
            border-radius: var(--border-radius-card);
            border: 1px solid var(--color-borde);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            width: 180px;
            padding: 8px 0;
            display: none;
            z-index: 1000;
        }

        .dropdown-menu.active {
            display: block;
            animation: fadeIn 0.2s ease-in-out;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            color: var(--color-texto-principal);
            font-size: 14px;
            text-decoration: none;
            background: none;
            border: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
            transition: var(--transition-smooth);
        }

        .dropdown-item:hover {
            background-color: #fff5f5;
            color: #e53e3e;
        }

        /* --- APP CONTAINER (SIDEBAR + MAIN CONTENT) --- */
        .app-container {
            display: flex;
            min-height: calc(100vh - 90px);
            position: relative;
        }

        /* MENÚ LATERAL (SIDEBAR) */
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
            font-family: 'Outfit', sans-serif;
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

        .sidebar-link-informes {
            background-color: rgba(0, 45, 114, 0.08);
            color: var(--color-azul);
            font-weight: 600;
        }
        .sidebar-link-informes .sidebar-icon {
            color: var(--color-azul);
        }

        .sidebar-link-inicio:hover,
        .sidebar-link-perfil:hover {
            background-color: rgba(0, 45, 114, 0.05);
            color: var(--color-azul);
        }
        .sidebar-link-inicio:hover .sidebar-icon,
        .sidebar-link-perfil:hover .sidebar-icon {
            color: var(--color-azul);
        }

        /* --- CONTENIDO PRINCIPAL --- */
        .main-content-area {
            flex-grow: 1;
            padding: 40px;
            width: 100%;
        }

        .content-card {
            background-color: #ffffff;
            border-radius: var(--border-radius-card);
            border: 1.5px solid #edf2f7;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.01);
            padding: 35px 40px;
        }

        .content-header-title {
            font-family: 'Outfit', sans-serif;
            font-size: 22px;
            font-weight: 700;
            color: var(--color-texto-principal);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 25px;
            text-align: center;
        }

        /* --- CONTENEDOR DE FILTROS --- */
        .filters-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 25px;
            align-items: center;
            justify-content: space-between;
        }

        .search-box-wrapper {
            position: relative;
            flex: 1 1 240px;
            min-width: 200px;
        }

        .search-input {
            width: 100%;
            height: 42px;
            border: 1.5px solid #cbd5e0;
            border-radius: 20px;
            padding: 0 15px 0 40px;
            font-family: 'Inter', sans-serif;
            font-size: 13.5px;
            color: var(--color-texto-principal);
            outline: none;
            transition: all 0.2s ease;
        }

        .search-input:focus {
            border-color: var(--color-azul);
            box-shadow: 0 0 0 3px rgba(0, 45, 114, 0.08);
        }

        .search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            color: var(--color-texto-claro);
            pointer-events: none;
        }

        .dropdown-filters-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }

        .filter-select {
            height: 42px;
            border: 1.5px solid #cbd5e0;
            border-radius: 20px;
            padding: 0 30px 0 14px;
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            color: var(--color-texto-principal);
            outline: none;
            background-color: #ffffff;
            cursor: pointer;
            transition: all 0.2s ease;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%234a5568' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 14px;
        }

        .filter-select:focus {
            border-color: var(--color-azul);
            box-shadow: 0 0 0 3px rgba(0, 45, 114, 0.08);
        }

        /* --- TABLA INSTITUCIONAL --- */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            border-radius: 12px;
            border: 1px solid #edf2f7;
        }

        .custom-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
            text-align: left;
        }

        .custom-table thead {
            background-color: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
        }

        .custom-table th {
            padding: 14px 16px;
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            color: var(--color-azul);
            text-transform: uppercase;
            letter-spacing: 0.04em;
            font-size: 12.5px;
            white-space: nowrap;
        }

        .custom-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #edf2f7;
            color: var(--color-texto-principal);
            vertical-align: middle;
        }

        .custom-table tbody tr {
            transition: background-color 0.15s ease;
        }

        .custom-table tbody tr:hover {
            background-color: #f8fafc;
        }

        .td-id {
            font-weight: 700;
            color: var(--color-texto-claro);
            width: 40px;
        }

        .td-curso {
            font-weight: 600;
            color: var(--color-texto-principal);
        }

        .td-codigo {
            font-family: 'Inter', monospace;
            font-size: 13px;
            color: var(--color-texto-secundario);
        }

        .td-seccion {
            font-weight: 700;
            color: var(--color-azul);
            text-align: center;
        }

        /* Botones de Acción */
        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            background: none;
            text-decoration: none;
        }

        .action-btn svg {
            width: 18px;
            height: 18px;
        }

        .view-btn {
            color: var(--color-azul);
            background-color: rgba(0, 45, 114, 0.06);
        }
        .view-btn:hover {
            background-color: var(--color-azul);
            color: #ffffff;
        }

        .edit-btn {
            color: var(--color-oro);
            background-color: rgba(172, 132, 0, 0.08);
        }
        .edit-btn:hover {
            background-color: var(--color-oro);
            color: #ffffff;
        }

        .delete-btn {
            color: #ef4444;
            background-color: #fee2e2;
        }
        .delete-btn:hover {
            background-color: #ef4444;
            color: #ffffff;
        }

        .no-records-row {
            text-align: center;
            padding: 40px !important;
            color: var(--color-texto-claro);
            font-style: italic;
        }

        /* --- MODAL DE CONFIRMACIÓN DE ELIMINACIÓN --- */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 20, 50, 0.35);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .modal-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        .confirm-status-card {
            background-color: #ffffff;
            border-radius: var(--border-radius-card);
            border: 2.5px solid #e53e3e;
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
            color: #d69e2e;
            margin-bottom: 15px;
            line-height: 1;
        }

        .alert-title {
            font-family: 'Outfit', sans-serif;
            font-size: 15px;
            font-weight: 800;
            color: #e53e3e;
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
            margin-top: 25px;
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
            border: none;
            outline: none;
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

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
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
            <a href="{{ route('coordinador.dashboard') }}">
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

        <!-- Menú Lateral (Sidebar) -->
        <aside class="admin-sidebar collapsed" id="docenteSidebar">
            <nav class="sidebar-nav">
                <!-- Enlace Inicio -->
                <a href="{{ route('coordinador.dashboard') }}" class="sidebar-link sidebar-link-inicio">
                    <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Inicio</span>
                </a>

                <!-- Enlace Perfil -->
                <a href="{{ route('coordinador.perfil') }}" class="sidebar-link sidebar-link-perfil">
                    <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Perfil</span>
                </a>

                <!-- Enlace Informes (Activo) -->
                <a href="{{ route('coordinador.informes') }}" class="sidebar-link sidebar-link-informes">
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

            <div class="content-card">
                <h2 class="content-header-title">Historial de Informes de Actividades</h2>

                <!-- Filtros Interactivos en Tiempo Real -->
                <div class="filters-container">
                    <!-- Búsqueda General -->
                    <div class="search-box-wrapper">
                        <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" id="searchInput" class="search-input" placeholder="Buscar por curso o código..." oninput="applyFilters()">
                    </div>

                    <!-- Filtros Selects -->
                    <div class="dropdown-filters-group">
                        <!-- Filtro Carrera -->
                        <select id="filterCarrera" class="filter-select" onchange="applyFilters()">
                            <option value="">Todas las Carreras</option>
                            @foreach ($carreras as $carr)
                                <option value="{{ $carr }}">{{ $carr }}</option>
                            @endforeach
                        </select>

                        <!-- Filtro Curso -->
                        <select id="filterCurso" class="filter-select" onchange="applyFilters()">
                            <option value="">Todos los Cursos</option>
                            @foreach ($cursosDocente as $cur)
                                <option value="{{ $cur }}">{{ $cur }}</option>
                            @endforeach
                        </select>

                        <!-- Filtro Periodo -->
                        <select id="filterPeriodo" class="filter-select" onchange="applyFilters()">
                            <option value="">Todos los Periodos</option>
                            @foreach ($periodos as $per)
                                <option value="{{ $per }}">{{ $per }}</option>
                            @endforeach
                        </select>

                        <!-- Filtro Año -->
                        <select id="filterAnio" class="filter-select" onchange="applyFilters()">
                            <option value="">Todos los Años</option>
                            @foreach ($anios as $a)
                                <option value="{{ $a }}">{{ $a }}</option>
                            @endforeach
                        </select>

                        <!-- Filtro Mes -->
                        <select id="filterMes" class="filter-select" onchange="applyFilters()">
                            <option value="">Todos los Meses</option>
                            @foreach (['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'] as $m)
                                <option value="{{ $m }}">{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Tabla de Informes -->
                <div class="table-responsive">
                    <table class="custom-table" id="informesTable">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Curso</th>
                                <th>Código</th>
                                <th style="text-align: center;">Sección</th>
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
                                            <a href="{{ route('coordinador.informes.ver', $inf->id) }}" target="_blank" class="action-btn view-btn" title="Visualizar informe">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>

                                            <!-- Editar -->
                                            <a href="{{ route('coordinador.informes.editar', $inf->id) }}" class="action-btn edit-btn" title="Editar informe">
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

    <!-- JAVASCRIPT -->
    <script>
        // 1. Filtrado en Tiempo Real
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

                const matchesSearch = !searchVal || rowCurso.includes(searchVal) || rowCodigo.includes(searchVal);
                const matchesCarrera = !carreraVal || rowCarrera.includes(carreraVal);
                const matchesCurso = !cursoVal || rowCurso.includes(cursoVal);
                const matchesPeriodo = !periodoVal || rowPeriodo.includes(periodoVal);
                const matchesAnio = !anioVal || rowAnio === anioVal;
                const matchesMes = !mesVal || rowMes.includes(mesVal);

                if (matchesSearch && matchesCarrera && matchesCurso && matchesPeriodo && matchesAnio && matchesMes) {
                    row.style.display = '';
                    visibleCount++;
                    const idCol = row.querySelector('.td-id');
                    if (idCol) {
                        idCol.textContent = `${visibleCount}.`;
                    }
                } else {
                    row.style.display = 'none';
                }
            });

            const noResultsRow = document.getElementById('noResultsRow');
            let noDataRow = document.getElementById('noDataRow');

            if (rows.length === 0) {
                if (!noDataRow) {
                    const tbody = document.getElementById('informesTableBody');
                    noDataRow = document.createElement('tr');
                    noDataRow.id = 'noDataRow';
                    noDataRow.innerHTML = `<td colspan="8" class="no-records-row">No se han registrado informes todavía.</td>`;
                    tbody.appendChild(noDataRow);
                }
                if (noResultsRow) noResultsRow.style.display = 'none';
            } else {
                if (noDataRow) noDataRow.style.display = 'none';
                if (noResultsRow) {
                    noResultsRow.style.display = (visibleCount === 0) ? '' : 'none';
                }
            }
        }

        // 2. Modal de Confirmación y Eliminación
        let targetId = null;

        function openDeleteModal(id, cursoNombre) {
            targetId = id;
            document.getElementById('deleteModalDesc').textContent = `Esta acción no se puede deshacer. Se eliminarán permanentemente las actividades registradas del informe de "${cursoNombre}".`;
            document.getElementById('deleteInformeModal').classList.add('active');
        }

        function closeDeleteModal() {
            targetId = null;
            document.getElementById('deleteInformeModal').classList.remove('active');
        }

        async function executeDeleteInforme() {
            if (!targetId) return;

            const btn = document.getElementById('btnConfirmDelete');
            btn.disabled = true;
            btn.textContent = 'ELIMINANDO...';

            try {
                const response = await fetch(`{{ url('/coordinador/informes') }}/${targetId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                btn.disabled = false;
                btn.textContent = 'CONFIRMAR';
                closeDeleteModal();

                if (response.ok && data.success) {
                    showToast(data.message || 'Informe eliminado permanentemente.', 'success');

                    const row = document.getElementById(`row-informe-${targetId}`);
                    if (row) {
                        row.style.transition = 'all 0.25s cubic-bezier(0.16, 1, 0.3, 1)';
                        row.style.opacity = '0';
                        row.style.transform = 'scale(0.95)';
                        setTimeout(() => {
                            row.remove();
                            applyFilters();
                        }, 250);
                    } else {
                        applyFilters();
                    }
                } else {
                    showToast(data.message || 'Error al eliminar el informe.', 'error');
                }
            } catch (err) {
                console.error('Error al eliminar informe:', err);
                btn.disabled = false;
                btn.textContent = 'CONFIRMAR';
                closeDeleteModal();
                showToast('Error de conexión con el servidor.', 'error');
            }
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
