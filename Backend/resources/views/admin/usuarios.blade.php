<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Sistema de Informes</title>
    <!-- Google Fonts: Outfit (headings) and Inter (body) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --color-azul: #002D72;
            /* Pantone 288C */
            --color-dorado: #AC8400;
            /* Pantone 118C */
            --color-terracota: #B94700;
            /* Pantone 1525C */
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
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s ease;
            flex-shrink: 0;
            position: fixed;
            /* Superponer sobre el contenido */
            top: 90px;
            /* Justo abajo del header */
            left: 0;
            height: calc(100vh - 90px);
            z-index: 99;
            /* Encima del contenido principal */
            box-shadow: 10px 0 25px rgba(0, 45, 114, 0.08);
            /* Sombra elegante */
            transform: translateX(0);
            overflow-y: auto;
        }

        .admin-sidebar.collapsed {
            transform: translateX(-100%);
            /* Deslizar a la izquierda */
            box-shadow: none;
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

        /* Estilos de Hover/Activo basados en los colores del dashboard */
        .sidebar-link-inicio:hover,
        .sidebar-link-inicio.active {
            background-color: rgba(0, 45, 114, 0.05);
            color: var(--color-azul);
        }

        .sidebar-link-usuarios:hover,
        .sidebar-link-usuarios.active {
            background-color: rgba(0, 45, 114, 0.05);
            color: var(--color-azul);
        }

        .sidebar-link-cursos:hover,
        .sidebar-link-cursos.active {
            background-color: rgba(172, 132, 0, 0.05);
            color: var(--color-dorado);
        }

        .sidebar-link-informes:hover,
        .sidebar-link-informes.active {
            background-color: rgba(185, 71, 0, 0.05);
            color: var(--color-terracota);
        }

        .sidebar-link-carga:hover,
        .sidebar-link-carga.active {
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
            max-height: 90vh;
            overflow-y: auto;
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
            background-color: #93c5fd;
            /* Azul claro */
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
            /* Rojo/Rosado claro */
            color: #991b1b;
            border-color: #ef4444;
            box-shadow: 0 4px 12px rgba(248, 113, 113, 0.25);
        }

        /* --- NUEVAS CLASES PARA GESTIÓN DE USUARIOS --- */
        .input-disabled {
            background-color: #edf2f7 !important;
            color: #718096 !important;
            cursor: not-allowed;
            border-color: #e2e8f0 !important;
        }

        .input-disabled:focus {
            border-color: #cbd5e0 !important;
            box-shadow: none !important;
        }

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
            background-color: #f7fafc;
            color: var(--color-azul);
        }

        .action-btn svg {
            width: 20px;
            height: 20px;
        }

        .status-badge.clickable {
            cursor: pointer;
            transition: transform 0.2s ease, opacity 0.2s ease;
        }

        .status-badge.clickable:hover {
            opacity: 0.85;
            transform: scale(1.03);
        }

        /* CARD DE ALERTA PARA CAMBIO DE ESTADO */
        .confirm-status-card {
            background-color: #ffffff;
            border-radius: var(--border-radius-card);
            border: 2.5px solid #e53e3e;
            /* Borde rojo del bosquejo */
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
            /* Amarillo de advertencia */
            margin-bottom: 15px;
            line-height: 1;
        }

        .alert-title {
            font-family: 'Outfit', sans-serif;
            font-size: 15px;
            font-weight: 800;
            color: #e53e3e;
            /* Texto de la cabecera en rojo */
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

        /* Estilo de éxito (verde) para reactivación de usuario */
        .confirm-status-card.success-style {
            border-color: #38a169;
            /* Verde */
            box-shadow: 0 20px 45px rgba(56, 161, 105, 0.15);
        }

        .confirm-status-card.success-style .alert-title {
            color: #38a169;
            /* Título verde */
        }

        .confirm-status-card.success-style .warning-triangle {
            color: #38a169;
            /* Icono verde */
        }

        /* CONTENEDOR DE ACCIONES DE AGREGAR Y CARGA MASIVA */
        .add-actions-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
            margin-bottom: 2px;
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
            /* Coincide con el estilo manuscrito del bosquejo */
            letter-spacing: 0.02em;
            text-align: center;
        }

        .bulk-load-link:hover {
            color: var(--color-azul);
            border-color: var(--color-azul);
            background-color: rgba(0, 45, 114, 0.02);
            box-shadow: 0 2px 5px rgba(0, 45, 114, 0.05);
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

    <!-- Contenedor general del layout de la aplicación (Sidebar + Contenido) -->
    <div class="app-container">

        <!-- Menú Lateral (Sidebar) -->
        <aside class="admin-sidebar collapsed" id="adminSidebar">
            <nav class="sidebar-nav">
                <!-- Enlace Inicio -->
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link sidebar-link-inicio">
                    <!-- Icono de Casa SVG -->
                    <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Inicio</span>
                </a>

                <!-- Enlace Usuarios (Activo) -->
                <a href="{{ route('admin.usuarios') }}" class="sidebar-link sidebar-link-usuarios active">
                    <!-- Icono de Usuario SVG -->
                    <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Usuarios</span>
                </a>

                <!-- Enlace Cursos -->
                <a href="{{ route('admin.cursos') }}" class="sidebar-link sidebar-link-cursos">
                    <!-- Icono de Libro SVG -->
                    <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span>Cursos</span>
                </a>

                <!-- Enlace Informes -->
                <a href="#" class="sidebar-link sidebar-link-informes">
                    <!-- Icono de Documento/Gráfico SVG -->
                    <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Informes</span>
                </a>

                <!-- Enlace Carga de datos -->
                <a href="{{ route('admin.carga-datos') }}" class="sidebar-link sidebar-link-carga">
                    <!-- Icono de Base de Datos SVG -->
                    <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                    </svg>
                    <span>Carga de datos</span>
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
                                        Se puede buscar por número de teléfono, correo electrónico o nombre de usuario
                                        por medio de texto.
                                    </div>
                                </div>
                            </div>
                            <input type="text" id="searchInput" class="input-text"
                                placeholder="Ej. Juan, juan@farusac.edu.gt, 5587...">
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

                    <!-- Botón y Acciones de Agregar / Carga Masiva -->
                    <div class="add-actions-container">
                        <button type="button" class="add-user-trigger" id="addUserBtn"
                            title="Agregar Usuario Individual">
                            <div class="add-user-avatar">
                                <!-- Icono de usuario SVG -->
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span class="plus-badge">+</span>
                            </div>
                        </button>
                        <a href="{{ route('admin.carga-datos') }}" class="bulk-load-link"
                            title="Ir a Carga de Datos Masiva">carga masiva</a>
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
                                <th>Número</th>
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
                    <input type="email" id="newUserEmail" class="input-text" placeholder="Ej. juan.perez@farusac.edu.gt"
                        required>
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
                    <label for="editUserPhone" class="modal-form-label">Número</label>
                    <input type="text" id="editUserPhone" class="input-text" placeholder="Ej. 5587-1751" required>
                </div>

                <div class="form-group">
                    <label for="editUserRole" class="modal-form-label">Rol</label>
                    <select id="editUserRole" class="select-filter" style="width: 100%;" required>
                        <option value="administrador">Administrador</option>
                        <option value="jefe">Jefe</option>
                        <option value="docente">Docente</option>
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

    <!-- Modal Emergente: Confirmación de Alerta de Cambio de Estado (Desactivar/Activar) -->
    <div class="modal-overlay" id="confirmStatusModal" onclick="closeConfirmStatusModal()">
        <div class="confirm-status-card" onclick="event.stopPropagation()" id="confirmStatusCard">
            <div class="alert-content">
                <div class="warning-triangle" id="alertIcon">⚠</div>
                <h3 class="alert-title" id="alertTitle">¿ESTÁ SEGURO QUE QUIERE DESACTIVAR EL USUARIO?</h3>
                <p class="alert-desc" id="alertDesc">El usuario perderá acceso al sistema hasta que vuelva a ser
                    habilitado</p>
            </div>
            <div class="modal-footer" style="margin-top: 25px;">
                <button type="button" class="modal-btn btn-submit" id="confirmStatusBtn"
                    onclick="executeToggleEstado()">Confirmar</button>
                <button type="button" class="modal-btn btn-cancel" onclick="closeConfirmStatusModal()">Cancelar</button>
            </div>
        </div>
    </div>

    <!-- JavaScript para Interactividad y Filtros Locales (Dashboard Vivo) -->
    <script>
        // Base de Datos de Usuarios cargada dinámicamente desde el backend
        let dbUsuarios = @json($usuarios);

        // Variables globales para rastrear el ID del usuario en edición o cambio de estado
        let statusTargetUserId = null;

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
                    (user.nombre && user.nombre.toLowerCase().includes(searchVal)) ||
                    (user.correo && user.correo.toLowerCase().includes(searchVal)) ||
                    (user.numero && user.numero.includes(searchVal));

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
                        <td colspan="8" class="no-records-row">No se encontraron usuarios con los filtros aplicados.</td>
                    </tr>
                `;
                return;
            }

            // Inyectar filas
            filtered.forEach((user, idx) => {
                const tr = document.createElement('tr');
                tr.setAttribute('data-id', user.id);

                // Formatear el Rol para mostrarlo elegante
                const rolFormatted = user.rol ? (user.rol.charAt(0).toUpperCase() + user.rol.slice(1)) : 'Docente';
                // Formatear la Plaza
                const plazaFormatted = user.plaza ? user.plaza.split(' ').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ') : 'Sin Plaza';
                // Formatear el Estado
                const estadoClass = (user.estado || 'activo') === 'activo' ? 'active' : 'inactive';
                const estadoFormatted = user.estado ? (user.estado.charAt(0).toUpperCase() + user.estado.slice(1)) : 'Activo';

                tr.innerHTML = `
                    <td class="td-id">${idx + 1}.</td>
                    <td class="td-nombre">${user.nombre}</td>
                    <td class="td-correo">${user.correo}</td>
                    <td class="td-numero">${user.numero || 'N/D'}</td>
                    <td class="td-rol">${rolFormatted}</td>
                    <td class="td-plaza">${plazaFormatted}</td>
                    <td>
                        <span class="status-badge clickable ${estadoClass}" onclick="confirmToggleEstado(${user.id})" title="Haga clic para cambiar estado">
                            ${estadoFormatted}
                        </span>
                    </td>
                    <td>
                        <button type="button" class="action-btn" onclick="openEditUserModal(${user.id})" title="Editar Usuario">
                            <!-- Icono de usuario + lápiz SVG combinado -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 22px; height: 22px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </button>
                    </td>
                `;
                tableBody.appendChild(tr);
            });
        }

        // --- FUNCIONALIDADES DE AGREGAR USUARIO ---
        function openAddUserModal() {
            document.getElementById('addUserModal').classList.add('active');
        }

        function closeAddUserModal() {
            document.getElementById('addUserModal').classList.remove('active');
            document.getElementById('addUserForm').reset();
        }

        function handleCreateUser(e) {
            e.preventDefault();

            const name = document.getElementById('newUserName').value.trim();
            const email = document.getElementById('newUserEmail').value.trim();
            const phone = document.getElementById('newUserPhone').value.trim();
            const role = document.getElementById('newUserRole').value;
            const plaza = document.getElementById('newUserPlaza').value;

            if (!name || !email || !phone || !role || !plaza) return;

            fetch("{{ route('admin.usuarios.crear') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    nombre: name,
                    correo: email,
                    numero: phone,
                    rol: role,
                    plaza: plaza
                })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        dbUsuarios.push(data.usuario);
                        closeAddUserModal();
                        renderTable();
                    } else {
                        alert(data.message || 'Error al crear el usuario');
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Ocurrió un error al intentar registrar el usuario.');
                });
        }

        // --- FUNCIONALIDADES DE EDITAR USUARIO ---
        function openEditUserModal(id) {
            const user = dbUsuarios.find(u => u.id === id);
            if (!user) return;

            document.getElementById('editUserId').value = user.id;
            document.getElementById('editUserName').value = user.nombre; // Disabled/Readonly
            document.getElementById('editUserEmail').value = user.correo; // Disabled/Readonly
            document.getElementById('editUserPhone').value = user.numero ?? '';
            document.getElementById('editUserRole').value = user.rol || 'docente';
            document.getElementById('editUserPlaza').value = user.plaza || 'titular';

            document.getElementById('editUserModal').classList.add('active');
        }

        function closeEditUserModal() {
            document.getElementById('editUserModal').classList.remove('active');
            document.getElementById('editUserForm').reset();
        }

        function handleUpdateUser(e) {
            e.preventDefault();

            const id = parseInt(document.getElementById('editUserId').value);
            const phone = document.getElementById('editUserPhone').value.trim();
            const role = document.getElementById('editUserRole').value;
            const plaza = document.getElementById('editUserPlaza').value;

            if (!phone || !role || !plaza) return;

            fetch(`/admin/usuarios/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    numero: phone,
                    rol: role,
                    plaza: plaza
                })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const idx = dbUsuarios.findIndex(u => u.id === id);
                        if (idx !== -1) {
                            dbUsuarios[idx] = data.usuario;
                        }
                        closeEditUserModal();
                        renderTable();
                    } else {
                        alert(data.message || 'Error al actualizar el usuario');
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Ocurrió un error al intentar actualizar el usuario.');
                });
        }

        // --- FUNCIONALIDADES DE CAMBIO DE ESTADO (CONFIRMACIÓN) ---
        function confirmToggleEstado(id) {
            const user = dbUsuarios.find(u => u.id === id);
            if (!user) return;

            statusTargetUserId = id;

            const alertTitle = document.getElementById('alertTitle');
            const alertDesc = document.getElementById('alertDesc');
            const confirmBtn = document.getElementById('confirmStatusBtn');
            const statusCard = document.getElementById('confirmStatusCard');
            const alertIcon = document.getElementById('alertIcon');

            if (user.estado === 'activo') {
                statusCard.classList.remove('success-style');
                alertIcon.textContent = '⚠';
                alertTitle.textContent = '¿ESTÁ SEGURO QUE QUIERE DESACTIVAR EL USUARIO?';
                alertDesc.textContent = 'El usuario perderá acceso al sistema hasta que vuelva a ser habilitado';
                confirmBtn.textContent = 'Desactivar';
            } else {
                statusCard.classList.add('success-style');
                alertIcon.textContent = '✔';
                alertTitle.textContent = '¿ESTÁ SEGURO QUE QUIERE ACTIVAR EL USUARIO?';
                alertDesc.textContent = 'El usuario volverá a tener acceso completo y regular al sistema.';
                confirmBtn.textContent = 'Activar';
            }
            confirmBtn.style.borderColor = '';
            confirmBtn.style.color = '';

            document.getElementById('confirmStatusModal').classList.add('active');
        }

        function closeConfirmStatusModal() {
            document.getElementById('confirmStatusModal').classList.remove('active');
            // Quitar estilos al cerrar para restablecer
            document.getElementById('confirmStatusCard').classList.remove('success-style');
            statusTargetUserId = null;
        }

        function executeToggleEstado() {
            if (statusTargetUserId === null) return;

            fetch(`/admin/usuarios/${statusTargetUserId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const idx = dbUsuarios.findIndex(u => u.id === statusTargetUserId);
                        if (idx !== -1) {
                            dbUsuarios[idx] = data.usuario;
                        }
                        closeConfirmStatusModal();
                        renderTable();
                    } else {
                        alert(data.message || 'Error al modificar el estado');
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Ocurrió un error al intentar cambiar el estado.');
                });
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

            // Cerrar sidebar al hacer clic en el contenido principal
            mainContent.addEventListener('click', () => {
                adminSidebar.classList.add('collapsed');
            });

            // Evitar que hacer clic dentro de la barra lateral propague y la cierre
            adminSidebar.addEventListener('click', (e) => {
                e.stopPropagation();
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

            // Cerrar modals al presionar Escape
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    closeAddUserModal();
                    closeEditUserModal();
                    closeConfirmStatusModal();
                }
            });
        });
    </script>

</body>

</html>