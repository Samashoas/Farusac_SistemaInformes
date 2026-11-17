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

    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
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