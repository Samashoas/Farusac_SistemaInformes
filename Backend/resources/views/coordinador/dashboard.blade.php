<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>FARUSAC - Panel del Coordinador</title>
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
            --color-borde: #edf2f7;
            --border-radius-card: 16px;
            --border-radius-input: 10px;
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

        .sidebar-link-inicio {
            background-color: rgba(0, 45, 114, 0.08);
            color: var(--color-azul);
            font-weight: 600;
        }
        .sidebar-link-inicio .sidebar-icon {
            color: var(--color-azul);
        }

        .sidebar-link-perfil:hover,
        .sidebar-link-cursos:hover,
        .sidebar-link-informes:hover {
            background-color: rgba(0, 45, 114, 0.05);
            color: var(--color-azul);
        }
        .sidebar-link:hover .sidebar-icon {
            color: var(--color-azul);
        }

        /* --- CONTENIDO PRINCIPAL --- */
        .main-content {
            flex: 1;
            padding: 40px;
            margin-left: 260px;
            transition: margin-left 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: calc(100vh - 90px);
            position: relative;
        }

        .main-content.expanded {
            margin-left: 0;
        }

        /* SECCIÓN SUPERIOR ANCLADA ARRIBA (ÁREA + SWITCH) */
        .dashboard-top-section {
            position: absolute;
            top: 25px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            z-index: 10;
        }

        /* BANNER DE ÁREA A CARGO */
        .area-badge-banner {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            background: #ffffff;
            border: 1.5px solid #cbd5e1;
            border-radius: 30px;
            padding: 6px 20px;
            box-shadow: 0 4px 15px rgba(0, 45, 114, 0.06);
            margin: 0;
            animation: fadeIn 0.4s ease;
        }

        .area-badge-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .area-label-title {
            font-family: 'Outfit', sans-serif;
            font-size: 13px;
            font-weight: 700;
            color: var(--color-texto-secundario);
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .area-name-pill {
            background: linear-gradient(135deg, rgba(0, 45, 114, 0.09) 0%, rgba(172, 132, 0, 0.13) 50%, rgba(185, 71, 0, 0.09) 100%);
            color: var(--color-azul);
            border: 1px solid rgba(0, 45, 114, 0.16);
            padding: 4px 16px;
            border-radius: 20px;
            font-family: 'Outfit', sans-serif;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.02em;
            box-shadow: 0 2px 6px rgba(0, 45, 114, 0.04);
        }

        /* --- SWITCH DE VISTAS (DOCENTE / COORDINADOR) --- */
        .view-switch-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            width: 100%;
            animation: fadeIn 0.3s ease;
        }

        .view-switch {
            display: inline-flex;
            background-color: #e2e8f0;
            border-radius: 35px;
            padding: 5px;
            position: relative;
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.06);
            gap: 6px;
        }

        .switch-btn {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            padding: 10px 24px;
            border: none;
            background: transparent;
            border-radius: 30px;
            font-family: 'Outfit', sans-serif;
            font-size: 13.5px;
            font-weight: 700;
            color: #64748b;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            text-transform: uppercase;
            letter-spacing: 0.03em;
            user-select: none;
        }

        .switch-btn .switch-icon {
            width: 18px;
            height: 18px;
            transition: all 0.3s ease;
        }

        .switch-btn:hover:not(.active) {
            color: var(--color-azul);
        }

        .switch-btn.active {
            background: linear-gradient(135deg, var(--color-azul), #1d4ed8);
            color: #ffffff;
            box-shadow: 0 4px 15px rgba(0, 45, 114, 0.25);
        }

        .switch-btn.active .switch-icon {
            color: #ffffff;
        }

        /* --- SECCIÓN VISTA COORDINADOR --- */
        .coordinador-content-section {
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
            max-width: 1000px;
            animation: fadeIn 0.4s ease;
            margin: auto 0;
            padding: 20px;
        }

        .coordinador-placeholder-card {
            background: #ffffff;
            border-radius: var(--border-radius-card);
            border: 1.5px solid var(--color-borde);
            padding: 50px 40px;
            width: 100%;
            max-width: 750px;
            text-align: center;
            box-shadow: var(--shadow-premium);
            position: relative;
            overflow: hidden;
        }

        .coordinador-placeholder-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--color-azul), var(--color-oro), var(--color-terracota));
        }

        .coordinador-placeholder-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 20px auto;
            border-radius: 50%;
            background: rgba(0, 45, 114, 0.07);
            color: var(--color-azul);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .coordinador-placeholder-icon svg {
            width: 36px;
            height: 36px;
        }

        .coordinador-placeholder-title {
            font-family: 'Outfit', sans-serif;
            font-size: 24px;
            font-weight: 700;
            color: var(--color-texto-principal);
            margin-bottom: 10px;
        }

        .coordinador-placeholder-subtitle {
            font-size: 14px;
            color: var(--color-texto-secundario);
            line-height: 1.6;
            max-width: 520px;
            margin: 0 auto;
        }

        /* --- CONTENEDOR DE CÍRCULOS (GRID / LISTA) --- */
        .circles-dashboard-container {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-start;
            justify-content: center;
            gap: 45px 50px;
            width: 100%;
            max-width: 1100px;
            margin: auto 0;
            padding: 20px;
            animation: fadeIn 0.4s ease;
        }

        /* Tarjeta Circular Base */
        .circle-card-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 170px;
            text-align: center;
            cursor: pointer;
            transition: var(--transition-smooth);
            position: relative;
        }

        .circle-card-item:hover {
            transform: translateY(-6px);
        }

        /* Círculo Gráfico */
        .circle-shape {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            background-color: #ffffff;
            border: 2px solid #cbd5e1;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.03);
            transition: var(--transition-smooth);
            position: relative;
        }

        .circle-card-item:hover .circle-shape {
            border-color: var(--color-azul);
            box-shadow: 0 14px 30px rgba(0, 45, 114, 0.12);
        }

        /* Icono de Curso */
        .circle-course-icon {
            width: 58px;
            height: 58px;
            color: var(--color-texto-principal);
            transition: color 0.3s ease, transform 0.3s ease;
            opacity: 0.85;
        }

        .circle-card-item:hover .circle-course-icon {
            color: var(--color-azul);
            transform: scale(1.08);
        }

        /* Círculo "Agregar nuevo Curso" */
        .circle-add-shape {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            background-color: #ffffff;
            border: 2px solid #cbd5e1;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.03);
            transition: var(--transition-smooth);
            position: relative;
        }

        .circle-inner-dashed {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            border: 2px dashed #94a3b8;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition-smooth);
        }

        .circle-add-icon {
            width: 32px;
            height: 32px;
            color: var(--color-texto-principal);
            transition: var(--transition-smooth);
        }

        .circle-card-add:hover .circle-add-shape {
            border-color: var(--color-azul);
            box-shadow: 0 14px 30px rgba(0, 45, 114, 0.12);
        }

        .circle-card-add:hover .circle-inner-dashed {
            border-color: var(--color-azul);
            transform: rotate(90deg);
        }

        .circle-card-add:hover .circle-add-icon {
            color: var(--color-azul);
        }

        /* Texto / Label inferior */
        .circle-label {
            margin-top: 16px;
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: var(--color-texto-principal);
            line-height: 1.35;
            word-break: break-word;
            transition: color 0.2s ease;
        }

        .circle-card-item:hover .circle-label {
            color: var(--color-azul);
        }

        /* Botón de desasignación rápida */
        .btn-unassign-course {
            position: absolute;
            top: 2px;
            right: 15px;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background-color: #fee2e2;
            color: #ef4444;
            border: 1px solid #fca5a5;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            opacity: 0;
            transform: scale(0.8);
            transition: var(--transition-smooth);
            z-index: 10;
        }

        .circle-card-item:hover .btn-unassign-course {
            opacity: 1;
            transform: scale(1);
        }

        .btn-unassign-course:hover {
            background-color: #ef4444;
            color: #ffffff;
        }

        /* --- MODAL CON FONDO TRASLÚCIDO Y DESENFOCADO (BLUR) --- */
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

        .modal-card {
            background: 
                linear-gradient(#ffffff, #ffffff) padding-box,
                linear-gradient(135deg, var(--color-azul), var(--color-oro), var(--color-terracota)) border-box;
            border: 2px solid transparent;
            border-radius: 20px;
            padding: 35px 40px;
            width: 100%;
            max-width: 520px;
            box-shadow: 0 20px 50px rgba(0, 45, 114, 0.15);
            transform: translateY(20px) scale(0.97);
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
        }

        .modal-overlay.active .modal-card {
            transform: translateY(0) scale(1);
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1.5px solid var(--color-borde);
            padding-bottom: 18px;
            margin-bottom: 25px;
        }

        .modal-title {
            font-family: 'Outfit', sans-serif;
            font-size: 22px;
            font-weight: 700;
            color: var(--color-texto-principal);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .modal-close-btn {
            background: none;
            border: none;
            color: var(--color-texto-claro);
            cursor: pointer;
            padding: 4px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s ease, background-color 0.2s ease;
        }

        .modal-close-btn:hover {
            color: #e53e3e;
            background-color: #fff5f5;
        }

        /* Formulario y Selectores en Cascada */
        .form-group-row {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            gap: 15px;
        }

        .form-label-side {
            width: 90px;
            font-family: 'Outfit', sans-serif;
            font-size: 15px;
            font-weight: 600;
            color: var(--color-texto-principal);
            flex-shrink: 0;
            text-align: right;
        }

        .select-wrapper {
            flex: 1;
            position: relative;
        }

        .custom-select {
            width: 100%;
            padding: 12px 38px 12px 16px;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            color: var(--color-texto-principal);
            background-color: #ffffff;
            border: 1.5px solid #cbd5e1;
            border-radius: 25px;
            outline: none;
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            transition: var(--transition-smooth);
        }

        .custom-select:focus {
            border-color: var(--color-azul);
            box-shadow: 0 0 0 3px rgba(0, 45, 114, 0.1);
        }

        .custom-select:disabled {
            background-color: #f1f5f9;
            color: #94a3b8;
            border-color: #e2e8f0;
            cursor: not-allowed;
        }

        .select-arrow {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            width: 18px;
            height: 18px;
            color: #64748b;
        }

        /* Alerta de Error dentro del Modal */
        .modal-alert-error {
            background-color: #fef2f2;
            border-left: 3.5px solid #ef4444;
            color: #991b1b;
            padding: 10px 14px;
            border-radius: 6px;
            font-size: 13px;
            margin-bottom: 20px;
            display: none;
        }

        /* Botón de Enviar Modal */
        .modal-btn-submit {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 13px 25px;
            background-color: var(--color-azul);
            color: #ffffff;
            border: none;
            border-radius: 25px;
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 45, 114, 0.25);
            transition: var(--transition-smooth);
            margin-top: 10px;
        }

        .modal-btn-submit:hover:not(:disabled) {
            background-color: #002257;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(0, 45, 114, 0.3);
        }

        .modal-btn-submit:disabled {
            background-color: #94a3b8;
            box-shadow: none;
            cursor: not-allowed;
            opacity: 0.7;
        }

        /* Toast de Notificación */
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

        /* CARD DE ALERTA PARA CONFIRMACIÓN / DESACTIVACIÓN */
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
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            border: none;
            outline: none;
        }

        .btn-submit {
            background-color: #ffffff;
            border: 2px solid #60a5fa;
            color: #2563eb;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
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
        }

        .btn-cancel:hover,
        .btn-cancel:active {
            background-color: #fca5a5;
            color: #991b1b;
            border-color: #ef4444;
            box-shadow: 0 4px 12px rgba(248, 113, 113, 0.25);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
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
        <aside class="admin-sidebar collapsed" id="coordinadorSidebar">
            <nav class="sidebar-nav">
                <!-- Enlace Inicio -->
                <a href="{{ route('coordinador.dashboard') }}" class="sidebar-link sidebar-link-inicio">
                    <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Inicio</span>
                </a>

                <!-- Enlace Perfil -->
                <a href="{{ route('coordinador.perfil') }}" class="sidebar-link sidebar-link-perfil">
                    <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Perfil</span>
                </a>

                <!-- Enlace Informes -->
                <a href="{{ route('coordinador.informes') }}" class="sidebar-link sidebar-link-informes">
                    <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Informes</span>
                </a>
            </nav>
        </aside>

        <!-- Cuerpo Principal -->
        <main class="main-content expanded" id="mainContent">

            <!-- BARRA SUPERIOR DE CONTROL (SWITCH ARRIBA + ÁREA DEBAJO) -->
            <div class="dashboard-top-section">
                <!-- 1. SWITCH DE VISTAS (INFORMES DOCENTE / INFORMES DE COORDINADOR) -->
                <div class="view-switch-container">
                    <div class="view-switch">
                        <button type="button" class="switch-btn active" id="btnSwitchDocente" onclick="switchView('docente')">
                            <svg class="switch-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <span>Informes Docente</span>
                        </button>
                        <button type="button" class="switch-btn" id="btnSwitchCoordinador" onclick="switchView('coordinador')">
                            <svg class="switch-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span>Informes de Coordinador</span>
                        </button>
                    </div>
                </div>

                @php
                    $areaActual = $user->area ?? session('coordinador_area');
                @endphp

                @if ($areaActual)
                    <!-- 2. Banner de Área a Cargo (Color diferenciado) -->
                    <div class="area-badge-banner">
                        <div class="area-badge-info">
                            <span class="area-label-title">Área a Cargo:</span>
                            <span class="area-name-pill" id="currentAreaDisplay">{{ $areaActual }}</span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- 1. VISTA: INFORMES DOCENTE (CÍRCULOS DE CURSOS ASIGNADOS) -->
            <div id="docenteViewSection" style="display: flex; width: 100%; justify-content: center;">
                <div class="circles-dashboard-container" id="coursesGrid">

                    <!-- Renderizado de Cursos Asignados -->
                    @foreach ($cursosAsignados as $curso)
                        <div class="circle-card-item" id="curso-item-{{ $curso->id }}" onclick="window.location.href='{{ route('coordinador.informes.crear') }}?curso_id={{ $curso->id }}'">
                            <button type="button" class="btn-unassign-course" title="Remover curso" onclick="event.stopPropagation(); unassignCourse({{ $curso->id }}, '{{ addslashes($curso->nombre_curso) }}', '{{ $curso->seccion }}')">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                            <div class="circle-shape">
                                <svg class="circle-course-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <div class="circle-label">
                                {{ $curso->nombre_curso }} - {{ $curso->seccion }}
                            </div>
                        </div>
                    @endforeach

                    <!-- Círculo: Agregar nuevo Curso -->
                    <div class="circle-card-item circle-card-add" id="btnOpenModal" onclick="openAddCourseModal()">
                        <div class="circle-add-shape">
                            <div class="circle-inner-dashed">
                                <svg class="circle-add-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                            </div>
                        </div>
                        <div class="circle-label">
                            Agregar nuevo Curso
                        </div>
                    </div>

                </div>
            </div>

            <!-- 2. VISTA: INFORMES DE COORDINADOR (ESTRUCTURA PARA FORMULARIO/GESTIÓN) -->
            <div id="coordinadorViewSection" class="coordinador-content-section">
                <div class="coordinador-placeholder-card">
                    <div class="coordinador-placeholder-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="coordinador-placeholder-title">Módulo de Informes de Coordinador</h3>
                    <p class="coordinador-placeholder-subtitle">
                        Vista de coordinación para el <strong>{{ $areaActual ?? 'Área Asignada' }}</strong>. Este espacio está preparado para el formulario y control de informes del coordinador.
                    </p>
                </div>
            </div>

        </main>
    </div>

    <!-- MODAL "AGREGAR NUEVO CURSO" (CON FONDO TRASLÚCIDO Y DESENFOCADO) -->
    <div class="modal-overlay" id="addCourseModalOverlay" onclick="handleBackdropClick(event)">
        <div class="modal-card" id="modalCard">
            <div class="modal-header">
                <h3 class="modal-title">Agregar nuevo Curso</h3>
                <button type="button" class="modal-close-btn" onclick="closeAddCourseModal()" title="Cerrar">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <!-- Alerta de Error dentro del Modal -->
            <div class="modal-alert-error" id="modalErrorMessage"></div>

            <form id="addCourseForm" onsubmit="handleCourseSubmit(event)">
                <!-- Selector: Carrera -->
                <div class="form-group-row">
                    <label for="selectCarrera" class="form-label-side">Carrera:</label>
                    <div class="select-wrapper">
                        <select id="selectCarrera" class="custom-select" onchange="onCarreraChange()">
                            <option value="">Selecciona una carrera...</option>
                            @foreach ($carreras as $carrera)
                                <option value="{{ $carrera }}">{{ $carrera }}</option>
                            @endforeach
                        </select>
                        <svg class="select-arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>

                <!-- Selector: Curso (Bloqueado hasta seleccionar Carrera) -->
                <div class="form-group-row">
                    <label for="selectCurso" class="form-label-side">Curso:</label>
                    <div class="select-wrapper">
                        <select id="selectCurso" class="custom-select" disabled onchange="onCursoChange()">
                            <option value="">Selecciona primero una carrera...</option>
                        </select>
                        <svg class="select-arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>

                <!-- Selector: Sección (Bloqueado hasta seleccionar Curso) -->
                <div class="form-group-row">
                    <label for="selectSeccion" class="form-label-side">Sección:</label>
                    <div class="select-wrapper">
                        <select id="selectSeccion" class="custom-select" disabled onchange="onSeccionChange()">
                            <option value="">Selecciona primero un curso...</option>
                        </select>
                        <svg class="select-arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>

                <button type="submit" class="modal-btn-submit" id="btnSubmitCourse" disabled>
                    Agregar Curso
                </button>
            </form>
        </div>
    </div>

    <!-- MODAL OBLIGATORIO: SELECCIÓN DE ÁREA A CARGO -->
    <div class="modal-overlay {{ !$areaActual ? 'active' : '' }}" id="areaSelectionModalOverlay">
        <div class="modal-card">
            <div class="modal-header">
                <h3 class="modal-title">Selección de Área</h3>
                @if ($areaActual)
                    <button type="button" class="modal-close-btn" onclick="closeAreaSelectionModal()" title="Cerrar">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                @endif
            </div>

            <p style="font-size: 14px; color: var(--color-texto-secundario); margin-bottom: 20px; line-height: 1.5;">
                Para acceder y gestionar las funciones correspondientes a su coordinación, seleccione el área de la cual es coordinador:
            </p>

            <div class="form-group-row">
                <label for="selectAreaModal" class="form-label-side">Área:</label>
                <div class="select-wrapper">
                    <select id="selectAreaModal" class="custom-select">
                        <option value="">Selecciona un área...</option>
                        @foreach ($areasDisponibles as $a)
                            <option value="{{ $a }}" {{ $areaActual == $a ? 'selected' : '' }}>
                                {{ $a }}
                            </option>
                        @endforeach
                    </select>
                    <svg class="select-arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>

            <div class="modal-alert-error" id="areaModalError"></div>

            <button type="button" class="modal-btn-submit" id="btnConfirmArea" onclick="confirmarSeleccionArea()">
                Confirmar Área
            </button>
        </div>
    </div>

    <!-- Modal Emergente: Confirmación de Alerta para Remover Curso -->
    <div class="modal-overlay" id="confirmDeleteModalOverlay" onclick="closeConfirmDeleteModal()">
        <div class="confirm-status-card" onclick="event.stopPropagation()">
            <div class="alert-content">
                <div class="warning-triangle">⚠</div>
                <h3 class="alert-title" id="confirmModalTitle">¿ESTÁ SEGURO QUE DESEA REMOVER EL CURSO?</h3>
                <p class="alert-desc" id="confirmModalDesc">El curso será eliminado de su lista de cursos asignados</p>
            </div>
            <div class="modal-footer" style="margin-top: 25px;">
                <button type="button" class="modal-btn btn-submit" id="btnConfirmDelete">REMOVER</button>
                <button type="button" class="modal-btn btn-cancel" onclick="closeConfirmDeleteModal()">CANCELAR</button>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="toast-notification" id="toastNotification">
        <span id="toastMessage"></span>
    </div>

    <!-- JAVASCRIPT: LÓGICA DE INTERACCIÓN, CASCADA Y AJAX -->
    <script>
        // Catálogo completo de cursos proveniente de la BD
        const catalogoCursos = @json($catalogoCursos);

        // Elementos DOM
        const modalOverlay = document.getElementById('addCourseModalOverlay');
        const areaSelectionModalOverlay = document.getElementById('areaSelectionModalOverlay');
        const confirmDeleteModalOverlay = document.getElementById('confirmDeleteModalOverlay');
        const selectCarrera = document.getElementById('selectCarrera');
        const selectCurso = document.getElementById('selectCurso');
        const selectSeccion = document.getElementById('selectSeccion');
        const btnSubmitCourse = document.getElementById('btnSubmitCourse');
        const modalErrorMessage = document.getElementById('modalErrorMessage');
        const coursesGrid = document.getElementById('coursesGrid');
        const btnOpenModal = document.getElementById('btnOpenModal');

        let pendingUnassignCursoId = null;

        // --- MANEJO DEL MODAL DE SELECCIÓN DE ÁREA ---
        function openAreaSelectionModal() {
            if (areaSelectionModalOverlay) {
                areaSelectionModalOverlay.classList.add('active');
            }
        }

        function closeAreaSelectionModal() {
            if (areaSelectionModalOverlay) {
                areaSelectionModalOverlay.classList.remove('active');
            }
        }

        async function confirmarSeleccionArea() {
            const selectAreaModal = document.getElementById('selectAreaModal');
            const areaError = document.getElementById('areaModalError');
            const btn = document.getElementById('btnConfirmArea');
            const val = selectAreaModal ? selectAreaModal.value : '';

            if (!val) {
                if (areaError) {
                    areaError.textContent = 'Por favor, seleccione un área válida.';
                    areaError.style.display = 'block';
                }
                return;
            }

            if (areaError) areaError.style.display = 'none';
            btn.disabled = true;
            btn.textContent = 'Guardando...';

            try {
                const response = await fetch('{{ route("coordinador.area.seleccionar") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ area: val })
                });

                const data = await response.json();
                if (data.success) {
                    showToast('Área guardada correctamente', 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                } else {
                    btn.disabled = false;
                    btn.textContent = 'Confirmar Área';
                    if (areaError) {
                        areaError.textContent = data.message || 'No se pudo guardar el área seleccionada.';
                        areaError.style.display = 'block';
                    }
                }
            } catch (err) {
                console.error(err);
                btn.disabled = false;
                btn.textContent = 'Confirmar Área';
                if (areaError) {
                    areaError.textContent = 'Error de conexión con el servidor.';
                    areaError.style.display = 'block';
                }
            }
        }

        // --- MANEJO DEL MODAL DE AGREGAR CURSO ---
        function openAddCourseModal() {
            resetModalForm();
            modalOverlay.classList.add('active');
        }

        function closeAddCourseModal() {
            modalOverlay.classList.remove('active');
        }

        function handleBackdropClick(event) {
            if (event.target === modalOverlay) {
                closeAddCourseModal();
            }
        }

        function resetModalForm() {
            selectCarrera.value = '';
            selectCurso.innerHTML = '<option value="">Selecciona primero una carrera...</option>';
            selectCurso.disabled = true;
            selectSeccion.innerHTML = '<option value="">Selecciona primero un curso...</option>';
            selectSeccion.disabled = true;
            btnSubmitCourse.disabled = true;
            modalErrorMessage.style.display = 'none';
            modalErrorMessage.textContent = '';
        }

        // --- MANEJO DEL MODAL DE ADVERTENCIA / CONFIRMACIÓN ---
        function unassignCourse(cursoId, nombreCurso, seccion) {
            pendingUnassignCursoId = cursoId;
            document.getElementById('confirmModalDesc').textContent = `El curso "${nombreCurso} - Sección ${seccion}" será eliminado de su lista de cursos asignados.`;
            confirmDeleteModalOverlay.classList.add('active');
        }

        function closeConfirmDeleteModal() {
            confirmDeleteModalOverlay.classList.remove('active');
            pendingUnassignCursoId = null;
        }

        function handleConfirmBackdropClick(event) {
            if (event.target === confirmDeleteModalOverlay) {
                closeConfirmDeleteModal();
            }
        }

        document.getElementById('btnConfirmDelete').addEventListener('click', () => {
            if (!pendingUnassignCursoId) return;

            const cursoId = pendingUnassignCursoId;
            const btn = document.getElementById('btnConfirmDelete');
            btn.disabled = true;
            btn.textContent = 'Removiendo...';

            fetch(`{{ url('/coordinador/cursos') }}/${cursoId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.textContent = 'Remover';
                closeConfirmDeleteModal();

                if (data.success) {
                    showToast(data.message, 'success');
                    const el = document.getElementById(`curso-item-${cursoId}`);
                    if (el) {
                        el.style.opacity = '0';
                        el.style.transform = 'scale(0.8)';
                        setTimeout(() => el.remove(), 300);
                    }
                } else {
                    showToast(data.message || 'No se pudo remover el curso.', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                btn.disabled = false;
                btn.textContent = 'Remover';
                closeConfirmDeleteModal();
                showToast('Error de conexión al intentar remover el curso.', 'error');
            });
        });

        // --- SELECTORES EN CASCADA (CARRERA -> CURSO -> SECCIÓN) ---

        // 1. Al cambiar de Carrera:
        function onCarreraChange() {
            const carreraSelected = selectCarrera.value;
            modalErrorMessage.style.display = 'none';

            // Resetear selects dependientes
            selectCurso.innerHTML = '<option value="">Seleccione un curso...</option>';
            selectSeccion.innerHTML = '<option value="">Seleccione primero un curso...</option>';
            selectSeccion.disabled = true;
            btnSubmitCourse.disabled = true;

            if (!carreraSelected) {
                selectCurso.innerHTML = '<option value="">Selecciona primero una carrera...</option>';
                selectCurso.disabled = true;
                return;
            }

            // Filtrar cursos de la carrera seleccionada y extraer nombres únicos
            const cursosDeCarrera = catalogoCursos.filter(c => c.carrera === carreraSelected);
            const nombresUnicos = [...new Set(cursosDeCarrera.map(c => c.nombre_curso))].sort();

            if (nombresUnicos.length === 0) {
                selectCurso.innerHTML = '<option value="">No hay cursos registrados para esta carrera</option>';
                selectCurso.disabled = true;
                return;
            }

            nombresUnicos.forEach(nombre => {
                const opt = document.createElement('option');
                opt.value = nombre;
                opt.textContent = nombre;
                selectCurso.appendChild(opt);
            });

            selectCurso.disabled = false;
        }

        // 2. Al cambiar de Curso:
        function onCursoChange() {
            const carreraSelected = selectCarrera.value;
            const cursoSelected = selectCurso.value;
            modalErrorMessage.style.display = 'none';

            selectSeccion.innerHTML = '<option value="">Seleccione una sección...</option>';
            btnSubmitCourse.disabled = true;

            if (!cursoSelected) {
                selectSeccion.innerHTML = '<option value="">Selecciona primero un curso...</option>';
                selectSeccion.disabled = true;
                return;
            }

            // Filtrar secciones disponibles para la carrera y curso seleccionados
            const seccionesDisponibles = catalogoCursos.filter(c => 
                c.carrera === carreraSelected && c.nombre_curso === cursoSelected
            );

            if (seccionesDisponibles.length === 0) {
                selectSeccion.innerHTML = '<option value="">No hay secciones disponibles</option>';
                selectSeccion.disabled = true;
                return;
            }

            seccionesDisponibles.forEach(c => {
                const opt = document.createElement('option');
                opt.value = c.id; // El valor es el ID real del curso en la BD
                opt.textContent = `Sección ${c.seccion} (${c.semestre} ${c.anio})`;
                selectSeccion.appendChild(opt);
            });

            selectSeccion.disabled = false;
        }

        // 3. Al cambiar de Sección:
        function onSeccionChange() {
            modalErrorMessage.style.display = 'none';
            btnSubmitCourse.disabled = !selectSeccion.value;
        }

        // --- ASIGNACIÓN DE CURSO VÍA AJAX ---
        function handleCourseSubmit(event) {
            event.preventDefault();
            const cursoId = selectSeccion.value;
            if (!cursoId) return;

            btnSubmitCourse.disabled = true;
            btnSubmitCourse.textContent = 'Asignando...';
            modalErrorMessage.style.display = 'none';

            fetch('{{ route("coordinador.cursos.asignar") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ curso_id: cursoId })
            })
            .then(res => res.json().then(data => ({ status: res.status, body: data })))
            .then(({ status, body }) => {
                btnSubmitCourse.textContent = 'Agregar Curso';

                if (status === 200 && body.success) {
                    closeAddCourseModal();
                    showToast(body.message, 'success');

                    // Crear e insertar el nuevo círculo de curso en el grid
                    const c = body.curso;
                    const cardDiv = document.createElement('div');
                    cardDiv.className = 'circle-card-item';
                    cardDiv.id = `curso-item-${c.id}`;
                    cardDiv.onclick = () => {
                        window.location.href = `{{ route('coordinador.informes.crear') }}?curso_id=${c.id}`;
                    };
                    cardDiv.innerHTML = `
                        <button type="button" class="btn-unassign-course" title="Remover curso" onclick="event.stopPropagation(); unassignCourse(${c.id}, '${escapeHtml(c.nombre_curso)}', '${c.seccion}')">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                        <div class="circle-shape">
                            <svg class="circle-course-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <div class="circle-label">
                            ${escapeHtml(c.nombre_curso)} - ${escapeHtml(c.seccion)}
                        </div>
                    `;

                    // Insertar antes del botón "+"
                    coursesGrid.insertBefore(cardDiv, btnOpenModal);
                } else {
                    btnSubmitCourse.disabled = false;
                    modalErrorMessage.textContent = body.message || 'Ocurrió un error al asignar el curso.';
                    modalErrorMessage.style.display = 'block';
                }
            })
            .catch(err => {
                console.error(err);
                btnSubmitCourse.disabled = false;
                btnSubmitCourse.textContent = 'Agregar Curso';
                modalErrorMessage.textContent = 'Error de conexión con el servidor.';
                modalErrorMessage.style.display = 'block';
            });
        }

        // Helper para escapar HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Helper para mostrar Toasts
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toastNotification');
            const toastMsg = document.getElementById('toastMessage');
            toastMsg.textContent = message;
            toast.className = `toast-notification ${type} show`;

            setTimeout(() => {
                toast.classList.remove('show');
            }, 3500);
        }

        // --- CONTROL DEL SWITCH DE VISTAS (DOCENTE / COORDINADOR) ---
        function switchView(view) {
            const btnDocente = document.getElementById('btnSwitchDocente');
            const btnCoordinador = document.getElementById('btnSwitchCoordinador');
            const docenteSection = document.getElementById('docenteViewSection');
            const coordinadorSection = document.getElementById('coordinadorViewSection');

            if (!btnDocente || !btnCoordinador || !docenteSection || !coordinadorSection) return;

            if (view === 'docente') {
                btnDocente.classList.add('active');
                btnCoordinador.classList.remove('active');
                docenteSection.style.display = 'flex';
                coordinadorSection.style.display = 'none';
                try { localStorage.setItem('coordinador_active_view', 'docente'); } catch(e){}
            } else {
                btnCoordinador.classList.add('active');
                btnDocente.classList.remove('active');
                docenteSection.style.display = 'none';
                coordinadorSection.style.display = 'flex';
                try { localStorage.setItem('coordinador_active_view', 'coordinador'); } catch(e){}
            }
        }

        // --- TOGGLE DE HEADER Y SIDEBAR ---
        document.addEventListener('DOMContentLoaded', () => {
            // Restaurar vista seleccionada previamente
            let savedView = 'docente';
            try { savedView = localStorage.getItem('coordinador_active_view') || 'docente'; } catch(e){}
            switchView(savedView);

            const profileToggle = document.getElementById('profileToggle');
            const profileDropdown = document.getElementById('profileDropdown');
            const hamburgerBtn = document.getElementById('hamburgerBtn');
            const coordinadorSidebar = document.getElementById('coordinadorSidebar');
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
                coordinadorSidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
            });

            mainContent.addEventListener('click', () => {
                coordinadorSidebar.classList.add('collapsed');
                mainContent.classList.add('expanded');
            });

            coordinadorSidebar.addEventListener('click', (e) => {
                e.stopPropagation();
            });
        });
    </script>
</body>

</html>
