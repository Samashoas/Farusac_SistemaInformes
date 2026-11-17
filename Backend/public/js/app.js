/**
 * FARUSAC - SISTEMA DE INFORMES: SCRIPT GLOBAL DE INTERFAZ (app.js)
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Manejo del Dropdown de Perfil
    const profileToggle = document.getElementById('profileToggle');
    const profileDropdown = document.getElementById('profileDropdown');

    if (profileToggle && profileDropdown) {
        profileToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            profileDropdown.classList.toggle('active');
            profileToggle.classList.toggle('active');
        });

        document.addEventListener('click', () => {
            profileDropdown.classList.remove('active');
            profileToggle.classList.remove('active');
        });

        profileDropdown.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    }

    // 2. Manejo del Menú Lateral (Sidebar) y Botón Hamburguesa
    const hamburgerBtn = document.getElementById('hamburgerBtn');
    const sidebar = document.getElementById('appSidebar') || document.querySelector('.admin-sidebar');
    const mainContent = document.getElementById('mainContent');

    if (hamburgerBtn && sidebar) {
        hamburgerBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            const willBeCollapsed = !sidebar.classList.contains('collapsed');
            if (willBeCollapsed) {
                sidebar.classList.add('collapsed');
                if (mainContent) mainContent.classList.add('expanded');
            } else {
                sidebar.classList.remove('collapsed');
                if (mainContent) mainContent.classList.remove('expanded');
            }
        });

        // Cerrar sidebar al hacer click en el contenido principal en pantallas pequeñas
        if (mainContent) {
            mainContent.addEventListener('click', () => {
                if (window.innerWidth <= 900 && !sidebar.classList.contains('collapsed')) {
                    sidebar.classList.add('collapsed');
                    mainContent.classList.add('expanded');
                }
            });
        }

        sidebar.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    }

    // 3. Manejador global de la tecla Escape para cerrar modales
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.active, .custom-modal-overlay.active, .info-modal-overlay.active, .modal-backdrop.active').forEach(m => {
                m.classList.remove('active');
            });
            document.body.style.overflow = '';
        }
    });

    // 4. Disparar notificaciones toast de sesión automáticamente (Flash messages)
    const toastEl = document.getElementById('toastNotification') || document.getElementById('globalToast');
    if (toastEl) {
        if (toastEl.dataset.success) window.showToast(toastEl.dataset.success, 'success');
        else if (toastEl.dataset.error) window.showToast(toastEl.dataset.error, 'error');
        else if (toastEl.dataset.warning) window.showToast(toastEl.dataset.warning, 'warning');
        else if (toastEl.dataset.info) window.showToast(toastEl.dataset.info, 'info');
    }
});

/**
 * Función global para obtener el token CSRF del meta tag.
 */
window.getCsrfToken = function () {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
};

/**
 * Función global para abrir un modal por su ID.
 * @param {string} id - ID del elemento modal
 */
window.openModal = function (id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
};
window.abrirModal = window.openModal;

/**
 * Función global para cerrar un modal por su ID o todos los activos si no se especifica.
 * @param {string} [id] - ID opcional del elemento modal
 */
window.closeModal = function (id) {
    if (!id) {
        document.querySelectorAll('.modal-overlay.active, .custom-modal-overlay.active, .info-modal-overlay.active, .modal-backdrop.active').forEach(m => {
            m.classList.remove('active');
        });
        document.body.style.overflow = '';
        return;
    }
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
};
window.cerrarModal = window.closeModal;

/**
 * Cerrar modal si se hace click fuera del contenedor.
 */
window.cerrarModalFuera = function (event, id) {
    if (event.target && event.target.id === id) {
        window.closeModal(id);
    }
};

/**
 * Función global para desplegar notificaciones Toast uniformes.
 * @param {string} message - Texto de la notificación
 * @param {string} type - 'success', 'error', 'warning', o 'info'
 * @param {number} duration - Duración en milisegundos (por defecto 3500ms)
 */
window.showToast = function (message, type = 'success', duration = 3500) {
    let toast = document.getElementById('toastNotification') || document.getElementById('globalToast');
    let toastMsg = document.getElementById('globalToastMessage');
    let toastIcon = document.getElementById('globalToastIcon');

    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'toastNotification';
        toast.className = 'toast-notification';
        document.body.appendChild(toast);
    }

    // Reiniciar clases de tipo
    toast.className = 'toast-notification ' + type + ' active show';

    if (toastIcon && toastMsg) {
        if (type === 'error') {
            toastIcon.innerHTML = `<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;
        } else if (type === 'warning') {
            toastIcon.innerHTML = `<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>`;
        } else {
            toastIcon.innerHTML = `<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;
        }
        toastMsg.textContent = message;
    } else {
        toast.textContent = message;
    }

    // Autoocultar tras la duración
    clearTimeout(window._toastTimeout);
    window._toastTimeout = setTimeout(() => {
        toast.classList.remove('active', 'show');
    }, duration);
};
