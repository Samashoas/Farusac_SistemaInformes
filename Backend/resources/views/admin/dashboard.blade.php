<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrador - Sistema de Informes</title>
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
        }

        /* --- HEADER / BARRA SUPERIOR --- */
        .admin-header {
            background-color: #ffffff;
            border-bottom: 2px solid transparent;
            /* Borde con gradiente en la parte inferior */
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

        .header-right:hover .profile-arrow {
            transform: translateY(2px);
        }

        /* Menú Desplegable */
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

        /* --- CUERPO PRINCIPAL --- */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 20px;
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
        }

        /* Grid de Cards */
        .cards-grid {
            display: flex;
            flex-direction: column;
            gap: 40px;
            width: 100%;
            align-items: center;
        }

        /* Fila Superior (3 Columnas - Gestiones) */
        .top-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            width: 100%;
        }

        @media (max-width: 900px) {
            .top-row {
                grid-template-columns: 1fr;
            }
        }

        /* Fila Inferior Centrada (Carga de Datos) */
        .bottom-row {
            width: 100%;
            display: flex;
            justify-content: center;
        }

        /* --- CARDS GENERALES --- */
        .dashboard-card {
            background: #ffffff;
            border: 1.5px solid #edf2f7;
            border-radius: var(--border-radius-card);
            padding: 85px 25px 35px 25px; /* Dejar espacio para la barra de cabecera superior */
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.02);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
        }

        /* Tarjeta Destacada (Carga de Datos en Fila Superior) */
        .dashboard-card.featured {
            max-width: 380px;
            width: 100%;
        }

        .dashboard-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(0, 45, 114, 0.05);
            border-color: transparent;
        }

        /* Hover específico según el rol/sección usando los colores */
        .card-carga:hover {
            background: 
                linear-gradient(#ffffff, #ffffff) padding-box,
                linear-gradient(135deg, var(--color-azul), var(--color-dorado)) border-box;
            border: 1.5px solid transparent;
        }

        .card-usuarios:hover {
            background: 
                linear-gradient(#ffffff, #ffffff) padding-box,
                linear-gradient(135deg, var(--color-azul), var(--color-azul)) border-box;
            border: 1.5px solid transparent;
        }

        .card-cursos:hover {
            background: 
                linear-gradient(#ffffff, #ffffff) padding-box,
                linear-gradient(135deg, var(--color-dorado), var(--color-dorado)) border-box;
            border: 1.5px solid transparent;
        }

        .card-informes:hover {
            background: 
                linear-gradient(#ffffff, #ffffff) padding-box,
                linear-gradient(135deg, var(--color-terracota), var(--color-terracota)) border-box;
            border: 1.5px solid transparent;
        }

        /* Nueva barra de cabecera del módulo con relleno de color */
        .card-header-bar {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 45px 0 20px;
            transition: all 0.3s ease;
            border-bottom: 1.5px solid #edf2f7;
        }

        /* Colores de relleno específicos por módulo */
        .card-carga .card-header-bar {
            background-color: rgba(0, 45, 114, 0.05);
            color: var(--color-azul);
        }
        .card-carga:hover .card-header-bar {
            background: linear-gradient(135deg, var(--color-azul), var(--color-dorado));
            color: #ffffff;
            border-bottom-color: transparent;
        }

        .card-usuarios .card-header-bar {
            background-color: rgba(0, 45, 114, 0.05);
            color: var(--color-azul);
        }
        .card-usuarios:hover .card-header-bar {
            background-color: var(--color-azul);
            color: #ffffff;
            border-bottom-color: transparent;
        }

        .card-cursos .card-header-bar {
            background-color: rgba(172, 132, 0, 0.05);
            color: var(--color-dorado);
        }
        .card-cursos:hover .card-header-bar {
            background-color: var(--color-dorado);
            color: #ffffff;
            border-bottom-color: transparent;
        }

        .card-informes .card-header-bar {
            background-color: rgba(185, 71, 0, 0.05);
            color: var(--color-terracota);
        }
        .card-informes:hover .card-header-bar {
            background-color: var(--color-terracota);
            color: #ffffff;
            border-bottom-color: transparent;
        }

        .card-title {
            font-family: 'Outfit', sans-serif;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            text-align: center;
        }

        /* Botón de más información (?) */
        .info-btn {
            position: absolute;
            right: 15px;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            border: 1.5px solid currentColor;
            background: none;
            color: currentColor;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Outfit', sans-serif;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            outline: none;
            padding: 0;
            z-index: 5;
        }

        .info-btn:hover {
            transform: scale(1.1);
            background-color: rgba(0, 0, 0, 0.05);
        }

        .dashboard-card:hover .info-btn:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        /* --- MODAL DE INFORMACIÓN --- */
        .info-modal-overlay {
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

        .info-modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-card {
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
            text-align: center;
            transform: scale(0.9);
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .info-modal-overlay.active .modal-card {
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

        .modal-header-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 20px auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-header-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .modal-title {
            font-family: 'Outfit', sans-serif;
            font-size: 22px;
            font-weight: 700;
            color: var(--color-texto-oscuro);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .modal-divider {
            width: 50px;
            height: 3px;
            background-color: var(--color-dorado);
            margin: 15px auto;
            border-radius: 2px;
        }

        .modal-description {
            font-size: 14.5px;
            color: var(--color-texto-claro);
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .modal-action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 35px;
            background-color: var(--color-azul);
            color: #ffffff;
            border: none;
            border-radius: 25px;
            font-family: 'Outfit', sans-serif;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0, 45, 114, 0.15);
            transition: all 0.2s ease;
        }

        .modal-action-btn:hover {
            background-color: #002257;
            box-shadow: 0 6px 14px rgba(0, 45, 114, 0.25);
        }

        /* Icono de la Tarjeta */
        .card-icon {
            width: 70px;
            height: 70px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.3s ease;
        }

        .card-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .dashboard-card:hover .card-icon {
            transform: scale(1.08);
        }

        /* Botones de las Tarjetas */
        .card-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 12px 20px;
            background-color: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 25px;
            font-family: 'Outfit', sans-serif;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            text-decoration: none;
            letter-spacing: 0.05em;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.03);
            transition: all 0.25s ease;
            outline: none;
            cursor: pointer;
        }

        /* Estilo de Hover para cada botón */
        .card-carga .card-btn {
            color: var(--color-azul);
        }

        .card-carga:hover .card-btn {
            background-color: var(--color-azul);
            border-color: var(--color-azul);
            color: #ffffff;
            box-shadow: 0 6px 14px rgba(0, 45, 114, 0.15);
        }

        .card-usuarios .card-btn {
            color: var(--color-azul);
        }

        .card-usuarios:hover .card-btn {
            background-color: var(--color-azul);
            border-color: var(--color-azul);
            color: #ffffff;
            box-shadow: 0 6px 14px rgba(0, 45, 114, 0.15);
        }

        .card-cursos .card-btn {
            color: var(--color-dorado);
        }

        .card-cursos:hover .card-btn {
            background-color: var(--color-dorado);
            border-color: var(--color-dorado);
            color: #ffffff;
            box-shadow: 0 6px 14px rgba(172, 132, 0, 0.15);
        }

        .card-informes .card-btn {
            color: var(--color-terracota);
        }

        .card-informes:hover .card-btn {
            background-color: var(--color-terracota);
            border-color: var(--color-terracota);
            color: #ffffff;
            box-shadow: 0 6px 14px rgba(185, 71, 0, 0.15);
        }
    </style>
</head>

<body>

    <!-- Header / Barra Superior -->
    <header class="admin-header">
        <div class="header-left">
            <img src="{{ asset('images/FarusacLogo.png') }}" class="header-logo" alt="Logo FARUSAC">
        </div>

        <h1 class="header-title">Sistema de Informes</h1>

        <div class="header-right" id="profileToggle">
            <div class="profile-info">
                <!-- Se obtienen los datos del usuario autenticado dinámicamente -->
                <div class="profile-name">{{ Auth::user()->nombre }}</div>
                <div class="profile-email">{{ Auth::user()->correo }}</div>
            </div>
            <!-- Inicial del Nombre en Avatar con estilo premium -->
            <div class="profile-avatar">
                {{ strtoupper(substr(Auth::user()->nombre, 0, 1)) }}
            </div>
            <!-- Icono Flecha Desplegable SVG -->
            <svg class="profile-arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 12 15 18 9"></polyline>
            </svg>

            <!-- Menú Desplegable -->
            <div class="dropdown-menu" id="profileDropdown">
                <!-- Formulario POST para cerrar sesión de manera segura (con cookies de sesión) -->
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

    <!-- Cuerpo Principal de Opciones -->
    <main class="main-content">
        <div class="cards-grid">

            <!-- Fila Superior: 3 Columnas (Gestiones) -->
            <div class="top-row">

                <!-- Gestión de Usuarios -->
                <div class="dashboard-card card-usuarios">
                    <div class="card-header-bar">
                        <h3 class="card-title">Gestión de Usuarios</h3>
                        <button type="button" class="info-btn" onclick="showInfo('usuarios')">?</button>
                    </div>
                    <!-- Imagen Icono: Gestión de Usuarios -->
                    <div class="card-icon">
                        <img src="{{ asset('images/Admin/GestionUsuarios.png') }}" alt="Gestión de Usuarios">
                    </div>
                    <a href="{{ route('admin.usuarios') }}" class="card-btn">Ir a Gestión de Usuarios</a>
                </div>

                <!-- Gestión de Cursos -->
                <div class="dashboard-card card-cursos">
                    <div class="card-header-bar">
                        <h3 class="card-title">Gestión de Cursos</h3>
                        <button type="button" class="info-btn" onclick="showInfo('cursos')">?</button>
                    </div>
                    <!-- Imagen Icono: Gestión de Cursos -->
                    <div class="card-icon">
                        <img src="{{ asset('images/Admin/GestionCursos.png') }}" alt="Gestión de Cursos">
                    </div>
                    <a href="#" class="card-btn">Ir a Gestión de Cursos</a>
                </div>

                <!-- Gestión de Informes -->
                <div class="dashboard-card card-informes">
                    <div class="card-header-bar">
                        <h3 class="card-title">Gestión de Informes</h3>
                        <button type="button" class="info-btn" onclick="showInfo('informes')">?</button>
                    </div>
                    <!-- Imagen Icono: Gestión de Informes -->
                    <div class="card-icon">
                        <img src="{{ asset('images/Admin/GestionInformes.png') }}" alt="Gestión de Informes">
                    </div>
                    <a href="#" class="card-btn">Ir a Gestión de Informes</a>
                </div>

            </div>

            <!-- Fila Inferior: Carga de Datos (Centrada y Destacada) -->
            <div class="bottom-row">
                <div class="dashboard-card featured card-carga">
                    <div class="card-header-bar">
                        <h3 class="card-title">Carga de Datos</h3>
                        <button type="button" class="info-btn" onclick="showInfo('carga')">?</button>
                    </div>
                    <!-- Imagen Icono: Carga de Datos -->
                    <div class="card-icon">
                        <img src="{{ asset('images/Admin/CargaDatos.png') }}" alt="Carga de Datos">
                    </div>
                    <a href="{{ route('admin.carga-datos') }}" class="card-btn">Ir a Carga de Datos</a>
                </div>
            </div>

        </div>
    </main>

    <!-- Modal de Información Emergente -->
    <div class="info-modal-overlay" id="infoModal" onclick="closeModal()">
        <div class="modal-card" onclick="event.stopPropagation()">
            <button type="button" class="modal-close-btn" onclick="closeModal()">×</button>
            <div class="modal-header-icon">
                <img id="modalIcon" src="" alt="">
            </div>
            <h2 class="modal-title" id="modalTitle">Título</h2>
            <div class="modal-divider"></div>
            <p class="modal-description" id="modalDescription">Descripción</p>
            <button type="button" class="modal-action-btn" onclick="closeModal()">Entendido</button>
        </div>
    </div>

    <!-- Scripts para Toggles de Menú de Perfil y Modales de Información -->
    <script>
        const moduleInfo = {
            carga: {
                title: 'Carga de Datos',
                description: 'Este módulo permite la carga masiva de usuarios (docentes, jefes y administradores) mediante un archivo estructurado en formato CSV. Facilita la administración rápida de cuentas autorizadas para acceder al sistema.',
                image: "{{ asset('images/Admin/CargaDatos.png') }}"
            },
            usuarios: {
                title: 'Gestión de Usuarios',
                description: 'Permite administrar la información general de los usuarios registrados en la plataforma. Podrás buscar, filtrar, modificar roles e inhabilitar cuentas de acceso de forma individual y segura.',
                image: "{{ asset('images/Admin/GestionUsuarios.png') }}"
            },
            cursos: {
                title: 'Gestión de Cursos',
                description: 'Este espacio está dedicado a la creación, edición y asignación de cursos de la facultad de arquitectura. Permite asignar docentes correspondientes y organizar la planificación académica.',
                image: "{{ asset('images/Admin/GestionCursos.png') }}"
            },
            informes: {
                title: 'Gestión de Informes',
                description: 'Permite dar seguimiento a los informes de actividades consolidados de los docentes. Podrás visualizar estadísticas de entrega, revisar bitácoras, exportar datos y validar la información consolidada.',
                image: "{{ asset('images/Admin/GestionInformes.png') }}"
            }
        };

        function showInfo(moduleKey) {
            const info = moduleInfo[moduleKey];
            if (!info) return;

            document.getElementById('modalTitle').textContent = info.title;
            document.getElementById('modalDescription').textContent = info.description;
            document.getElementById('modalIcon').src = info.image;
            document.getElementById('modalIcon').alt = info.title;
            
            document.getElementById('infoModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('infoModal').classList.remove('active');
        }

        document.addEventListener('DOMContentLoaded', () => {
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
                    closeModal();
                }
            });
        });
    </script>

</body>

</html>