@php
    $user = Auth::user();
    $dashboardRoute = 'login';
    if ($user) {
        if ($user->isAdministrador()) {
            $dashboardRoute = 'admin.dashboard';
        } elseif ($user->isCoordinador()) {
            $dashboardRoute = 'coordinador.dashboard';
        } else {
            $dashboardRoute = 'docente.dashboard';
        }
    }
@endphp

<header class="admin-header">
    <div class="header-left">
        <button type="button" class="hamburger-btn" id="hamburgerBtn" title="Alternar Menú" aria-label="Alternar Menú">
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
        </button>
        <a href="{{ route($dashboardRoute) }}" title="Ir al Panel Principal">
            <img src="{{ asset('images/FarusacLogo.png') }}" class="header-logo" alt="Logo FARUSAC">
        </a>
    </div>

    <h1 class="header-title">Sistema de Informes</h1>

    @if ($user)
        <div class="header-right" id="profileToggle" title="Opciones de Cuenta">
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
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>Cerrar Sesión</span>
                </button>
            </div>
        </div>
    @endif
</header>
