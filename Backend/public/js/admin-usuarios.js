/**
 * FARUSAC - SISTEMA DE INFORMES: GESTIÓN DE USUARIOS (admin-usuarios.js)
 */

let dbUsuarios = [];
let statusTargetUserId = null;

function initAdminUsuarios(usuarios) {
    dbUsuarios = Array.isArray(usuarios) ? usuarios : (usuarios?.data || []);
    renderTable();

    document.getElementById('searchInput')?.addEventListener('input', renderTable);
    document.getElementById('rolFilter')?.addEventListener('change', renderTable);
    document.getElementById('estadoFilter')?.addEventListener('change', renderTable);
    document.getElementById('addUserBtn')?.addEventListener('click', openAddUserModal);
}

function renderTable() {
    const tableBody = document.getElementById('usersTableBody');
    if (!tableBody) return;

    const searchVal = document.getElementById('searchInput')?.value.toLowerCase().trim() || '';
    const rolVal = document.getElementById('rolFilter')?.value || 'todos';
    const estadoVal = document.getElementById('estadoFilter')?.value || 'todos';

    const filtered = dbUsuarios.filter(user => {
        const matchesSearch =
            (user.nombre && user.nombre.toLowerCase().includes(searchVal)) ||
            (user.correo && user.correo.toLowerCase().includes(searchVal)) ||
            (user.numero && user.numero.includes(searchVal));

        const matchesRol = (rolVal === 'todos') || (user.rol === rolVal);
        const matchesEstado = (estadoVal === 'todos') || (user.estado === estadoVal);

        return matchesSearch && matchesRol && matchesEstado;
    });

    tableBody.innerHTML = '';

    if (filtered.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="8" class="no-records-row">No se encontraron usuarios con los filtros aplicados.</td>
            </tr>
        `;
        return;
    }

    filtered.forEach((user, idx) => {
        const tr = document.createElement('tr');
        tr.setAttribute('data-id', user.id);

        const rolFormatted = user.rol ? (user.rol.charAt(0).toUpperCase() + user.rol.slice(1)) : 'Docente';
        const plazaFormatted = user.plaza ? user.plaza.split(' ').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ') : 'Sin Plaza';
        const estadoClass = (user.estado || 'activo') === 'activo' ? 'active' : 'inactive';
        const estadoFormatted = user.estado ? (user.estado.charAt(0).toUpperCase() + user.estado.slice(1)) : 'Activo';

        tr.innerHTML = `
            <td class="td-id">${idx + 1}.</td>
            <td class="td-nombre">${user.nombre}</td>
            <td class="td-correo">${user.correo}</td>
            <td class="td-numero">${user.numero || 'N/D'}</td>
            <td class="td-rol">${rolFormatted}</td>
            <td class="td-plaza">${plazaFormatted}</td>
            <td>
                <span class="status-badge clickable ${estadoClass}" onclick="confirmToggleEstado(${user.id})" title="Haga clic para cambiar estado">
                    ${estadoFormatted}
                </span>
            </td>
            <td>
                <button type="button" class="action-btn edit-btn" onclick="openEditUserModal(${user.id})" title="Editar Usuario">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 20px; height: 20px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                </button>
            </td>
        `;
        tableBody.appendChild(tr);
    });
}

function openAddUserModal() {
    window.openModal('addUserModal');
}

function closeAddUserModal() {
    window.closeModal('addUserModal');
    document.getElementById('addUserForm')?.reset();
}

function handleCreateUser(e) {
    e.preventDefault();
    const name = document.getElementById('newUserName')?.value.trim();
    const email = document.getElementById('newUserEmail')?.value.trim();
    const phone = document.getElementById('newUserPhone')?.value.trim();
    const role = document.getElementById('newUserRole')?.value;
    const plaza = document.getElementById('newUserPlaza')?.value;

    if (!name || !email || !phone || !role || !plaza) return;

    fetch("/admin/usuarios", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.getCsrfToken()
        },
        body: JSON.stringify({ nombre: name, correo: email, numero: phone, rol: role, plaza: plaza })
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                dbUsuarios.push(data.usuario);
                closeAddUserModal();
                renderTable();
                window.showToast('Usuario creado correctamente', 'success');
            } else {
                window.showToast(data.message || 'Error al crear el usuario', 'error');
            }
        })
        .catch(err => {
            console.error(err);
            window.showToast('Ocurrió un error al intentar registrar el usuario.', 'error');
        });
}

function openEditUserModal(id) {
    const user = dbUsuarios.find(u => u.id === id);
    if (!user) return;

    document.getElementById('editUserId').value = user.id;
    document.getElementById('editUserName').value = user.nombre;
    document.getElementById('editUserEmail').value = user.correo;
    document.getElementById('editUserPhone').value = user.numero ?? '';
    document.getElementById('editUserRole').value = user.rol || 'docente';
    document.getElementById('editUserPlaza').value = user.plaza || 'titular';

    window.openModal('editUserModal');
}

function closeEditUserModal() {
    window.closeModal('editUserModal');
    document.getElementById('editUserForm')?.reset();
}

function handleUpdateUser(e) {
    e.preventDefault();
    const id = parseInt(document.getElementById('editUserId').value);
    const phone = document.getElementById('editUserPhone').value.trim();
    const role = document.getElementById('editUserRole').value;
    const plaza = document.getElementById('editUserPlaza').value;

    if (!phone || !role || !plaza) return;

    fetch(`/admin/usuarios/${id}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.getCsrfToken()
        },
        body: JSON.stringify({ numero: phone, rol: role, plaza: plaza })
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const idx = dbUsuarios.findIndex(u => u.id === id);
                if (idx !== -1) {
                    dbUsuarios[idx] = data.usuario;
                }
                closeEditUserModal();
                renderTable();
                window.showToast('Usuario actualizado correctamente', 'success');
            } else {
                window.showToast(data.message || 'Error al actualizar el usuario', 'error');
            }
        })
        .catch(err => {
            console.error(err);
            window.showToast('Ocurrió un error al intentar actualizar el usuario.', 'error');
        });
}

function confirmToggleEstado(id) {
    const user = dbUsuarios.find(u => u.id === id);
    if (!user) return;

    statusTargetUserId = id;
    const alertTitle = document.getElementById('alertTitle');
    const alertDesc = document.getElementById('alertDesc');
    const confirmBtn = document.getElementById('confirmStatusBtn');
    const statusCard = document.getElementById('confirmStatusCard');
    const alertIcon = document.getElementById('alertIcon');

    if (user.estado === 'activo') {
        if (statusCard) statusCard.classList.remove('success-style');
        if (alertIcon) alertIcon.textContent = '⚠';
        if (alertTitle) alertTitle.textContent = '¿ESTÁ SEGURO QUE QUIERE DESACTIVAR EL USUARIO?';
        if (alertDesc) alertDesc.textContent = 'El usuario perderá acceso al sistema hasta que vuelva a ser habilitado.';
        if (confirmBtn) confirmBtn.textContent = 'Desactivar';
    } else {
        if (statusCard) statusCard.classList.add('success-style');
        if (alertIcon) alertIcon.textContent = '✔';
        if (alertTitle) alertTitle.textContent = '¿ESTÁ SEGURO QUE QUIERE ACTIVAR EL USUARIO?';
        if (alertDesc) alertDesc.textContent = 'El usuario volverá a tener acceso completo y regular al sistema.';
        if (confirmBtn) confirmBtn.textContent = 'Activar';
    }

    window.openModal('confirmStatusModal');
}

function closeConfirmStatusModal() {
    window.closeModal('confirmStatusModal');
    const statusCard = document.getElementById('confirmStatusCard');
    if (statusCard) statusCard.classList.remove('success-style');
    statusTargetUserId = null;
}

function executeToggleEstado() {
    if (statusTargetUserId === null) return;

    fetch(`/admin/usuarios/${statusTargetUserId}/toggle-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.getCsrfToken()
        }
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const idx = dbUsuarios.findIndex(u => u.id === statusTargetUserId);
                if (idx !== -1) {
                    dbUsuarios[idx] = data.usuario;
                }
                closeConfirmStatusModal();
                renderTable();
                window.showToast('Estado actualizado correctamente', 'info');
            } else {
                window.showToast(data.message || 'Error al modificar el estado', 'error');
            }
        })
        .catch(err => {
            console.error(err);
            window.showToast('Ocurrió un error al intentar cambiar el estado.', 'error');
        });
}
