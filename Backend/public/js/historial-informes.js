/**
 * FARUSAC - SISTEMA DE INFORMES: LÓGICA DE HISTORIAL DE INFORMES (historial-informes.js)
 */

let informeToDeleteId = null;

function applyFilters() {
    const searchVal = document.getElementById('searchInput')?.value.trim().toLowerCase() || '';
    const carreraVal = document.getElementById('filterCarrera')?.value.trim().toLowerCase() || '';
    const cursoVal = document.getElementById('filterCurso')?.value.trim().toLowerCase() || '';
    const periodoVal = document.getElementById('filterPeriodo')?.value.trim().toLowerCase() || '';
    const anioVal = document.getElementById('filterAnio')?.value.trim() || '';
    const mesVal = document.getElementById('filterMes')?.value.trim().toLowerCase() || '';

    const rows = document.querySelectorAll('#informesTableBody .informe-row');
    let visibleCount = 0;

    rows.forEach(row => {
        const rowCarrera = row.getAttribute('data-carrera') || '';
        const rowCurso = row.getAttribute('data-curso') || '';
        const rowCodigo = row.getAttribute('data-codigo') || '';
        const rowPeriodo = row.getAttribute('data-periodo') || '';
        const rowAnio = row.getAttribute('data-anio') || '';
        const rowMes = row.getAttribute('data-mes') || '';

        const matchesSearch = !searchVal || rowCurso.includes(searchVal) || rowCodigo.includes(searchVal);
        const matchesCarrera = !carreraVal || rowCarrera.includes(carreraVal);
        const matchesCurso = !cursoVal || rowCurso.includes(cursoVal);
        const matchesPeriodo = !periodoVal || rowPeriodo.includes(periodoVal);
        const matchesAnio = !anioVal || rowAnio === anioVal;
        const matchesMes = !mesVal || rowMes.includes(mesVal);

        if (matchesSearch && matchesCarrera && matchesCurso && matchesPeriodo && matchesAnio && matchesMes) {
            row.style.display = '';
            visibleCount++;
            const idCol = row.querySelector('.td-id');
            if (idCol) {
                idCol.textContent = `${visibleCount}.`;
            }
        } else {
            row.style.display = 'none';
        }
    });

    const noResultsRow = document.getElementById('noResultsRow');
    let noDataRow = document.getElementById('noDataRow');

    if (rows.length === 0) {
        if (!noDataRow) {
            const tbody = document.getElementById('informesTableBody');
            if (tbody) {
                noDataRow = document.createElement('tr');
                noDataRow.id = 'noDataRow';
                noDataRow.innerHTML = `<td colspan="8" class="no-records-row">No se han registrado informes todavía.</td>`;
                tbody.appendChild(noDataRow);
            }
        }
        if (noDataRow) noDataRow.style.display = '';
        if (noResultsRow) noResultsRow.style.display = 'none';
    } else {
        if (noDataRow) noDataRow.style.display = 'none';
        if (noResultsRow) {
            noResultsRow.style.display = (visibleCount === 0) ? '' : 'none';
        }
    }
}

function openDeleteModal(id, cursoNombre) {
    informeToDeleteId = id;
    const desc = document.getElementById('deleteModalDesc');
    if (desc) {
        desc.innerHTML = `Esta acción no se puede deshacer. Se eliminará permanentemente el informe de <b>${cursoNombre}</b> junto a todas sus semanas registradas.`;
    }
    window.openModal('deleteInformeModal');
}

function closeDeleteModal() {
    informeToDeleteId = null;
    window.closeModal('deleteInformeModal');
}

async function executeDeleteInforme() {
    if (!informeToDeleteId) return;

    const targetId = informeToDeleteId;
    const btn = document.getElementById('btnConfirmDelete');
    if (btn) {
        btn.disabled = true;
        btn.textContent = 'ELIMINANDO...';
    }

    const rolePrefix = window.historialRolePrefix || 'docente';
    const deleteUrl = `/${rolePrefix}/informes/${targetId}`;

    try {
        const response = await fetch(deleteUrl, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.getCsrfToken(),
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (btn) {
            btn.disabled = false;
            btn.textContent = 'CONFIRMAR';
        }
        closeDeleteModal();

        if (response.ok && data.success) {
            window.showToast(data.message || 'Informe eliminado permanentemente.', 'success');

            const row = document.getElementById(`row-informe-${targetId}`);
            if (row) {
                row.style.transition = 'all 0.25s cubic-bezier(0.16, 1, 0.3, 1)';
                row.style.opacity = '0';
                row.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    row.remove();
                    applyFilters();
                }, 250);
            } else {
                applyFilters();
            }
        } else {
            window.showToast(data.message || 'Error al eliminar el informe.', 'error');
        }
    } catch (err) {
        console.error('Error al eliminar informe:', err);
        if (btn) {
            btn.disabled = false;
            btn.textContent = 'CONFIRMAR';
        }
        closeDeleteModal();
        window.showToast('Error de conexión con el servidor.', 'error');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('searchInput')?.addEventListener('input', applyFilters);
    document.getElementById('filterCarrera')?.addEventListener('change', applyFilters);
    document.getElementById('filterCurso')?.addEventListener('change', applyFilters);
    document.getElementById('filterPeriodo')?.addEventListener('change', applyFilters);
    document.getElementById('filterAnio')?.addEventListener('change', applyFilters);
    document.getElementById('filterMes')?.addEventListener('change', applyFilters);
});
