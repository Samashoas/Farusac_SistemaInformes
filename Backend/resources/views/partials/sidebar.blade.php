@php
    $user = Auth::user();
@endphp

<aside class="admin-sidebar collapsed" id="appSidebar">
    <nav class="sidebar-nav">
        @if ($user && $user->isAdministrador())
            <!-- NAVEGACIÓN ADMINISTRADOR -->
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link sidebar-link-inicio {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>Inicio</span>
            </a>

            <a href="{{ route('admin.usuarios') }}" class="sidebar-link sidebar-link-usuarios {{ request()->routeIs('admin.usuarios*') ? 'active' : '' }}">
                <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span>Usuarios</span>
            </a>

            <a href="{{ route('admin.cursos') }}" class="sidebar-link sidebar-link-cursos {{ request()->routeIs('admin.cursos*') ? 'active' : '' }}">
                <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <span>Cursos</span>
            </a>

            <a href="{{ route('admin.informes') }}" class="sidebar-link sidebar-link-informes {{ request()->routeIs('admin.informes*') ? 'active' : '' }}">
                <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Informes</span>
            </a>

            <a href="{{ route('admin.carga-datos') }}" class="sidebar-link sidebar-link-carga {{ request()->routeIs('admin.carga-datos*') ? 'active' : '' }}">
                <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                <span>Carga de Datos</span>
            </a>

        @elseif ($user && $user->isCoordinador())
            <!-- NAVEGACIÓN COORDINADOR -->
            <a href="{{ route('coordinador.dashboard') }}" class="sidebar-link sidebar-link-inicio {{ request()->routeIs('coordinador.dashboard') ? 'active' : '' }}">
                <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>Inicio</span>
            </a>

            <a href="{{ route('coordinador.perfil') }}" class="sidebar-link sidebar-link-perfil {{ request()->routeIs('coordinador.perfil*') ? 'active' : '' }}">
                <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span>Perfil</span>
            </a>

            <div class="sidebar-section-title">Docencia</div>

            <a href="{{ route('coordinador.informes') }}" class="sidebar-link sidebar-link-informes {{ request()->routeIs('coordinador.informes') ? 'active' : '' }}">
                <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Mis Informes</span>
            </a>

            <a href="{{ route('coordinador.informes.crear') }}" class="sidebar-link {{ request()->routeIs('coordinador.informes.crear') ? 'active' : '' }}">
                <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <span>Crear Informe Docente</span>
            </a>

            <div class="sidebar-section-title">Coordinación</div>

            <a href="{{ route('coordinador.informes-coordinacion.crear') }}" class="sidebar-link {{ request()->routeIs('coordinador.informes-coordinacion.*') ? 'active' : '' }}">
                <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                </svg>
                <span>Informe de Área</span>
            </a>

        @else
            <!-- NAVEGACIÓN DOCENTE -->
            <a href="{{ route('docente.dashboard') }}" class="sidebar-link sidebar-link-inicio {{ request()->routeIs('docente.dashboard') ? 'active' : '' }}">
                <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>Inicio</span>
            </a>

            <a href="{{ route('docente.perfil') }}" class="sidebar-link sidebar-link-perfil {{ request()->routeIs('docente.perfil*') ? 'active' : '' }}">
                <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span>Perfil</span>
            </a>

            <a href="{{ route('docente.informes') }}" class="sidebar-link sidebar-link-informes {{ request()->routeIs('docente.informes') ? 'active' : '' }}">
                <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Informes</span>
            </a>

            <a href="{{ route('docente.informes.crear') }}" class="sidebar-link {{ request()->routeIs('docente.informes.crear') ? 'active' : '' }}">
                <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <span>Crear Informe</span>
            </a>
        @endif
    </nav>
</aside>
