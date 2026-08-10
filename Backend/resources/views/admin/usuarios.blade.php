<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Sistema de Informes</title>
    <!-- Google Fonts: Outfit (headings) and Inter (body) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --color-azul: #002D72;      /* Pantone 288C */
            --color-dorado: #AC8400;    /* Pantone 118C */
            --color-terracota: #B94700; /* Pantone 1525C */
            --color-texto-oscuro: #1a202c;
            --color-texto-claro: #4a5568;
            --color-fondo: #f7fafc;
            --border-radius-card: 16px;
            --sidebar-width: 260px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--color-fondo);
            color: var(--color-texto-oscuro);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        /* --- HEADER / BARRA SUPERIOR --- */
        .admin-header {
            background-color: #ffffff;
            border-bottom: 2px solid transparent;
            background-image: linear-gradient(white, white), 
                              linear-gradient(90deg, var(--color-azul), var(--color-dorado), var(--color-terracota));
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
        }

        /* Botón de Hamburguesa */
        .hamburger-btn {
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
            margin-right: 15px;
            display: flex;
            flex-direction: column;
            justify-content: space-around;
            width: 38px;
            height: 38px;
            border-radius: 8px;
            transition: background-color 0.2s ease;
            outline: none;
        }

        .hamburger-btn:hover {
            background-color: #f7fafc;
        }

        .hamburger-line {
            width: 22px;
            height: 2.5px;
            background-color: var(--color-azul);
            border-radius: 2px;
            transition: all 0.3s ease;
        }

        .header-logo {
            height: 55px;
            object-fit: contain;
            transition: transform 0.3s ease;
        }

        .header-logo:hover {
            transform: scale(1.03);
        }

        .header-title {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            font-family: 'Outfit', sans-serif;
            font-size: 26px;
            font-weight: 700;
            color: var(--color-texto-oscuro);
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin: 0;
        }

        /* Perfil de Usuario y Dropdown */
        .header-right {
            display: flex;
            align-items: center;
            position: relative;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 30px;
            transition: background-color 0.2s ease;
        }

        .header-right:hover {
            background-color: #f7fafc;
        }

        .profile-info {
            text-align: right;
            margin-right: 15px;
        }

        .profile-name {
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: var(--color-texto-oscuro);
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
            background: linear-gradient(135deg, var(--color-azul), var(--color-dorado));
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Outfit', sans-serif;
            font-size: 18px;
            font-weight: 700;
            border: 2px solid #ffffff;
            box-shadow: 0 2px 8px rgba(0, 45, 114, 0.15);
        }

        .profile-arrow {
            width: 18px;
            height: 18px;
            color: var(--color-texto-claro);
            margin-left: 8px;
            transition: transform 0.2s ease;
        }

        .dropdown-menu {
            position: absolute;
            top: 65px;
            right: 10px;
            background-color: #ffffff;
            border: 1px solid rgba(0, 45, 114, 0.08);
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            width: 180px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            overflow: hidden;
        }

        .dropdown-menu.active {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-item {
            display: block;
            width: 100%;
            padding: 14px 20px;
            text-align: left;
            background: none;
            border: none;
            font-family: 'Inter', sans-serif;
            font-size: 13.5px;
            font-weight: 500;
            color: #e53e3e;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .dropdown-item:hover {
            background-color: #fff5f5;
        }

        /* --- APP LAYOUT (Sidebar + Content) --- */
        .app-container {
            display: flex;
            flex: 1;
            position: relative;
            min-height: calc(100vh - 90px);
        }

        /* SIDEBAR / MENÚ LATERAL */
        .admin-sidebar {
            width: var(--sidebar-width);
            background-color: #ffffff;
            border-right: 1.5px solid #edf2f7;
            padding: 30px 15px;
            display: flex;
            flex-direction: column;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            flex-shrink: 0;
        }

        .admin-sidebar.collapsed {
            width: 0;
            padding: 30px 0;
            overflow: hidden;
            border-right-width: 0;
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 14px 20px;
            border-radius: 12px;
            color: var(--color-texto-claro);
            text-decoration: none;
            font-family: 'Outfit', sans-serif;
            font-size: 15px;
            font-weight: 600;
            letter-spacing: 0.02em;
            transition: all 0.2s ease;
        }

        .sidebar-link:hover {
            background-color: #f7fafc;
            color: var(--color-azul);
        }

        .sidebar-link.active {
            background-color: rgba(0, 45, 114, 0.05);
            color: var(--color-azul);
        }

        .sidebar-icon {
            width: 22px;
            height: 22px;
            margin-right: 15px;
            flex-shrink: 0;
        }

        /* ÁREA DE CONTENIDO PRINCIPAL */
        .main-content-area {
            flex: 1;
            padding: 40px;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            max-width: 100%;
        }

        /* --- VISTA DE GESTIÓN DE USUARIOS --- */
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
            color: var(--color-texto-oscuro);
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
            gap: 25px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .filters-left {
            display: flex;
            align-items: flex-end;
            gap: 20px;
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
            font-size: 13px;
            font-weight: 600;
            color: var(--color-texto-claro);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Tooltip informativo (?) */
        .tooltip-container {
            position: relative;
            display: inline-flex;
            margin-left: 6px;
            cursor: pointer;
        }

        .tooltip-btn {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background-color: #e2e8f0;
            color: var(--color-texto-claro);
            font-size: 10px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            outline: none;
        }

        .tooltip-content {
            position: absolute;
            bottom: 25px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #2d3748;
            color: #ffffff;
            padding: 10px 14px;
            border-radius: 6px;
            font-size: 11px;
            font-family: 'Inter', sans-serif;
            font-weight: 400;
            line-height: 1.4;
            width: 220px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            text-align: left;
            text-transform: none;
            letter-spacing: normal;
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s ease;
            z-index: 10;
        }

        .tooltip-content::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            border-width: 5px;
            border-style: solid;
            border-color: #2d3748 transparent transparent transparent;
        }

        .tooltip-container:hover .tooltip-content {
            opacity: 1;
            visibility: visible;
        }

        .input-text {
            width: 100%;
            height: 42px;
            border: 1.5px solid #cbd5e0;
            border-radius: 8px;
            padding: 0 15px;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            color: var(--color-texto-oscuro);
            transition: all 0.2s ease;
            outline: none;
        }

        .input-text:focus {
            border-color: var(--color-azul);
            box-shadow: 0 0 0 3px rgba(0, 45, 114, 0.08);
        }

        .select-filter {
            height: 42px;
            border: 1.5px solid #cbd5e0;
            border-radius: 8px;
            padding: 0 35px 0 15px;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            color: var(--color-texto-oscuro);
            outline: none;
            background-color: #ffffff;
            cursor: pointer;
            min-width: 140px;
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

        /* Botón de Agregar Usuario */
        .add-user-trigger {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: none;
            border: none;
            cursor: pointer;
            padding: 6px 12px;
            border-radius: 8px;
            transition: background-color 0.2s ease;
            outline: none;
            margin-bottom: 2px;
        }

        .add-user-trigger:hover {
            background-color: #f7fafc;
        }

        .add-user-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background-color: #edf2f7;
            color: var(--color-texto-claro);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            transition: all 0.2s ease;
        }

        .add-user-trigger:hover .add-user-avatar {
            background-color: rgba(0, 45, 114, 0.06);
            color: var(--color-azul);
        }

        .add-user-avatar svg {
            width: 24px;
            height: 24px;
        }

        .plus-badge {
            position: absolute;
            bottom: -2px;
            right: -2px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background-color: #48bb78;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            border: 2px solid #ffffff;
        }

        /* --- TABLA DE USUARIOS --- */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            border-radius: 12px;
            border: 1.5px solid #edf2f7;
            margin-top: 20px;
        }

        .users-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 14px;
        }

        .users-table th {
            background-color: #fcfcfc;
            color: var(--color-texto-claro);
            font-family: 'Outfit', sans-serif;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 18px 24px;
            border-bottom: 2px solid #edf2f7;
        }

        .users-table td {
            padding: 16px 24px;
            border-bottom: 1.5px solid #edf2f7;
            color: var(--color-texto-oscuro);
            vertical-align: middle;
        }

        .users-table tr:last-child td {
            border-bottom: none;
        }

        .users-table tr:hover td {
            background-color: rgba(0, 45, 114, 0.01);
        }

        .users-table .td-id {
            font-weight: 600;
            color: var(--color-texto-claro);
            width: 60px;
        }

        .users-table .td-nombre {
            font-weight: 600;
            color: var(--color-texto-oscuro);
        }

        .users-table .td-correo {
            color: var(--color-texto-claro);
        }

        .users-table .td-numero {
            font-family: monospace;
            font-size: 13.5px;
            color: var(--color-texto-oscuro);
        }

        .users-table .td-rol {
            font-weight: 600;
            color: var(--color-azul);
        }

        /* Insignias de Estado */
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11.5px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }

        .status-badge.active {
            background-color: rgba(72, 187, 120, 0.08);
            color: #276749;
        }

        .status-badge.inactive {
            background-color: rgba(229, 62, 62, 0.08);
            color: #9b2c2c;
        }

        .no-records-row {
            text-align: center;
            color: var(--color-texto-claro);
            font-style: italic;
        }

        /* --- MODAL AGREGAR USUARIO --- */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 45, 114, 0.18);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .add-user-modal {
            background-color: #ffffff;
            border-radius: var(--border-radius-card);
            border: 2px solid transparent;
            background-image: linear-gradient(white, white), 
                              linear-gradient(135deg, var(--color-azul), var(--color-dorado), var(--color-terracota));
            background-origin: border-box;
            background-clip: padding-box, border-box;
            padding: 40px;
            max-width: 480px;
            width: 90%;
            box-shadow: 0 25px 50px rgba(0, 45, 114, 0.12);
            position: relative;
            transform: scale(0.9);
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .modal-overlay.active .add-user-modal {
            transform: scale(1);
        }

        .modal-close-btn {
            position: absolute;
            top: 15px;
            right: 20px;
            background: none;
            border: none;
            font-size: 28px;
            color: var(--color-texto-claro);
            cursor: pointer;
            transition: color 0.2s ease;
            outline: none;
        }

        .modal-close-btn:hover {
            color: var(--color-texto-oscuro);
        }

        .modal-title {
            font-family: 'Outfit', sans-serif;
            font-size: 20px;
            font-weight: 700;
            color: var(--color-texto-oscuro);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 1.5px solid #edf2f7;
            padding-bottom: 15px;
        }

        .modal-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .modal-form-label {
            font-family: 'Outfit', sans-serif;
            font-size: 13px;
            font-weight: 600;
            color: var(--color-texto-claro);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .modal-form .input-text {
            height: 44px;
        }

        /* Botones del Modal */
        .modal-footer {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            margin-top: 15px;
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
            background-color: #93c5fd; /* Azul claro */
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
            background-color: #fca5a5; /* Rojo/Rosado claro */
            color: #991b1b;
            border-color: #ef4444;
            box-shadow: 0 4px 12px rgba(248, 113, 113, 0.25);
        }
    </style>
</head>

<body>

    <!-- Header / Barra Superior -->
    <header class="admin-header">
        <div class="header-left">
            <!-- Botón Hamburger para colapsar/expandir el menú lateral -->
            <button type="button" class="hamburger-btn" id="hamburgerBtn">
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
            </button>
            <a href="{{ route('admin.dashboard') }}">
                <img src="{{ asset('images/FarusacLogo.png') }}" class="header-logo" alt="Logo FARUSAC">
            </a>
        </div>

        <h1 class="header-title">Sistema de Informes</h1>

        <div class="header-right" id="profileToggle">
            <div class="profile-info">
                <div class="profile-name">{{ Auth::user()->nombre }}</div>
                <div class="profile-email">{{ Auth::user()->correo }}</div>
            </div>
            <div class="profile-avatar">
                {{ strtoupper(substr(Auth::user()->nombre, 0, 1)) }}
            </div>
            <svg class="profile-arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 12 15 18 9"></polyline>
            </svg>

            <div class="dropdown-menu" id="profileDropdown">
                <form action="{{ route('logout') }}" method="POST" id="logout-form" style="display: none;">
                    @csrf
                </form>
                <button type="button" onclick="event.stopPropagation(); document.getElementById('logout-form').submit();" class="dropdown-item">
                    Cerrar sesión
                </button>
            </div>
        </div>
    </header>

    <!-- Contenedor general del layout de la aplicación (Sidebar + Contenido) -->
    <div class="app-container">
        
        <!-- Menú Lateral (Sidebar) -->
        <aside class="admin-sidebar" id="adminSidebar">
            <nav class="sidebar-nav">
                <!-- Enlace Inicio -->
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link">
                    <!-- Icono de Casa SVG -->
                    <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Inicio</span>
                </a>
                
                <!-- Enlace Cursos -->
                <a href="#" class="sidebar-link">
                    <!-- Icono de Libro SVG -->
                    <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span>Cursos</span>
                </a>
                
                <!-- Enlace Informes -->
                <a href="#" class="sidebar-link">
                    <!-- Icono de Documento/Gráfico SVG -->
                    <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Informes</span>
                </a>
            </nav>
        </aside>

        <!-- Área de Contenido Principal -->
        <main class="main-content-area" id="mainContent">
            
            <!-- Tarjeta de Contenido Principal -->
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
                                        Se puede buscar por número de teléfono, correo electrónico o nombre de usuario por medio de texto.
                                    </div>
                                </div>
                            </div>
                            <input type="text" id="searchInput" class="input-text" placeholder="Ej. Juan, juan@farusac.edu.gt, 5587...">
                        </div>

                        <!-- Filtro de Rol -->
                        <div class="form-group">
                            <label for="rolFilter" class="label-with-info">Rol</label>
                            <select id="rolFilter" class="select-filter">
                                <option value="todos">Todos</option>
                                <option value="administrador">Administrador</option>
                                <option value="docente">Docente</option>
                                <option value="jefe">Jefe</option>
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

                    <!-- Botón para Agregar Usuario -->
                    <button type="button" class="add-user-trigger" id="addUserBtn">
                        <div class="add-user-avatar">
                            <!-- Icono de usuario SVG -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span class="plus-badge">+</span>
                        </div>
                    </button>

                </div>

                <!-- Tabla de Usuarios -->
                <div class="table-responsive">
                    <table class="users-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Correo</th>
                                <th>Número</th>
                                <th>Rol</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody">
                            <!-- Fila de ejemplo precargada en base a tu plantilla -->
                            <tr data-id="1">
                                <td class="td-id">1.</td>
                                <td class="td-nombre">Juan Pablo Samayoa Ruiz</td>
                                <td class="td-correo">juan.samayoa@farusac.edu.gt</td>
                                <td class="td-numero">5587-1751</td>
                                <td class="td-rol">Administrador</td>
                                <td>
                                    <span class="status-badge active">Activo</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>

        </main>

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
                    <input type="email" id="newUserEmail" class="input-text" placeholder="Ej. juan.perez@farusac.edu.gt" required>
                </div>

                <div class="form-group">
                    <label for="newUserPhone" class="modal-form-label">Número</label>
                    <input type="text" id="newUserPhone" class="input-text" placeholder="Ej. 5587-1751" required>
                </div>

                <div class="form-group">
                    <label for="newUserRole" class="modal-form-label">Rol</label>
                    <select id="newUserRole" class="select-filter" style="width: 100%;" required>
                        <option value="administrador">Administrador</option>
                        <option value="jefe">Jefe</option>
                        <option value="docente">Docente</option>
                    </select>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="modal-btn btn-submit">Agregar</button>
                    <button type="button" class="modal-btn btn-cancel" onclick="closeAddUserModal()">Cancelar</button>
                </div>

            </form>
        </div>
    </div>

    <!-- JavaScript para Interactividad y Filtros Locales (Dashboard Vivo) -->
    <script>
        // Base de Datos de Usuarios Locales para simular un dashboard 100% vivo e interactivo
        let dbUsuarios = [
            {
                id: 1,
                nombre: "Juan Pablo Samayoa Ruiz",
                correo: "juan.samayoa@farusac.edu.gt",
                numero: "5587-1751",
                rol: "administrador",
                estado: "activo"
            },
            {
                id: 2,
                nombre: "María Eugenia López",
                correo: "maria.lopez@farusac.edu.gt",
                numero: "4122-3850",
                rol: "docente",
                estado: "activo"
            },
            {
                id: 3,
                nombre: "Carlos Humberto Méndez",
                correo: "carlos.mendez@farusac.edu.gt",
                numero: "2201-9475",
                rol: "jefe",
                estado: "activo"
            },
            {
                id: 4,
                nombre: "Ana Lucía Castillo",
                correo: "ana.castillo@farusac.edu.gt",
                numero: "5938-1204",
                rol: "docente",
                estado: "inactivo"
            }
        ];

        // Función para renderizar la tabla con filtrado dinámico
        function renderTable() {
            const tableBody = document.getElementById('usersTableBody');
            const searchVal = document.getElementById('searchInput').value.toLowerCase().trim();
            const rolVal = document.getElementById('rolFilter').value;
            const estadoVal = document.getElementById('estadoFilter').value;

            // Filtrar los usuarios
            const filtered = dbUsuarios.filter(user => {
                // Filtro de búsqueda textual (nombre, correo o número)
                const matchesSearch = 
                    user.nombre.toLowerCase().includes(searchVal) ||
                    user.correo.toLowerCase().includes(searchVal) ||
                    user.numero.includes(searchVal);
                
                // Filtro de Rol
                const matchesRol = (rolVal === 'todos') || (user.rol === rolVal);

                // Filtro de Estado
                const matchesEstado = (estadoVal === 'todos') || (user.estado === estadoVal);

                return matchesSearch && matchesRol && matchesEstado;
            });

            // Limpiar tabla
            tableBody.innerHTML = '';

            if (filtered.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="no-records-row">No se encontraron usuarios con los filtros aplicados.</td>
                    </tr>
                `;
                return;
            }

            // Inyectar filas
            filtered.forEach((user, idx) => {
                const tr = document.createElement('tr');
                tr.setAttribute('data-id', user.id);

                // Formatear el Rol para mostrarlo elegante
                const rolFormatted = user.rol.charAt(0).toUpperCase() + user.rol.slice(1);
                // Formatear el Estado
                const estadoClass = user.estado === 'activo' ? 'active' : 'inactive';
                const estadoFormatted = user.estado.charAt(0).toUpperCase() + user.estado.slice(1);

                tr.innerHTML = `
                    <td class="td-id">${idx + 1}.</td>
                    <td class="td-nombre">${user.nombre}</td>
                    <td class="td-correo">${user.correo}</td>
                    <td class="td-numero">${user.numero}</td>
                    <td class="td-rol">${rolFormatted}</td>
                    <td>
                        <span class="status-badge ${estadoClass}">${estadoFormatted}</span>
                    </td>
                `;
                tableBody.appendChild(tr);
            });
        }

        // Abrir y Cerrar Modal de Agregar Usuario
        function openAddUserModal() {
            document.getElementById('addUserModal').classList.add('active');
        }

        function closeAddUserModal() {
            document.getElementById('addUserModal').classList.remove('active');
            document.getElementById('addUserForm').reset();
        }

        // Acción al agregar un nuevo usuario
        function handleCreateUser(e) {
            e.preventDefault();
            
            const name = document.getElementById('newUserName').value.trim();
            const email = document.getElementById('newUserEmail').value.trim();
            const phone = document.getElementById('newUserPhone').value.trim();
            const role = document.getElementById('newUserRole').value;

            if (!name || !email || !phone || !role) return;

            // Registrar nuevo usuario en nuestra base de datos local temporal
            const newId = dbUsuarios.length > 0 ? Math.max(...dbUsuarios.map(u => u.id)) + 1 : 1;
            const newUser = {
                id: newId,
                nombre: name,
                correo: email,
                numero: phone,
                rol: role,
                estado: "activo"
            };

            dbUsuarios.push(newUser);
            
            // Cerrar el modal y refrescar la tabla
            closeAddUserModal();
            renderTable();
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Inicializar render de tabla
            renderTable();

            // Asignar listeners a los filtros
            document.getElementById('searchInput').addEventListener('input', renderTable);
            document.getElementById('rolFilter').addEventListener('change', renderTable);
            document.getElementById('estadoFilter').addEventListener('change', renderTable);

            // Modal Trigger
            document.getElementById('addUserBtn').addEventListener('click', openAddUserModal);

            // Hamburger Sidebar Toggle
            const hamburgerBtn = document.getElementById('hamburgerBtn');
            const adminSidebar = document.getElementById('adminSidebar');
            const mainContent = document.getElementById('mainContent');

            hamburgerBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                adminSidebar.classList.toggle('collapsed');
            });

            // Dropdown de perfil
            const profileToggle = document.getElementById('profileToggle');
            const profileDropdown = document.getElementById('profileDropdown');

            profileToggle.addEventListener('click', (e) => {
                e.stopPropagation();
                profileDropdown.classList.toggle('active');
            });

            document.addEventListener('click', () => {
                profileDropdown.classList.remove('active');
            });

            // Cerrar modal al presionar Escape
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    closeAddUserModal();
                }
            });
        });
    </script>

</body>

</html>
