<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>FARUSAC -
        {{ isset($informe) ? 'Edición de Informe de Coordinación' : 'Creación de Informe de Coordinación' }}</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        /* --- VARIABLES DE COLOR Y ESTILOS GLOBALES --- */
        :root {
            --color-azul: #002D72;
            /* Pantone 288C */
            --color-oro: #AC8400;
            /* Pantone 118C */
            --color-terracota: #B94700;
            /* Pantone 1525C */
            --color-fondo: #f8fafc;
            --color-tarjeta: #ffffff;
            --color-texto-principal: #1a202c;
            --color-texto-secundario: #4a5568;
            --color-texto-claro: #718096;
            --color-borde: #cbd5e1;
            --border-radius-card: 20px;
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
            from {
                opacity: 0;
                transform: translateY(-5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
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
            text-decoration: none;
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

        /* --- TARJETA PRINCIPAL DEL FORMULARIO --- */
        .form-card-panel {
            background-color: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 20px;
            padding: 35px 40px;
            box-shadow: var(--shadow-premium);
            margin-bottom: 25px;
            position: relative;
        }

        .panel-header-title {
            font-family: 'Outfit', sans-serif;
            font-size: 18px;
            font-weight: 700;
            color: var(--color-azul);
            margin-bottom: 20px;
            padding-bottom: 14px;
            border-bottom: 1.5px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        .panel-title-text {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .badge-num {
            background: linear-gradient(135deg, var(--color-azul), var(--color-oro));
            color: #ffffff;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 800;
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

        .form-input,
        .form-select,
        .form-textarea {
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

        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            border-color: var(--color-azul);
            box-shadow: 0 0 0 3px rgba(0, 45, 114, 0.08);
        }

        .input-readonly {
            background-color: #f1f5f9;
            color: #475569;
            cursor: default;
        }

        .form-textarea {
            border-radius: 16px;
            resize: vertical;
            min-height: 85px;
            line-height: 1.5;
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
            color: var(--color-texto-claro);
            transition: transform 0.2s ease;
        }

        .form-hint-desc {
            font-size: 12.5px;
            color: var(--color-texto-secundario);
            margin-top: -10px;
            margin-bottom: 16px;
            line-height: 1.5;
        }

        /* TABLAS FORMULARIO */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            border-radius: 14px;
            border: 1.5px solid #e2e8f0;
            background-color: #ffffff;
            margin-top: 8px;
        }

        .custom-report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            text-align: left;
        }

        .custom-report-table thead th {
            background-color: #f8fafc;
            color: var(--color-azul);
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            padding: 12px 14px;
            border-bottom: 2px solid #cbd5e1;
            border-right: 1px solid #e2e8f0;
            white-space: nowrap;
            letter-spacing: 0.02em;
        }

        .custom-report-table thead th:last-child {
            border-right: none;
        }

        .custom-report-table tbody td {
            padding: 10px 12px;
            border-bottom: 1px solid #f1f5f9;
            border-right: 1px solid #f1f5f9;
            vertical-align: middle;
            color: var(--color-texto-principal);
        }

        .custom-report-table tbody td:last-child {
            border-right: none;
        }

        .custom-report-table tbody tr:hover {
            background-color: #f8fafc;
        }

        .table-input {
            width: 100%;
            padding: 7px 12px;
            border: 1.2px solid #cbd5e1;
            border-radius: 10px;
            font-size: 12.5px;
            outline: none;
            transition: var(--transition-smooth);
            background-color: #ffffff;
        }

        .table-input:focus {
            border-color: var(--color-azul);
            box-shadow: 0 0 0 2px rgba(0, 45, 114, 0.1);
        }

        .table-select {
            display: inline-flex;
            width: auto;
            min-width: 58px;
            padding: 5px 8px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            text-align: center;
            text-align-last: center;
            outline: none;
            cursor: pointer;
            transition: var(--transition-smooth);
            border: 1px solid transparent;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
            appearance: auto;
        }

        .table-select.val-si {
            color: #15803d;
            background-color: #dcfce7;
            border-color: #86efac;
        }

        .table-select.val-no {
            color: #b91c1c;
            background-color: #fee2e2;
            border-color: #fca5a5;
        }

        .status-indicator-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 48px;
            padding: 5px 10px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            user-select: none;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
            border: 1px solid transparent;
        }

        .status-indicator-badge.val-si {
            color: #15803d;
            background-color: #dcfce7;
            border-color: #86efac;
        }

        .status-indicator-badge.val-no {
            color: #b91c1c;
            background-color: #fee2e2;
            border-color: #fca5a5;
        }

        .btn-add-row {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 18px;
            background: linear-gradient(135deg, rgba(0, 45, 114, 0.08) 0%, rgba(172, 132, 0, 0.12) 50%, rgba(185, 71, 0, 0.08) 100%);
            color: var(--color-azul);
            border: 1.5px solid rgba(0, 45, 114, 0.2);
            border-radius: 20px;
            font-family: 'Outfit', sans-serif;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition-smooth);
        }

        .btn-add-row:hover {
            background-color: #eff6ff;
            border-color: var(--color-azul);
            transform: translateY(-1px);
        }

        .btn-delete-row {
            background: #fee2e2;
            color: #ef4444;
            border: 1px solid #fca5a5;
            border-radius: 8px;
            padding: 6px 10px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition-smooth);
        }

        .btn-delete-row:hover {
            background: #ef4444;
            color: #ffffff;
        }

        /* --- BOTONES INFERIORES DE ACCIÓN --- */
        .bottom-actions-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1.5px solid #e2e8f0;
            flex-wrap: wrap;
            gap: 15px;
        }

        .btn-back-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 24px;
            border: 1.5px solid #cbd5e1;
            border-radius: 25px;
            background-color: #ffffff;
            color: var(--color-texto-secundario);
            font-family: 'Outfit', sans-serif;
            font-size: 13.5px;
            font-weight: 700;
            text-decoration: none;
            transition: var(--transition-smooth);
        }

        .btn-back-action:hover {
            background-color: #f1f5f9;
            color: var(--color-azul);
            border-color: var(--color-azul);
        }

        .actions-right-group {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-draft-action {
            padding: 11px 24px;
            background-color: #ffffff;
            border: 1.5px solid #94a3b8;
            color: #475569;
            border-radius: 25px;
            font-family: 'Outfit', sans-serif;
            font-size: 13.5px;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition-smooth);
        }

        .btn-draft-action:hover {
            background-color: #f1f5f9;
            color: var(--color-texto-principal);
            border-color: #64748b;
        }

        .btn-submit-action {
            padding: 11px 32px;
            background-color: var(--color-azul);
            color: #ffffff;
            border: none;
            border-radius: 25px;
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(0, 45, 114, 0.25);
            transition: var(--transition-smooth);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-submit-action:hover {
            background-color: #002257;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(0, 45, 114, 0.35);
        }

        /* --- TOAST DE NOTIFICACIÓN --- */
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

        /* --- ESTILOS PARA VER INFORME DOCENTE Y OBSERVACIONES --- */
        .btn-ver-docente {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background: linear-gradient(135deg, rgba(0, 45, 114, 0.08), rgba(172, 132, 0, 0.12));
            color: var(--color-azul);
            border: 1px solid rgba(0, 45, 114, 0.25);
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 700;
            font-family: 'Outfit', sans-serif;
            cursor: pointer;
            transition: var(--transition-smooth);
            white-space: nowrap;
        }

        .btn-ver-docente:hover {
            background: linear-gradient(135deg, var(--color-azul), #0044ad);
            color: #ffffff;
            border-color: var(--color-azul);
            box-shadow: 0 4px 12px rgba(0, 45, 114, 0.25);
            transform: translateY(-1px);
        }

        .badge-sin-informe {
            display: inline-block;
            padding: 5px 10px;
            background-color: #f1f5f9;
            color: #94a3b8;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            white-space: nowrap;
            border: 1px solid #e2e8f0;
        }

        .btn-obs-trigger {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            width: 100%;
            min-width: 130px;
            max-width: 200px;
            padding: 6px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: var(--transition-smooth);
            text-align: left;
            border: 1.5px solid #cbd5e1;
            background-color: #ffffff;
        }

        .btn-obs-trigger.empty {
            color: #64748b;
            border-style: dashed;
            background-color: #f8fafc;
        }

        .btn-obs-trigger.empty:hover {
            border-color: var(--color-azul);
            color: var(--color-azul);
            background-color: #ffffff;
        }

        .btn-obs-trigger.filled {
            color: #065f46;
            background-color: #ecfdf5;
            border-color: #a7f3d0;
            font-weight: 500;
        }

        .btn-obs-trigger.filled:hover {
            border-color: #10b981;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.2);
        }

        .obs-btn-text {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            flex: 1;
        }

        /* --- MODALES FLOTANTES EN LA MISMA PESTAÑA --- */
        .custom-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.55);
            backdrop-filter: blur(5px);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            opacity: 0;
            pointer-events: none;
            transition: all 0.25s ease;
        }

        .custom-modal-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        .custom-modal-container {
            background-color: #ffffff;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            width: 100%;
            max-height: 88vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            border: 1.5px solid #e2e8f0;
            transform: scale(0.96);
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .custom-modal-overlay.active .custom-modal-container {
            transform: scale(1);
        }

        .modal-accent-bar {
            height: 4px;
            background: linear-gradient(90deg, var(--color-azul), var(--color-oro), var(--color-terracota));
            width: 100%;
        }

        .modal-header-box {
            padding: 18px 24px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #fafbfc;
        }

        .modal-title-main {
            font-family: 'Outfit', sans-serif;
            font-size: 17px;
            font-weight: 700;
            color: var(--color-azul);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-close-btn {
            background: none;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #64748b;
            font-size: 18px;
            transition: var(--transition-smooth);
        }

        .modal-close-btn:hover {
            background-color: #fee2e2;
            color: #ef4444;
        }

        .modal-body-scroll {
            padding: 24px;
            overflow-y: auto;
            flex: 1;
        }

        .modal-footer-box {
            padding: 14px 24px;
            border-top: 1px solid #e2e8f0;
            background-color: #fafbfc;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
        }

        /* Estilos del reporte institucional dentro del modal */
        .doc-preview-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 24px;
        }

        .doc-preview-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid var(--color-azul);
            padding-bottom: 14px;
            margin-bottom: 18px;
        }

        .doc-preview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
            background-color: #f8fafc;
            padding: 14px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            margin-bottom: 18px;
        }

        .doc-preview-field {
            font-size: 12px;
        }

        .doc-preview-field strong {
            color: #475569;
            display: block;
            margin-bottom: 2px;
            font-size: 11px;
            text-transform: uppercase;
        }

        .doc-link-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background-color: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            font-size: 12px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .doc-link-pill:hover {
            background-color: #dbeafe;
            color: #1e40af;
            transform: translateY(-1px);
        }

        .doc-table-mini {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            margin-top: 10px;
        }

        .doc-table-mini th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: 600;
            padding: 8px 10px;
            border: 1px solid #cbd5e1;
            text-align: left;
            font-size: 11px;
        }

        .doc-table-mini td {
            padding: 8px 10px;
            border: 1px solid #cbd5e1;
            vertical-align: top;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
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

    <div class="app-container">

        <!-- Menú Lateral (Sidebar) -->
        <aside class="admin-sidebar collapsed" id="coordinadorSidebar">
            <nav class="sidebar-nav">
                <!-- Enlace Inicio -->
                <a href="{{ route('coordinador.dashboard') }}" class="sidebar-link">
                    <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Inicio</span>
                </a>

                <!-- Enlace Perfil -->
                <a href="{{ route('coordinador.perfil') }}" class="sidebar-link">
                    <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Perfil</span>
                </a>

                <!-- Enlace Informes (Activo) -->
                <a href="{{ route('coordinador.informes') }}" class="sidebar-link sidebar-link-informes active">
                    <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Informes</span>
                </a>
            </nav>
        </aside>

        <!-- Cuerpo Principal -->
        <main class="main-content expanded" id="mainContent">

            <div class="wizard-container">
                <!-- Botón de Ayuda Top-Right -->
                <a href="{{ route('coordinador.dashboard') }}" class="help-btn" title="Volver al Panel">✕</a>

                <h2 class="wizard-title">
                    {{ isset($informe) ? 'Edición de Informe de Coordinación' : 'Creación de Informe de Coordinación' }}
                </h2>

                @if (isset($isLocked) && $isLocked)
                    <div
                        style="background-color: #fee2e2; border: 1.5px solid #ef4444; color: #991b1b; padding: 14px 20px; border-radius: 12px; margin-bottom: 24px; font-weight: 600; font-size: 13.5px; display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 20px;">🔒</span>
                        <span>Este informe se encuentra <b>bloqueado para edición</b> porque ha vencido el plazo máximo de 3
                            días desde su envío original.</span>
                    </div>
                @endif

                <form id="formInformeCoordinacion" onsubmit="event.preventDefault();">

                    <!-- ========================================== -->
                    <!-- DATOS GENERALES DEL INFORME -->
                    <!-- ========================================== -->
                    <div class="form-card-panel">
                        <div class="panel-header-title">
                            <div class="panel-title-text">
                                <span class="badge-num">📋</span>
                                <span>Datos Generales de la Coordinación</span>
                            </div>
                        </div>

                        <!-- Fila 1: Área, Coordinador, Periodo Semestral -->
                        <div class="form-row">
                            <div class="form-col">
                                <label class="form-label">*Área a Cargo:</label>
                                <input type="text" class="form-input input-readonly" value="{{ $area }}" readonly>
                            </div>

                            <div class="form-col">
                                <label class="form-label">*Coordinador(a):</label>
                                <input type="text" class="form-input input-readonly" value="{{ $user->nombre }}"
                                    readonly>
                            </div>

                            <div class="form-col">
                                <label class="form-label" for="selectPeriodo">*Periodo Semestral:</label>
                                <div class="select-container">
                                    @php
                                        $currentPeriodo = isset($informe) ? $informe->periodo : ($periodoDefecto ?? ('Segundo Semestre ' . date('Y')));
                                    @endphp
                                    <select id="selectPeriodo" class="form-select" required>
                                        <option value="Primer Semestre {{ date('Y') }}" {{ str_contains($currentPeriodo, 'Primer Semestre') ? 'selected' : '' }}>Primer Semestre {{ date('Y') }}
                                        </option>
                                        <option value="Segundo Semestre {{ date('Y') }}" {{ str_contains($currentPeriodo, 'Segundo Semestre') ? 'selected' : '' }}>
                                            Segundo Semestre {{ date('Y') }}</option>
                                        <option value="Vacaciones Junio {{ date('Y') }}" {{ str_contains($currentPeriodo, 'Vacaciones Junio') ? 'selected' : '' }}>
                                            Vacaciones Junio {{ date('Y') }}</option>
                                        <option value="Vacaciones Diciembre {{ date('Y') }}" {{ str_contains($currentPeriodo, 'Vacaciones Diciembre') ? 'selected' : '' }}>
                                            Vacaciones Diciembre {{ date('Y') }}</option>
                                    </select>
                                    <svg class="select-custom-arrow" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Fila 2: Mes, Año -->
                        <div class="form-row">
                            <div class="form-col">
                                <label class="form-label" for="selectMes">*Mes del Informe:</label>
                                <div class="select-container">
                                    @php
                                        $currentMes = isset($informe) ? $informe->mes : ($mesDefecto ?? \Carbon\Carbon::now()->locale('es')->translatedFormat('F'));
                                        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                                    @endphp
                                    <select id="selectMes" class="form-select" required>
                                        @foreach ($meses as $m)
                                            <option value="{{ $m }}" {{ strcasecmp($currentMes, $m) == 0 ? 'selected' : '' }}>
                                                {{ $m }}</option>
                                        @endforeach
                                    </select>
                                    <svg class="select-custom-arrow" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>

                            <div class="form-col form-col-small">
                                <label class="form-label" for="inputAnio">*Año:</label>
                                <input type="number" id="inputAnio" class="form-input"
                                    value="{{ isset($informe) ? $informe->anio : date('Y') }}" required>
                            </div>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- 1. COMPETENCIA DEL ÁREA -->
                    <!-- ========================================== -->
                    <div class="form-card-panel">
                        <div class="panel-header-title">
                            <div class="panel-title-text">
                                <span class="badge-num">1</span>
                                <span>Competencia del Área</span>
                            </div>
                        </div>

                        <p class="form-hint-desc" style="margin-top: 4px;"><strong>Programas de las
                                asignaturas:</strong> (Solo en el primer mes del semestre. Se cargan automáticamente las
                            asignaturas registradas del área)</p>

                        <div class="table-responsive">
                            <table class="custom-report-table" id="tablaProgramas">
                                <thead>
                                    <tr>
                                        <th style="width: 40%;">Asignatura</th>
                                        <th style="width: 60%;">Enlace a programa (URL Drive / Cloud)</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyProgramas">
                                    @php
                                        $listaProgramas = [];
                                        if (isset($programasCompletos) && count($programasCompletos) > 0) {
                                            $listaProgramas = $programasCompletos;
                                        } elseif (isset($programasIniciales) && count($programasIniciales) > 0) {
                                            $listaProgramas = $programasIniciales;
                                        } elseif (isset($informe) && $informe->programas && $informe->programas->count() > 0) {
                                            $listaProgramas = $informe->programas->map(function ($p) {
                                                return [
                                                    'asignatura' => $p->asignatura,
                                                    'enlace_programa' => $p->enlace_programa
                                                ];
                                            });
                                        } elseif (isset($asignaturasUnicas) && count($asignaturasUnicas) > 0) {
                                            $listaProgramas = $asignaturasUnicas->map(function ($asig) {
                                                return [
                                                    'asignatura' => $asig,
                                                    'enlace_programa' => ''
                                                ];
                                            });
                                        }
                                    @endphp

                                    @forelse ($listaProgramas as $p)
                                        @php
                                            $asigName = is_array($p) ? ($p['asignatura'] ?? '') : (is_object($p) ? ($p->asignatura ?? '') : $p);
                                            $asigLink = is_array($p) ? ($p['enlace_programa'] ?? '') : (is_object($p) ? ($p->enlace_programa ?? '') : '');
                                        @endphp
                                        <tr class="fila-programa" data-asig-name="{{ $asigName }}">
                                            <td>
                                                <input type="text" class="table-input prog-asignatura"
                                                    value="{{ $asigName }}" readonly
                                                    style="background-color: #f8fafc; font-weight: 600; color: #1e293b; cursor: default;">
                                            </td>
                                            <td>
                                                <input type="url" class="table-input prog-enlace" value="{{ $asigLink }}"
                                                    placeholder="https://drive.google.com/...">
                                            </td>
                                        </tr>
                                    @empty
                                        <tr id="rowEmptyProgramas">
                                            <td colspan="2" style="text-align: center; color: #94a3b8; padding: 18px;">
                                                No se encontraron asignaturas registradas para esta área en el sistema.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- 2. INFORMACIÓN GENERAL DE ASIGNATURAS -->
                    <!-- ========================================== -->
                    <div class="form-card-panel">
                        <div class="panel-header-title">
                            <div class="panel-title-text">
                                <span class="badge-num">2</span>
                                <span>Información General de Asignaturas Impartidas en el Área</span>
                            </div>
                        </div>

                        <p class="form-hint-desc">Se coloca una fila por cada sección de asignatura impartida en el
                            área.</p>

                        <div class="table-responsive">
                            <table class="custom-report-table" id="tablaAsignaturas">
                                <thead>
                                    <tr>
                                        <th style="width: 125px; text-align: center;">Informe Docente</th>
                                        <th>Docente a cargo</th>
                                        <th>Asignatura</th>
                                        <th style="width: 65px; text-align: center;">Sección</th>
                                        <th style="width: 95px; text-align: center;">Presentó informe</th>
                                        <th style="width: 95px; text-align: center;">Sala reuniones</th>
                                        <th style="width: 95px; text-align: center;">Enlace Classroom</th>
                                        <th style="width: 95px; text-align: center;">Enlaces evaluación</th>
                                        <th style="width: 95px; text-align: center;">Evidencias grales</th>
                                        <th style="min-width: 170px;">Observaciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyAsignaturas">
                                    @php
                                        $itemsAsignaturas = isset($informe) ? $informe->asignaturas : $filasAsignaturas;
                                    @endphp
                                    @foreach ($itemsAsignaturas as $index => $asig)
                                        @php
                                            $cursoId = is_array($asig) ? ($asig['curso_id'] ?? null) : $asig->curso_id;
                                            $docenteNombre = is_array($asig) ? ($asig['docente_nombre'] ?? '') : $asig->docente_nombre;
                                            $nombreAsignatura = is_array($asig) ? ($asig['asignatura'] ?? '') : $asig->asignatura;
                                            $seccion = is_array($asig) ? ($asig['seccion'] ?? '') : $asig->seccion;
                                            $presento = is_array($asig) ? ($asig['presento_informe'] ?? false) : $asig->presento_informe;
                                            $sala = is_array($asig) ? ($asig['tiene_sala_reuniones'] ?? false) : $asig->tiene_sala_reuniones;
                                            $virtual = is_array($asig) ? ($asig['funciona_enlace_virtual'] ?? false) : $asig->funciona_enlace_virtual;
                                            $eval = is_array($asig) ? ($asig['funciona_enlace_evaluacion'] ?? false) : $asig->funciona_enlace_evaluacion;
                                            $evid = is_array($asig) ? ($asig['evidencias_generales'] ?? false) : $asig->evidencias_generales;
                                            $obs = is_array($asig) ? ($asig['observaciones'] ?? '') : $asig->observaciones;

                                            // Buscar informe docente correspondiente
                                            $docenteInf = isset($docenteInformes) ? $docenteInformes->get($cursoId) : null;
                                            $docenteInformeId = is_array($asig) ? ($asig['docente_informe_id'] ?? ($docenteInf ? $docenteInf->id : null)) : ($docenteInf ? $docenteInf->id : null);
                                        @endphp
                                        <tr class="fila-asig-general" data-curso-id="{{ $cursoId }}"
                                            data-row-index="{{ $index }}" data-asig-nombre="{{ $nombreAsignatura }}"
                                            data-asig-seccion="{{ $seccion }}" data-asig-docente="{{ $docenteNombre }}">
                                            <td style="text-align: center;">
                                                <div class="col-docente-report-action" data-curso-id="{{ $cursoId }}">
                                                    @if ($docenteInformeId)
                                                        <button type="button" class="btn-ver-docente"
                                                            onclick="abrirModalInformeDocente({{ $docenteInformeId }})"
                                                            title="Visualizar informe entregado por el docente">
                                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2.2">
                                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                                <circle cx="12" cy="12" r="3"></circle>
                                                            </svg>
                                                            <span>Ver Informe</span>
                                                        </button>
                                                    @else
                                                        <span class="badge-sin-informe">Sin informe</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" class="table-input asig-docente"
                                                    value="{{ $docenteNombre }}" readonly
                                                    style="background-color: #f8fafc; font-weight: 600; color: #1e293b; cursor: default;">
                                            </td>
                                            <td>
                                                <input type="text" class="table-input asig-nombre"
                                                    value="{{ $nombreAsignatura }}" readonly
                                                    style="background-color: #f8fafc; font-weight: 600; color: #1e293b; cursor: default;">
                                            </td>
                                            <td style="text-align: center;">
                                                <input type="text" class="table-input asig-seccion" value="{{ $seccion }}"
                                                    readonly
                                                    style="text-align: center; width: 55px; background-color: #f8fafc; font-weight: 600; color: #1e293b; cursor: default;">
                                            </td>
                                            <td style="text-align: center;">
                                                <select
                                                    class="table-select asig-presento {{ $presento ? 'val-si' : 'val-no' }}"
                                                    onchange="actualizarColorSelect(this)">
                                                    <option value="1" {{ $presento ? 'selected' : '' }}>Sí</option>
                                                    <option value="0" {{ !$presento ? 'selected' : '' }}>No</option>
                                                </select>
                                            </td>
                                            <td style="text-align: center;">
                                                <select class="table-select asig-sala {{ $sala ? 'val-si' : 'val-no' }}"
                                                    onchange="actualizarColorSelect(this)">
                                                    <option value="1" {{ $sala ? 'selected' : '' }}>Sí</option>
                                                    <option value="0" {{ !$sala ? 'selected' : '' }}>No</option>
                                                </select>
                                            </td>
                                            <td style="text-align: center;">
                                                <select
                                                    class="table-select asig-virtual {{ $virtual ? 'val-si' : 'val-no' }}"
                                                    onchange="actualizarColorSelect(this)">
                                                    <option value="1" {{ $virtual ? 'selected' : '' }}>Sí</option>
                                                    <option value="0" {{ !$virtual ? 'selected' : '' }}>No</option>
                                                </select>
                                            </td>
                                            <td style="text-align: center;">
                                                <select class="table-select asig-eval {{ $eval ? 'val-si' : 'val-no' }}"
                                                    onchange="actualizarColorSelect(this)">
                                                    <option value="1" {{ $eval ? 'selected' : '' }}>Sí</option>
                                                    <option value="0" {{ !$eval ? 'selected' : '' }}>No</option>
                                                </select>
                                            </td>
                                            <td style="text-align: center;">
                                                <select class="table-select asig-evid {{ $evid ? 'val-si' : 'val-no' }}"
                                                    onchange="actualizarColorSelect(this)">
                                                    <option value="1" {{ $evid ? 'selected' : '' }}>Sí</option>
                                                    <option value="0" {{ !$evid ? 'selected' : '' }}>No</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="hidden" class="asig-obs" value="{{ $obs }}">
                                                <button type="button"
                                                    class="btn-obs-trigger {{ !empty($obs) ? 'filled' : 'empty' }}"
                                                    onclick="abrirModalObservacion(this, 'asig')">
                                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2.2">
                                                        <path
                                                            d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7">
                                                        </path>
                                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z">
                                                        </path>
                                                    </svg>
                                                    <span
                                                        class="obs-btn-text">{{ !empty($obs) ? Str::limit($obs, 18) : '+ Observación' }}</span>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Herramientas virtuales -->
                        <div class="form-row" style="margin-top: 22px; margin-bottom: 0;">
                            <div class="form-col form-col-full">
                                <label class="form-label" for="inputHerramientasVirtuales">Herramientas virtuales
                                    utilizadas para la docencia en línea:</label>
                                <textarea id="inputHerramientasVirtuales" class="form-textarea"
                                    placeholder="Ej. Google Classroom, Google Meet, Zoom, Campus Virtual FARUSAC, etc.">{{ isset($informe) ? $informe->herramientas_virtuales : '' }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- 3. SÍNTESIS DE LOS INFORMES PRESENTADOS -->
                    <!-- ========================================== -->
                    <div class="form-card-panel">
                        <div class="panel-header-title">
                            <div class="panel-title-text">
                                <span class="badge-num">3</span>
                                <span>Síntesis de los Informes Presentados por los Docentes</span>
                            </div>
                        </div>

                        <!-- 3.1 Avance del curso -->
                        <div style="margin-bottom: 30px;">
                            <h3
                                style="font-family: 'Outfit', sans-serif; font-size: 15px; font-weight: 700; color: var(--color-azul); margin-bottom: 6px;">
                                3.1. Avance del curso con relación a la programación mensual
                            </h3>
                            <p class="form-hint-desc">Se coloca una fila por cada sección de asignatura. Si existe
                                alguna complicación en cumplir el porcentaje de avance al impartir los contenidos
                                programados para el mes, indicar la razón en la columna de “Observaciones”.</p>

                            <div class="table-responsive">
                                <table class="custom-report-table" id="tablaAvances">
                                    <thead>
                                        <tr>
                                            <th>Docente a cargo</th>
                                            <th>Asignatura</th>
                                            <th style="width: 65px; text-align: center;">Sección</th>
                                            <th style="width: 110px; text-align: center;">% de Avance</th>
                                            <th style="min-width: 170px;">Observaciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyAvances">
                                        @php
                                            $itemsAvances = isset($informe) ? $informe->avances : $filasAsignaturas;
                                        @endphp
                                        @foreach ($itemsAvances as $av)
                                            @php
                                                $cursoId = is_array($av) ? ($av['curso_id'] ?? null) : $av->curso_id;
                                                $docenteNombre = is_array($av) ? ($av['docente_nombre'] ?? '') : $av->docente_nombre;
                                                $nombreAsignatura = is_array($av) ? ($av['asignatura'] ?? '') : $av->asignatura;
                                                $seccion = is_array($av) ? ($av['seccion'] ?? '') : $av->seccion;
                                                $porcentaje = is_array($av) ? ($av['porcentaje_avance'] ?? 100) : $av->porcentaje_avance;
                                                $obs = is_array($av) ? ($av['observaciones'] ?? '') : $av->observaciones;
                                            @endphp
                                            <tr class="fila-avance" data-curso-id="{{ $cursoId }}"
                                                data-asig-nombre="{{ $nombreAsignatura }}"
                                                data-asig-seccion="{{ $seccion }}" data-asig-docente="{{ $docenteNombre }}">
                                                <td>
                                                    <input type="text" class="table-input avance-docente"
                                                        value="{{ $docenteNombre }}" readonly
                                                        style="background-color: #f8fafc; font-weight: 600; color: #1e293b; cursor: default;">
                                                </td>
                                                <td>
                                                    <input type="text" class="table-input avance-asignatura"
                                                        value="{{ $nombreAsignatura }}" readonly
                                                        style="background-color: #f8fafc; font-weight: 600; color: #1e293b; cursor: default;">
                                                </td>
                                                <td style="text-align: center;">
                                                    <input type="text" class="table-input avance-seccion"
                                                        value="{{ $seccion }}" readonly
                                                        style="text-align: center; width: 55px; background-color: #f8fafc; font-weight: 600; color: #1e293b; cursor: default;">
                                                </td>
                                                <td style="text-align: center;">
                                                    <div style="display: inline-flex; align-items: center; gap: 4px;">
                                                        <input type="number" min="0" max="100"
                                                            class="table-input avance-porcentaje" value="{{ $porcentaje }}"
                                                            style="text-align: center; width: 70px;">
                                                        <span
                                                            style="font-weight: 700; color: var(--color-texto-secundario);">%</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="hidden" class="avance-obs" value="{{ $obs }}">
                                                    <button type="button"
                                                        class="btn-obs-trigger {{ !empty($obs) ? 'filled' : 'empty' }}"
                                                        onclick="abrirModalObservacion(this, 'avance')">
                                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2.2">
                                                            <path
                                                                d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7">
                                                            </path>
                                                            <path
                                                                d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z">
                                                            </path>
                                                        </svg>
                                                        <span
                                                            class="obs-btn-text">{{ !empty($obs) ? Str::limit($obs, 18) : '+ Observación' }}</span>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- 3.2 Reporte de estudiantes con problemas -->
                        <div>
                            <div
                                style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; flex-wrap: wrap; gap: 10px;">
                                <h3
                                    style="font-family: 'Outfit', sans-serif; font-size: 15px; font-weight: 700; color: var(--color-azul);">
                                    3.2. Reporte de estudiantes con problemas para recibir el curso en línea
                                </h3>
                                <button type="button" class="btn-add-row" onclick="addEstudianteRow()">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2.5">
                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                    <span>Agregar Fila de Estudiantes</span>
                                </button>
                            </div>
                            <p class="form-hint-desc">Reporte de alumnos con inconvenientes técnicos, de conectividad o
                                académicos reportados por los docentes.</p>

                            <div class="table-responsive">
                                <table class="custom-report-table" id="tablaEstudiantes">
                                    <thead>
                                        <tr>
                                            <th style="width: 30%;">Asignatura</th>
                                            <th style="width: 12%; text-align: center;">Sección</th>
                                            <th style="width: 18%; text-align: center;">Cantidad con inconvenientes</th>
                                            <th style="width: 35%;">Carné de los estudiantes</th>
                                            <th style="width: 5%; text-align: center;">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyEstudiantes">
                                        @if (isset($informe) && $informe->estudiantes->count() > 0)
                                            @foreach ($informe->estudiantes as $est)
                                                <tr>
                                                    <td>
                                                        <input type="text" class="table-input est-asignatura"
                                                            value="{{ $est->asignatura }}"
                                                            placeholder="Nombre de la Asignatura">
                                                    </td>
                                                    <td style="text-align: center;">
                                                        <input type="text" class="table-input est-seccion"
                                                            value="{{ $est->seccion }}" style="text-align: center;"
                                                            placeholder="Sección">
                                                    </td>
                                                    <td style="text-align: center;">
                                                        <input type="number" min="0" class="table-input est-cantidad"
                                                            value="{{ $est->cantidad_estudiantes }}" style="text-align: center;"
                                                            placeholder="0">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="table-input est-carne"
                                                            value="{{ $est->carne_estudiantes }}"
                                                            placeholder="Ej. 202100123, 202204567">
                                                    </td>
                                                    <td style="text-align: center;">
                                                        <button type="button" class="btn-delete-row"
                                                            onclick="deleteTableRow(this)" title="Eliminar fila">✕</button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td>
                                                    <input type="text" class="table-input est-asignatura"
                                                        placeholder="Nombre de la Asignatura">
                                                </td>
                                                <td style="text-align: center;">
                                                    <input type="text" class="table-input est-seccion"
                                                        style="text-align: center;" placeholder="Sección">
                                                </td>
                                                <td style="text-align: center;">
                                                    <input type="number" min="0" class="table-input est-cantidad" value="0"
                                                        style="text-align: center;" placeholder="0">
                                                </td>
                                                <td>
                                                    <input type="text" class="table-input est-carne"
                                                        placeholder="Ej. 202100123, 202204567">
                                                </td>
                                                <td style="text-align: center;">
                                                    <button type="button" class="btn-delete-row"
                                                        onclick="deleteTableRow(this)" title="Eliminar fila">✕</button>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- 4. INFORME DE ACTIVIDADES DE COORDINACIÓN DE ÁREA Y OBSERVACIONES GENERALES -->
                    <!-- ========================================== -->
                    <div class="form-card-panel">
                        <div class="panel-header-title">
                            <div class="panel-title-text">
                                <span class="badge-num">4</span>
                                <span>Informe de Actividades de Coordinación de Área y Observaciones Generales</span>
                            </div>
                        </div>
                        <p class="form-hint-desc">Adjunte el enlace al documento o carpeta en la nube (Google Drive, OneDrive, etc.) con el informe de actividades de coordinación y observaciones generales.</p>

                        <div class="form-row" style="margin-top: 14px; margin-bottom: 0;">
                            <div class="form-col form-col-full">
                                <label class="form-label" for="inputEnlaceActividadesCoordinacion">Enlace de actividades de coordinación y observaciones (Drive / Nube):</label>
                                <input type="url" id="inputEnlaceActividadesCoordinacion" class="form-input" placeholder="https://drive.google.com/..." value="{{ isset($informe) ? $informe->enlace_actividades_coordinacion : '' }}">
                            </div>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- 5. INFORME DE AUXILIARES (DE HABERLOS) -->
                    <!-- ========================================== -->
                    <div class="form-card-panel">
                        <div class="panel-header-title">
                            <div class="panel-title-text">
                                <span class="badge-num">5</span>
                                <span>Informe de Auxiliares (De haberlos)</span>
                            </div>
                        </div>
                        <p class="form-hint-desc">Adjunte el enlace al informe mensual o carpeta con las constancias de los auxiliares de cátedra asignados al área (de haberlos).</p>

                        <div class="form-row" style="margin-top: 14px; margin-bottom: 0;">
                            <div class="form-col form-col-full">
                                <label class="form-label" for="inputEnlaceInformeAuxiliares">Enlace de informe de auxiliares (Drive / Nube):</label>
                                <input type="url" id="inputEnlaceInformeAuxiliares" class="form-input" placeholder="https://drive.google.com/..." value="{{ isset($informe) ? $informe->enlace_informe_auxiliares : '' }}">
                            </div>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- 6. INFORME DE DOCENTES CON PERMISO LABORAL, SUSPENSIÓN Y/O INASISTENCIA -->
                    <!-- ========================================== -->
                    <div class="form-card-panel">
                        <div class="panel-header-title">
                            <div class="panel-title-text">
                                <span class="badge-num">6</span>
                                <span>Informe de Docentes con Permiso Laboral, Suspensión y/o Inasistencia</span>
                            </div>
                        </div>
                        <p class="form-hint-desc">Adjunte el enlace al documento o carpeta con las justificaciones, permisos laborales, suspensiones del IGSS o reportes de inasistencia de los docentes del área.</p>

                        <div class="form-row" style="margin-top: 14px; margin-bottom: 0;">
                            <div class="form-col form-col-full">
                                <label class="form-label" for="inputEnlaceDocentesPermisos">Enlace de docentes con permiso, suspensión o inasistencia (Drive / Nube):</label>
                                <input type="url" id="inputEnlaceDocentesPermisos" class="form-input" placeholder="https://drive.google.com/..." value="{{ isset($informe) ? $informe->enlace_docentes_permisos : '' }}">
                            </div>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- BARRA DE ACCIONES INFERIOR -->
                    <!-- ========================================== -->
                    <div class="bottom-actions-container">
                        <a href="{{ route('coordinador.dashboard') }}" class="btn-back-action">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5">
                                <line x1="19" y1="12" x2="5" y2="12"></line>
                                <polyline points="12 19 5 12 12 5"></polyline>
                            </svg>
                            <span>Volver al Panel</span>
                        </a>

                        <div class="actions-right-group">
                            <button type="button" class="btn-draft-action"
                                onclick="submitInformeCoordinacion('borrador')">
                                Guardar Borrador
                            </button>
                            <button type="button" class="btn-submit-action"
                                onclick="submitInformeCoordinacion('enviado')">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.5">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                <span>{{ isset($informe) ? 'Actualizar y Enviar' : 'Enviar Informe Oficial' }}</span>
                            </button>
                        </div>
                    </div>

                </form>
            </div>

        </main>
    </div>

    <!-- Toast Notification -->
    <div id="toastNotification" class="toast-notification"></div>

    <!-- ======================================================== -->
    <!-- MODAL 1: VISUALIZACIÓN DE INFORME ENTREGADO POR EL DOCENTE (FORMATO PDF OFICIAL) -->
    <!-- ======================================================== -->
    <div class="custom-modal-overlay" id="modalVerInformeDocente"
        onclick="cerrarModalFuera(event, 'modalVerInformeDocente')">
        <div class="custom-modal-container"
            style="max-width: 980px; width: 95vw; height: 90vh; display: flex; flex-direction: column;">
            <div class="modal-accent-bar"></div>
            <div class="modal-header-box" style="padding: 14px 24px; flex-shrink: 0;">
                <div>
                    <div class="modal-title-main">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                        <span>Informe Mensual del Docente (Formato Institucional Oficial)</span>
                    </div>
                    <div style="font-size: 12px; color: #64748b; margin-top: 2px;" id="modalDocenteSubtitulo">
                        Visualización en formato de documento oficial FARUSAC
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <button type="button" class="btn-submit-action" onclick="imprimirIframeDocente()"
                        style="padding: 7px 16px; font-size: 12px; display: inline-flex; align-items: center; gap: 6px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <polyline points="6 9 6 2 18 2 18 9"></polyline>
                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                            <rect x="6" y="14" width="12" height="8"></rect>
                        </svg>
                        <span>Imprimir / PDF</span>
                    </button>
                    <button type="button" class="modal-close-btn" onclick="cerrarModal('modalVerInformeDocente')"
                        title="Cerrar (Esc)">✕</button>
                </div>
            </div>

            <div style="flex: 1; position: relative; overflow: hidden; background: #f8fafc; padding: 0;">
                <div id="modalDocenteLoader"
                    style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: #ffffff; display: flex; flex-direction: column; align-items: center; justify-content: center; z-index: 10;">
                    <div class="spinner-border"
                        style="display: inline-block; width: 32px; height: 32px; border: 3px solid #cbd5e1; border-top-color: var(--color-azul); border-radius: 50%; animation: spin 0.8s linear infinite;">
                    </div>
                    <p style="margin-top: 12px; font-weight: 600; color: #475569; font-size: 13.5px;">Cargando documento
                        oficial del docente...</p>
                </div>
                <iframe id="iframeDocenteInforme" src="about:blank"
                    style="width: 100%; height: 100%; border: none; display: block;"
                    onload="const l = document.getElementById('modalDocenteLoader'); if(l) l.style.display='none';"></iframe>
            </div>

            <div class="modal-footer-box" style="padding: 12px 24px; flex-shrink: 0;">
                <button type="button" class="btn-draft-action" onclick="cerrarModal('modalVerInformeDocente')">Cerrar
                    Visualización</button>
            </div>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL 2: VENTANA EMERGENTE DE OBSERVACIONES -->
    <!-- ======================================================== -->
    <div class="custom-modal-overlay" id="modalObservaciones" onclick="cerrarModalFuera(event, 'modalObservaciones')">
        <div class="custom-modal-container" style="max-width: 580px;">
            <div class="modal-accent-bar"></div>
            <div class="modal-header-box">
                <div>
                    <div class="modal-title-main">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                        <span>Observaciones de la Asignatura</span>
                    </div>
                    <div style="font-size: 12px; color: #64748b; margin-top: 2px;" id="modalObsInfoCurso">
                        Curso — Sección — Docente
                    </div>
                </div>
                <button type="button" class="modal-close-btn" onclick="cerrarModal('modalObservaciones')"
                    title="Cerrar">✕</button>
            </div>

            <div class="modal-body-scroll" style="padding: 20px 24px;">
                <label for="modalObsTextarea"
                    style="font-size: 12.5px; font-weight: 700; color: #334155; display: block; margin-bottom: 8px;">
                    Detalle de Observaciones para el Informe:
                </label>
                <textarea id="modalObsTextarea" class="form-textarea"
                    style="min-height: 170px; font-size: 13.5px; line-height: 1.6; border-radius: 12px;"
                    placeholder="Ingrese aquí las observaciones detalladas sobre la entrega del informe, salas virtuales, avances, o situaciones particulares de esta asignatura..."></textarea>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 8px;">
                    <span style="font-size: 11.5px; color: #94a3b8;" id="modalObsCharCount">0 caracteres</span>
                    <button type="button"
                        style="background: none; border: none; font-size: 11.5px; color: #dc2626; cursor: pointer;"
                        onclick="document.getElementById('modalObsTextarea').value=''; actualizarContadorObs();">Limpiar
                        texto</button>
                </div>
            </div>

            <div class="modal-footer-box">
                <button type="button" class="btn-draft-action"
                    onclick="cerrarModal('modalObservaciones')">Cancelar</button>
                <button type="button" class="btn-submit-action" onclick="guardarObservacionModal()"
                    style="padding: 9px 22px;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <span>Guardar Observación</span>
                </button>
            </div>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const informeId = "{{ isset($informe) ? $informe->id : '' }}";

        // Variables de control de observaciones
        let currentObsTriggerBtn = null;
        let currentObsHiddenInput = null;

        // Toggle Sidebar
        const hamburgerBtn = document.getElementById('hamburgerBtn');
        const sidebar = document.getElementById('coordinadorSidebar');
        const mainContent = document.getElementById('mainContent');

        if (hamburgerBtn && sidebar && mainContent) {
            hamburgerBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
            });

            mainContent.addEventListener('click', () => {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('expanded');
            });

            sidebar.addEventListener('click', (e) => {
                e.stopPropagation();
            });
        }

        // Toggle Profile Dropdown
        const profileToggle = document.getElementById('profileToggle');
        const profileDropdown = document.getElementById('profileDropdown');
        if (profileToggle && profileDropdown) {
            profileToggle.addEventListener('click', (e) => {
                e.stopPropagation();
                profileDropdown.classList.toggle('active');
            });
            document.addEventListener('click', () => {
                profileDropdown.classList.remove('active');
            });
        }

        // Estilos interactivos para selects Sí/No
        function toggleSelectStyle(selectEl) {
            if (selectEl.value === '1') {
                selectEl.classList.add('val-si');
                selectEl.classList.remove('val-no');
            } else {
                selectEl.classList.add('val-no');
                selectEl.classList.remove('val-si');
            }
        }

        // Eliminar fila de tabla dinámica
        function deleteTableRow(btn) {
            const tr = btn.closest('tr');
            if (tr) tr.remove();
        }



        // Agregar fila a Estudiantes con problemas (Inciso 3.2)
        function addEstudianteRow() {
            const tbody = document.getElementById('tbodyEstudiantes');
            const tr = document.createElement('tr');
            tr.innerHTML = `
            <td>
                <input type="text" class="table-input est-asignatura" placeholder="Nombre de la Asignatura">
            </td>
            <td style="text-align: center;">
                <input type="text" class="table-input est-seccion" style="text-align: center;" placeholder="Sección">
            </td>
            <td style="text-align: center;">
                <input type="number" min="0" class="table-input est-cantidad" value="0" style="text-align: center;" placeholder="0">
            </td>
            <td>
                <input type="text" class="table-input est-carne" placeholder="Ej. 202100123, 202204567">
            </td>
            <td style="text-align: center;">
                <button type="button" class="btn-delete-row" onclick="deleteTableRow(this)" title="Eliminar fila">✕</button>
            </td>
        `;
            tbody.appendChild(tr);
        }

        // Modal Control Helpers
        function abrirModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        }

        function cerrarModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            }
        }

        function cerrarModalFuera(event, modalId) {
            if (event.target && event.target.id === modalId) {
                cerrarModal(modalId);
            }
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                cerrarModal('modalVerInformeDocente');
                cerrarModal('modalObservaciones');
            }
        });

        // Abrir Modal de Observaciones
        function abrirModalObservacion(btn, tipo) {
            currentObsTriggerBtn = btn;
            const tr = btn.closest('tr');
            if (!tr) return;

            const cursoNombre = tr.getAttribute('data-asig-nombre') || tr.querySelector('.asig-nombre, .avance-asignatura')?.value || 'Asignatura';
            const seccion = tr.getAttribute('data-asig-seccion') || tr.querySelector('.asig-seccion, .avance-seccion')?.value || '—';
            const docente = tr.getAttribute('data-asig-docente') || tr.querySelector('.asig-docente, .avance-docente')?.value || '—';

            currentObsHiddenInput = tr.querySelector(tipo === 'asig' ? '.asig-obs' : '.avance-obs');
            const currentVal = currentObsHiddenInput ? currentObsHiddenInput.value : '';

            document.getElementById('modalObsInfoCurso').innerHTML = `<strong>${cursoNombre}</strong> (Sección <b>${seccion}</b>) &bull; Docente: <b>${docente}</b>`;
            const textarea = document.getElementById('modalObsTextarea');
            textarea.value = currentVal;
            actualizarContadorObs();

            abrirModal('modalObservaciones');
            setTimeout(() => textarea.focus(), 150);
        }

        // Actualizar contador de caracteres
        function actualizarContadorObs() {
            const textarea = document.getElementById('modalObsTextarea');
            const count = textarea ? textarea.value.length : 0;
            const el = document.getElementById('modalObsCharCount');
            if (el) el.textContent = `${count} caracteres`;
        }
        document.getElementById('modalObsTextarea')?.addEventListener('input', actualizarContadorObs);

        // Guardar Observación desde el Modal
        function guardarObservacionModal() {
            const textarea = document.getElementById('modalObsTextarea');
            const val = textarea.value.trim();

            if (currentObsHiddenInput) {
                currentObsHiddenInput.value = val;
            }

            if (currentObsTriggerBtn) {
                const btnText = currentObsTriggerBtn.querySelector('.obs-btn-text');
                if (val) {
                    currentObsTriggerBtn.classList.remove('empty');
                    currentObsTriggerBtn.classList.add('filled');
                    if (btnText) btnText.textContent = val.length > 20 ? val.substring(0, 18) + '...' : val;
                } else {
                    currentObsTriggerBtn.classList.remove('filled');
                    currentObsTriggerBtn.classList.add('empty');
                    if (btnText) btnText.textContent = '+ Observación';
                }
            }

            cerrarModal('modalObservaciones');
            showToast('✓ Observación registrada en el informe', 'success');
        }

        // Abrir Modal de Informe de Docente (En la misma ventana con formato oficial de PDF)
        function abrirModalInformeDocente(docenteInformeId) {
            if (!docenteInformeId) return;

            abrirModal('modalVerInformeDocente');

            const loader = document.getElementById('modalDocenteLoader');
            const iframe = document.getElementById('iframeDocenteInforme');
            const subtitulo = document.getElementById('modalDocenteSubtitulo');

            if (loader) loader.style.display = 'flex';

            const urlInforme = `/coordinador/informes/${docenteInformeId}/ver`;

            if (iframe) {
                iframe.src = `${urlInforme}?embed=1`;
            }

            if (subtitulo) {
                subtitulo.textContent = 'Documento institucional oficial FARUSAC';
            }
        }

        // Imprimir directamente el documento oficial desde el modal
        function imprimirIframeDocente() {
            const iframe = document.getElementById('iframeDocenteInforme');
            if (iframe && iframe.contentWindow) {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
            }
        }

        // Actualizar estilo visual del select según su valor
        function actualizarColorSelect(select) {
            if (!select) return;
            if (select.value === '1') {
                select.classList.remove('val-no');
                select.classList.add('val-si');
            } else {
                select.classList.remove('val-si');
                select.classList.add('val-no');
            }
        }

        // Cargar dinámicamente los datos de docentes cuando cambia periodo o mes
        async function cargarDatosDocentes(mostrarToast = true) {
            const periodo = document.getElementById('selectPeriodo')?.value;
            const mes = document.getElementById('selectMes')?.value;

            if (!periodo || !mes) return;

            try {
                const res = await fetch(`/coordinador/informes-coordinacion/datos-docentes?periodo=${encodeURIComponent(periodo)}&mes=${encodeURIComponent(mes)}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                const data = await res.json();

                if (res.ok && data.success) {
                    // 1. Sincronizar Programas de Asignaturas (Inciso 1)
                    if (data.programas && Array.isArray(data.programas) && data.programas.length > 0) {
                        const tbodyProg = document.getElementById('tbodyProgramas');
                        if (tbodyProg) {
                            const existingRows = tbodyProg.querySelectorAll('tr.fila-programa');

                            if (existingRows.length === 0) {
                                tbodyProg.innerHTML = '';
                                data.programas.forEach(p => {
                                    const tr = document.createElement('tr');
                                    tr.className = 'fila-programa';
                                    tr.setAttribute('data-asig-name', p.asignatura);
                                    tr.innerHTML = `
                                    <td>
                                        <input type="text" class="table-input prog-asignatura" value="${p.asignatura}" readonly style="background-color: #f8fafc; font-weight: 600; color: #1e293b; cursor: default;">
                                    </td>
                                    <td>
                                        <input type="url" class="table-input prog-enlace" value="${p.enlace_programa || ''}" placeholder="https://drive.google.com/...">
                                    </td>
                                `;
                                    tbodyProg.appendChild(tr);
                                });
                            } else {
                                data.programas.forEach(p => {
                                    let found = false;
                                    existingRows.forEach(tr => {
                                        const inputAsig = tr.querySelector('.prog-asignatura');
                                        const inputLink = tr.querySelector('.prog-enlace');
                                        if (inputAsig && inputAsig.value.trim().toLowerCase() === p.asignatura.trim().toLowerCase()) {
                                            found = true;
                                            // Actualizar el enlace si el docente entregó programa en este mes
                                            if (p.enlace_programa) {
                                                inputLink.value = p.enlace_programa;
                                            }
                                        }
                                    });

                                    if (!found) {
                                        const tr = document.createElement('tr');
                                        tr.className = 'fila-programa';
                                        tr.setAttribute('data-asig-name', p.asignatura);
                                        tr.innerHTML = `
                                        <td>
                                            <input type="text" class="table-input prog-asignatura" value="${p.asignatura}" readonly style="background-color: #f8fafc; font-weight: 600; color: #1e293b; cursor: default;">
                                        </td>
                                        <td>
                                            <input type="url" class="table-input prog-enlace" value="${p.enlace_programa || ''}" placeholder="https://drive.google.com/...">
                                        </td>
                                    `;
                                        tbodyProg.appendChild(tr);
                                    }
                                });
                            }
                        }
                    }

                    // 2. Sincronizar Información General de Asignaturas (Inciso 2)
                    if (data.datos && Array.isArray(data.datos)) {
                        const mapDatos = {};
                        data.datos.forEach(d => {
                            mapDatos[d.curso_id] = d;
                        });

                        document.querySelectorAll('#tbodyAsignaturas tr.fila-asig-general').forEach(tr => {
                            const cId = tr.getAttribute('data-curso-id');
                            const d = mapDatos[cId];
                            if (d) {
                                const inPresento = tr.querySelector('.asig-presento');
                                if (inPresento) {
                                    inPresento.value = d.presento_informe ? '1' : '0';
                                    actualizarColorSelect(inPresento);
                                }

                                const inSala = tr.querySelector('.asig-sala');
                                if (inSala) {
                                    inSala.value = d.tiene_sala_reuniones ? '1' : '0';
                                    actualizarColorSelect(inSala);
                                }

                                const inVirtual = tr.querySelector('.asig-virtual');
                                if (inVirtual) {
                                    inVirtual.value = d.funciona_enlace_virtual ? '1' : '0';
                                    actualizarColorSelect(inVirtual);
                                }

                                const inEval = tr.querySelector('.asig-eval');
                                if (inEval) {
                                    inEval.value = d.funciona_enlace_evaluacion ? '1' : '0';
                                    actualizarColorSelect(inEval);
                                }

                                const inEvid = tr.querySelector('.asig-evid');
                                if (inEvid) {
                                    inEvid.value = d.evidencias_generales ? '1' : '0';
                                    actualizarColorSelect(inEvid);
                                }

                                // Actualizar botón de visualización de informe docente
                                const actionCol = tr.querySelector('.col-docente-report-action');
                                if (actionCol) {
                                    if (d.docente_informe_id) {
                                        actionCol.innerHTML = `
                                        <button type="button" class="btn-ver-docente" onclick="abrirModalInformeDocente(${d.docente_informe_id})" title="Visualizar informe entregado por el docente en ${mes}">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                            <span>Ver Informe</span>
                                        </button>
                                    `;
                                    } else {
                                        actionCol.innerHTML = `<span class="badge-sin-informe">Sin informe</span>`;
                                    }
                                }
                            }
                        });
                    }

                    if (mostrarToast) {
                        showToast(`✓ Filtro aplicado: datos de docentes sincronizados para ${mes}`, 'success');
                    }
                }
            } catch (err) {
                console.error('Error al sincronizar datos de docentes:', err);
            }
        }

        document.getElementById('selectPeriodo')?.addEventListener('change', () => cargarDatosDocentes(true));
        document.getElementById('selectMes')?.addEventListener('change', () => cargarDatosDocentes(true));

        document.addEventListener('DOMContentLoaded', () => {
            // En modo creación, sincronizar con el mes seleccionado inicialmente
            if (!informeId) {
                cargarDatosDocentes(false);
            }
        });

        // Toast de Notificación
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toastNotification');
            if (!toast) return;
            toast.textContent = message;
            toast.className = `toast-notification show ${type}`;
            setTimeout(() => {
                toast.classList.remove('show');
            }, 4000);
        }

        // Envío / Guardado del Informe
        async function submitInformeCoordinacion(estado = 'enviado') {
            const periodo = document.getElementById('selectPeriodo').value;
            const mes = document.getElementById('selectMes').value;
            const anio = document.getElementById('inputAnio').value;
            const herramientasVirtuales = document.getElementById('inputHerramientasVirtuales').value;

            // Recopilar Programas (Inciso 1)
            const programas = [];
            document.querySelectorAll('#tbodyProgramas tr').forEach(tr => {
                const asig = tr.querySelector('.prog-asignatura')?.value.trim();
                const link = tr.querySelector('.prog-enlace')?.value.trim();
                if (asig) {
                    programas.push({
                        asignatura: asig,
                        enlace_programa: link || null
                    });
                }
            });

            // Recopilar Asignaturas (Inciso 2)
            const asignaturas = [];
            document.querySelectorAll('#tbodyAsignaturas tr.fila-asig-general').forEach(tr => {
                const cursoId = tr.getAttribute('data-curso-id');
                const docente = tr.querySelector('.asig-docente')?.value.trim();
                const nombre = tr.querySelector('.asig-nombre')?.value.trim();
                const seccion = tr.querySelector('.asig-seccion')?.value.trim();
                const presento = tr.querySelector('.asig-presento')?.value === '1';
                const sala = tr.querySelector('.asig-sala')?.value === '1';
                const virtual = tr.querySelector('.asig-virtual')?.value === '1';
                const evaluacion = tr.querySelector('.asig-eval')?.value === '1';
                const evidencias = tr.querySelector('.asig-evid')?.value === '1';
                const obs = tr.querySelector('.asig-obs')?.value.trim();

                if (nombre) {
                    asignaturas.push({
                        curso_id: cursoId || null,
                        docente_nombre: docente || '—',
                        asignatura: nombre,
                        seccion: seccion || '—',
                        presento_informe: presento,
                        tiene_sala_reuniones: sala,
                        funciona_enlace_virtual: virtual,
                        funciona_enlace_evaluacion: evaluacion,
                        evidencias_generales: evidencias,
                        observaciones: obs || null
                    });
                }
            });

            // Recopilar Avances (Inciso 3.1)
            const avances = [];
            document.querySelectorAll('#tbodyAvances tr.fila-avance').forEach(tr => {
                const cursoId = tr.getAttribute('data-curso-id');
                const docente = tr.querySelector('.avance-docente')?.value.trim();
                const nombre = tr.querySelector('.avance-asignatura')?.value.trim();
                const seccion = tr.querySelector('.avance-seccion')?.value.trim();
                const porcentaje = parseInt(tr.querySelector('.avance-porcentaje')?.value || '0', 10);
                const obs = tr.querySelector('.avance-obs')?.value.trim();

                if (nombre) {
                    avances.push({
                        curso_id: cursoId || null,
                        docente_nombre: docente || '—',
                        asignatura: nombre,
                        seccion: seccion || '—',
                        porcentaje_avance: porcentaje,
                        observaciones: obs || null
                    });
                }
            });

            // Recopilar Estudiantes con problemas (Inciso 3.2)
            const estudiantes = [];
            document.querySelectorAll('#tbodyEstudiantes tr').forEach(tr => {
                const asig = tr.querySelector('.est-asignatura')?.value.trim();
                const seccion = tr.querySelector('.est-seccion')?.value.trim();
                const cantidad = parseInt(tr.querySelector('.est-cantidad')?.value || '0', 10);
                const carne = tr.querySelector('.est-carne')?.value.trim();

                if (asig) {
                    estudiantes.push({
                        asignatura: asig,
                        seccion: seccion || '—',
                        cantidad_estudiantes: cantidad,
                        carne_estudiantes: carne || null
                    });
                }
            });

            // Recopilar Enlaces de Secciones 4, 5 y 6
            const enlaceActividadesCoordinacion = document.getElementById('inputEnlaceActividadesCoordinacion')?.value.trim() || null;
            const enlaceInformeAuxiliares = document.getElementById('inputEnlaceInformeAuxiliares')?.value.trim() || null;
            const enlaceDocentesPermisos = document.getElementById('inputEnlaceDocentesPermisos')?.value.trim() || null;

            const payload = {
                periodo: periodo,
                mes: mes,
                anio: parseInt(anio, 10),
                herramientas_virtuales: herramientasVirtuales,
                enlace_actividades_coordinacion: enlaceActividadesCoordinacion,
                enlace_informe_auxiliares: enlaceInformeAuxiliares,
                enlace_docentes_permisos: enlaceDocentesPermisos,
                estado: estado,
                programas: programas,
                asignaturas: asignaturas,
                avances: avances,
                estudiantes: estudiantes
            };

            const url = informeId
                ? `/coordinador/informes-coordinacion/${informeId}`
                : '/coordinador/informes-coordinacion';

            const method = informeId ? 'PUT' : 'POST';

            try {
                const res = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();

                if (res.ok && data.success) {
                    showToast(data.message || 'Informe guardado exitosamente.', 'success');
                    setTimeout(() => {
                        window.location.href = data.redirect_url || "{{ route('coordinador.dashboard') }}";
                    }, 1200);
                } else {
                    showToast(data.message || 'Error al guardar el informe.', 'error');
                }
            } catch (err) {
                showToast('Error de conexión con el servidor: ' + err.message, 'error');
            }
        }
    </script>
</body>

</html>