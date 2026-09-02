<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FARUSAC - Creación de Informe</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        /* --- VARIABLES DE COLOR Y ESTILOS GLOBALES --- */
        :root {
            --color-azul: #002D72;       /* Pantone 288C */
            --color-oro: #AC8400;        /* Pantone 118C */
            --color-terracota: #B94700;  /* Pantone 1525C */
            --color-fondo: #f8fafc;
            --color-tarjeta: #ffffff;
            --color-texto-principal: #1a202c;
            --color-texto-secundario: #4a5568;
            --color-texto-claro: #718096;
            --color-borde: #cbd5e1;
            --border-radius-card: 16px;
            --border-radius-input: 25px;
            --shadow-premium: 0 10px 30px rgba(0, 45, 114, 0.05);
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
            display: flex;
            flex-direction: column;
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
            gap: 20px;
        }

        .hamburger-btn {
            background: none;
            border: none;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            gap: 5px;
            padding: 8px;
            border-radius: 8px;
            transition: var(--transition-smooth);
        }

        .hamburger-btn:hover {
            background-color: #f7fafc;
        }

        .hamburger-line {
            width: 24px;
            height: 2.2px;
            background-color: var(--color-texto-principal);
            border-radius: 2px;
            transition: var(--transition-smooth);
        }

        .header-logo {
            height: 55px;
            object-fit: contain;
            transition: transform 0.3s ease;
        }

        .header-title {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            font-family: 'Outfit', sans-serif;
            font-size: 26px;
            font-weight: 700;
            color: var(--color-texto-principal);
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin: 0;
        }

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
            color: var(--color-texto-principal);
        }

        .profile-email {
            font-size: 11px;
            color: var(--color-texto-secundario);
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
            background-color: #fff5f5;
            color: #e53e3e;
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

        .sidebar-link:hover {
            background-color: rgba(0, 45, 114, 0.05);
            color: var(--color-azul);
        }
        .sidebar-link:hover .sidebar-icon {
            color: var(--color-azul);
        }

        /* --- CONTENIDO PRINCIPAL --- */
        .main-content {
            flex: 1;
            padding: 30px 40px;
            margin-left: 260px;
            transition: margin-left 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: calc(100vh - 90px);
            width: 100%;
        }

        .main-content.expanded {
            margin-left: 0;
        }

        /* WRAPPER DEL FORMULARIO */
        .wizard-container {
            width: 100%;
            max-width: 1050px;
            position: relative;
            margin-bottom: 50px;
        }

        /* Botón de Ayuda Top-Right */
        .help-btn {
            position: absolute;
            top: 0;
            right: 0;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            border: 1.5px solid var(--color-borde);
            background-color: #ffffff;
            color: var(--color-texto-secundario);
            font-family: 'Outfit', sans-serif;
            font-size: 16px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition-smooth);
        }

        .help-btn:hover {
            border-color: var(--color-azul);
            color: var(--color-azul);
            box-shadow: 0 4px 10px rgba(0, 45, 114, 0.1);
        }

        .wizard-title {
            text-align: center;
            font-family: 'Outfit', sans-serif;
            font-size: 26px;
            font-weight: 700;
            color: var(--color-texto-principal);
            margin-bottom: 25px;
        }

        /* --- STEPPER (INDICADOR DE FASES) --- */
        .stepper-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            margin-bottom: 35px;
        }

        .step-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            cursor: pointer;
        }

        .step-circle {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background-color: #ffffff;
            border: 2px solid var(--color-borde);
            color: var(--color-texto-claro);
            font-family: 'Outfit', sans-serif;
            font-size: 20px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition-smooth);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
        }

        .step-item.active .step-circle {
            background-color: var(--color-azul);
            border-color: var(--color-azul);
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(0, 45, 114, 0.25);
        }

        .step-item.completed .step-circle {
            background-color: #ffffff;
            border-color: var(--color-azul);
            color: var(--color-azul);
        }

        .step-label {
            margin-top: 8px;
            font-family: 'Outfit', sans-serif;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--color-texto-secundario);
            transition: color 0.2s ease;
        }

        .step-item.active .step-label {
            color: var(--color-azul);
            font-weight: 700;
        }

        .stepper-arrow {
            width: 40px;
            height: 24px;
            color: var(--color-texto-claro);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 22px;
        }

        /* --- TARJETA PRINCIPAL DEL FORMULARIO --- */
        .form-card-panel {
            background-color: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 20px;
            padding: 35px 40px;
            box-shadow: var(--shadow-premium);
        }

        /* CAMPOS Y LAYOUT */
        .form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 22px;
        }

        .form-col {
            flex: 1;
            min-width: 160px;
            display: flex;
            flex-direction: column;
        }

        .form-col-small {
            flex: 0 0 130px;
        }

        .form-col-full {
            flex: 1 1 100%;
        }

        .form-label {
            font-family: 'Outfit', sans-serif;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--color-texto-principal);
            margin-bottom: 6px;
        }

        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 10px 18px;
            font-family: 'Inter', sans-serif;
            font-size: 13.5px;
            color: var(--color-texto-principal);
            background-color: #ffffff;
            border: 1.5px solid var(--color-borde);
            border-radius: var(--border-radius-input);
            outline: none;
            transition: var(--transition-smooth);
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            border-color: var(--color-azul);
            box-shadow: 0 0 0 3px rgba(0, 45, 114, 0.08);
        }

        .input-readonly {
            background-color: #f1f5f9;
            color: #475569;
            cursor: default;
        }

        .input-error {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15) !important;
        }

        .form-hint {
            font-size: 11px;
            color: var(--color-texto-claro);
            margin-top: 4px;
            font-style: italic;
        }

        .form-textarea {
            border-radius: 16px;
            resize: vertical;
            min-height: 80px;
        }

        /* Select con Flecha Personalizada */
        .select-container {
            position: relative;
        }

        .select-container select {
            appearance: none;
            -webkit-appearance: none;
            padding-right: 38px;
            cursor: pointer;
        }

        .select-custom-arrow {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            width: 18px;
            height: 18px;
            color: #64748b;
        }

        /* --- FASE 2: REGISTRO SEMANAL --- */
        .semanas-list {
            display: flex;
            flex-direction: column;
            gap: 25px;
            margin-bottom: 25px;
        }

        .semana-card {
            background-color: #ffffff;
            border: 1.5px solid #cbd5e1;
            border-radius: 16px;
            padding: 22px 26px;
            position: relative;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
            animation: fadeIn 0.3s ease;
        }

        .semana-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .semana-badge {
            font-family: 'Outfit', sans-serif;
            font-size: 15px;
            font-weight: 700;
            color: var(--color-texto-principal);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .btn-delete-semana {
            background: none;
            border: none;
            color: #ef4444;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 4px;
            transition: background-color 0.2s ease;
        }

        .btn-delete-semana:hover {
            background-color: #fee2e2;
        }

        /* Botón Agregar Semana */
        .add-semana-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 25px 0 35px 0;
            text-align: center;
        }

        .btn-add-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: #ffffff;
            border: 2px solid #cbd5e1;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition-smooth);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
            margin-bottom: 10px;
        }

        .btn-add-circle-dashed {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 2px dashed #94a3b8;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition-smooth);
        }

        .btn-add-circle:hover {
            border-color: var(--color-azul);
            transform: scale(1.05);
            box-shadow: 0 8px 20px rgba(0, 45, 114, 0.15);
        }

        .btn-add-circle:hover .btn-add-circle-dashed {
            border-color: var(--color-azul);
            transform: rotate(90deg);
        }

        .btn-add-circle svg {
            width: 20px;
            height: 20px;
            color: var(--color-texto-principal);
            transition: color 0.2s ease;
        }

        .btn-add-circle:hover svg {
            color: var(--color-azul);
        }

        .add-semana-hint {
            font-size: 12px;
            color: var(--color-texto-secundario);
            font-style: italic;
        }

        /* --- BOTONERA INFERIOR DE NAVEGACIÓN DEL ASISTENTE --- */
        .wizard-actions {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }

        .btn-wizard {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 35px;
            border-radius: 25px;
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            cursor: pointer;
            transition: var(--transition-smooth);
            border: none;
            outline: none;
        }

        .btn-wizard-next {
            background-color: var(--color-azul);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 45, 114, 0.25);
        }

        .btn-wizard-next:hover {
            background-color: #002257;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(0, 45, 114, 0.35);
        }

        .btn-wizard-prev {
            background-color: #ffffff;
            border: 2px solid #94a3b8;
            color: #475569;
        }

        .btn-wizard-prev:hover {
            background-color: #f1f5f9;
            color: var(--color-texto-principal);
            border-color: #64748b;
        }

        .btn-wizard-finish {
            background-color: var(--color-azul);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 45, 114, 0.25);
        }

        .btn-wizard-finish:hover {
            background-color: #002257;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(0, 45, 114, 0.35);
        }

        /* --- MODAL DE CONFIRMACIÓN (IDÉNTICO A GESTIÓN DE USUARIOS) --- */
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

        /* Estilo de éxito (verde) para confirmación de envío idéntico al de activación */
        .confirm-status-card.success-style {
            border-color: #38a169;
            box-shadow: 0 20px 45px rgba(56, 161, 105, 0.15);
        }

        .confirm-status-card.success-style .alert-title {
            color: #38a169;
        }

        .confirm-status-card.success-style .success-check-icon {
            font-size: 52px;
            color: #38a169;
            margin-bottom: 15px;
            line-height: 1;
            font-weight: 700;
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

        /* Toast */
        .toast-notification {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background-color: #1e293b;
            color: #ffffff;
            padding: 14px 22px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 2000;
            transform: translateY(100px);
            opacity: 0;
            transition: var(--transition-smooth);
        }

        .toast-notification.show {
            transform: translateY(0);
            opacity: 1;
        }

        .toast-notification.success {
            border-left: 4px solid #48bb78;
        }

        .toast-notification.error {
            border-left: 4px solid #ef4444;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
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

    <div class="app-container">

        <!-- Menú Lateral (Sidebar) -->
        <aside class="admin-sidebar collapsed" id="docenteSidebar">
            <nav class="sidebar-nav">
                <!-- Enlace Inicio -->
                <a href="{{ route('docente.dashboard') }}" class="sidebar-link">
                    <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Inicio</span>
                </a>

                <!-- Enlace Perfil -->
                <a href="#" class="sidebar-link">
                    <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Perfil</span>
                </a>

                <!-- Enlace Informes (Activo) -->
                <a href="{{ route('docente.informes.crear') }}" class="sidebar-link sidebar-link-informes">
                    <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Informes</span>
                </a>
            </nav>
        </aside>

        <!-- Cuerpo Principal -->
        <main class="main-content expanded" id="mainContent">

            <div class="wizard-container">
                <!-- Botón de Ayuda Top-Right -->
                <button type="button" class="help-btn" title="Ayuda y Requisitos">?</button>

                <h2 class="wizard-title">Creación de Informe</h2>

                <!-- INDICADOR DE FASES (STEPPER) -->
                <div class="stepper-wrapper">
                    <div class="step-item active" id="stepIndicator1" onclick="goToStep(1)">
                        <div class="step-circle">1</div>
                        <div class="step-label">Información General</div>
                    </div>

                    <div class="stepper-arrow">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </div>

                    <div class="step-item" id="stepIndicator2" onclick="goToStep(2)">
                        <div class="step-circle">2</div>
                        <div class="step-label">Registro Semanal</div>
                    </div>
                </div>

                <!-- FORMULARIO ASISTENTE -->
                <form id="formCrearInforme" onsubmit="event.preventDefault();">

                    <!-- ========================================== -->
                    <!-- FASE 1: INFORMACIÓN GENERAL -->
                    <!-- ========================================== -->
                    <div class="form-card-panel" id="phase1Panel">

                        <!-- Fila 1: Docente, Correo, Curso, Sección, Estudiantes Asignados -->
                        <div class="form-row">
                            <div class="form-col">
                                <label class="form-label">*Nombre del docente:</label>
                                <input type="text" id="inputDocenteNombre" class="form-input input-readonly" value="{{ $user->nombre }}" readonly>
                            </div>

                            <div class="form-col">
                                <label class="form-label">*Correo:</label>
                                <input type="text" id="inputDocenteCorreo" class="form-input input-readonly" value="{{ $user->correo }}" readonly>
                            </div>

                            <div class="form-col">
                                <label class="form-label" for="cursoIdSelect">*Curso:</label>
                                <div class="select-container">
                                    <select id="cursoIdSelect" class="form-select" onchange="onCursoSelected()" required>
                                        <option value="">Seleccione un curso...</option>
                                        @foreach ($cursosAsignados as $c)
                                            <option value="{{ $c->id }}" 
                                                data-seccion="{{ $c->seccion }}" 
                                                data-anio="{{ $c->anio }}"
                                                data-semestre="{{ $c->semestre }}"
                                                {{ (isset($selectedCurso) && $selectedCurso->id == $c->id) ? 'selected' : '' }}>
                                                {{ $c->nombre_curso }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <svg class="select-custom-arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>

                            <div class="form-col form-col-small">
                                <label class="form-label">*Sección:</label>
                                <input type="text" id="inputSeccion" class="form-input input-readonly" value="{{ isset($selectedCurso) ? $selectedCurso->seccion : '' }}" readonly placeholder="—">
                            </div>

                            <div class="form-col form-col-small">
                                <label class="form-label" for="inputEstudiantesAsignados">*Estudiantes Asignados:</label>
                                <input type="number" id="inputEstudiantesAsignados" class="form-input" min="1" placeholder="Ej. 35" required>
                            </div>
                        </div>

                        <!-- Fila 2: Año, Periodo y Mes -->
                        <div class="form-row">
                            <div class="form-col form-col-small">
                                <label class="form-label" for="inputAnio">* Año:</label>
                                <input type="text" id="inputAnio" class="form-input input-readonly" value="{{ isset($selectedCurso) ? $selectedCurso->anio : date('Y') }}" readonly placeholder="2026">
                            </div>

                            <div class="form-col">
                                <label class="form-label" for="selectPeriodo">* Periodo</label>
                                <div class="select-container">
                                    <select id="selectPeriodo" class="form-select" required>
                                        <option value="Primer Semestre">Primer Semestre</option>
                                        <option value="Segundo Semestre">Segundo Semestre</option>
                                        <option value="Vacaciones Junio">Vacaciones Junio</option>
                                        <option value="Vacaciones Diciembre">Vacaciones Diciembre</option>
                                    </select>
                                    <svg class="select-custom-arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>

                            <div class="form-col">
                                <label class="form-label" for="selectMes">* Mes</label>
                                <div class="select-container">
                                    <select id="selectMes" class="form-select" required>
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
                                    <svg class="select-custom-arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Fila 3: Listado de asistencia, Enlace de evidencia, Enlace de Meet o Zoom -->
                        <div class="form-row">
                            <div class="form-col">
                                <label class="form-label" for="inputListadoAsistencia">Listado de asistencia:</label>
                                <input type="url" id="inputListadoAsistencia" class="form-input" placeholder="https://drive.google.com/drive/folders/1aBcD...">
                            </div>

                            <div class="form-col">
                                <label class="form-label" for="inputEnlaceEvidencia">Enlace de evidencia:</label>
                                <input type="url" id="inputEnlaceEvidencia" class="form-input" placeholder="https://drive.google.com/drive/folders/1xYz...">
                            </div>

                            <div class="form-col">
                                <label class="form-label" for="inputEnlaceMeet">Enlace de Meet o Zoom:</label>
                                <input type="url" id="inputEnlaceMeet" class="form-input" placeholder="https://meet.google.com/abc-defg-hij">
                            </div>
                        </div>

                        <!-- Fila 4: Enlace de classroom, campus virtual o google drive -->
                        <div class="form-row">
                            <div class="form-col-full">
                                <label class="form-label" for="inputEnlaceClassroom">Enlace de classroom, campus virtual o google drive</label>
                                <input type="url" id="inputEnlaceClassroom" class="form-input" placeholder="https://classroom.google.com/c/MzQ1Nj...">
                            </div>
                        </div>

                        <!-- Fila 5: Herramientas / Estrategias de evaluación -->
                        <div class="form-row">
                            <div class="form-col-full">
                                <label class="form-label" for="inputEstrategias">Describa herramientas y/o estrategias de evaluación que utiliza dentro de su asignatura (Matrices de evaluación, rubricas, etc):</label>
                                <textarea id="inputEstrategias" class="form-textarea" rows="3" placeholder="Escriba las herramientas y estrategias utilizadas..."></textarea>
                            </div>
                        </div>

                        <div class="wizard-actions">
                            <button type="button" class="btn-wizard btn-wizard-next" onclick="goToStep(2)">
                                SIGUIENTE -&gt;
                            </button>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- FASE 2: REGISTRO SEMANAL -->
                    <!-- ========================================== -->
                    <div class="form-card-panel" id="phase2Panel" style="display: none;">

                        <!-- Contenedor Dinámico de Semanas -->
                        <div class="semanas-list" id="semanasContainer">
                            <!-- Semana 1 (Por defecto) -->
                            <div class="semana-card" id="semanaCard-1" data-semana="1">
                                <div class="semana-header">
                                    <span class="semana-badge">SEMANA 1</span>
                                </div>

                                <div class="form-row">
                                    <div class="form-col-full">
                                        <label class="form-label">Contenido/Actividad realizada</label>
                                        <textarea class="form-textarea semana-actividad" rows="3" placeholder="Describa el contenido o actividad realizada durante la semana..."></textarea>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-col form-col-small" style="flex: 0 0 180px;">
                                        <label class="form-label">No. Estudiantes que participaron</label>
                                        <input type="number" class="form-input semana-estudiantes" min="0" value="0">
                                    </div>

                                    <div class="form-col">
                                        <label class="form-label">Metodologías utilizadas</label>
                                        <textarea class="form-textarea semana-metodologias" rows="3" placeholder="Describa las metodologías..."></textarea>
                                    </div>

                                    <div class="form-col">
                                        <label class="form-label">Plataforma, aplicación o medios de comunicación</label>
                                        <textarea class="form-textarea semana-medios" rows="3" placeholder="Ej. Google Meet, Classroom, WhatsApp..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botón Circular: Agregar otra Semana -->
                        <div class="add-semana-wrapper">
                            <div class="btn-add-circle" onclick="addNewSemana()" title="Agregar otra semana">
                                <div class="btn-add-circle-dashed">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                </div>
                            </div>
                            <span class="add-semana-hint">Agregar otro cuadro de semana y la semana se tiene que autoincrementar</span>
                        </div>

                        <div class="wizard-actions">
                            <button type="button" class="btn-wizard btn-wizard-prev" onclick="goToStep(1)">
                                &lt;- ANTERIOR
                            </button>
                            <button type="button" class="btn-wizard btn-wizard-finish" id="btnFinishWizard" onclick="openFinishModal()">
                                TERMINAR -&gt;
                            </button>
                        </div>
                    </div>

                </form>
            </div>

        </main>
    </div>

    <!-- 1. MODAL DE CONFIRMACIÓN DE ENVÍO (ADVERTENCIA) -->
    <div class="modal-overlay" id="confirmFinishModal" onclick="closeFinishModal()">
        <div class="confirm-status-card" onclick="event.stopPropagation()">
            <div class="alert-content">
                <div class="warning-triangle">⚠</div>
                <h3 class="alert-title">¿ESTÁ SEGURO QUE DESEA FINALIZAR Y ENVIAR EL INFORME?</h3>
                <p class="alert-desc">
                    Al confirmar, el informe será registrado en el sistema. Recuerde que tiene un <b>máximo de 3 días</b> para editar o modificar su informe antes de que se bloquee para revisión.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="modal-btn btn-submit" id="btnConfirmSubmit" onclick="executeSubmitInforme()">CONFIRMAR</button>
                <button type="button" class="modal-btn btn-cancel" onclick="closeFinishModal()">CANCELAR</button>
            </div>
        </div>
    </div>

    <!-- 2. MODAL DE ÉXITO (NOTIFICACIÓN VERDE TRAS GUARDAR) -->
    <div class="modal-overlay" id="successInformeModal">
        <div class="confirm-status-card success-style" onclick="event.stopPropagation()">
            <div class="alert-content">
                <div class="success-check-icon">✓</div>
                <h3 class="alert-title">¡INFORME ENVIADO EXITOSAMENTE!</h3>
                <p class="alert-desc">
                    El informe ha sido creado y enviado exitosamente. Recuerda que tienes un <b>máximo de 3 días</b> para editar o modificar tu informe antes de que se bloquee para revisión.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="modal-btn btn-submit" id="btnSuccessConfirm" onclick="redirectToDashboard()">CONFIRMAR</button>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="toast-notification" id="toastNotification">
        <span id="toastMessage"></span>
    </div>

    <!-- JAVASCRIPT: CONTROL DE WIZARD, SEMANAS DINÁMICAS Y AJAX -->
    <script>
        let currentStep = 1;
        let semanaCount = 1;

        // Auto-actualizar sección, año y periodo al seleccionar un curso
        function onCursoSelected() {
            const select = document.getElementById('cursoIdSelect');
            const seccionInput = document.getElementById('inputSeccion');
            const anioInput = document.getElementById('inputAnio');
            const periodoSelect = document.getElementById('selectPeriodo');

            const selectedOption = select.options[select.selectedIndex];
            if (selectedOption && selectedOption.value) {
                seccionInput.value = selectedOption.getAttribute('data-seccion') || '';
                anioInput.value = selectedOption.getAttribute('data-anio') || new Date().getFullYear();

                const semestreVal = selectedOption.getAttribute('data-semestre') || '';
                if (semestreVal) {
                    for (let opt of periodoSelect.options) {
                        if (opt.value.toLowerCase().includes(semestreVal.toLowerCase()) || 
                            (semestreVal === '1' && opt.value.includes('Primer')) || 
                            (semestreVal === '2' && opt.value.includes('Segundo'))) {
                            periodoSelect.value = opt.value;
                            break;
                        }
                    }
                }
            } else {
                seccionInput.value = '';
                anioInput.value = new Date().getFullYear();
            }
        }

        // Navegación entre pasos con validación estricta de campos obligatorios (*)
        function goToStep(step) {
            if (step === 2) {
                // Limpiar errores visuales previos
                document.querySelectorAll('.input-error').forEach(el => el.classList.remove('input-error'));

                let hasErrors = false;
                let firstErrorEl = null;

                const fieldsToValidate = [
                    { id: 'cursoIdSelect', name: 'Curso' },
                    { id: 'inputSeccion', name: 'Sección' },
                    { id: 'inputEstudiantesAsignados', name: 'Estudiantes Asignados', isNumber: true },
                    { id: 'inputAnio', name: 'Año' },
                    { id: 'selectPeriodo', name: 'Periodo' },
                    { id: 'selectMes', name: 'Mes' }
                ];

                for (let field of fieldsToValidate) {
                    const el = document.getElementById(field.id);
                    if (!el) continue;
                    const val = el.value.trim();

                    if (!val || (field.isNumber && (isNaN(val) || parseInt(val) <= 0))) {
                        el.classList.add('input-error');
                        hasErrors = true;
                        if (!firstErrorEl) firstErrorEl = el;
                    }
                }

                if (hasErrors) {
                    const estudiantesEl = document.getElementById('inputEstudiantesAsignados');
                    const estudiantesVal = parseInt(estudiantesEl.value.trim());
                    if (estudiantesEl.classList.contains('input-error') && (!estudiantesEl.value.trim() || estudiantesVal <= 0)) {
                        showToast('La cantidad de estudiantes asignados debe ser mayor a 0.', 'error');
                    } else {
                        showToast('Por favor completa todos los campos obligatorios (*) antes de avanzar.', 'error');
                    }
                    if (firstErrorEl) firstErrorEl.focus();
                    return;
                }

                currentStep = 2;
                document.getElementById('phase1Panel').style.display = 'none';
                document.getElementById('phase2Panel').style.display = 'block';

                document.getElementById('stepIndicator1').classList.remove('active');
                document.getElementById('stepIndicator1').classList.add('completed');
                document.getElementById('stepIndicator2').classList.add('active');
            } else {
                currentStep = 1;
                document.getElementById('phase2Panel').style.display = 'none';
                document.getElementById('phase1Panel').style.display = 'block';

                document.getElementById('stepIndicator2').classList.remove('active');
                document.getElementById('stepIndicator1').classList.add('active');
                document.getElementById('stepIndicator1').classList.remove('completed');
            }
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Agregar nueva semana autoincrementada
        function addNewSemana() {
            semanaCount++;
            const container = document.getElementById('semanasContainer');

            const semanaDiv = document.createElement('div');
            semanaDiv.className = 'semana-card';
            semanaDiv.id = `semanaCard-${semanaCount}`;
            semanaDiv.setAttribute('data-semana', semanaCount);

            semanaDiv.innerHTML = `
                <div class="semana-header">
                    <span class="semana-badge">SEMANA ${semanaCount}</span>
                    <button type="button" class="btn-delete-semana" onclick="deleteSemana(${semanaCount})">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                        Eliminar
                    </button>
                </div>

                <div class="form-row">
                    <div class="form-col-full">
                        <label class="form-label">Contenido/Actividad realizada</label>
                        <textarea class="form-textarea semana-actividad" rows="3" placeholder="Describa el contenido o actividad realizada durante la semana..."></textarea>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col form-col-small" style="flex: 0 0 180px;">
                        <label class="form-label">No. Estudiantes que participaron</label>
                        <input type="number" class="form-input semana-estudiantes" min="0" value="0">
                    </div>

                    <div class="form-col">
                        <label class="form-label">Metodologías utilizadas</label>
                        <textarea class="form-textarea semana-metodologias" rows="3" placeholder="Describa las metodologías..."></textarea>
                    </div>

                    <div class="form-col">
                        <label class="form-label">Plataforma, aplicación o medios de comunicación</label>
                        <textarea class="form-textarea semana-medios" rows="3" placeholder="Ej. Google Meet, Classroom, WhatsApp..."></textarea>
                    </div>
                </div>
            `;

            container.appendChild(semanaDiv);
        }

        // Eliminar semana
        function deleteSemana(num) {
            const card = document.getElementById(`semanaCard-${num}`);
            if (card) {
                card.remove();
                renumberSemanas();
            }
        }

        // Reenumerar semanas para mantener consistencia
        function renumberSemanas() {
            const cards = document.querySelectorAll('#semanasContainer .semana-card');
            semanaCount = cards.length;
            cards.forEach((card, index) => {
                const newNum = index + 1;
                card.id = `semanaCard-${newNum}`;
                card.setAttribute('data-semana', newNum);
                const badge = card.querySelector('.semana-badge');
                if (badge) badge.textContent = `SEMANA ${newNum}`;

                const delBtn = card.querySelector('.btn-delete-semana');
                if (delBtn) {
                    delBtn.setAttribute('onclick', `deleteSemana(${newNum})`);
                }
            });
        }

        // 1. Abrir Modal de Confirmación previo a enviar
        function openFinishModal() {
            const estudiantesAsignados = parseInt(document.getElementById('inputEstudiantesAsignados').value) || 0;
            if (estudiantesAsignados <= 0) {
                showToast('La cantidad de estudiantes asignados debe ser mayor a 0.', 'error');
                goToStep(1);
                document.getElementById('inputEstudiantesAsignados').classList.add('input-error');
                document.getElementById('inputEstudiantesAsignados').focus();
                return;
            }

            const primeraActividad = document.querySelector('.semana-actividad')?.value.trim();
            if (!primeraActividad) {
                showToast('Por favor describe el contenido o actividad realizada en al menos una semana.', 'error');
                return;
            }

            document.getElementById('confirmFinishModal').classList.add('active');
        }

        // Cerrar Modal de Confirmación
        function closeFinishModal() {
            document.getElementById('confirmFinishModal').classList.remove('active');
        }

        // 2. Enviar informe vía AJAX al confirmar en el modal de advertencia
        function executeSubmitInforme() {
            const btnConfirm = document.getElementById('btnConfirmSubmit');
            const estudiantesAsignados = parseInt(document.getElementById('inputEstudiantesAsignados').value) || 0;

            btnConfirm.disabled = true;
            btnConfirm.textContent = 'ENVIANDO...';

            // Recopilar datos de Fase 1
            const anioVal = document.getElementById('inputAnio').value.trim() || new Date().getFullYear();
            const periodoVal = document.getElementById('selectPeriodo').value.trim();

            const payload = {
                curso_id: document.getElementById('cursoIdSelect').value,
                periodo: `${periodoVal} ${anioVal}`,
                mes: document.getElementById('selectMes').value,
                estudiantes_asignados: estudiantesAsignados,
                listado_asistencia_url: document.getElementById('inputListadoAsistencia').value.trim() || null,
                enlace_evidencia_url: document.getElementById('inputEnlaceEvidencia').value.trim() || null,
                enlace_meet_zoom_url: document.getElementById('inputEnlaceMeet').value.trim() || null,
                enlace_classroom_drive_url: document.getElementById('inputEnlaceClassroom').value.trim() || null,
                estrategias_evaluacion: document.getElementById('inputEstrategias').value.trim() || null,
                semanas: []
            };

            // Recopilar datos de Fase 2
            const semanaCards = document.querySelectorAll('#semanasContainer .semana-card');
            semanaCards.forEach((card, index) => {
                payload.semanas.push({
                    numero_semana: index + 1,
                    actividad_realizada: card.querySelector('.semana-actividad').value.trim() || 'Sin descripción',
                    estudiantes_participaron: parseInt(card.querySelector('.semana-estudiantes').value) || 0,
                    metodologias: card.querySelector('.semana-metodologias').value.trim() || null,
                    medios_comunicacion: card.querySelector('.semana-medios').value.trim() || null,
                });
            });

            fetch('{{ route("docente.informes.guardar") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json().then(data => ({ status: res.status, body: data })))
            .then(({ status, body }) => {
                btnConfirm.disabled = false;
                btnConfirm.textContent = 'CONFIRMAR';
                closeFinishModal();

                if (status === 200 && body.success) {
                    // Abrir la notificación modal verde de éxito con botón único de confirmar
                    document.getElementById('successInformeModal').classList.add('active');
                } else {
                    showToast(body.message || 'Ocurrió un error al registrar el informe.', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                btnConfirm.disabled = false;
                btnConfirm.textContent = 'CONFIRMAR';
                closeFinishModal();
                showToast('Error de conexión con el servidor.', 'error');
            });
        }

        // 3. Redirigir a inicio al hacer clic en CONFIRMAR en la notificación verde de éxito
        function redirectToDashboard() {
            window.location.href = '{{ route("docente.dashboard") }}';
        }

        // Helper para Toasts
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toastNotification');
            const toastMsg = document.getElementById('toastMessage');
            toastMsg.textContent = message;
            toast.className = `toast-notification ${type} show`;

            setTimeout(() => {
                toast.classList.remove('show');
            }, 3800);
        }

        // Toggle Sidebar y Perfil
        document.addEventListener('DOMContentLoaded', () => {
            const profileToggle = document.getElementById('profileToggle');
            const profileDropdown = document.getElementById('profileDropdown');
            const hamburgerBtn = document.getElementById('hamburgerBtn');
            const docenteSidebar = document.getElementById('docenteSidebar');
            const mainContent = document.getElementById('mainContent');

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
                mainContent.classList.toggle('expanded');
            });

            mainContent.addEventListener('click', () => {
                docenteSidebar.classList.add('collapsed');
                mainContent.classList.add('expanded');
            });

            docenteSidebar.addEventListener('click', (e) => {
                e.stopPropagation();
            });

            // Limpiar clase de error al interactuar con los campos obligatorios
            ['cursoIdSelect', 'inputEstudiantesAsignados', 'selectPeriodo', 'inputAnio', 'selectMes'].forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.addEventListener('input', () => el.classList.remove('input-error'));
                    el.addEventListener('change', () => el.classList.remove('input-error'));
                }
            });

            // Trigger inicial si ya hay curso seleccionado
            onCursoSelected();
        });
    </script>
</body>

</html>
