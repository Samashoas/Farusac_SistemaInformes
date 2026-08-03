<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión - Sistema de Informes</title>
    <!-- Google Fonts: Outfit (headings) and Inter (body) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@400;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        /* CSS Reset y variables */
        :root {
            --color-azul: #002D72;
            /* Pantone 288C */
            --color-dorado: #AC8400;
            /* Pantone 118C */
            --color-terracota: #B94700;
            /* Pantone 1525C */
            --color-texto-oscuro: #2d3748;
            --color-texto-claro: #718096;
            --color-fondo: #f7fafc;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: radial-gradient(circle at 0% 0%, rgba(0, 45, 114, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 100% 100%, rgba(185, 71, 0, 0.04) 0%, transparent 50%),
                var(--color-fondo);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
            overflow-x: hidden;
        }

        /* Animaciones */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        /* Contenedor Principal */
        .login-container {
            width: 100%;
            max-width: 480px;
            display: flex;
            flex-direction: column;
            align-items: center;
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        /* Header del Logo (Estructura de la plantilla) */
        .logo-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
            animation: fadeIn 1s ease-out both;
            animation-delay: 0.2s;
            width: 100%;
            padding: 0 10px;
        }

        .logo-img {
            max-width: 100%;
            height: 120px;
            object-fit: contain;
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .logo-img:hover {
            transform: scale(1.025);
        }

        /* Tarjeta de Inicio de Sesión */
        .login-card {
            background:
                linear-gradient(rgba(255, 255, 255, 0.88), rgba(255, 255, 255, 0.88)) padding-box,
                linear-gradient(135deg, var(--color-azul), var(--color-dorado), var(--color-terracota)) border-box;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 2px solid transparent;
            border-radius: 18px;
            padding: 40px 30px;
            width: 100%;
            text-align: center;
            box-shadow: 0 15px 35px rgba(0, 45, 114, 0.04),
                0 5px 15px rgba(0, 0, 0, 0.02);
            position: relative;
            transition: all 0.3s ease;
        }

        .login-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 40px rgba(0, 45, 114, 0.07),
                0 8px 20px rgba(0, 0, 0, 0.04);
        }

        /* Títulos de la Tarjeta */
        .card-title {
            font-family: 'Outfit', sans-serif;
            font-size: 22px;
            font-weight: 700;
            color: var(--color-texto-oscuro);
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .card-subtitle {
            font-family: 'Outfit', sans-serif;
            font-size: 11px;
            font-weight: 600;
            color: var(--color-terracota);
            letter-spacing: 0.1em;
            text-transform: uppercase;
            margin-bottom: 35px;
            opacity: 0.9;
        }

        /* Botón de Google */
        .google-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 14px 24px;
            background-color: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 30px;
            color: #3c4043;
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            text-decoration: none;
            letter-spacing: 0.05em;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            outline: none;
        }

        .google-btn:hover {
            background-color: #ffffff;
            border-color: var(--color-dorado);
            color: var(--color-azul);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(0, 45, 114, 0.08);
        }

        .google-btn:active {
            transform: translateY(1px);
            background-color: #f7fafc;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        /* Caja de Alerta de Errores */
        .alert-box {
            display: flex;
            align-items: center;
            background-color: rgba(185, 71, 0, 0.08);
            border-left: 3px solid var(--color-terracota);
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 25px;
            text-align: left;
            animation: fadeIn 0.5s ease-out;
        }

        .alert-icon {
            width: 20px;
            height: 20px;
            color: var(--color-terracota);
            margin-right: 12px;
            flex-shrink: 0;
        }

        .alert-text {
            font-size: 12.5px;
            color: #8c3600;
            font-weight: 500;
            line-height: 1.4;
        }

        /* Footer de la página */
        .footer-text {
            margin-top: 25px;
            font-size: 11px;
            color: var(--color-texto-claro);
            text-align: center;
            letter-spacing: 0.02em;
            animation: fadeIn 1.2s ease-out both;
            animation-delay: 0.4s;
        }
    </style>
</head>

<body>

    <div class="login-container">

        <!-- Header con el Logo de FARUSAC -->
        <div class="logo-wrapper">
            <img src="{{ asset('images/FarusacLogo.png') }}" class="logo-img" alt="Logo FARUSAC">
        </div>

        <!-- Tarjeta de Login -->
        <div class="login-card">

            <h2 class="card-title">Sistema de Informes</h2>
            <div class="card-subtitle">Acceso Seguro - Solo Cuentas Autorizadas</div>

            <!-- Mostrar errores si existen (Dominio inválido, error de login, etc.) -->
            @if ($errors->any())
                <div class="alert-box">
                    <svg class="alert-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003zM12 8.25a.75.75 0 01.75.75v3.75a.75.75 0 01-1.5 0V9a.75.75 0 01.75-.75zm0 8.25a.75.75 0 100-1.5.75.75 0 000 1.5z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="alert-text">{{ $errors->first('correo') }}</span>
                </div>
            @endif

            <!-- Botón de Inicio de Sesión de Google -->
            <a href="{{ route('google.login') }}" class="google-btn">
                <!-- SVG Oficial de Google -->
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18"
                    style="margin-right: 12px; display: block;">
                    <path fill="#4285F4"
                        d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                    <path fill="#34A853"
                        d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                    <path fill="#FBBC05"
                        d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" />
                    <path fill="#EA4335"
                        d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" />
                </svg>
                Acceder con Google
            </a>

        </div>

        <!-- Footer con información institucional -->
        <div class="footer-text">
            © 2026 Facultad de Arquitectura - USAC. Todos los derechos reservados.
        </div>

    </div>

</body>

</html>