<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrador - Sistema de Informes</title>
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

        /* Fila Superior Centrada (Carga de Datos) */
        .top-row {
            width: 100%;
            display: flex;
            justify-content: center;
        }

        /* Fila Inferior (3 Columnas) */
        .bottom-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            width: 100%;
        }

        @media (max-width: 900px) {
            .bottom-row {
                grid-template-columns: 1fr;
            }
        }

        /* --- CARDS GENERALES --- */
        .dashboard-card {
            background: 
                linear-gradient(#ffffff, #ffffff) padding-box,
                linear-gradient(135deg, rgba(0, 45, 114, 0.1), rgba(172, 132, 0, 0.1)) border-box;
            border: 2px solid transparent;
            border-radius: var(--border-radius-card);
            padding: 35px 25px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.02);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
        }

        /* Tarjeta Destacada (Carga de Datos en Fila Superior) */
        .dashboard-card.featured {
            max-width: 380px;
            width: 100%;
        }

        .dashboard-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(0, 45, 114, 0.05);
        }

        /* Hover específico según el rol/sección usando los colores */
        .card-carga:hover {
            background-image: linear-gradient(white, white), 
                              linear-gradient(135deg, var(--color-azul), var(--color-dorado));
        }

        .card-usuarios:hover {
            background-image: linear-gradient(white, white), 
                              linear-gradient(135deg, var(--color-azul), var(--color-azul));
        }

        .card-cursos:hover {
            background-image: linear-gradient(white, white), 
                              linear-gradient(135deg, var(--color-dorado), var(--color-dorado));
        }

        .card-informes:hover {
            background-image: linear-gradient(white, white), 
                              linear-gradient(135deg, var(--color-terracota), var(--color-terracota));
        }

        /* Cabecera de la Tarjeta */
        .card-header-line {
            width: 100%;
            padding-bottom: 15px;
            border-bottom: 1.5px solid #edf2f7;
            margin-bottom: 25px;
        }

        .card-title {
            font-family: 'Outfit', sans-serif;
            font-size: 18px;
            font-weight: 700;
            color: var(--color-texto-oscuro);
            letter-spacing: 0.05em;
            text-transform: uppercase;
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
            <svg class="profile-arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 12 15 18 9"></polyline>
            </svg>

            <!-- Menú Desplegable -->
            <div class="dropdown-menu" id="profileDropdown">
                <!-- Formulario POST para cerrar sesión de manera segura (con cookies de sesión) -->
                <form action="{{ route('logout') }}" method="POST" id="logout-form" style="display: none;">
                    @csrf
                </form>
                <button type="button" onclick="event.stopPropagation(); document.getElementById('logout-form').submit();" class="dropdown-item">
                    Cerrar sesión
                </button>
            </div>
        </div>
    </header>

    <!-- Cuerpo Principal de Opciones -->
    <main class="main-content">
        <div class="cards-grid">
            
            <!-- Fila Superior: Carga de Datos (Centrada y Destacada) -->
            <div class="top-row">
                <div class="dashboard-card featured card-carga">
                    <div class="card-header-line">
                        <h3 class="card-title">Carga de Datos</h3>
                    </div>
                    <!-- Icono SVG: Hoja de datos con cilindro de Base de Datos -->
                    <div class="card-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#002D72" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width: 100%; height: 100%;">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                            <!-- Base de datos sutil -->
                            <ellipse cx="12" cy="18" rx="3" ry="1.5" stroke="#AC8400" fill="none"></ellipse>
                            <path d="M9 18v2.5c0 .8 1.3 1.5 3 1.5s3-.7 3-1.5V18" stroke="#AC8400"></path>
                        </svg>
                    </div>
                    <a href="{{ route('admin.carga-datos') }}" class="card-btn">Ir a Carga de Datos</a>
                </div>
            </div>

            <!-- Fila Inferior: 3 Columnas (Gestiones) -->
            <div class="bottom-row">
                
                <!-- Gestión de Usuarios -->
                <div class="dashboard-card card-usuarios">
                    <div class="card-header-line">
                        <h3 class="card-title">Gestión de Usuarios</h3>
                    </div>
                    <!-- Icono SVG: Contorno de Perfil de Usuario -->
                    <div class="card-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#002D72" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width: 100%; height: 100%;">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <a href="#" class="card-btn">Ir a Gestión de Usuarios</a>
                </div>

                <!-- Gestión de Cursos -->
                <div class="dashboard-card card-cursos">
                    <div class="card-header-line">
                        <h3 class="card-title">Gestión de Cursos</h3>
                    </div>
                    <!-- Icono SVG: Libro Abierto -->
                    <div class="card-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#AC8400" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width: 100%; height: 100%;">
                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                        </svg>
                    </div>
                    <a href="#" class="card-btn">Ir a Gestión de Cursos</a>
                </div>

                <!-- Gestión de Informes -->
                <div class="dashboard-card card-informes">
                    <div class="card-header-line">
                        <h3 class="card-title">Gestión de Informes</h3>
                    </div>
                    <!-- Icono SVG: Documento con Gráfico Circular -->
                    <div class="card-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#B94700" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width: 100%; height: 100%;">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <circle cx="10" cy="14" r="3"></circle>
                            <path d="M10 11v3h3"></path>
                        </svg>
                    </div>
                    <a href="#" class="card-btn">Ir a Gestión de Informes</a>
                </div>

            </div>

        </div>
    </main>

    <!-- Script para Toggle de Menú del Perfil -->
    <script>
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
        });
    </script>

</body>

</html>