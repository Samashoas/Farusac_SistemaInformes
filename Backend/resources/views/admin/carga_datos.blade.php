<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carga de Datos - Panel Administrador</title>
    <!-- Google Fonts: Outfit (headings) and Inter (body) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --color-azul: #002D72;      /* Pantone 288C */
            --color-oro: #AC8400;       /* Pantone 118C */
            --color-terracota: #B94700; /* Pantone 1525C */
            --color-texto-principal: #1a202c;
            --color-texto-secundario: #4a5568;
            --color-texto-claro: #718096;
            --color-fondo: #f8fafc;
            --color-borde: #edf2f7;
            --transition-smooth: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            --border-radius-card: 16px;
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
            gap: 15px;
        }

        /* Botón de Hamburguesa */
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

        .sidebar-link-cursos:hover {
            background-color: rgba(172, 132, 0, 0.05);
            color: var(--color-oro);
        }
        .sidebar-link-cursos:hover .sidebar-icon {
            color: var(--color-oro);
        }

        .sidebar-link-informes:hover {
            background-color: rgba(185, 71, 0, 0.05);
            color: var(--color-terracota);
        }
        .sidebar-link-informes:hover .sidebar-icon {
            color: var(--color-terracota);
        }

        /* Módulo Carga de Datos Activo (Azul) */
        .sidebar-link-carga {
            background-color: rgba(0, 45, 114, 0.08);
            color: var(--color-azul);
            font-weight: 600;
        }
        .sidebar-link-carga .sidebar-icon {
            color: var(--color-azul);
        }
        .sidebar-link-carga:hover {
            background-color: rgba(0, 45, 114, 0.12);
        }

        /* --- CUERPO PRINCIPAL --- */
        .main-content {
            flex: 1;
            padding: 40px;
            margin-left: 260px;
            transition: margin-left 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            min-height: calc(100vh - 90px);
        }

        .main-content.expanded {
            margin-left: 0;
        }

        /* --- ESTILOS DE LA VISTA DE SELECCIÓN (DASHBOARD) --- */
        .selection-grid-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            width: 100%;
            max-width: 900px;
            margin: 20px auto 0 auto;
            animation: fadeIn 0.4s ease;
        }

        .section-main-title {
            font-family: 'Outfit', sans-serif;
            font-size: 26px;
            font-weight: 700;
            color: var(--color-texto-principal);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            text-align: center;
            margin-bottom: 35px;
        }

        .cards-selection-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            width: 100%;
        }

        @media (max-width: 768px) {
            .cards-selection-wrapper {
                grid-template-columns: 1fr;
                gap: 25px;
            }
        }

        .selection-card {
            background-color: #ffffff;
            border: 2px solid var(--color-borde);
            border-radius: 20px;
            padding: 50px 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
            transition: var(--transition-smooth);
            cursor: pointer;
        }

        .selection-card-icon {
            width: 80px;
            height: 80px;
            color: var(--color-texto-principal);
            opacity: 0.85;
            margin-bottom: 35px;
            transition: color 0.3s ease, transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .selection-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            max-width: 220px;
            padding: 12px 25px;
            background-color: transparent;
            color: var(--color-texto-principal);
            border: 2px solid #cbd5e0;
            border-radius: 25px;
            font-family: 'Outfit', sans-serif;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            transition: all 0.25s ease;
            cursor: pointer;
        }

        /* Tarjeta Usuarios (Azul) */
        .selection-card-usuarios:hover {
            transform: translateY(-5px);
            border-color: var(--color-azul);
            box-shadow: 0 20px 40px rgba(0, 45, 114, 0.08);
        }

        .selection-card-usuarios:hover .selection-card-icon {
            color: var(--color-azul);
            transform: scale(1.05);
        }

        .selection-card-usuarios:hover .selection-btn {
            background-color: var(--color-azul);
            color: #ffffff;
            border-color: var(--color-azul);
            box-shadow: 0 4px 12px rgba(0, 45, 114, 0.25);
        }

        /* Tarjeta Cursos (Oro / Amarillo) */
        .selection-card-cursos:hover {
            transform: translateY(-5px);
            border-color: var(--color-oro);
            box-shadow: 0 20px 40px rgba(172, 132, 0, 0.08);
        }

        .selection-card-cursos:hover .selection-card-icon {
            color: var(--color-oro);
            transform: scale(1.05);
        }

        .selection-card-cursos:hover .selection-btn {
            background-color: var(--color-oro);
            color: #ffffff;
            border-color: var(--color-oro);
            box-shadow: 0 4px 12px rgba(172, 132, 0, 0.35);
        }

        /* --- VISTA DE FORMULARIO DE CARGA --- */
        .upload-view-container {
            width: 100%;
            max-width: 760px;
            display: none;
            animation: fadeIn 0.4s ease;
        }

        .back-link-wrapper {
            width: 100%;
            margin-bottom: 25px;
            text-align: left;
        }

        .back-link-btn {
            display: inline-flex;
            align-items: center;
            background: none;
            border: none;
            color: var(--color-azul);
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: color 0.2s ease;
            font-family: 'Inter', sans-serif;
        }

        .back-link-btn:hover {
            color: var(--color-oro);
        }

        .back-icon {
            width: 16px;
            height: 16px;
            margin-right: 8px;
            stroke-width: 2.5px;
        }

        /* Botones de acción de la tarjeta */
        .card-actions-wrapper {
            display: flex;
            gap: 12px;
            margin-bottom: 25px;
            justify-content: flex-start;
        }

        .card-action-btn {
            display: inline-flex;
            align-items: center;
            padding: 10px 18px;
            background-color: #ffffff;
            color: var(--color-texto-secundario);
            border: 1.5px solid var(--color-borde);
            border-radius: 20px;
            font-family: 'Inter', sans-serif;
            font-size: 12.5px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: var(--transition-smooth);
        }

        .card-action-btn:hover {
            border-color: var(--color-azul);
            color: var(--color-azul);
            background-color: rgba(0, 45, 114, 0.02);
            box-shadow: 0 2px 6px rgba(0, 45, 114, 0.05);
        }

        .cursos-inst-btn:hover {
            border-color: var(--color-oro);
            color: var(--color-oro);
            background-color: rgba(172, 132, 0, 0.02);
            box-shadow: 0 2px 6px rgba(172, 132, 0, 0.05);
        }

        /* Tarjeta de Formulario */
        .form-card {
            background: 
                linear-gradient(#ffffff, #ffffff) padding-box,
                linear-gradient(135deg, var(--color-azul), var(--color-oro), var(--color-terracota)) border-box;
            border: 2px solid transparent;
            border-radius: var(--border-radius-card);
            padding: 40px;
            width: 100%;
            box-shadow: 0 15px 35px rgba(0, 45, 114, 0.04);
        }

        .card-title-group {
            border-bottom: 1.5px solid #edf2f7;
            padding-bottom: 20px;
            margin-bottom: 30px;
            text-align: center;
        }

        .card-main-title {
            font-family: 'Outfit', sans-serif;
            font-size: 26px;
            font-weight: 700;
            color: var(--color-texto-principal);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            text-align: center;
            margin-bottom: 5px;
        }

        .card-main-subtitle {
            font-family: 'Outfit', sans-serif;
            font-size: 18px;
            font-weight: 700;
            color: var(--color-texto-secundario);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            text-align: center;
            margin-bottom: 5px;
        }

        /* Drag & Drop Area */
        .file-upload-area {
            border: 2px dashed #cbd5e0;
            border-radius: 12px;
            padding: 40px 20px;
            text-align: center;
            background-color: #fcfcfc;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 15px;
            margin-bottom: 25px;
            position: relative;
        }

        .file-upload-area:hover {
            border-color: var(--color-azul);
            background-color: rgba(0, 45, 114, 0.01);
        }

        #dropAreaCursos:hover {
            border-color: var(--color-oro);
            background-color: rgba(172, 132, 0, 0.01);
        }

        .upload-icon {
            width: 50px;
            height: 50px;
            color: var(--color-azul);
            margin-bottom: 15px;
            opacity: 0.8;
            transition: transform 0.25s ease;
        }

        .file-upload-area:hover .upload-icon {
            transform: translateY(-3px);
        }

        .file-input {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .upload-text {
            font-size: 14px;
            color: var(--color-texto-principal);
            font-weight: 500;
        }

        .upload-hint {
            font-size: 12px;
            color: var(--color-texto-secundario);
            margin-top: 5px;
        }

        .file-selected-name {
            margin-top: 10px;
            font-size: 14px;
            font-weight: 600;
            color: var(--color-oro);
            word-break: break-all;
        }

        /* Instrucciones del CSV */
        .csv-instructions {
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: left;
        }

        .csv-instructions.usuarios-inst {
            background-color: rgba(0, 45, 114, 0.03);
            border-left: 3.5px solid var(--color-azul);
        }

        .csv-instructions.cursos-inst {
            background-color: rgba(172, 132, 0, 0.04);
            border-left: 3.5px solid var(--color-oro);
        }

        .instructions-title {
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 12px;
            letter-spacing: 0.05em;
        }

        .usuarios-inst .instructions-title {
            color: var(--color-azul);
        }

        .cursos-inst .instructions-title {
            color: #8c6c00;
        }

        .instructions-list {
            list-style: none;
            font-size: 13px;
            color: var(--color-texto-secundario);
            line-height: 1.65;
        }

        .instructions-list li {
            position: relative;
            padding-left: 18px;
            margin-bottom: 8px;
        }

        .instructions-list li::before {
            content: "•";
            position: absolute;
            left: 5px;
            font-weight: bold;
        }

        .usuarios-inst li::before {
            color: var(--color-azul);
        }

        .cursos-inst li::before {
            color: var(--color-oro);
        }

        /* --- ESTILOS DE LOS REPORTES DE CARGA --- */
        .report-section {
            margin-top: 30px;
            display: none;
            animation: fadeIn 0.3s ease;
            width: 100%;
        }

        .report-tabs {
            display: flex;
            gap: 25px;
            margin-bottom: 25px;
            border-bottom: 1.5px solid var(--color-borde);
            padding-bottom: 12px;
            justify-content: flex-start;
        }

        .report-tab-btn {
            background: none;
            border: none;
            font-family: 'Outfit', sans-serif;
            font-size: 14.5px;
            font-weight: 600;
            cursor: pointer;
            padding-bottom: 10px;
            position: relative;
            transition: var(--transition-smooth);
            color: var(--color-texto-claro);
        }

        .report-tab-btn span {
            font-weight: 700;
        }

        .tab-btn-success.active {
            color: #2f855a;
        }
        .tab-btn-success.active::after {
            content: "";
            position: absolute;
            bottom: -1.5px;
            left: 0;
            right: 0;
            height: 2.5px;
            background-color: #48bb78;
            border-radius: 2px;
        }

        .tab-btn-error.active {
            color: #c53030;
        }
        .tab-btn-error.active::after {
            content: "";
            position: absolute;
            bottom: -1.5px;
            left: 0;
            right: 0;
            height: 2.5px;
            background-color: #e53e3e;
            border-radius: 2px;
        }

        .report-title {
            font-family: 'Outfit', sans-serif;
            font-size: 16px;
            font-weight: 700;
            color: var(--color-texto-principal);
            margin-bottom: 15px;
            text-align: left;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Tabla de Reportes */
        .report-table-wrapper {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid var(--color-borde);
            border-radius: 8px;
            background-color: #ffffff;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .report-table th {
            background-color: #fcfcfc;
            padding: 12px 16px;
            font-family: 'Outfit', sans-serif;
            font-size: 12px;
            font-weight: 700;
            color: var(--color-texto-secundario);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1.5px solid var(--color-borde);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .report-table td {
            padding: 12px 16px;
            font-size: 13px;
            color: var(--color-texto-secundario);
            border-bottom: 1px solid var(--color-borde);
        }

        .report-table tr:last-child td {
            border-bottom: none;
        }

        /* Lista de Errores */
        .error-log-list {
            list-style: none;
            background-color: #fffaf0;
            border: 1px solid #feebc8;
            border-radius: 8px;
            padding: 20px;
            max-height: 250px;
            overflow-y: auto;
            text-align: left;
        }

        .error-log-item {
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            color: #c05621;
            margin-bottom: 10px;
            line-height: 1.5;
        }

        .error-log-item:last-child {
            margin-bottom: 0;
        }
            margin-top: 5px;
        }

        /* Drag & Drop Area */
        .file-upload-area {
            border: 2px dashed #cbd5e0;
            border-radius: 12px;
            padding: 40px 20px;
            text-align: center;
            background-color: #fcfcfc;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-bottom: 25px;
            position: relative;
        }

        .file-upload-area:hover {
            border-color: var(--color-azul);
            background-color: rgba(0, 45, 114, 0.01);
        }

        .upload-icon {
            width: 50px;
            height: 50px;
            color: var(--color-azul);
            margin-bottom: 15px;
            opacity: 0.8;
        }

        .file-input {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .upload-text {
            font-size: 14px;
            color: var(--color-texto-principal);
            font-weight: 500;
        }

        .upload-hint {
            font-size: 12px;
            color: var(--color-texto-secundario);
            margin-top: 5px;
        }

        .file-selected-name {
            margin-top: 10px;
            font-size: 14px;
            font-weight: 600;
            color: var(--color-oro);
            word-break: break-all;
        }

        /* Instrucciones del CSV */
        .csv-instructions {
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: left;
        }

        .csv-instructions.usuarios-inst {
            background-color: rgba(0, 45, 114, 0.03);
            border-left: 3.5px solid var(--color-azul);
        }

        .csv-instructions.cursos-inst {
            background-color: rgba(172, 132, 0, 0.04);
            border-left: 3.5px solid var(--color-oro);
        }

        .instructions-title {
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 12px;
            letter-spacing: 0.05em;
        }

        .usuarios-inst .instructions-title {
            color: var(--color-azul);
        }

        .cursos-inst .instructions-title {
            color: #8c6c00;
        }

        .instructions-list {
            list-style: none;
            font-size: 13px;
            color: var(--color-texto-secundario);
            line-height: 1.65;
        }

        .instructions-list li {
            position: relative;
            padding-left: 18px;
            margin-bottom: 8px;
        }

        .instructions-list li::before {
            content: "•";
            position: absolute;
            left: 5px;
            font-weight: bold;
        }

        .usuarios-inst li::before {
            color: var(--color-azul);
        }

        .cursos-inst li::before {
            color: var(--color-oro);
        }

        /* Botón de Carga */
        .submit-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 15px 30px;
            color: #ffffff;
            border: none;
            border-radius: 30px;
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            transition: all 0.25s ease;
            cursor: pointer;
            outline: none;
        }

        .usuarios-btn {
            background-color: var(--color-azul);
            box-shadow: 0 4px 10px rgba(0, 45, 114, 0.2);
        }

        .usuarios-btn:hover {
            background-color: #002257;
            box-shadow: 0 6px 15px rgba(0, 45, 114, 0.3);
        }

        .cursos-btn {
            background-color: var(--color-oro);
            box-shadow: 0 4px 10px rgba(172, 132, 0, 0.25);
        }

        .cursos-btn:hover {
            background-color: #8c6c00;
            box-shadow: 0 6px 15px rgba(172, 132, 0, 0.35);
        }

        .submit-btn:hover {
            transform: translateY(-1px);
        }

        .submit-btn:active {
            transform: translateY(1px);
        }

        /* Alertas de Éxito y Error */
        .alert-success {
            display: flex;
            align-items: center;
            background-color: rgba(72, 187, 120, 0.08);
            border-left: 3.5px solid #48bb78;
            border-radius: 8px;
            padding: 14px 20px;
            margin-bottom: 25px;
            text-align: left;
            width: 100%;
        }

        .alert-error {
            display: flex;
            align-items: center;
            background-color: rgba(229, 62, 62, 0.08);
            border-left: 3.5px solid #e53e3e;
            border-radius: 8px;
            padding: 14px 20px;
            margin-bottom: 25px;
            text-align: left;
            width: 100%;
        }

        .alert-icon {
            width: 22px;
            height: 22px;
            margin-right: 12px;
            flex-shrink: 0;
        }

        .alert-success .alert-icon {
            color: #48bb78;
        }

        .alert-error .alert-icon {
            color: #e53e3e;
        }

        .alert-text {
            font-size: 13.5px;
            font-weight: 500;
            line-height: 1.4;
        }

        .alert-success .alert-text {
            color: #276749;
        }

        .alert-error .alert-text {
            color: #9b2c2c;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
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
        <aside class="admin-sidebar collapsed" id="adminSidebar">
            <nav class="sidebar-nav">
                <!-- Enlace Inicio -->
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link sidebar-link-inicio">
                    <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Inicio</span>
                </a>

                <!-- Enlace Usuarios -->
                <a href="{{ route('admin.usuarios') }}" class="sidebar-link sidebar-link-usuarios">
                    <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Usuarios</span>
                </a>

                <!-- Enlace Cursos -->
                <a href="{{ route('admin.cursos') }}" class="sidebar-link sidebar-link-cursos">
                    <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span>Cursos</span>
                </a>

                <!-- Enlace Informes -->
                <a href="#" class="sidebar-link sidebar-link-informes">
                    <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Informes</span>
                </a>

                <!-- Enlace Carga de datos (Activo) -->
                <a href="{{ route('admin.carga-datos') }}" class="sidebar-link sidebar-link-carga">
                    <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                    </svg>
                    <span>Carga de datos</span>
                </a>
            </nav>
        </aside>

        <!-- Cuerpo Principal -->
        <main class="main-content expanded" id="mainContent">

            <!-- Notificación de Éxito / Feedback -->
            @if (session('exito'))
                <div class="alert-success">
                    <svg class="alert-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path fill="#fff" d="M10 15.172l-3.293-3.293-1.414 1.414L10 18l8-8-1.414-1.414z"></path>
                    </svg>
                    <span class="alert-text">{{ session('exito') }}</span>
                </div>
            @endif

            <!-- Notificación de Errores de Validación -->
            @if ($errors->any())
                <div class="alert-error">
                    <svg class="alert-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path fill="#fff" d="M13.414 12l3.293-3.293-1.414-1.414L12 10.586 8.707 7.293 7.293 8.707 10.586 12l-3.293 3.293 1.414 1.414L12 13.414l3.293 3.293 1.414-1.414z"></path>
                    </svg>
                    <span class="alert-text">{{ $errors->first() }}</span>
                </div>
            @endif

            <!-- 1. VISTA DE SELECCIÓN INICIAL (DASHBOARD) -->
            <div class="selection-grid-container" id="loadSelectionView">
                <h2 class="section-main-title">CARGA MASIVA DE DATOS</h2>
                
                <div class="cards-selection-wrapper">
                    <!-- Tarjeta Cargar Usuarios -->
                    <div class="selection-card selection-card-usuarios" onclick="showUploadView('usuarios')">
                        <svg class="selection-card-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <button class="selection-btn">CARGAR USUARIOS</button>
                    </div>

                    <!-- Tarjeta Cargar Cursos -->
                    <div class="selection-card selection-card-cursos" onclick="showUploadView('cursos')">
                        <svg class="selection-card-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        <button class="selection-btn">CARGAR CURSOS</button>
                    </div>
                </div>
            </div>

            <!-- 2. PANALES DE FORMULARIO (OCULTOS INICIALMENTE) -->

            <!-- Formulario de Carga: Usuarios -->
            <div class="upload-view-container" id="uploadUsuariosView">
                <div class="back-link-wrapper">
                    <button type="button" class="back-link-btn" onclick="showSelectionView()">
                        <svg class="back-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                        Volver a la selección
                    </button>
                </div>

                <div class="form-card">
                    <div class="card-title-group">
                        <h2 class="card-main-title">Carga Masiva de datos</h2>
                        <div class="card-main-subtitle" style="color: var(--color-azul);">USUARIOS</div>
                    </div>

                    <div class="card-actions-wrapper">
                        <a href="{{ route('admin.usuarios') }}" class="card-action-btn">Ir a gestión de usuarios</a>
                        <button type="button" class="card-action-btn" onclick="downloadExample('usuarios')">Descargar ejemplo</button>
                    </div>

                    <form id="formUsuariosCSV" onsubmit="event.preventDefault();">
                        <div class="file-upload-area" id="dropAreaUsuarios" onclick="document.getElementById('archivo_csv_usuarios').click()">
                            <svg class="upload-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <input type="file" name="archivo_csv" id="archivo_csv_usuarios" class="file-input" accept=".csv" required style="display:none;" onchange="uploadCSVFile('usuarios')">
                            <div class="upload-text" id="uploadTextUsuarios">Arrastra el archivo CSV o hacer click para buscar el archivo</div>
                            <div class="upload-hint">Tamaño máximo admitido: 5 MB</div>
                            <div class="file-selected-name" id="fileNameUsuarios"></div>
                        </div>

                        <!-- Instrucciones -->
                        <div class="csv-instructions usuarios-inst">
                            <h4 class="instructions-title">Requisitos del Archivo</h4>
                            <ul class="instructions-list">
                                <li>El archivo debe estar delimitado por comas (formato CSV estándar).</li>
                                <li>Debe contener los encabezados exactos en la primera fila: <b>Nombre, Correo, Numero, Plaza</b>.</li>
                                <li>El correo electrónico debe pertenecer al dominio institucional <b>@farusac.edu.gt</b>.</li>
                                <li>Las plazas válidas son estrictamente: <b>Titular</b>, <b>Titular+Ampliacion</b> o <b>Interino</b>.</li>
                            </ul>
                        </div>
                    </form>

                    <!-- Resultados / Reporte de Carga de Usuarios -->
                    <div class="report-section" id="reportUsuarios">
                        <div class="report-tabs">
                            <button type="button" class="report-tab-btn tab-btn-success active" onclick="switchReportTab('usuarios', 'success')">
                                Datos cargados exitosamente: <span id="successCountUsuarios">0</span>
                            </button>
                            <button type="button" class="report-tab-btn tab-btn-error" onclick="switchReportTab('usuarios', 'error')">
                                Datos con error: <span id="errorCountUsuarios">0</span>
                            </button>
                        </div>
                        
                        <div id="reportContentUsuarios"></div>
                    </div>
                </div>
            </div>

            <!-- Formulario de Carga: Cursos -->
            <div class="upload-view-container" id="uploadCursosView">
                <div class="back-link-wrapper">
                    <button type="button" class="back-link-btn" onclick="showSelectionView()">
                        <svg class="back-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                        Volver a la selección
                    </button>
                </div>

                <div class="form-card">
                    <div class="card-title-group">
                        <h2 class="card-main-title">Carga Masiva de datos</h2>
                        <div class="card-main-subtitle" style="color: var(--color-oro);">CURSOS</div>
                    </div>

                    <div class="card-actions-wrapper">
                        <a href="{{ route('admin.cursos') }}" class="card-action-btn cursos-inst-btn">Ir a gestión de cursos</a>
                        <button type="button" class="card-action-btn cursos-inst-btn" onclick="downloadExample('cursos')">Descargar ejemplo</button>
                    </div>

                    <form id="formCursosCSV" onsubmit="event.preventDefault();">
                        <div class="file-upload-area" id="dropAreaCursos" onclick="document.getElementById('archivo_csv_cursos').click()">
                            <svg class="upload-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" style="color: var(--color-oro);">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <input type="file" name="archivo_csv" id="archivo_csv_cursos" class="file-input" accept=".csv" required style="display:none;" onchange="uploadCSVFile('cursos')">
                            <div class="upload-text" id="uploadTextCursos">Arrastra el archivo CSV o hacer click para buscar el archivo</div>
                            <div class="upload-hint">Tamaño máximo admitido: 5 MB</div>
                            <div class="file-selected-name" id="fileNameCursos"></div>
                        </div>

                        <!-- Instrucciones -->
                        <div class="csv-instructions cursos-inst">
                            <h4 class="instructions-title">Requisitos del Archivo</h4>
                            <ul class="instructions-list">
                                <li>El archivo debe estar delimitado por comas (formato CSV estándar).</li>
                                <li>Debe contener los encabezados exactos en la primera fila: <b>Carrera, Area, Curso, Codigo, Seccion, Anio, Semestre</b>.</li>
                                <li>Las carreras válidas son: <b>Arquitectura</b> o <b>Diseño Gráfico</b>.</li>
                                <li>Los semestres válidos son: <b>Primer Semestre</b>, <b>Segundo Semestre</b>, <b>Vacaciones Junio</b> o <b>Vacaciones Diciembre</b>.</li>
                            </ul>
                        </div>
                    </form>

                    <!-- Resultados / Reporte de Carga de Cursos -->
                    <div class="report-section" id="reportCursos">
                        <div class="report-tabs">
                            <button type="button" class="report-tab-btn tab-btn-success active" onclick="switchReportTab('cursos', 'success')">
                                Datos cargados exitosamente: <span id="successCountCursos">0</span>
                            </button>
                            <button type="button" class="report-tab-btn tab-btn-error" onclick="switchReportTab('cursos', 'error')">
                                Datos con error: <span id="errorCountCursos">0</span>
                            </button>
                        </div>
                        
                        <div id="reportContentCursos"></div>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- Script para Toggle de Menú del Perfil, Sidebar e indicación de archivos seleccionados -->
    <script>
        // Funciones de navegación del switcher
        function showUploadView(type) {
            document.getElementById('loadSelectionView').style.display = 'none';
            if (type === 'usuarios') {
                document.getElementById('uploadUsuariosView').style.display = 'block';
                document.getElementById('uploadCursosView').style.display = 'none';
            } else if (type === 'cursos') {
                document.getElementById('uploadUsuariosView').style.display = 'none';
                document.getElementById('uploadCursosView').style.display = 'block';
            }
            // Ocultar reportes previos al cambiar de vista
            document.getElementById('reportUsuarios').style.display = 'none';
            document.getElementById('reportCursos').style.display = 'none';
        }

        function showSelectionView() {
            document.getElementById('loadSelectionView').style.display = 'flex';
            document.getElementById('uploadUsuariosView').style.display = 'none';
            document.getElementById('uploadCursosView').style.display = 'none';
        }

        // Descarga de archivos CSV de ejemplo dinámica
        function downloadExample(type) {
            let csvContent = "";
            let filename = "";
            if (type === 'usuarios') {
                csvContent = "Nombre,Correo,Numero,Plaza\nJuan Perez,juan.perez@farusac.edu.gt,5587-1751,Titular\nMaria Gomez,maria.gomez@farusac.edu.gt,5894-2231,Titular+Ampliacion\nCarlos Lopez,carlos.lopez@farusac.edu.gt,4481-9952,Interino";
                filename = "ejemplo_usuarios.csv";
            } else if (type === 'cursos') {
                csvContent = "Carrera,Area,Curso,Codigo,Seccion,Anio,Semestre\nArquitectura,Área de Tecnología y Expresión,Fotografía,30313,A,2026,Primer Semestre\nDiseño Gráfico,Área de Comunicación,Diseño Web I,10542,B,2026,Segundo Semestre\nArquitectura,Área de Diseño,Diseño Arquitectónico I,20015,C,2026,Vacaciones Junio";
                filename = "ejemplo_cursos.csv";
            }
            
            const blob = new Blob([new Uint8Array([0xEF, 0xBB, 0xBF]), csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement("a");
            link.href = URL.createObjectURL(blob);
            link.setAttribute("download", filename);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Estado local de los reportes cargados
        let lastReportData = {
            usuarios: null,
            cursos: null
        };

        // Función para subir archivos vía AJAX asíncronamente
        function uploadCSVFile(type) {
            const fileInput = document.getElementById(type === 'usuarios' ? 'archivo_csv_usuarios' : 'archivo_csv_cursos');
            if (fileInput.files.length === 0) return;

            const file = fileInput.files[0];
            const formData = new FormData();
            formData.append('archivo_csv', file);

            // Cambiar estados a "Procesando..."
            const uploadText = document.getElementById(type === 'usuarios' ? 'uploadTextUsuarios' : 'uploadTextCursos');
            const fileName = document.getElementById(type === 'usuarios' ? 'fileNameUsuarios' : 'fileNameCursos');
            const dropArea = document.getElementById(type === 'usuarios' ? 'dropAreaUsuarios' : 'dropAreaCursos');

            const originalText = uploadText.textContent;
            uploadText.textContent = "Procesando archivo CSV...";
            fileName.textContent = `Analizando: ${file.name}`;
            dropArea.style.opacity = '0.7';

            const url = type === 'usuarios' ? '{{ route("admin.importar") }}' : '{{ route("admin.importar-cursos") }}';

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(res => {
                if (!res.ok) {
                    throw new Error('Error de comunicación con el servidor.');
                }
                return res.json();
            })
            .then(data => {
                // Restablecer estilos de carga
                uploadText.textContent = "Arrastra el archivo CSV o hacer click para buscar el archivo";
                fileName.textContent = "";
                dropArea.style.opacity = '1';
                fileInput.value = ""; // Limpiar input para futuras cargas

                // Mostrar reporte
                displayReport(type, data);
            })
            .catch(err => {
                console.error(err);
                alert(err.message || 'Ocurrió un error al subir e importar el archivo CSV.');
                uploadText.textContent = "Arrastra el archivo CSV o hacer click para buscar el archivo";
                fileName.textContent = "";
                dropArea.style.opacity = '1';
                fileInput.value = "";
            });
        }

        // Renderizar paneles de reportes
        function displayReport(type, data) {
            lastReportData[type] = data;

            // Mostrar el contenedor del reporte
            const reportSection = document.getElementById(type === 'usuarios' ? 'reportUsuarios' : 'reportCursos');
            reportSection.style.display = 'block';

            // Actualizar contadores numéricos
            document.getElementById(type === 'usuarios' ? 'successCountUsuarios' : 'successCountCursos').textContent = data.success_count;
            document.getElementById(type === 'usuarios' ? 'errorCountUsuarios' : 'errorCountCursos').textContent = data.error_count;

            // Activar por defecto la pestaña de éxitos
            switchReportTab(type, 'success');
        }

        // Cambiar pestañas del reporte
        function switchReportTab(type, tab) {
            const data = lastReportData[type];
            if (!data) return;

            const reportContent = document.getElementById(type === 'usuarios' ? 'reportContentUsuarios' : 'reportContentCursos');
            if (!reportContent) return;

            const tabsContainer = document.getElementById(type === 'usuarios' ? 'reportUsuarios' : 'reportCursos');
            const successBtn = tabsContainer.querySelector('.tab-btn-success');
            const errorBtn = tabsContainer.querySelector('.tab-btn-error');

            if (tab === 'success') {
                successBtn.classList.add('active');
                errorBtn.classList.remove('active');

                if (data.success_records.length === 0) {
                    reportContent.innerHTML = `
                        <div class="report-title">Datos Cargados exitosamente</div>
                        <p style="font-size: 13px; color: var(--color-texto-secundario); text-align: left; padding: 10px 0;">
                            No se cargó ningún registro nuevo en esta sesión.
                        </p>`;
                    return;
                }

                let html = '<div class="report-title">Datos Cargados exitosamente</div>';
                html += '<div class="report-table-wrapper"><table class="report-table"><thead><tr>';
                
                if (type === 'usuarios') {
                    html += '<th>ID</th><th>Nombre</th><th>Correo</th><th>Número</th><th>Rol</th><th>Plaza</th><th>Estado</th>';
                    html += '</tr></thead><tbody>';
                    data.success_records.forEach((r, idx) => {
                        html += `<tr>
                            <td>${idx + 1}.</td>
                            <td style="font-weight: 600; color: var(--color-texto-principal);">${r.nombre}</td>
                            <td>${r.correo}</td>
                            <td>${r.numero}</td>
                            <td style="text-transform: capitalize;">${r.rol}</td>
                            <td>${r.plaza}</td>
                            <td><span style="color: #48bb78; font-weight: 600;">${r.estado}</span></td>
                        </tr>`;
                    });
                } else {
                    html += '<th>ID</th><th>Carrera</th><th>Área</th><th>Curso</th><th>Código</th><th>Sección</th><th>Jornada</th>';
                    html += '</tr></thead><tbody>';
                    data.success_records.forEach((r, idx) => {
                        html += `<tr>
                            <td>${idx + 1}.</td>
                            <td>${r.carrera}</td>
                            <td>${r.area}</td>
                            <td style="font-weight: 600; color: var(--color-texto-principal);">${r.curso}</td>
                            <td>${r.codigo}</td>
                            <td>${r.seccion}</td>
                            <td>${r.jornada}</td>
                        </tr>`;
                    });
                }
                html += '</tbody></table></div>';
                reportContent.innerHTML = html;

            } else if (tab === 'error') {
                successBtn.classList.remove('active');
                errorBtn.classList.add('active');

                if (data.error_lines.length === 0) {
                    reportContent.innerHTML = `
                        <div class="report-title">Datos con error</div>
                        <p style="font-size: 13px; color: var(--color-texto-secundario); text-align: left; padding: 10px 0;">
                            No se detectaron errores en este archivo.
                        </p>`;
                    return;
                }

                let html = '<div class="report-title">Datos con error</div>';
                html += '<ul class="error-log-list">';
                data.error_lines.forEach(line => {
                    html += `<li class="error-log-item">- ${line}</li>`;
                });
                html += '</ul>';
                reportContent.innerHTML = html;
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const profileToggle = document.getElementById('profileToggle');
            const profileDropdown = document.getElementById('profileDropdown');
            const hamburgerBtn = document.getElementById('hamburgerBtn');
            const adminSidebar = document.getElementById('adminSidebar');
            const mainContent = document.getElementById('mainContent');

            // Cargar selección por defecto al cargar la página
            showSelectionView();

            // Dropdown Toggle
            profileToggle.addEventListener('click', (e) => {
                e.stopPropagation();
                profileDropdown.classList.toggle('active');
            });

            document.addEventListener('click', () => {
                profileDropdown.classList.remove('active');
            });

            // Hamburger Sidebar Toggle
            hamburgerBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                adminSidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
            });

            mainContent.addEventListener('click', () => {
                adminSidebar.classList.add('collapsed');
                mainContent.classList.add('expanded');
            });

            adminSidebar.addEventListener('click', (e) => {
                e.stopPropagation();
            });

            // --- EVENTOS DRAG & DROP: USUARIOS ---
            const fileInputUsuarios = document.getElementById('archivo_csv_usuarios');
            const dropAreaUsuarios = document.getElementById('dropAreaUsuarios');

            dropAreaUsuarios.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropAreaUsuarios.style.borderColor = 'var(--color-azul)';
                dropAreaUsuarios.style.backgroundColor = 'rgba(0, 45, 114, 0.02)';
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropAreaUsuarios.addEventListener(eventName, () => {
                    dropAreaUsuarios.style.borderColor = '#cbd5e0';
                    dropAreaUsuarios.style.backgroundColor = '#fcfcfc';
                });
            });

            dropAreaUsuarios.addEventListener('drop', (e) => {
                e.preventDefault();
                if (e.dataTransfer.files.length > 0) {
                    fileInputUsuarios.files = e.dataTransfer.files;
                    uploadCSVFile('usuarios');
                }
            });

            // --- EVENTOS DRAG & DROP: CURSOS ---
            const fileInputCursos = document.getElementById('archivo_csv_cursos');
            const dropAreaCursos = document.getElementById('dropAreaCursos');

            dropAreaCursos.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropAreaCursos.style.borderColor = 'var(--color-oro)';
                dropAreaCursos.style.backgroundColor = 'rgba(172, 132, 0, 0.02)';
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropAreaCursos.addEventListener(eventName, () => {
                    dropAreaCursos.style.borderColor = '#cbd5e0';
                    dropAreaCursos.style.backgroundColor = '#fcfcfc';
                });
            });

            dropAreaCursos.addEventListener('drop', (e) => {
                e.preventDefault();
                if (e.dataTransfer.files.length > 0) {
                    fileInputCursos.files = e.dataTransfer.files;
                    uploadCSVFile('cursos');
                }
            });
        });
    </script>
</body>
</html>
