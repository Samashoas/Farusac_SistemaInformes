<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Historial y Descarga de Informes - Panel Administrador</title>
    <!-- Google Fonts: Outfit (headings) and Inter (body) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Client-side High-Fidelity PDF & ZIP Generation Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

    <style>
        :root {
            --color-azul: #002D72;
            /* Pantone 288C */
            --color-oro: #AC8400;
            /* Pantone 118C */
            --color-terracota: #B94700;
            /* Pantone 1525C - Color insignia del módulo */
            --color-texto-principal: #1a202c;
            --color-texto-secundario: #4a5568;
            --color-texto-claro: #718096;
            --color-fondo: #f4f7fa;
            --color-tarjeta: #ffffff;
            --color-borde: #e2e8f0;
            --border-radius-card: 16px;
            --border-radius-input: 10px;
            --sidebar-width: 260px;
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
            padding: 8px;
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
            height: 52px;
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
            white-space: nowrap;
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
            z-index: 1000;
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

        /* --- APP LAYOUT --- */
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
            gap: 14px;
        }

        .sidebar-icon {
            width: 22px;
            height: 22px;
            stroke-width: 2;
            flex-shrink: 0;
        }

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
            color: var(--color-oro);
        }

        .sidebar-link-informes:hover,
        .sidebar-link-informes.active {
            background-color: rgba(185, 71, 0, 0.08);
            color: var(--color-terracota);
        }

        .sidebar-link-carga:hover,
        .sidebar-link-carga.active {
            background-color: rgba(0, 45, 114, 0.05);
            color: var(--color-azul);
        }

        /* --- CONTENIDO PRINCIPAL --- */
        .main-content-area {
            flex: 1;
            padding: 35px 40px 60px 40px;
            width: 100%;
            max-width: 1600px;
            margin: 0 auto;
            transition: all 0.3s ease;
        }

        /* --- TARJETAS KPI / MÉTRICAS --- */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        @media (max-width: 1200px) {
            .kpi-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 650px) {
            .kpi-grid {
                grid-template-columns: 1fr;
            }
        }

        .kpi-card {
            background: #ffffff;
            border-radius: var(--border-radius-card);
            border: 1.5px solid #edf2f7;
            padding: 22px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 16px rgba(0, 45, 114, 0.03);
            transition: var(--transition-smooth);
            position: relative;
            overflow: hidden;
        }

        .kpi-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 45, 114, 0.07);
        }

        .kpi-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
        }

        .kpi-total::before {
            background-color: var(--color-terracota);
        }

        .kpi-docentes::before {
            background-color: var(--color-azul);
        }

        .kpi-coordinacion::before {
            background-color: var(--color-oro);
        }

        .kpi-participantes::before {
            background-color: #38a169;
        }

        .kpi-info {
            display: flex;
            flex-direction: column;
        }

        .kpi-label {
            font-family: 'Outfit', sans-serif;
            font-size: 13px;
            font-weight: 700;
            color: var(--color-texto-claro);
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 6px;
        }

        .kpi-value {
            font-family: 'Outfit', sans-serif;
            font-size: 30px;
            font-weight: 800;
            color: var(--color-texto-principal);
            line-height: 1;
        }

        .kpi-subtext {
            font-size: 12px;
            color: var(--color-texto-claro);
            margin-top: 6px;
            font-weight: 500;
        }

        .kpi-icon-wrap {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .kpi-total .kpi-icon-wrap {
            background-color: rgba(185, 71, 0, 0.1);
            color: var(--color-terracota);
        }

        .kpi-docentes .kpi-icon-wrap {
            background-color: rgba(0, 45, 114, 0.1);
            color: var(--color-azul);
        }

        .kpi-coordinacion .kpi-icon-wrap {
            background-color: rgba(172, 132, 0, 0.12);
            color: var(--color-oro);
        }

        .kpi-participantes .kpi-icon-wrap {
            background-color: rgba(56, 161, 105, 0.1);
            color: #38a169;
        }

        .kpi-icon-wrap svg {
            width: 28px;
            height: 28px;
        }

        /* --- CONTENEDOR PRINCIPAL DEL HISTORIAL --- */
        .content-card {
            background-color: var(--color-tarjeta);
            border-radius: var(--border-radius-card);
            border: 1.5px solid #edf2f7;
            box-shadow: 0 10px 30px rgba(0, 45, 114, 0.04);
            padding: 30px;
            position: relative;
        }

        .content-header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1.5px solid #f1f5f9;
        }

        .content-title-block {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .content-header-title {
            font-family: 'Outfit', sans-serif;
            font-size: 24px;
            font-weight: 800;
            color: var(--color-azul);
            text-transform: uppercase;
            letter-spacing: 0.03em;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .content-header-subtitle {
            font-size: 13.5px;
            color: var(--color-texto-claro);
            font-weight: 400;
        }

        /* Botón de Descarga Masiva */
        .btn-bulk-download {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 24px;
            background: linear-gradient(135deg, var(--color-terracota), #d9531e);
            color: #ffffff;
            border-radius: 25px;
            font-family: 'Outfit', sans-serif;
            font-size: 13.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            text-decoration: none;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(185, 71, 0, 0.25);
            transition: all 0.25s ease;
        }

        .btn-bulk-download:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(185, 71, 0, 0.35);
            background: linear-gradient(135deg, #9f3c00, #c44310);
        }

        .btn-bulk-download svg {
            width: 20px;
            height: 20px;
        }

        /* --- SISTEMA DE PESTAÑAS (TABS) --- */
        .tabs-container {
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 2px solid #edf2f7;
            margin-bottom: 25px;
            overflow-x: auto;
            padding-bottom: 2px;
        }

        .tab-btn {
            background: none;
            border: none;
            padding: 12px 22px;
            font-family: 'Outfit', sans-serif;
            font-size: 14.5px;
            font-weight: 700;
            color: var(--color-texto-claro);
            cursor: pointer;
            border-radius: 10px 10px 0 0;
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .tab-btn:hover {
            color: var(--color-azul);
            background-color: rgba(0, 45, 114, 0.03);
        }

        .tab-btn.active {
            color: var(--color-terracota);
        }

        .tab-btn.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 3.5px;
            background-color: var(--color-terracota);
            border-radius: 3px 3px 0 0;
        }

        .tab-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 2px 9px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            background-color: #edf2f7;
            color: var(--color-texto-secundario);
            transition: all 0.2s ease;
        }

        .tab-btn.active .tab-badge {
            background-color: rgba(185, 71, 0, 0.12);
            color: var(--color-terracota);
        }

        /* --- BARRA DE FILTROS INTERACTIVA --- */
        .filters-container {
            background-color: #f8fafc;
            border: 1.5px solid #edf2f7;
            border-radius: 14px;
            padding: 20px;
            margin-bottom: 25px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .filters-top-row {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .search-box-wrapper {
            position: relative;
            flex: 1;
            min-width: 280px;
        }

        .search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            color: var(--color-texto-claro);
            pointer-events: none;
        }

        .search-input {
            width: 100%;
            padding: 12px 16px 12px 44px;
            border: 1.5px solid #e2e8f0;
            border-radius: var(--border-radius-input);
            font-family: 'Inter', sans-serif;
            font-size: 13.5px;
            background-color: #ffffff;
            color: var(--color-texto-principal);
            outline: none;
            transition: var(--transition-smooth);
        }

        .search-input:focus {
            border-color: var(--color-azul);
            box-shadow: 0 0 0 3px rgba(0, 45, 114, 0.1);
        }

        .dropdown-filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
        }

        .filter-select {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: var(--border-radius-input);
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            font-weight: 500;
            background-color: #ffffff;
            color: var(--color-texto-principal);
            outline: none;
            cursor: pointer;
            transition: var(--transition-smooth);
        }

        .filter-select:focus {
            border-color: var(--color-azul);
            box-shadow: 0 0 0 3px rgba(0, 45, 114, 0.1);
        }

        .filters-status-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13px;
            color: var(--color-texto-claro);
            padding-top: 4px;
        }

        .results-counter {
            font-weight: 600;
            color: var(--color-texto-secundario);
        }

        .btn-clear-filters {
            background: none;
            border: none;
            color: var(--color-terracota);
            font-family: 'Outfit', sans-serif;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: underline;
            padding: 2px 6px;
            transition: color 0.2s ease;
        }

        .btn-clear-filters:hover {
            color: #903700;
        }

        /* --- BARRA FLOTANTE DE ACCIONES PARA ELEMENTOS SELECCIONADOS --- */
        .selection-toolbar {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%) translateY(120px);
            background-color: #0b2341;
            color: #ffffff;
            padding: 12px 24px;
            border-radius: 30px;
            display: flex;
            align-items: center;
            gap: 18px;
            box-shadow: 0 15px 35px rgba(0, 20, 50, 0.35);
            z-index: 1050;
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .selection-toolbar.active {
            transform: translateX(-50%) translateY(0);
            opacity: 1;
        }

        .selection-count-badge {
            font-family: 'Outfit', sans-serif;
            font-size: 13.5px;
            font-weight: 700;
            color: #f3b228;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-toolbar-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 16px;
            border-radius: 20px;
            font-family: 'Outfit', sans-serif;
            font-size: 12.5px;
            font-weight: 700;
            text-transform: uppercase;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
        }

        .btn-toolbar-zip {
            background-color: var(--color-terracota);
            color: #ffffff;
        }

        .btn-toolbar-zip:hover {
            background-color: #d9531e;
            transform: scale(1.03);
        }

        .btn-toolbar-pdf {
            background-color: #ffffff;
            color: var(--color-azul);
        }

        .btn-toolbar-pdf:hover {
            background-color: #f1f5f9;
            transform: scale(1.03);
        }

        .btn-toolbar-clear {
            background: none;
            color: #94a3b8;
            font-size: 12px;
            text-decoration: underline;
            padding: 4px;
        }

        .btn-toolbar-clear:hover {
            color: #ffffff;
        }

        /* --- TABLAS CUSTOM --- */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            border-radius: 12px;
            border: 1px solid #edf2f7;
        }

        .custom-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 13.5px;
            background-color: #ffffff;
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

        .chk-cell {
            width: 38px;
            text-align: center;
        }

        .custom-checkbox {
            width: 17px;
            height: 17px;
            accent-color: var(--color-terracota);
            cursor: pointer;
        }

        .td-num {
            font-weight: 700;
            color: var(--color-texto-claro);
            width: 45px;
        }

        /* Bloque de Usuario (Docente / Coordinador) */
        .user-cell-block {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-mini-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            font-weight: 700;
            color: #ffffff;
            flex-shrink: 0;
        }

        .avatar-docente {
            background: linear-gradient(135deg, var(--color-azul), #1e40af);
        }

        .avatar-coord {
            background: linear-gradient(135deg, var(--color-oro), var(--color-terracota));
        }

        .user-info-text {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-weight: 600;
            color: var(--color-texto-principal);
        }

        .user-email {
            font-size: 11.5px;
            color: var(--color-texto-claro);
        }

        /* Badges de Estado y Categorías */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 9px;
            border-radius: 8px;
            font-size: 11.5px;
            font-weight: 600;
            letter-spacing: 0.02em;
            white-space: nowrap;
        }

        .badge-type-docente {
            background-color: rgba(0, 45, 114, 0.08);
            color: var(--color-azul);
            border: 1px solid rgba(0, 45, 114, 0.2);
        }

        .badge-type-coord {
            background-color: rgba(185, 71, 0, 0.08);
            color: var(--color-terracota);
            border: 1px solid rgba(185, 71, 0, 0.2);
        }

        .badge-area {
            background-color: rgba(172, 132, 0, 0.09);
            color: #876700;
            font-weight: 600;
        }

        .badge-carrera {
            background-color: #f1f5f9;
            color: var(--color-texto-secundario);
        }

        .badge-seccion {
            background-color: #e2e8f0;
            color: var(--color-texto-principal);
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 6px;
        }

        .badge-count {
            background-color: rgba(0, 45, 114, 0.06);
            color: var(--color-azul);
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 12px;
        }

        /* Botones de Acción en Tablas */
        .actions-cell {
            display: flex;
            align-items: center;
            gap: 8px;
            justify-content: flex-end;
        }

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

        .action-btn.view-btn {
            color: var(--color-azul);
            background-color: rgba(0, 45, 114, 0.08);
        }

        .action-btn.view-btn:hover {
            background-color: var(--color-azul);
            color: #ffffff;
            transform: scale(1.06);
        }

        .action-btn.delete-btn {
            color: #ef4444;
            background-color: #fee2e2;
        }

        .action-btn.delete-btn:hover {
            background-color: #ef4444;
            color: #ffffff;
            transform: scale(1.06);
        }

        .no-records-row {
            text-align: center;
            padding: 45px 20px !important;
            color: var(--color-texto-claro);
            font-style: italic;
        }

        /* --- MODAL GENERAL / OVERLAY --- */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 20, 50, 0.4);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1100;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            padding: 20px;
        }

        .modal-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        /* --- MODAL DE DESCARGA MASIVA POR MES --- */
        .bulk-download-card {
            background-color: #ffffff;
            border-radius: var(--border-radius-card);
            border: 2px solid transparent;
            background-image: linear-gradient(white, white),
                linear-gradient(135deg, var(--color-terracota), var(--color-oro), var(--color-azul));
            background-origin: border-box;
            background-clip: padding-box, border-box;
            max-width: 620px;
            width: 100%;
            box-shadow: 0 25px 60px rgba(0, 45, 114, 0.2);
            transform: scale(0.92);
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .modal-overlay.active .bulk-download-card {
            transform: scale(1);
        }

        .bulk-modal-header {
            padding: 22px 28px;
            background-color: #fafbfc;
            border-bottom: 1.5px solid #edf2f7;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .bulk-modal-title {
            font-family: 'Outfit', sans-serif;
            font-size: 18px;
            font-weight: 800;
            color: var(--color-terracota);
            text-transform: uppercase;
            letter-spacing: 0.03em;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-close-icon-btn {
            background-color: #f1f5f9;
            border: 1.5px solid #e2e8f0;
            color: #64748b;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            outline: none;
            padding: 0;
            flex-shrink: 0;
        }

        .modal-close-icon-btn:hover {
            background-color: #fee2e2;
            color: #dc2626;
            border-color: #fca5a5;
            transform: scale(1.08);
        }

        .bulk-modal-body {
            padding: 28px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-grid-2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-label {
            font-family: 'Outfit', sans-serif;
            font-size: 12.5px;
            font-weight: 700;
            color: var(--color-texto-principal);
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }

        .form-control {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid #cbd5e1;
            border-radius: var(--border-radius-input);
            font-family: 'Inter', sans-serif;
            font-size: 13.5px;
            background-color: #ffffff;
            color: var(--color-texto-principal);
            outline: none;
            transition: var(--transition-smooth);
        }

        .form-control:focus {
            border-color: var(--color-terracota);
            box-shadow: 0 0 0 3px rgba(185, 71, 0, 0.12);
        }

        .bulk-info-banner {
            background-color: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-left: 4px solid var(--color-terracota);
            padding: 14px 18px;
            border-radius: 10px;
            font-size: 13px;
            color: var(--color-texto-secundario);
            line-height: 1.5;
        }

        .bulk-actions-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 5px;
        }

        .btn-export-option {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 20px;
            border-radius: 12px;
            border: 1.5px solid #e2e8f0;
            background-color: #ffffff;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-export-option:hover {
            border-color: var(--color-terracota);
            background-color: rgba(185, 71, 0, 0.03);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(185, 71, 0, 0.08);
        }

        .export-opt-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .export-opt-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .opt-zip .export-opt-icon {
            background-color: rgba(185, 71, 0, 0.1);
            color: var(--color-terracota);
        }

        .opt-pdf .export-opt-icon {
            background-color: rgba(0, 45, 114, 0.1);
            color: var(--color-azul);
        }

        .opt-csv .export-opt-icon {
            background-color: rgba(56, 161, 105, 0.1);
            color: #38a169;
        }

        .export-opt-text {
            display: flex;
            flex-direction: column;
            text-align: left;
        }

        .export-opt-title {
            font-family: 'Outfit', sans-serif;
            font-size: 14.5px;
            font-weight: 700;
            color: var(--color-texto-principal);
        }

        .export-opt-desc {
            font-size: 12px;
            color: var(--color-texto-claro);
        }

        /* --- MODAL SUPERPUESTO DE VISUALIZACIÓN DE INFORME OFICIAL (LIGHTBOX IFRAME) --- */
        .report-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.28s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .report-modal-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        .report-modal-window {
            background-color: #ffffff;
            border-radius: 18px;
            width: 100%;
            max-width: 1100px;
            height: 94vh;
            max-height: 980px;
            display: flex;
            flex-direction: column;
            box-shadow: 0 30px 70px -10px rgba(0, 0, 0, 0.45), 0 0 0 1px rgba(255, 255, 255, 0.15);
            transform: scale(0.95) translateY(12px);
            transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1);
            overflow: hidden;
        }

        .report-modal-overlay.active .report-modal-window {
            transform: scale(1) translateY(0);
        }

        .report-modal-header {
            padding: 14px 24px;
            background: #ffffff;
            border-bottom: 1.5px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-shrink: 0;
        }

        .report-modal-header-left {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }

        .report-badge-type {
            font-family: 'Outfit', sans-serif;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 5px 12px;
            border-radius: 8px;
            flex-shrink: 0;
        }

        .report-badge-docente {
            background-color: #eff6ff;
            color: #1e40af;
            border: 1.5px solid #bfdbfe;
        }

        .report-badge-coord {
            background-color: #fff7ed;
            color: #9a3412;
            border: 1.5px solid #fed7aa;
        }

        .report-modal-title {
            font-family: 'Outfit', sans-serif;
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 500px;
        }

        .report-modal-subtitle {
            font-size: 12px;
            color: #64748b;
            margin: 2px 0 0 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 500px;
        }

        .report-modal-header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .report-btn-tool {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 16px;
            border-radius: 9px;
            font-family: 'Outfit', sans-serif;
            font-size: 12.5px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
            border: 1.5px solid transparent;
        }

        .btn-tool-print {
            background-color: var(--color-azul);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 45, 114, 0.18);
        }

        .btn-tool-print:hover {
            background-color: #003B95;
            transform: translateY(-1px);
        }

        .btn-tool-tab {
            background-color: #f1f5f9;
            color: #334155;
            border-color: #cbd5e1;
        }

        .btn-tool-tab:hover {
            background-color: #e2e8f0;
            color: #0f172a;
        }

        .report-btn-close {
            background-color: #f1f5f9;
            border: 1.5px solid #e2e8f0;
            color: #64748b;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            outline: none;
            padding: 0;
            flex-shrink: 0;
        }

        .report-btn-close:hover {
            background-color: #fee2e2;
            color: #dc2626;
            border-color: #fca5a5;
            transform: scale(1.08) rotate(90deg);
        }

        .report-modal-body {
            flex: 1;
            position: relative;
            width: 100%;
            height: 100%;
            background-color: #f8fafc;
            overflow: hidden;
        }

        .report-preview-iframe {
            width: 100%;
            height: 100%;
            border: none;
            display: block;
            background-color: #f8fafc;
        }

        .report-modal-loader {
            position: absolute;
            inset: 0;
            background-color: rgba(248, 250, 252, 0.92);
            backdrop-filter: blur(4px);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 14px;
            z-index: 10;
            transition: opacity 0.25s ease;
        }

        .report-loader-spinner {
            width: 42px;
            height: 42px;
            border: 3.5px solid #e2e8f0;
            border-top-color: var(--color-azul);
            border-right-color: var(--color-terracota);
            border-radius: 50%;
            animation: spinReport 0.8s linear infinite;
        }

        .report-loader-text {
            font-family: 'Outfit', sans-serif;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--color-texto-secundario);
        }

        @keyframes spinReport {
            to {
                transform: rotate(360deg);
            }
        }

        /* --- MODAL CONFIRMAR ELIMINACIÓN --- */
        .confirm-delete-card {
            background-color: #ffffff;
            border-radius: var(--border-radius-card);
            border: 2.5px solid #e53e3e;
            padding: 35px 40px;
            max-width: 480px;
            width: 90%;
            box-shadow: 0 20px 45px rgba(229, 62, 62, 0.15);
            text-align: center;
            position: relative;
            transform: scale(0.9);
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .modal-overlay.active .confirm-delete-card {
            transform: scale(1);
        }

        .warning-icon {
            font-size: 52px;
            color: #d69e2e;
            margin-bottom: 12px;
            line-height: 1;
        }

        .alert-title {
            font-family: 'Outfit', sans-serif;
            font-size: 16px;
            font-weight: 800;
            color: #e53e3e;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 12px;
            line-height: 1.4;
        }

        .alert-desc {
            font-size: 13.5px;
            color: #718096;
            line-height: 1.5;
            font-weight: 500;
            margin-bottom: 25px;
        }

        .confirm-actions-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
        }

        .btn-confirm-delete {
            background-color: #ffffff;
            border: 2px solid #ef4444;
            color: #dc2626;
            padding: 10px 24px;
            border-radius: 20px;
            font-family: 'Outfit', sans-serif;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-confirm-delete:hover {
            background-color: #ef4444;
            color: #ffffff;
        }

        .btn-cancel-delete {
            background-color: #ffffff;
            border: 2px solid #cbd5e1;
            color: #475569;
            padding: 10px 24px;
            border-radius: 20px;
            font-family: 'Outfit', sans-serif;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-cancel-delete:hover {
            background-color: #f1f5f9;
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
            z-index: 1200;
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

        /* --- ESTILOS MODAL PROGRESO ZIP EN NAVEGADOR --- */
        .zip-spinner {
            width: 24px;
            height: 24px;
            border: 3px solid rgba(0, 45, 114, 0.15);
            border-top-color: var(--color-azul);
            border-right-color: var(--color-terracota);
            border-radius: 50%;
            animation: spinZip 0.75s linear infinite;
            flex-shrink: 0;
        }

        @keyframes spinZip {
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
            <button type="button" class="hamburger-btn" id="hamburgerBtn" title="Menú de Navegación">
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

    <!-- Contenedor general del layout (Sidebar + Contenido) -->
    <div class="app-container">

        <!-- Menú Lateral (Sidebar) -->
        <aside class="admin-sidebar collapsed" id="adminSidebar">
            <nav class="sidebar-nav">
                <!-- Enlace Inicio -->
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link sidebar-link-inicio">
                    <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Inicio</span>
                </a>

                <!-- Enlace Usuarios -->
                <a href="{{ route('admin.usuarios') }}" class="sidebar-link sidebar-link-usuarios">
                    <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Usuarios</span>
                </a>

                <!-- Enlace Cursos -->
                <a href="{{ route('admin.cursos') }}" class="sidebar-link sidebar-link-cursos">
                    <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span>Cursos</span>
                </a>

                <!-- Enlace Informes (Activo) -->
                <a href="{{ route('admin.informes') }}" class="sidebar-link sidebar-link-informes active">
                    <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Informes</span>
                </a>

                <!-- Enlace Carga de datos -->
                <a href="{{ route('admin.carga-datos') }}" class="sidebar-link sidebar-link-carga">
                    <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                    </svg>
                    <span>Carga de datos</span>
                </a>
            </nav>
        </aside>

        <!-- Área de Contenido Principal -->
        <main class="main-content-area" id="mainContent">

            <!-- Fila de Tarjetas KPI / Métricas -->
            <div class="kpi-grid">
                <!-- Total Informes -->
                <div class="kpi-card kpi-total">
                    <div class="kpi-info">
                        <span class="kpi-label">Total Informes</span>
                        <span class="kpi-value">{{ $stats['total'] }}</span>
                        <span class="kpi-subtext">Registros globales</span>
                    </div>
                    <div class="kpi-icon-wrap">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>

                <!-- Informes Docentes -->
                <div class="kpi-card kpi-docentes">
                    <div class="kpi-info">
                        <span class="kpi-label">De Docentes</span>
                        <span class="kpi-value">{{ $stats['docentes'] }}</span>
                        <span class="kpi-subtext">{{ $stats['docentes_unicos'] }} docentes activos</span>
                    </div>
                    <div class="kpi-icon-wrap">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                        </svg>
                    </div>
                </div>

                <!-- Informes Coordinación -->
                <div class="kpi-card kpi-coordinacion">
                    <div class="kpi-info">
                        <span class="kpi-label">De Coordinación</span>
                        <span class="kpi-value">{{ $stats['coordinacion'] }}</span>
                        <span class="kpi-subtext">{{ $stats['coordinadores_unicos'] }} coordinadores</span>
                    </div>
                    <div class="kpi-icon-wrap">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                </div>

                <!-- Usuarios Participantes -->
                <div class="kpi-card kpi-participantes">
                    <div class="kpi-info">
                        <span class="kpi-label">Cuentas Activas</span>
                        <span class="kpi-value">{{ $stats['docentes_unicos'] + $stats['coordinadores_unicos'] }}</span>
                        <span class="kpi-subtext">Con informes entregados</span>
                    </div>
                    <div class="kpi-icon-wrap">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Tarjeta Contenedora Principal -->
            <div class="content-card">

                <div class="content-header-row">
                    <div class="content-title-block">
                        <h2 class="content-header-title">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Historial y Descarga de Informes
                        </h2>
                        <p class="content-header-subtitle">
                            Visualización, consulta y descarga masiva de informes de Docentes y Coordinadores para
                            resguardo en Drive.
                        </p>
                    </div>

                    <!-- Botón de Descarga Masiva por Mes -->
                    <button type="button" class="btn-bulk-download" onclick="openBulkDownloadModal()">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <span>Descargar Informes</span>
                    </button>
                </div>

                <!-- Pestañas de Selección (Tabs) -->
                <div class="tabs-container">
                    <button type="button" class="tab-btn active" id="tabBtnDocentes" onclick="switchTab('docentes')">
                        <span>Informes de Docentes</span>
                        <span class="tab-badge" id="tabCountDocentes">{{ $informesDocentes->count() }}</span>
                    </button>
                    <button type="button" class="tab-btn" id="tabBtnCoordinacion" onclick="switchTab('coordinacion')">
                        <span>Informes de Coordinación de Área</span>
                        <span class="tab-badge" id="tabCountCoordinacion">{{ $informesCoordinacion->count() }}</span>
                    </button>
                </div>

                <!-- Barra de Filtros Interactiva -->
                <div class="filters-container">
                    <div class="filters-top-row">
                        <!-- Búsqueda en Vivo -->
                        <div class="search-box-wrapper">
                            <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input type="text" id="searchInput" class="search-input"
                                placeholder="Buscar por docente, coordinador, curso, código o correo..."
                                oninput="applyFilters()">
                        </div>
                    </div>

                    <!-- Dropdowns de Filtrado -->
                    <div class="dropdown-filters-grid">
                        <!-- Filtro Carrera -->
                        <select id="filterCarrera" class="filter-select" onchange="applyFilters()">
                            <option value="">Todas las Carreras</option>
                            @foreach ($carreras as $carr)
                                <option value="{{ $carr }}">{{ $carr }}</option>
                            @endforeach
                        </select>

                        <!-- Filtro Área -->
                        <select id="filterArea" class="filter-select" onchange="applyFilters()">
                            <option value="">Todas las Áreas</option>
                            @foreach ($areas as $a)
                                <option value="{{ $a }}">{{ $a }}</option>
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
                            @foreach ($anios as $yr)
                                <option value="{{ $yr }}">{{ $yr }}</option>
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

                    <!-- Barra de Estado y Limpieza -->
                    <div class="filters-status-row">
                        <span class="results-counter" id="resultsCounterText">Cargando informes...</span>
                        <button type="button" class="btn-clear-filters" onclick="clearFilters()">Limpiar
                            filtros</button>
                    </div>
                </div>

                <!-- 1. TABLA: INFORMES DE DOCENTES -->
                <div id="sectionDocentes" class="tab-content-section">
                    <div class="table-responsive">
                        <table class="custom-table" id="tablaDocentes">
                            <thead>
                                <tr>
                                    <th class="chk-cell">
                                        <input type="checkbox" id="chkSelectAllDocentes" class="custom-checkbox"
                                            onchange="toggleSelectAll('docente')">
                                    </th>
                                    <th style="width: 45px;">No.</th>
                                    <th>Docente</th>
                                    <th>Curso & Sección</th>
                                    <th>Carrera & Área</th>
                                    <th>Año / Periodo</th>
                                    <th>Mes</th>
                                    <th style="text-align: center;">Estudiantes</th>
                                    <th style="text-align: right;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyDocentes">
                                @forelse ($informesDocentes as $index => $inf)
                                    @php
                                        $docenteNombre = $inf->usuario->nombre ?? 'Docente no asignado';
                                        $docenteCorreo = $inf->usuario->correo ?? '';
                                        $cursoNombre = $inf->curso->nombre_curso ?? 'Sin curso';
                                        $cursoCodigo = $inf->curso->codigo_curso ?? '—';
                                        $cursoSeccion = $inf->curso->seccion ?? '—';
                                        $cursoCarrera = $inf->curso->carrera ?? '';
                                        $cursoArea = $inf->curso->area ?? '';
                                        $anioVal = $inf->curso->anio ?? date('Y', strtotime($inf->created_at));

                                        $periodoClean = trim(preg_replace('/\d{4}/', '', $inf->periodo));
                                        if (!$periodoClean) {
                                            $periodoClean = $inf->periodo;
                                        }
                                    @endphp
                                    <tr class="informe-docente-row" id="row-docente-{{ $inf->id }}" data-tipo="docente"
                                        data-nombre="{{ strtolower($docenteNombre) }}"
                                        data-correo="{{ strtolower($docenteCorreo) }}"
                                        data-curso="{{ strtolower($cursoNombre) }}"
                                        data-codigo="{{ strtolower($cursoCodigo) }}"
                                        data-carrera="{{ strtolower($cursoCarrera) }}"
                                        data-area="{{ strtolower($cursoArea) }}"
                                        data-periodo="{{ strtolower($periodoClean) }}" data-anio="{{ $anioVal }}"
                                        data-mes="{{ strtolower($inf->mes) }}">

                                        <td class="chk-cell">
                                            <input type="checkbox" class="custom-checkbox chk-item chk-docente"
                                                value="{{ $inf->id }}" onchange="updateSelectionState()">
                                        </td>
                                        <td class="td-num seq-num">{{ $index + 1 }}.</td>

                                        <!-- Docente Info -->
                                        <td>
                                            <div class="user-cell-block">
                                                <div class="user-mini-avatar avatar-docente">
                                                    {{ strtoupper(substr($docenteNombre, 0, 1)) }}
                                                </div>
                                                <div class="user-info-text">
                                                    <span class="user-name">{{ $docenteNombre }}</span>
                                                    <span class="user-email">{{ $docenteCorreo }}</span>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Curso & Sección -->
                                        <td>
                                            <div style="display: flex; flex-direction: column; gap: 3px;">
                                                <span
                                                    style="font-weight: 600; color: var(--color-texto-principal);">{{ $cursoNombre }}</span>
                                                <div style="display: flex; align-items: center; gap: 6px;">
                                                    <span
                                                        style="font-size: 11px; font-family: monospace; color: var(--color-texto-claro);">Cód:
                                                        {{ $cursoCodigo }}</span>
                                                    <span class="badge badge-seccion">Sec. {{ $cursoSeccion }}</span>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Carrera & Área -->
                                        <td>
                                            <div
                                                style="display: flex; flex-direction: column; gap: 4px; align-items: flex-start;">
                                                @if($cursoCarrera)
                                                    <span class="badge badge-carrera">{{ $cursoCarrera }}</span>
                                                @endif
                                                @if($cursoArea)
                                                    <span class="badge badge-area">{{ $cursoArea }}</span>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Año y Periodo -->
                                        <td>
                                            <div style="display: flex; flex-direction: column; gap: 2px;">
                                                <span
                                                    style="font-weight: 700; color: var(--color-azul);">{{ $anioVal }}</span>
                                                <span
                                                    style="font-size: 12px; color: var(--color-texto-secundario);">{{ $periodoClean }}</span>
                                            </div>
                                        </td>

                                        <!-- Mes -->
                                        <td>
                                            <span style="font-weight: 600;">{{ $inf->mes }}</span>
                                        </td>

                                        <!-- Estudiantes Asignados -->
                                        <td style="text-align: center;">
                                            <span class="badge badge-count">{{ $inf->estudiantes_asignados ?? 0 }}</span>
                                        </td>

                                        <!-- Acciones -->
                                        <td>
                                            <div class="actions-cell">
                                                <!-- Visualizar Informe (Ventana Superpuesta) -->
                                                <button type="button" class="action-btn view-btn"
                                                    title="Visualizar informe oficial"
                                                    onclick="openReportPreviewModal('docente', {{ $inf->id }}, '{{ addslashes($cursoNombre) }} - Sec. {{ addslashes($cursoSeccion) }}', '{{ addslashes($docenteNombre) }} • {{ addslashes($inf->mes) }} {{ addslashes($anioVal) }}')">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </button>

                                                <!-- Eliminar Informe -->
                                                <button type="button" class="action-btn delete-btn" title="Eliminar informe"
                                                    onclick="openDeleteModal('docente', {{ $inf->id }}, '{{ addslashes($cursoNombre) }} - {{ addslashes($docenteNombre) }}')">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="emptyDocentesRow">
                                        <td colspan="9" class="no-records-row">
                                            No se han registrado informes de docentes todavía.
                                        </td>
                                    </tr>
                                @endforelse

                                <tr id="noResultsDocentesRow" style="display: none;">
                                    <td colspan="9" class="no-records-row">
                                        No se encontraron informes de docentes con los filtros aplicados.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 2. TABLA: INFORMES DE COORDINACIÓN -->
                <div id="sectionCoordinacion" class="tab-content-section" style="display: none;">
                    <div class="table-responsive">
                        <table class="custom-table" id="tablaCoordinacion">
                            <thead>
                                <tr>
                                    <th class="chk-cell">
                                        <input type="checkbox" id="chkSelectAllCoord" class="custom-checkbox"
                                            onchange="toggleSelectAll('coordinacion')">
                                    </th>
                                    <th style="width: 45px;">No.</th>
                                    <th>Coordinador(a)</th>
                                    <th>Área de Coordinación</th>
                                    <th>Año / Periodo</th>
                                    <th>Mes</th>
                                    <th>Resumen de Supervisión</th>
                                    <th style="text-align: right;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyCoordinacion">
                                @forelse ($informesCoordinacion as $index => $infC)
                                    @php
                                        $coordNombre = $infC->usuario->nombre ?? 'Coordinador no asignado';
                                        $coordCorreo = $infC->usuario->correo ?? '';
                                        $areaCoord = $infC->area ?? 'General';
                                        $anioVal = $infC->anio ?? date('Y', strtotime($infC->created_at));

                                        $periodoClean = trim(preg_replace('/\d{4}/', '', $infC->periodo));
                                        if (!$periodoClean) {
                                            $periodoClean = $infC->periodo;
                                        }

                                        $countProg = $infC->programas ? $infC->programas->count() : 0;
                                        $countAsig = $infC->asignaturas ? $infC->asignaturas->count() : 0;
                                        $countAvance = $infC->avances ? $infC->avances->count() : 0;
                                        $countEst = $infC->estudiantes ? $infC->estudiantes->count() : 0;
                                    @endphp
                                    <tr class="informe-coordinacion-row" id="row-coordinacion-{{ $infC->id }}"
                                        data-tipo="coordinacion" data-nombre="{{ strtolower($coordNombre) }}"
                                        data-correo="{{ strtolower($coordCorreo) }}"
                                        data-area="{{ strtolower($areaCoord) }}"
                                        data-periodo="{{ strtolower($periodoClean) }}" data-anio="{{ $anioVal }}"
                                        data-mes="{{ strtolower($infC->mes) }}" data-carrera="">

                                        <td class="chk-cell">
                                            <input type="checkbox" class="custom-checkbox chk-item chk-coord"
                                                value="{{ $infC->id }}" onchange="updateSelectionState()">
                                        </td>
                                        <td class="td-num seq-num">{{ $index + 1 }}.</td>

                                        <!-- Coordinador Info -->
                                        <td>
                                            <div class="user-cell-block">
                                                <div class="user-mini-avatar avatar-coord">
                                                    {{ strtoupper(substr($coordNombre, 0, 1)) }}
                                                </div>
                                                <div class="user-info-text">
                                                    <span class="user-name">{{ $coordNombre }}</span>
                                                    <span class="user-email">{{ $coordCorreo }}</span>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Área de Coordinación -->
                                        <td>
                                            <span class="badge badge-area"
                                                style="font-size: 12.5px; padding: 4px 10px;">{{ $areaCoord }}</span>
                                        </td>

                                        <!-- Año y Periodo -->
                                        <td>
                                            <div style="display: flex; flex-direction: column; gap: 2px;">
                                                <span
                                                    style="font-weight: 700; color: var(--color-terracota);">{{ $anioVal }}</span>
                                                <span
                                                    style="font-size: 12px; color: var(--color-texto-secundario);">{{ $periodoClean }}</span>
                                            </div>
                                        </td>

                                        <!-- Mes -->
                                        <td>
                                            <span style="font-weight: 600;">{{ $infC->mes }}</span>
                                        </td>

                                        <!-- Resumen de Supervisión -->
                                        <td>
                                            <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                                                @if($countProg > 0)
                                                    <span class="badge badge-carrera"
                                                        title="Programas entregados">{{ $countProg }} prog.</span>
                                                @endif
                                                <span class="badge badge-type-docente"
                                                    title="Asignaturas revisadas">{{ $countAsig }} asig.</span>
                                                <span class="badge badge-type-coord"
                                                    title="Cursos con avance">{{ $countAvance }} avances</span>
                                                @if($countEst > 0)
                                                    <span class="badge" style="background-color: #fef3c7; color: #92400e;"
                                                        title="Cursos con estudiantes con problemas">{{ $countEst }} alertas
                                                        est.</span>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Acciones -->
                                        <td>
                                            <div class="actions-cell">
                                                <!-- Visualizar Informe (Ventana Superpuesta) -->
                                                <button type="button" class="action-btn view-btn"
                                                    title="Visualizar informe oficial"
                                                    onclick="openReportPreviewModal('coordinacion', {{ $infC->id }}, 'Área {{ addslashes($areaCoord) }} - {{ addslashes($infC->mes) }} {{ addslashes($anioVal) }}', '{{ addslashes($coordNombre) }} • {{ addslashes($infC->mes) }} {{ addslashes($anioVal) }}')">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </button>

                                                <!-- Eliminar Informe -->
                                                <button type="button" class="action-btn delete-btn" title="Eliminar informe"
                                                    onclick="openDeleteModal('coordinacion', {{ $infC->id }}, 'Coordinación {{ addslashes($areaCoord) }} - {{ addslashes($coordNombre) }}')">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="emptyCoordinacionRow">
                                        <td colspan="8" class="no-records-row">
                                            No se han registrado informes de coordinación todavía.
                                        </td>
                                    </tr>
                                @endforelse

                                <tr id="noResultsCoordinacionRow" style="display: none;">
                                    <td colspan="8" class="no-records-row">
                                        No se encontraron informes de coordinación con los filtros aplicados.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- BARRA FLOTANTE DE SELECCIÓN MÚLTIPLE -->
    <div class="selection-toolbar" id="selectionToolbar">
        <span class="selection-count-badge">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
            <span id="selectedCountText">0 seleccionados</span>
        </span>
        <button type="button" class="btn-toolbar-action btn-toolbar-zip" onclick="downloadSelectedZip()">
            Descargar ZIP
        </button>
        <button type="button" class="btn-toolbar-action btn-toolbar-pdf" onclick="viewSelectedPdf()">
            Ver PDF Consolidado
        </button>
        <button type="button" class="btn-toolbar-clear" onclick="clearSelection()">
            Deseleccionar
        </button>
    </div>

    <!-- MODAL DE DESCARGA MASIVA POR MES / FILTROS -->
    <div class="modal-overlay" id="bulkDownloadModal" onclick="closeBulkDownloadModal()">
        <div class="bulk-download-card" onclick="event.stopPropagation()">
            <div class="bulk-modal-header">
                <div class="bulk-modal-title">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Descarga Masiva de Informes
                </div>
                <button type="button" class="modal-close-icon-btn" onclick="closeBulkDownloadModal()"
                    title="Cerrar ventana (Esc)">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <div class="bulk-modal-body">
                <div class="bulk-info-banner">
                    Selecciona el <strong>mes y año</strong> para empaquetar y descargar los informes en un archivo
                    <strong>ZIP</strong> con sus archivos en formato <strong>PDF</strong> organizados por:<br>
                </div>

                <div class="form-grid-2">
                    <!-- Selector de Mes -->
                    <div class="form-group">
                        <label class="form-label" for="bulkMes">Mes a Descargar</label>
                        <select id="bulkMes" class="form-control" onchange="updateBulkPreviewCount()">
                            <option value="">Todos los Meses</option>
                            @foreach (['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'] as $m)
                                <option value="{{ $m }}" {{ $m === 'Septiembre' ? 'selected' : '' }}>{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Selector de Año -->
                    <div class="form-group">
                        <label class="form-label" for="bulkAnio">Año</label>
                        <select id="bulkAnio" class="form-control" onchange="updateBulkPreviewCount()">
                            <option value="">Todos los Años</option>
                            @foreach ($anios as $yr)
                                <option value="{{ $yr }}" {{ $yr == date('Y') ? 'selected' : '' }}>{{ $yr }}</option>
                            @endforeach
                            @if($anios->isEmpty())
                                <option value="{{ date('Y') }}" selected>{{ date('Y') }}</option>
                            @endif
                        </select>
                    </div>
                </div>

                <div class="form-grid-2">
                    <!-- Tipo de Informes -->
                    <div class="form-group">
                        <label class="form-label" for="bulkTipo">Tipo de Informes</label>
                        <select id="bulkTipo" class="form-control" onchange="updateBulkPreviewCount()">
                            <option value="todos">Todos (Docentes y Coordinación)</option>
                            <option value="docente">Solo Informes de Docentes</option>
                            <option value="coordinacion">Solo Informes de Coordinación</option>
                        </select>
                    </div>

                    <!-- Carrera Opcional -->
                    <div class="form-group">
                        <label class="form-label" for="bulkCarrera">Carrera (Opcional)</label>
                        <select id="bulkCarrera" class="form-control" onchange="updateBulkPreviewCount()">
                            <option value="">Todas las Carreras</option>
                            @foreach ($carreras as $carr)
                                <option value="{{ $carr }}">{{ $carr }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Opciones de Exportación -->
                <div class="bulk-actions-group">
                    <!-- Opción 1: ZIP Completo -->
                    <a href="#" id="btnBulkZip" class="btn-export-option opt-zip"
                        style="border-color: var(--color-terracota); background-color: rgba(185, 71, 0, 0.04);"
                        onclick="triggerBulkZip(event)">
                        <div class="export-opt-left">
                            <div class="export-opt-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                            </div>
                            <div class="export-opt-text">
                                <span class="export-opt-title" style="color: var(--color-terracota);">Descargar ZIP con
                                    Archivos PDF</span>
                                <span class="export-opt-desc">Carpetas estructuradas: <code>Año/Mes/Docente</code> o
                                    <code>Año/Mes/Coordinador</code> con PDFs listos para Drive</span>
                            </div>
                        </div>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </a>

                    <!-- Opción 2: PDF Consolidado del Mes -->
                    <a href="#" id="btnBulkPdf" target="_blank" class="btn-export-option opt-pdf"
                        onclick="triggerBulkPdf(event)">
                        <div class="export-opt-left">
                            <div class="export-opt-icon">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                            </div>
                            <div class="export-opt-text">
                                <span class="export-opt-title">Abrir Todo el Mes en PDF Consolidado</span>
                                <span class="export-opt-desc">Vista continua lista para guardar/imprimir todos los
                                    informes del mes en un único archivo PDF</span>
                            </div>
                        </div>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </a>

                    <!-- Opción 3: Hoja de Resumen Excel / CSV -->
                    <a href="#" id="btnBulkCsv" class="btn-export-option opt-csv" onclick="triggerBulkCsv(event)">
                        <div class="export-opt-left">
                            <div class="export-opt-icon">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="export-opt-text">
                                <span class="export-opt-title">Descargar Planilla de Control (.csv)</span>
                                <span class="export-opt-desc">Matriz con todos los docentes, entregas, estudiantes y
                                    enlaces de Drive y Meet</span>
                            </div>
                        </div>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL DE CONFIRMACIÓN DE ELIMINACIÓN -->
    <div class="modal-overlay" id="deleteConfirmModal" onclick="closeDeleteModal()">
        <div class="confirm-delete-card" onclick="event.stopPropagation()">
            <div class="warning-icon">⚠</div>
            <h3 class="alert-title">¿ESTÁ SEGURO DE ELIMINAR ESTE INFORME?</h3>
            <p class="alert-desc" id="deleteModalMessage">
                Esta acción es permanente y eliminará todas las actividades y registros asociados al informe
                seleccionado.
            </p>
            <div class="confirm-actions-row">
                <button type="button" class="btn-confirm-delete" id="btnExecuteDelete"
                    onclick="executeDelete()">CONFIRMAR ELIMINACIÓN</button>
                <button type="button" class="btn-cancel-delete" onclick="closeDeleteModal()">CANCELAR</button>
            </div>
        </div>
    </div>

    <!-- MODAL SUPERPUESTO DE VISUALIZACIÓN DE INFORME OFICIAL (LIGHTBOX IFRAME) -->
    <div class="report-modal-overlay" id="reportPreviewModal" onclick="closeReportPreviewModal()">
        <div class="report-modal-window" onclick="event.stopPropagation()">
            <!-- Header con info y herramientas -->
            <div class="report-modal-header">
                <div class="report-modal-header-left">
                    <span class="report-badge-type report-badge-docente" id="previewModalBadge">Docente</span>
                    <div>
                        <h3 class="report-modal-title" id="previewModalTitle">Cargando informe...</h3>
                        <p class="report-modal-subtitle" id="previewModalSubtitle">FARUSAC • Control Académico</p>
                    </div>
                </div>
                <div class="report-modal-header-actions">
                    <button type="button" class="report-btn-tool btn-tool-print" onclick="printReportIframe()"
                        title="Imprimir o Exportar PDF">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.2">
                            <polyline points="6 9 6 2 18 2 18 9"></polyline>
                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                            <rect x="6" y="14" width="12" height="8"></rect>
                        </svg>
                        <span>Imprimir / PDF</span>
                    </button>
                    <a href="#" id="previewModalFullTabBtn" target="_blank" class="report-btn-tool btn-tool-tab"
                        title="Abrir en pestaña completa del navegador">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.2">
                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                            <polyline points="15 3 21 3 21 9"></polyline>
                            <line x1="10" y1="14" x2="21" y2="3"></line>
                        </svg>
                        <span>Pestaña</span>
                    </a>
                    <button type="button" class="report-btn-close" onclick="closeReportPreviewModal()"
                        title="Cerrar ventana (Esc)">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Iframe Container y Loader -->
            <div class="report-modal-body">
                <div class="report-modal-loader" id="previewIframeLoader">
                    <div class="report-loader-spinner"></div>
                    <span class="report-loader-text">Cargando informe institucional oficial...</span>
                </div>
                <iframe id="reportPreviewIframe" class="report-preview-iframe" src="about:blank"
                    onload="hidePreviewLoader()"></iframe>
            </div>
        </div>
    </div>

    <!-- MODAL DE PROGRESO DE GENERACIÓN ZIP EN NAVEGADOR -->
    <div class="modal-overlay" id="zipProgressModal" style="z-index: 12000;">
        <div class="bulk-download-card" style="max-width: 550px;" onclick="event.stopPropagation()">
            <div class="bulk-modal-header" style="background: linear-gradient(135deg, rgba(185, 71, 0, 0.05), rgba(0, 45, 114, 0.05));">
                <div class="bulk-modal-title" style="color: var(--color-azul);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span>Generando Archivo ZIP (PDFs Oficiales)</span>
                </div>
                <button type="button" class="modal-close-icon-btn" id="btnCancelZipProgress" onclick="cancelZipGeneration()" title="Cancelar proceso">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <div class="bulk-modal-body" style="padding: 24px; text-align: center;">
                <div style="display: flex; align-items: center; justify-content: center; gap: 12px; margin-bottom: 6px;">
                    <div class="zip-spinner" id="zipSpinner"></div>
                    <span id="zipProgressStatusText" style="font-family: 'Outfit', sans-serif; font-size: 15.5px; font-weight: 700; color: var(--color-texto-principal);">Iniciando motor de renderizado...</span>
                </div>

                <div id="zipCurrentDocBox" style="background-color: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 12px 16px; margin: 12px 0; text-align: left;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 4px;">
                        <span id="zipDocBadge" class="badge badge-type-docente">Docente</span>
                        <span id="zipDocCounter" style="font-size: 12px; font-weight: 700; color: var(--color-terracota);">0 / 0</span>
                    </div>
                    <div id="zipDocTitle" style="font-family: 'Outfit', sans-serif; font-size: 13.5px; font-weight: 700; color: var(--color-texto-principal); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        Preparando informe...
                    </div>
                    <div id="zipDocPath" style="font-size: 11.5px; color: var(--color-texto-secundario); font-family: monospace; margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        ---
                    </div>
                </div>

                <!-- Barra de Progreso -->
                <div style="background-color: #e2e8f0; border-radius: 20px; height: 12px; overflow: hidden; width: 100%; position: relative; margin: 16px 0 8px 0;">
                    <div id="zipProgressBarFill" style="background: linear-gradient(90deg, var(--color-terracota), var(--color-oro), var(--color-azul)); height: 100%; width: 0%; border-radius: 20px; transition: width 0.3s ease;"></div>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 12px; font-weight: 600; color: var(--color-texto-secundario);">
                    <span>Progreso total</span>
                    <span id="zipProgressPercentText" style="color: var(--color-azul); font-weight: 700;">0%</span>
                </div>

                <div class="bulk-info-banner" style="margin-top: 16px; font-size: 11.5px; text-align: left; border-left-color: var(--color-azul); line-height: 1.45;">
                    Cada PDF se renderiza con <strong>fidelidad visual 100% idéntica</strong> al formato oficial de la FARUSAC en tu navegador. Por favor, mantén esta pestaña abierta mientras se completa la descarga.
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden worker iframe for offscreen PDF generation -->
    <iframe id="zipWorkerIframe" style="position: fixed; left: -99999px; top: -99999px; width: 1050px; height: 1400px; border: none; opacity: 0; pointer-events: none;" src="about:blank"></iframe>

    <!-- FORMULARIO OCULTO PARA PETICIONES POST DE SELECCIONADOS -->
    <form id="formSeleccionadosPost" method="POST" target="_blank" style="display: none;">
        @csrf
        <input type="hidden" name="docente_ids" id="postDocenteIds">
        <input type="hidden" name="coordinacion_ids" id="postCoordIds">
    </form>

    <!-- TOAST NOTIFICATION -->
    <div class="toast-notification" id="toastNotification">
        <span id="toastMessage"></span>
    </div>

    <!-- JAVASCRIPT LOGIC -->
    <script>
        let currentTab = 'docentes';

        // 1. Cambio de Pestañas
        function switchTab(tab) {
            currentTab = tab;
            const tabBtnDoc = document.getElementById('tabBtnDocentes');
            const tabBtnCoord = document.getElementById('tabBtnCoordinacion');
            const secDoc = document.getElementById('sectionDocentes');
            const secCoord = document.getElementById('sectionCoordinacion');
            const filterCarrera = document.getElementById('filterCarrera');

            if (tab === 'docentes') {
                tabBtnDoc.classList.add('active');
                tabBtnCoord.classList.remove('active');
                secDoc.style.display = '';
                secCoord.style.display = 'none';
                filterCarrera.disabled = false;
                filterCarrera.style.opacity = '1';
            } else {
                tabBtnCoord.classList.add('active');
                tabBtnDoc.classList.remove('active');
                secCoord.style.display = '';
                secDoc.style.display = 'none';
                filterCarrera.disabled = true;
                filterCarrera.style.opacity = '0.5';
            }

            applyFilters();
        }

        // 2. Aplicar Filtros en Tiempo Real
        function applyFilters() {
            const searchVal = document.getElementById('searchInput').value.trim().toLowerCase();
            const carreraVal = document.getElementById('filterCarrera').value.trim().toLowerCase();
            const areaVal = document.getElementById('filterArea').value.trim().toLowerCase();
            const periodoVal = document.getElementById('filterPeriodo').value.trim().toLowerCase();
            const anioVal = document.getElementById('filterAnio').value.trim();
            const mesVal = document.getElementById('filterMes').value.trim().toLowerCase();

            if (currentTab === 'docentes') {
                const rows = document.querySelectorAll('#tbodyDocentes .informe-docente-row');
                let visibleCount = 0;

                rows.forEach(row => {
                    const rNombre = row.getAttribute('data-nombre') || '';
                    const rCorreo = row.getAttribute('data-correo') || '';
                    const rCurso = row.getAttribute('data-curso') || '';
                    const rCodigo = row.getAttribute('data-codigo') || '';
                    const rCarrera = row.getAttribute('data-carrera') || '';
                    const rArea = row.getAttribute('data-area') || '';
                    const rPeriodo = row.getAttribute('data-periodo') || '';
                    const rAnio = row.getAttribute('data-anio') || '';
                    const rMes = row.getAttribute('data-mes') || '';

                    const matchesSearch = !searchVal ||
                        rNombre.includes(searchVal) ||
                        rCorreo.includes(searchVal) ||
                        rCurso.includes(searchVal) ||
                        rCodigo.includes(searchVal) ||
                        rArea.includes(searchVal);

                    const matchesCarrera = !carreraVal || rCarrera.includes(carreraVal);
                    const matchesArea = !areaVal || rArea.includes(areaVal);
                    const matchesPeriodo = !periodoVal || rPeriodo.includes(periodoVal);
                    const matchesAnio = !anioVal || rAnio === anioVal;
                    const matchesMes = !mesVal || rMes.includes(mesVal);

                    if (matchesSearch && matchesCarrera && matchesArea && matchesPeriodo && matchesAnio && matchesMes) {
                        row.style.display = '';
                        visibleCount++;
                        const numCell = row.querySelector('.seq-num');
                        if (numCell) numCell.textContent = `${visibleCount}.`;
                    } else {
                        row.style.display = 'none';
                    }
                });

                const noResults = document.getElementById('noResultsDocentesRow');
                const emptyRow = document.getElementById('emptyDocentesRow');

                if (rows.length === 0) {
                    if (emptyRow) emptyRow.style.display = '';
                    if (noResults) noResults.style.display = 'none';
                } else {
                    if (emptyRow) emptyRow.style.display = 'none';
                    if (noResults) noResults.style.display = (visibleCount === 0) ? '' : 'none';
                }

                document.getElementById('resultsCounterText').textContent = `Mostrando ${visibleCount} de ${rows.length} informes de docentes`;
                document.getElementById('tabCountDocentes').textContent = rows.length;

            } else {
                const rows = document.querySelectorAll('#tbodyCoordinacion .informe-coordinacion-row');
                let visibleCount = 0;

                rows.forEach(row => {
                    const rNombre = row.getAttribute('data-nombre') || '';
                    const rCorreo = row.getAttribute('data-correo') || '';
                    const rArea = row.getAttribute('data-area') || '';
                    const rPeriodo = row.getAttribute('data-periodo') || '';
                    const rAnio = row.getAttribute('data-anio') || '';
                    const rMes = row.getAttribute('data-mes') || '';

                    const matchesSearch = !searchVal ||
                        rNombre.includes(searchVal) ||
                        rCorreo.includes(searchVal) ||
                        rArea.includes(searchVal);

                    const matchesArea = !areaVal || rArea.includes(areaVal);
                    const matchesPeriodo = !periodoVal || rPeriodo.includes(periodoVal);
                    const matchesAnio = !anioVal || rAnio === anioVal;
                    const matchesMes = !mesVal || rMes.includes(mesVal);

                    if (matchesSearch && matchesArea && matchesPeriodo && matchesAnio && matchesMes) {
                        row.style.display = '';
                        visibleCount++;
                        const numCell = row.querySelector('.seq-num');
                        if (numCell) numCell.textContent = `${visibleCount}.`;
                    } else {
                        row.style.display = 'none';
                    }
                });

                const noResults = document.getElementById('noResultsCoordinacionRow');
                const emptyRow = document.getElementById('emptyCoordinacionRow');

                if (rows.length === 0) {
                    if (emptyRow) emptyRow.style.display = '';
                    if (noResults) noResults.style.display = 'none';
                } else {
                    if (emptyRow) emptyRow.style.display = 'none';
                    if (noResults) noResults.style.display = (visibleCount === 0) ? '' : 'none';
                }

                document.getElementById('resultsCounterText').textContent = `Mostrando ${visibleCount} de ${rows.length} informes de coordinación`;
                document.getElementById('tabCountCoordinacion').textContent = rows.length;
            }

            updateSelectionState();
        }

        // 3. Limpiar Filtros
        function clearFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('filterCarrera').value = '';
            document.getElementById('filterArea').value = '';
            document.getElementById('filterPeriodo').value = '';
            document.getElementById('filterAnio').value = '';
            document.getElementById('filterMes').value = '';
            applyFilters();
        }

        // 4. Gestión de Selección con Checkboxes
        function toggleSelectAll(type) {
            const masterChk = (type === 'docente')
                ? document.getElementById('chkSelectAllDocentes')
                : document.getElementById('chkSelectAllCoord');

            const isChecked = masterChk.checked;
            const chkClass = (type === 'docente') ? '.chk-docente' : '.chk-coord';
            const rows = document.querySelectorAll((type === 'docente') ? '#tbodyDocentes .informe-docente-row' : '#tbodyCoordinacion .informe-coordinacion-row');

            rows.forEach(row => {
                if (row.style.display !== 'none') {
                    const chk = row.querySelector(chkClass);
                    if (chk) chk.checked = isChecked;
                }
            });

            updateSelectionState();
        }

        function updateSelectionState() {
            const selectedDocentes = Array.from(document.querySelectorAll('.chk-docente:checked')).map(c => c.value);
            const selectedCoord = Array.from(document.querySelectorAll('.chk-coord:checked')).map(c => c.value);
            const totalSelected = selectedDocentes.length + selectedCoord.length;

            const toolbar = document.getElementById('selectionToolbar');
            const countText = document.getElementById('selectedCountText');

            if (totalSelected > 0) {
                toolbar.classList.add('active');
                countText.textContent = `${totalSelected} informe(s) seleccionado(s)`;
            } else {
                toolbar.classList.remove('active');
            }
        }

        function clearSelection() {
            document.querySelectorAll('.custom-checkbox').forEach(chk => chk.checked = false);
            updateSelectionState();
        }

        function downloadSelectedZip() {
            const selectedDocentes = Array.from(document.querySelectorAll('.chk-docente:checked')).map(c => c.value);
            const selectedCoord = Array.from(document.querySelectorAll('.chk-coord:checked')).map(c => c.value);

            if (selectedDocentes.length === 0 && selectedCoord.length === 0) return;

            const form = document.getElementById('formSeleccionadosPost');
            form.action = "{{ route('admin.informes.descargar-seleccionados-zip') }}";
            form.target = "_self";
            document.getElementById('postDocenteIds').value = selectedDocentes.join(',');
            document.getElementById('postCoordIds').value = selectedCoord.join(',');
            form.submit();
        }

        function viewSelectedPdf() {
            const selectedDocentes = Array.from(document.querySelectorAll('.chk-docente:checked')).map(c => c.value);
            const selectedCoord = Array.from(document.querySelectorAll('.chk-coord:checked')).map(c => c.value);

            if (selectedDocentes.length === 0 && selectedCoord.length === 0) return;

            const form = document.getElementById('formSeleccionadosPost');
            form.action = "{{ route('admin.informes.consolidado-seleccionados-pdf') }}";
            form.target = "_blank";
            document.getElementById('postDocenteIds').value = selectedDocentes.join(',');
            document.getElementById('postCoordIds').value = selectedCoord.join(',');
            form.submit();
        }

        // 5. Modal de Descarga Masiva por Mes
        function openBulkDownloadModal() {
            const activeMes = document.getElementById('filterMes').value;
            const activeAnio = document.getElementById('filterAnio').value;
            const activeCarrera = document.getElementById('filterCarrera').value;

            if (activeMes) document.getElementById('bulkMes').value = activeMes;
            if (activeAnio) document.getElementById('bulkAnio').value = activeAnio;
            if (activeCarrera) document.getElementById('bulkCarrera').value = activeCarrera;

            document.getElementById('bulkDownloadModal').classList.add('active');
        }

        function closeBulkDownloadModal() {
            document.getElementById('bulkDownloadModal').classList.remove('active');
        }

        function getBulkParams() {
            const mes = document.getElementById('bulkMes').value;
            const anio = document.getElementById('bulkAnio').value;
            const tipo = document.getElementById('bulkTipo').value;
            const carrera = document.getElementById('bulkCarrera').value;

            const params = new URLSearchParams();
            if (mes) params.append('mes', mes);
            if (anio) params.append('anio', anio);
            if (tipo) params.append('tipo', tipo);
            if (carrera) params.append('carrera', carrera);

            return params.toString();
        }

        function triggerBulkZip(e) {
            e.preventDefault();
            const params = getBulkParams();
            window.location.href = `{{ route('admin.informes.descargar-zip') }}?${params}`;
            showToast('Generando archivo ZIP de informes...', 'success');
        }

        function triggerBulkPdf(e) {
            e.preventDefault();
            const params = getBulkParams();
            window.open(`{{ route('admin.informes.consolidado-pdf') }}?${params}`, '_blank');
        }

        function triggerBulkCsv(e) {
            e.preventDefault();
            const params = getBulkParams();
            window.location.href = `{{ route('admin.informes.descargar-csv') }}?${params}`;
            showToast('Descargando planilla de control CSV...', 'success');
        }

        // 6. Modal de Eliminación
        let deleteType = null;
        let deleteTargetId = null;

        function openDeleteModal(type, id, label) {
            deleteType = type;
            deleteTargetId = id;
            const msg = document.getElementById('deleteModalMessage');
            msg.textContent = `Esta acción no se puede deshacer. Se eliminará permanentemente el informe de ${type === 'docente' ? 'docente' : 'coordinación'}: "${label}".`;
            document.getElementById('deleteConfirmModal').classList.add('active');
        }

        function closeDeleteModal() {
            deleteType = null;
            deleteTargetId = null;
            document.getElementById('deleteConfirmModal').classList.remove('active');
        }

        async function executeDelete() {
            if (!deleteType || !deleteTargetId) return;

            const btn = document.getElementById('btnExecuteDelete');
            btn.disabled = true;
            btn.textContent = 'ELIMINANDO...';

            const url = (deleteType === 'docente')
                ? `{{ url('/admin/informes/docente') }}/${deleteTargetId}`
                : `{{ url('/admin/informes/coordinacion') }}/${deleteTargetId}`;

            try {
                const response = await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                btn.disabled = false;
                btn.textContent = 'CONFIRMAR ELIMINACIÓN';
                closeDeleteModal();

                if (response.ok && data.success) {
                    showToast(data.message || 'Informe eliminado exitosamente.', 'success');

                    const rowId = (deleteType === 'docente') ? `row-docente-${deleteTargetId}` : `row-coordinacion-${deleteTargetId}`;
                    const row = document.getElementById(rowId);
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
                console.error(err);
                btn.disabled = false;
                btn.textContent = 'CONFIRMAR ELIMINACIÓN';
                closeDeleteModal();
                showToast('Error de conexión con el servidor.', 'error');
            }
        }

        // 7. Modal Superpuesto de Vista Previa de Informe Oficial (Lightbox Iframe)
        const adminDocenteVerBaseUrl = "{{ url('/admin/informes/docente') }}";
        const adminCoordVerBaseUrl = "{{ url('/admin/informes/coordinacion') }}";

        function openReportPreviewModal(type, id, title, subtitle) {
            const modal = document.getElementById('reportPreviewModal');
            const iframe = document.getElementById('reportPreviewIframe');
            const loader = document.getElementById('previewIframeLoader');
            const titleEl = document.getElementById('previewModalTitle');
            const subtitleEl = document.getElementById('previewModalSubtitle');
            const badgeEl = document.getElementById('previewModalBadge');
            const fullTabBtn = document.getElementById('previewModalFullTabBtn');

            // Configurar títulos y badge
            titleEl.textContent = title || 'Informe Oficial';
            subtitleEl.textContent = subtitle || 'FARUSAC • Control Académico';

            if (type === 'docente') {
                badgeEl.textContent = 'Docente';
                badgeEl.className = 'report-badge-type report-badge-docente';
                const verUrl = `${adminDocenteVerBaseUrl}/${id}/ver`;
                iframe.src = `${verUrl}?embed=1`;
                fullTabBtn.href = verUrl;
            } else {
                badgeEl.textContent = 'Coordinación';
                badgeEl.className = 'report-badge-type report-badge-coord';
                const verUrl = `${adminCoordVerBaseUrl}/${id}/ver`;
                iframe.src = `${verUrl}?embed=1`;
                fullTabBtn.href = verUrl;
            }

            // Mostrar loader
            if (loader) loader.style.display = 'flex';

            // Abrir modal y bloquear scroll de fondo
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeReportPreviewModal() {
            const modal = document.getElementById('reportPreviewModal');
            modal.classList.remove('active');
            document.body.style.overflow = '';

            // Limpiar el iframe luego de la animación de cierre para liberar memoria
            setTimeout(() => {
                const iframe = document.getElementById('reportPreviewIframe');
                if (iframe && !modal.classList.contains('active')) {
                    iframe.src = 'about:blank';
                }
            }, 300);
        }

        function hidePreviewLoader() {
            const loader = document.getElementById('previewIframeLoader');
            const iframe = document.getElementById('reportPreviewIframe');
            if (iframe && (iframe.src.includes('about:blank') || !iframe.src)) return;
            if (loader) {
                loader.style.display = 'none';
            }
        }

        function printReportIframe() {
            const iframe = document.getElementById('reportPreviewIframe');
            if (iframe && iframe.contentWindow) {
                try {
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();
                } catch (e) {
                    console.error('Error al invocar impresión de iframe:', e);
                    window.open(iframe.src.replace('?embed=1', ''), '_blank');
                }
            }
        }

        // 8. Toast Helper
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toastNotification');
            const toastMsg = document.getElementById('toastMessage');
            toastMsg.textContent = message;
            toast.className = `toast-notification ${type} show`;

            setTimeout(() => {
                toast.classList.remove('show');
            }, 3800);
        }

        // 9. Inicialización
        document.addEventListener('DOMContentLoaded', () => {
            const profileToggle = document.getElementById('profileToggle');
            const profileDropdown = document.getElementById('profileDropdown');
            const hamburgerBtn = document.getElementById('hamburgerBtn');
            const adminSidebar = document.getElementById('adminSidebar');

            profileToggle.addEventListener('click', (e) => {
                e.stopPropagation();
                profileDropdown.classList.toggle('active');
            });

            document.addEventListener('click', () => {
                profileDropdown.classList.remove('active');
            });

            hamburgerBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                adminSidebar.classList.toggle('collapsed');
            });

            document.addEventListener('click', (e) => {
                if (!adminSidebar.contains(e.target) && !hamburgerBtn.contains(e.target)) {
                    adminSidebar.classList.add('collapsed');
                }
            });

            adminSidebar.addEventListener('click', (e) => {
                e.stopPropagation();
            });

            // Cerrar modales con Escape
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    closeReportPreviewModal();
                    closeDeleteModal();
                    closeBulkDownloadModal();
                }
            });

            // Aplicar filtros iniciales
            applyFilters();
        });
    </script>

</body>

</html>