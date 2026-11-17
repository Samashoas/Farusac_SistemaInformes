/**
 * FARUSAC - SISTEMA DE INFORMES: LÓGICA DE PERFIL (perfil.js)
 */

let initialTelefono = '';
let updateRouteUrl = '';

function initPerfil(numeroInicial, routeUrl) {
    initialTelefono = numeroInicial || '';
    updateRouteUrl = routeUrl || '/docente/perfil/actualizar';
    const inputTelefono = document.getElementById('inputTelefono');
    if (inputTelefono) {
        inputTelefono.addEventListener('input', checkChanges);
    }
}

function checkChanges() {
    const inputTelefono = document.getElementById('inputTelefono');
    const actionsContainer = document.getElementById('profileActionsContainer');
    if (!inputTelefono || !actionsContainer) return;

    const currentVal = inputTelefono.value.trim();
    const storedVal = (initialTelefono || '').trim();

    if (currentVal !== storedVal) {
        actionsContainer.classList.add('visible');
    } else {
        actionsContainer.classList.remove('visible');
    }
}

function cancelarEdicion() {
    const inputTelefono = document.getElementById('inputTelefono');
    const actionsContainer = document.getElementById('profileActionsContainer');
    if (inputTelefono) inputTelefono.value = initialTelefono || '';
    if (actionsContainer) actionsContainer.classList.remove('visible');
}

async function guardarPerfil() {
    const inputTelefono = document.getElementById('inputTelefono');
    const actionsContainer = document.getElementById('profileActionsContainer');
    const btnConfirmar = document.getElementById('btnConfirmar');
    const btnCancelar = document.getElementById('btnCancelar');

    if (!inputTelefono) return;
    const nuevoTelefono = inputTelefono.value.trim();

    if (btnConfirmar) {
        btnConfirmar.disabled = true;
        btnConfirmar.textContent = 'GUARDANDO...';
    }
    if (btnCancelar) btnCancelar.disabled = true;

    try {
        const response = await fetch(updateRouteUrl, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.getCsrfToken(),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ numero: nuevoTelefono })
        });

        const data = await response.json();

        if (btnConfirmar) {
            btnConfirmar.disabled = false;
            btnConfirmar.textContent = 'CONFIRMAR';
        }
        if (btnCancelar) btnCancelar.disabled = false;

        if (response.ok && data.success) {
            initialTelefono = nuevoTelefono;
            inputTelefono.value = nuevoTelefono;
            if (actionsContainer) actionsContainer.classList.remove('visible');
            window.showToast(data.message || 'Perfil actualizado exitosamente.', 'success');
        } else {
            window.showToast(data.message || 'Error al actualizar el perfil.', 'error');
        }
    } catch (err) {
        console.error('Error al actualizar perfil:', err);
        if (btnConfirmar) {
            btnConfirmar.disabled = false;
            btnConfirmar.textContent = 'CONFIRMAR';
        }
        if (btnCancelar) btnCancelar.disabled = false;
        window.showToast('Error de conexión con el servidor.', 'error');
    }
}