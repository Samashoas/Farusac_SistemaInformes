<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FARUSAC - Gestión de Cursos</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* --- DISEÑO SISTEMA Y PALETA INSTITUCIONAL --- */
        :root {
            --color-azul: #002D72;       /* Pantone 288C */
            --color-oro: #AC8400;        /* Pantone 118C - Activo en Cursos */
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

        /* Hover dinámico por módulo */
        .sidebar-link-inicio:hover {
            background-color: rgba(0, 45, 114, 0.05);
            color: var(--color-azul);
        }
        .sidebar-link-inicio:hover .sidebar-icon {
            color: var(--color-azul);
        }

        .sidebar-link-usuarios:hover {
            background-color: rgba(0, 45, 114, 0.05);
            color: var(--color-azul);
        }
        .sidebar-link-usuarios:hover .sidebar-icon {
            color: var(--color-azul);
        }

        /* Módulo Activo Cursos - Color Oro */
        .sidebar-link-cursos {
            background-color: rgba(172, 132, 0, 0.08);
            color: var(--color-oro);
            font-weight: 600;
        }
        .sidebar-link-cursos .sidebar-icon {
            color: var(--color-oro);
        }

        .sidebar-link-cursos:hover {
            background-color: rgba(172, 132, 0, 0.12);
        }

        .sidebar-link-informes:hover {
            background-color: rgba(185, 71, 0, 0.05);
            color: var(--color-terracota);
        }
        .sidebar-link-informes:hover .sidebar-icon {
            color: var(--color-terracota);
        }

        .sidebar-link-carga:hover {
            background-color: rgba(0, 45, 114, 0.05);
            color: var(--color-azul);
        }
        .sidebar-link-carga:hover .sidebar-icon {
            color: var(--color-azul);
        }

        /* --- CONTENIDO PRINCIPAL --- */
        .main-content-area {
            flex-grow: 1;
            padding: 40px;
            transition: padding-left 0.3s ease;
            width: 100%;
        }

        /* --- VISTA DE GESTIÓN DE CURSOS --- */
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
            width: 280px;
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

        /* --- BOTÓN DE AGREGAR Y ACCIONES DE CARGA MASIVA --- */
        .add-actions-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
            margin-bottom: 2px;
        }

        .add-course-trigger {
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
        }

        .add-course-trigger:hover {
            background-color: #f7fafc;
        }

        .add-course-avatar {
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

        .add-course-trigger:hover .add-course-avatar {
            background-color: rgba(0, 45, 114, 0.06);
            color: var(--color-azul);
        }

        .add-course-avatar svg {
            width: 22px;
            height: 22px;
        }

        .plus-badge {
            position: absolute;
            bottom: -2px;
            right: -2px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background-color: #48bb78; /* Verde */
            color: #ffffff;
            font-size: 12px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #ffffff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .bulk-load-link {
            font-family: 'Outfit', sans-serif;
            font-size: 11.5px;
            font-weight: 600;
            color: var(--color-texto-claro);
            text-decoration: none;
            padding: 4px 10px;
            border: 1.5px solid #e2e8f0;
            border-radius: 6px;
            background-color: #ffffff;
            transition: all 0.2s ease;
            text-transform: lowercase;
            letter-spacing: 0.02em;
            text-align: center;
        }

        .bulk-load-link:hover {
            color: var(--color-azul);
            border-color: var(--color-azul);
            background-color: rgba(0, 45, 114, 0.02);
            box-shadow: 0 2px 5px rgba(0, 45, 114, 0.05);
        }

        /* --- TABLA DE CURSOS --- */
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

        .td-carrera {
            font-weight: 600;
            color: var(--color-texto-principal);
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
        }

        .action-btn:hover {
            background-color: #fff5f5;
            color: #e53e3e; /* Rojo papelera */
        }

        .action-btn svg {
            width: 20px;
            height: 20px;
        }

        /* --- MODALES Y OVERLAYS --- */
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

        /* CARD DE ALERTA PARA ELIMINACIÓN */
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

        /* MODAL REGULAR (AGREGAR CURSO) */
        .modal-card {
            background-color: #ffffff;
            border-radius: var(--border-radius-card);
            border: 2px solid transparent;
            background-image: linear-gradient(white, white),
                linear-gradient(135deg, var(--color-azul), var(--color-oro), var(--color-terracota));
            background-origin: border-box;
            background-clip: padding-box, border-box;
            padding: 25px 35px;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 25px 50px rgba(0, 45, 114, 0.12);
            position: relative;
            transform: scale(0.9);
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .modal-overlay.active .modal-card {
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
            color: var(--color-texto-principal);
        }

        .modal-title {
            font-family: 'Outfit', sans-serif;
            font-size: 20px;
            font-weight: 700;
            color: var(--color-texto-principal);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 1.5px solid #edf2f7;
            padding-bottom: 10px;
        }

        .modal-form {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .modal-form-label {
            font-family: 'Outfit', sans-serif;
            font-size: 13px;
            font-weight: 600;
            color: var(--color-texto-claro);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: block;
            margin-bottom: 2px;
        }

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
            background-color: #fca5a5; /* Rojo claro */
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
        <aside class="admin-sidebar collapsed" id="adminSidebar">
            <nav class="sidebar-nav">
                <!-- Enlace Inicio -->
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link sidebar-link-inicio">
                    <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Inicio</span>
                </a>

                <!-- Enlace Usuarios -->
                <a href="{{ route('admin.usuarios') }}" class="sidebar-link sidebar-link-usuarios">
                    <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Usuarios</span>
                </a>

                <!-- Enlace Cursos (Activo - Oro) -->
                <a href="{{ route('admin.cursos') }}" class="sidebar-link sidebar-link-cursos">
                    <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span>Cursos</span>
                </a>

                <!-- Enlace Informes -->
                <a href="#" class="sidebar-link sidebar-link-informes">
                    <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Informes</span>
                </a>

                <!-- Enlace Carga de datos -->
                <a href="{{ route('admin.carga-datos') }}" class="sidebar-link sidebar-link-carga">
                    <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                    </svg>
                    <span>Carga de Datos</span>
                </a>
            </nav>
        </aside>

        <!-- Area de Contenido Principal -->
        <main class="main-content-area" id="mainContent">

            <!-- Tarjeta de Contenido Principal -->
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
                                        Indicar que se puede buscar por nombre o codigo de curso
                                    </div>
                                </div>
                            </div>
                            <input type="text" id="searchInput" class="input-text" style="width: 100%;"
                                placeholder="Ej. Fotografía, 30313...">
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
                            </select>
                        </div>

                        <!-- Filtro Año -->
                        <div class="form-group">
                            <label for="anioFilter" class="label-with-info">Año</label>
                            <select id="anioFilter" class="select-filter">
                                <option value="todos">Todos</option>
                                <option value="2026">2026</option>
                                <option value="2025">2025</option>
                                <option value="2024">2024</option>
                            </select>
                        </div>
                    </div>

                    <!-- Botón Agregar Curso y Carga Masiva -->
                    <div class="add-actions-container">
                        <button type="button" class="add-course-trigger" id="addCourseBtn" title="Agregar Curso Individual">
                            <div class="add-course-avatar">
                                <!-- Icono de Libro SVG -->
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
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

        </main>
    </div>

    <!-- Modal Emergente: Agregar Curso (Estructura de Maquetado) -->
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
                <div class="form-group">
                    <label for="newArea" class="modal-form-label">Área</label>
                    <input type="text" id="newArea" class="input-text" style="width: 100%;" placeholder="Ej. Área Tecnología y Expresión" required>
                </div>
                <div class="form-group">
                    <label for="newCurso" class="modal-form-label">Nombre del Curso</label>
                    <input type="text" id="newCurso" class="input-text" style="width: 100%;" placeholder="Ej. Fotografía" required>
                </div>
                <div class="form-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div>
                        <label for="newCodigo" class="modal-form-label">Código de Curso</label>
                        <input type="text" id="newCodigo" class="input-text" style="width: 100%;" placeholder="Ej. 30313" required>
                    </div>
                    <div>
                        <label for="newSeccion" class="modal-form-label">Sección</label>
                        <input type="text" id="newSeccion" class="input-text" style="width: 100%;" placeholder="Ej. A" required>
                    </div>
                </div>
                <div class="form-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div>
                        <label for="newJornada" class="modal-form-label">Jornada</label>
                        <select id="newJornada" class="select-filter" style="width: 100%;" required>
                            <option value="Matutina">Matutina</option>
                            <option value="Vespertina">Vespertina</option>
                            <option value="Nocturna">Nocturna</option>
                        </select>
                    </div>
                    <div>
                        <label for="newSemestre" class="modal-form-label">Semestre</label>
                        <select id="newSemestre" class="select-filter" style="width: 100%;" required>
                            <option value="Primer Semestre">Primer Semestre</option>
                            <option value="Segundo Semestre">Segundo Semestre</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="newAnio" class="modal-form-label">Año</label>
                    <select id="newAnio" class="select-filter" style="width: 100%;" required>
                        <option value="2026">2026</option>
                        <option value="2025">2025</option>
                        <option value="2024">2024</option>
                    </select>
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

    <!-- JavaScript para Interactividad y Filtros Locales (Dashboard Vivo) -->
    <script>
        // Datos estáticos iniciales de cursos simulados para el maquetado del dashboard vivo
        let dbCursos = [
            {
                id: 1,
                carrera: "Arquitectura",
                area: "Área Tecnología y Expresión",
                curso: "Fotografía",
                codigo: "30313",
                seccion: "A",
                jornada: "Matutina",
                semestre: "Primer Semestre",
                anio: "2026"
            },
            {
                id: 2,
                carrera: "Diseño Gráfico",
                area: "Área de Comunicación",
                curso: "Diseño Web I",
                codigo: "10542",
                seccion: "B",
                jornada: "Vespertina",
                semestre: "Segundo Semestre",
                anio: "2026"
            },
            {
                id: 3,
                carrera: "Arquitectura",
                area: "Área de Diseño",
                curso: "Diseño Arquitectónico IV",
                codigo: "40231",
                seccion: "C",
                jornada: "Matutina",
                semestre: "Primer Semestre",
                anio: "2025"
            }
        ];

        let deleteTargetCourseId = null;

        // Renderizar la tabla con filtros
        function renderTable() {
            const tableBody = document.getElementById('coursesTableBody');
            const searchVal = document.getElementById('searchInput').value.toLowerCase().trim();
            const carreraVal = document.getElementById('carreraFilter').value;
            const semestreVal = document.getElementById('semestreFilter').value;
            const anioVal = document.getElementById('anioFilter').value;

            // Filtrado local
            const filtered = dbCursos.filter(c => {
                const matchesSearch = (c.curso && c.curso.toLowerCase().includes(searchVal)) || (c.codigo && c.codigo.includes(searchVal));
                const matchesCarrera = (carreraVal === 'todos') || (c.carrera === carreraVal);
                const matchesSemestre = (semestreVal === 'todos') || (c.semestre === semestreVal);
                const matchesAnio = (anioVal === 'todos') || (c.anio === anioVal);

                return matchesSearch && matchesCarrera && matchesSemestre && matchesAnio;
            });

            tableBody.innerHTML = '';

            if (filtered.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="8" class="no-records-row">No se encontraron cursos con los filtros aplicados.</td>
                    </tr>
                `;
                return;
            }

            filtered.forEach((c, idx) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="td-id">${idx + 1}.</td>
                    <td class="td-carrera">${c.carrera}</td>
                    <td>${c.area}</td>
                    <td class="td-curso">${c.curso}</td>
                    <td class="td-codigo">${c.codigo}</td>
                    <td>${c.seccion}</td>
                    <td>${c.jornada}</td>
                    <td>
                        <button type="button" class="action-btn" onclick="confirmDeleteCourse(${c.id})" title="Eliminar Curso">
                            <!-- Icono de Papelera SVG -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </td>
                `;
                tableBody.appendChild(tr);
            });
        }

        // --- AGREGAR CURSO ---
        function openAddCourseModal() {
            document.getElementById('addCourseModal').classList.add('active');
        }

        function closeAddCourseModal() {
            document.getElementById('addCourseModal').classList.remove('active');
            document.getElementById('addCourseForm').reset();
        }

        function handleCreateCourse(e) {
            e.preventDefault();

            const carrera = document.getElementById('newCarrera').value;
            const area = document.getElementById('newArea').value.trim();
            const curso = document.getElementById('newCurso').value.trim();
            const codigo = document.getElementById('newCodigo').value.trim();
            const seccion = document.getElementById('newSeccion').value.trim();
            const jornada = document.getElementById('newJornada').value;
            const semestre = document.getElementById('newSemestre').value;
            const anio = document.getElementById('newAnio').value;

            if (!carrera || !area || !curso || !codigo || !seccion || !jornada || !semestre || !anio) return;

            const newId = dbCursos.length > 0 ? Math.max(...dbCursos.map(c => c.id)) + 1 : 1;
            const newC = {
                id: newId,
                carrera,
                area,
                curso,
                codigo,
                seccion,
                jornada,
                semestre,
                anio
            };

            dbCursos.push(newC);

            closeAddCourseModal();
            renderTable();
        }

        // --- ELIMINAR CURSO ---
        function confirmDeleteCourse(id) {
            deleteTargetCourseId = id;
            document.getElementById('confirmDeleteModal').classList.add('active');
        }

        function closeConfirmDeleteModal() {
            document.getElementById('confirmDeleteModal').classList.remove('active');
            deleteTargetCourseId = null;
        }

        // Simular eliminación en el dashboard de maquetado
        function executeDeleteCourse() {
            if (deleteTargetCourseId === null) return;

            dbCursos = dbCursos.filter(c => c.id !== deleteTargetCourseId);

            closeConfirmDeleteModal();
            renderTable();
        }

        document.addEventListener('DOMContentLoaded', () => {
            renderTable();

            // Listeners de filtros
            document.getElementById('searchInput').addEventListener('input', renderTable);
            document.getElementById('carreraFilter').addEventListener('change', renderTable);
            document.getElementById('semestreFilter').addEventListener('change', renderTable);
            document.getElementById('anioFilter').addEventListener('change', renderTable);

            // Modal Triggers
            document.getElementById('addCourseBtn').addEventListener('click', openAddCourseModal);

            // Hamburger Sidebar
            const hamburgerBtn = document.getElementById('hamburgerBtn');
            const adminSidebar = document.getElementById('adminSidebar');
            const mainContent = document.getElementById('mainContent');

            hamburgerBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                adminSidebar.classList.toggle('collapsed');
            });

            mainContent.addEventListener('click', () => {
                adminSidebar.classList.add('collapsed');
            });

            adminSidebar.addEventListener('click', (e) => {
                e.stopPropagation();
            });

            // Perfil Dropdown
            const profileToggle = document.getElementById('profileToggle');
            const profileDropdown = document.getElementById('profileDropdown');

            profileToggle.addEventListener('click', (e) => {
                e.stopPropagation();
                profileDropdown.classList.toggle('active');
            });

            document.addEventListener('click', () => {
                profileDropdown.classList.remove('active');
            });

            // Tecla Escape para cerrar modales
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    closeAddCourseModal();
                    closeConfirmDeleteModal();
                }
            });
        });
    </script>
</body>

</html>
