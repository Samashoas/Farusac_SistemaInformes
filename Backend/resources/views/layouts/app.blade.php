<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('/') }}">
    <title>@yield('title', 'FARUSAC - Sistema de Informes')</title>

    <!-- Google Fonts: Inter & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Estilos Globales Centralizados -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/report-modal.css') }}">

    @stack('styles')
</head>
<body>
    @include('partials.header')

    <div class="app-container">
        @include('partials.sidebar')

        <main class="main-content expanded" id="mainContent">
            @yield('content')
        </main>
    </div>

    <!-- Notificación Toast con Componente Blade -->
    <x-toast />

    <!-- Modal Superpuesto Global de Visualización de Informes Oficiales -->
    <x-report-preview-modal />

    <!-- Scripts Globales Centralizados -->
    <script src="{{ asset('js/app.js') }}"></script>

    @stack('scripts')
</body>
</html>
