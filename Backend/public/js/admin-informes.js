/**
 * FARUSAC - SISTEMA DE INFORMES: ADMIN INFORMES (admin-informes.js)
 */

let currentTab = 'docentes';

function switchTab(tab) {
    currentTab = tab;
    const tabBtnDoc = document.getElementById('tabBtnDocentes');
    const tabBtnCoord = document.getElementById('tabBtnCoordinacion');
    const secDoc = document.getElementById('sectionDocentes');
    const secCoord = document.getElementById('sectionCoordinacion');
    const filterCarrera = document.getElementById('filterCarrera');

    if (tab === 'docentes') {
        tabBtnDoc.classList.add('active');
        tabBtnCoord.classList.remove('active');
        secDoc.style.display = '';
        secCoord.style.display = 'none';
        filterCarrera.disabled = false;
        filterCarrera.style.opacity = '1';
    } else {
        tabBtnCoord.classList.add('active');
        tabBtnDoc.classList.remove('active');
        secCoord.style.display = '';
        secDoc.style.display = 'none';
        filterCarrera.disabled = true;
        filterCarrera.style.opacity = '0.5';
    }

    applyFilters();
}

function applyFilters() {
    const searchVal = document.getElementById('searchInput').value.trim().toLowerCase();
    const carreraVal = document.getElementById('filterCarrera').value.trim().toLowerCase();
    const areaVal = document.getElementById('filterArea').value.trim().toLowerCase();
    const periodoVal = document.getElementById('filterPeriodo').value.trim().toLowerCase();
    const anioVal = document.getElementById('filterAnio').value.trim();
    const mesVal = document.getElementById('filterMes').value.trim().toLowerCase();
    const hasFilters = Boolean(searchVal || carreraVal || areaVal || periodoVal || anioVal || mesVal);

    if (currentTab === 'docentes') {
        const rows = document.querySelectorAll('#tbodyDocentes .informe-docente-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const rNombre = row.getAttribute('data-nombre') || '';
            const rCorreo = row.getAttribute('data-correo') || '';
            const rCurso = row.getAttribute('data-curso') || '';
            const rCodigo = row.getAttribute('data-codigo') || '';
            const rCarrera = row.getAttribute('data-carrera') || '';
            const rArea = row.getAttribute('data-area') || '';
            const rPeriodo = row.getAttribute('data-periodo') || '';
            const rAnio = row.getAttribute('data-anio') || '';
            const rMes = row.getAttribute('data-mes') || '';

            const matchesSearch = !searchVal ||
                rNombre.includes(searchVal) ||
                rCorreo.includes(searchVal) ||
                rCurso.includes(searchVal) ||
                rCodigo.includes(searchVal) ||
                rArea.includes(searchVal);

            const matchesCarrera = !carreraVal || rCarrera.includes(carreraVal);
            const matchesArea = !areaVal || rArea.includes(areaVal);
            const matchesPeriodo = !periodoVal || rPeriodo.includes(periodoVal);
            const matchesAnio = !anioVal || rAnio === anioVal;
            const matchesMes = !mesVal || rMes.includes(mesVal);

            if (matchesSearch && matchesCarrera && matchesArea && matchesPeriodo && matchesAnio && matchesMes) {
                row.style.display = '';
                visibleCount++;
                const numCell = row.querySelector('.seq-num');
                if (numCell) {
                    const baseSeq = row.getAttribute('data-seq');
                    numCell.textContent = (hasFilters || !baseSeq) ? `${visibleCount}.` : `${baseSeq}.`;
                }
            } else {
                row.style.display = 'none';
            }
        });

        const noResults = document.getElementById('noResultsDocentesRow');
        const emptyRow = document.getElementById('emptyDocentesRow');

        if (rows.length === 0) {
            if (emptyRow) emptyRow.style.display = '';
            if (noResults) noResults.style.display = 'none';
        } else {
            if (emptyRow) emptyRow.style.display = 'none';
            if (noResults) noResults.style.display = (visibleCount === 0) ? '' : 'none';
        }

        if (hasFilters) {
            document.getElementById('resultsCounterText').textContent = `Filtrados: ${visibleCount} de ${rows.length} informes en esta página`;
        } else {
            document.getElementById('resultsCounterText').textContent = `Mostrando ${rows.length} informes en esta página`;
        }

    } else {
        const rows = document.querySelectorAll('#tbodyCoordinacion .informe-coordinacion-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const rNombre = row.getAttribute('data-nombre') || '';
            const rCorreo = row.getAttribute('data-correo') || '';
            const rArea = row.getAttribute('data-area') || '';
            const rPeriodo = row.getAttribute('data-periodo') || '';
            const rAnio = row.getAttribute('data-anio') || '';
            const rMes = row.getAttribute('data-mes') || '';

            const matchesSearch = !searchVal ||
                rNombre.includes(searchVal) ||
                rCorreo.includes(searchVal) ||
                rArea.includes(searchVal);

            const matchesArea = !areaVal || rArea.includes(areaVal);
            const matchesPeriodo = !periodoVal || rPeriodo.includes(periodoVal);
            const matchesAnio = !anioVal || rAnio === anioVal;
            const matchesMes = !mesVal || rMes.includes(mesVal);

            if (matchesSearch && matchesArea && matchesPeriodo && matchesAnio && matchesMes) {
                row.style.display = '';
                visibleCount++;
                const numCell = row.querySelector('.seq-num');
                if (numCell) {
                    const baseSeq = row.getAttribute('data-seq');
                    numCell.textContent = (hasFilters || !baseSeq) ? `${visibleCount}.` : `${baseSeq}.`;
                }
            } else {
                row.style.display = 'none';
            }
        });

        const noResults = document.getElementById('noResultsCoordinacionRow');
        const emptyRow = document.getElementById('emptyCoordinacionRow');

        if (rows.length === 0) {
            if (emptyRow) emptyRow.style.display = '';
            if (noResults) noResults.style.display = 'none';
        } else {
            if (emptyRow) emptyRow.style.display = 'none';
            if (noResults) noResults.style.display = (visibleCount === 0) ? '' : 'none';
        }

        if (hasFilters) {
            document.getElementById('resultsCounterText').textContent = `Filtrados: ${visibleCount} de ${rows.length} informes en esta página`;
        } else {
            document.getElementById('resultsCounterText').textContent = `Mostrando ${rows.length} informes en esta página`;
        }
    }

    updateSelectionState();
}

function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('filterCarrera').value = '';
    document.getElementById('filterArea').value = '';
    document.getElementById('filterPeriodo').value = '';
    document.getElementById('filterAnio').value = '';
    document.getElementById('filterMes').value = '';
    applyFilters();
}

function toggleSelectAll(type) {
    const masterChk = (type === 'docente')
        ? document.getElementById('chkSelectAllDocentes')
        : document.getElementById('chkSelectAllCoord');

    const isChecked = masterChk.checked;
    const chkClass = (type === 'docente') ? '.chk-docente' : '.chk-coord';
    const rows = document.querySelectorAll((type === 'docente') ? '#tbodyDocentes .informe-docente-row' : '#tbodyCoordinacion .informe-coordinacion-row');

    rows.forEach(row => {
        if (row.style.display !== 'none') {
            const chk = row.querySelector(chkClass);
            if (chk) chk.checked = isChecked;
        }
    });

    updateSelectionState();
}

function updateSelectionState() {
    const selectedDocentes = Array.from(document.querySelectorAll('.chk-docente:checked')).map(c => c.value);
    const selectedCoord = Array.from(document.querySelectorAll('.chk-coord:checked')).map(c => c.value);
    const totalSelected = selectedDocentes.length + selectedCoord.length;

    const toolbar = document.getElementById('selectionToolbar');
    const countText = document.getElementById('selectedCountText');

    if (totalSelected > 0) {
        toolbar.classList.add('active');
        countText.textContent = `${totalSelected} informe(s) seleccionado(s)`;
    } else {
        toolbar.classList.remove('active');
    }
}

function clearSelection() {
    document.querySelectorAll('.custom-checkbox').forEach(chk => chk.checked = false);
    updateSelectionState();
}

function downloadSelectedZip() {
    const selectedDocentes = Array.from(document.querySelectorAll('.chk-docente:checked')).map(c => c.value);
    const selectedCoord = Array.from(document.querySelectorAll('.chk-coord:checked')).map(c => c.value);

    if (selectedDocentes.length === 0 && selectedCoord.length === 0) return;

    if (window.showToast) window.showToast('Generando archivo ZIP de informes seleccionados...', 'info');

    const form = document.getElementById('formSeleccionadosPost');
    form.action = window.route ? window.route("/admin/informes/descargar-seleccionados-zip") : "/admin/informes/descargar-seleccionados-zip";
    form.target = "_self";
    document.getElementById('postDocenteIds').value = selectedDocentes.join(',');
    document.getElementById('postCoordIds').value = selectedCoord.join(',');
    form.submit();
}

function viewSelectedPdf() {
    const selectedDocentes = Array.from(document.querySelectorAll('.chk-docente:checked')).map(c => c.value);
    const selectedCoord = Array.from(document.querySelectorAll('.chk-coord:checked')).map(c => c.value);

    if (selectedDocentes.length === 0 && selectedCoord.length === 0) return;

    const form = document.getElementById('formSeleccionadosPost');
    form.action = window.route ? window.route("/admin/informes/consolidado-seleccionados-pdf") : "/admin/informes/consolidado-seleccionados-pdf";
    form.target = "_blank";
    document.getElementById('postDocenteIds').value = selectedDocentes.join(',');
    document.getElementById('postCoordIds').value = selectedCoord.join(',');
    form.submit();
}

function openBulkDownloadModal() {
    const filterMes = document.getElementById('filterMes');
    const filterAnio = document.getElementById('filterAnio');
    const filterCarrera = document.getElementById('filterCarrera');

    const bulkMes = document.getElementById('bulkMes');
    const bulkAnio = document.getElementById('bulkAnio');
    const bulkCarrera = document.getElementById('bulkCarrera');

    if (bulkMes) bulkMes.value = (filterMes && filterMes.value) ? filterMes.value : '';
    if (bulkAnio) bulkAnio.value = (filterAnio && filterAnio.value) ? filterAnio.value : '';
    if (bulkCarrera) bulkCarrera.value = (filterCarrera && filterCarrera.value) ? filterCarrera.value : '';

    updateBulkPreviewCount();
    document.getElementById('bulkDownloadModal').classList.add('active');
}

function updateBulkPreviewCount() {
    // Función de control para los eventos onchange del modal de descarga
}

function closeBulkDownloadModal() {
    document.getElementById('bulkDownloadModal').classList.remove('active');
}

function getBulkParams() {
    const mes = document.getElementById('bulkMes').value;
    const anio = document.getElementById('bulkAnio').value;
    const tipo = document.getElementById('bulkTipo').value;
    const carrera = document.getElementById('bulkCarrera').value;

    const params = new URLSearchParams();
    if (mes) params.append('mes', mes);
    if (anio) params.append('anio', anio);
    if (tipo) params.append('tipo', tipo);
    if (carrera) params.append('carrera', carrera);

    return params.toString();
}

function triggerBulkZip(e) {
    e.preventDefault();
    closeBulkDownloadModal();
    const params = getBulkParams();

    if (window.showToast) window.showToast('Generando archivo ZIP de informes...', 'info');
    const baseUrl = window.route ? window.route('/admin/informes/descargar-zip') : '/admin/informes/descargar-zip';
    window.location.href = `${baseUrl}?${params}`;
}

function triggerBulkPdf(e) {
    e.preventDefault();
    const params = getBulkParams();
    const baseUrl = window.route ? window.route('/admin/informes/consolidado-pdf') : '/admin/informes/consolidado-pdf';
    window.open(`${baseUrl}?${params}`, '_blank');
}

function triggerBulkCsv(e) {
    e.preventDefault();
    const params = getBulkParams();
    const baseUrl = window.route ? window.route('/admin/informes/descargar-csv') : '/admin/informes/descargar-csv';
    window.location.href = `${baseUrl}?${params}`;
    if (window.showToast) window.showToast('Descargando planilla de control CSV...', 'info');
}

let deleteType = null;
let deleteTargetId = null;

function openDeleteModal(type, id, label) {
    deleteType = type;
    deleteTargetId = id;
    const msg = document.getElementById('deleteModalMessage');
    msg.textContent = `Esta acción no se puede deshacer. Se eliminará permanentemente el informe de ${type === 'docente' ? 'docente' : 'coordinación'}: "${label}".`;
    document.getElementById('deleteConfirmModal').classList.add('active');
}

function closeDeleteModal() {
    deleteType = null;
    deleteTargetId = null;
    document.getElementById('deleteConfirmModal').classList.remove('active');
}

async function executeDelete() {
    if (!deleteType || !deleteTargetId) return;

    const btn = document.getElementById('btnExecuteDelete');
    btn.disabled = true;
    btn.textContent = 'ELIMINANDO...';

    const rawPath = (deleteType === 'docente')
        ? `/admin/informes/docente/${deleteTargetId}`
        : `/admin/informes/coordinacion/${deleteTargetId}`;
    const url = window.route ? window.route(rawPath) : rawPath;

    try {
        const response = await fetch(url, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.getCsrfToken(),
                'Accept': 'application/json'
            }
        });

        const data = await response.json();
        btn.disabled = false;
        btn.textContent = 'CONFIRMAR ELIMINACIÓN';
        closeDeleteModal();

        if (response.ok && data.success) {
            if (window.showToast) window.showToast(data.message || 'Informe eliminado exitosamente.', 'success');

            const rowId = (deleteType === 'docente') ? `row-docente-${deleteTargetId}` : `row-coordinacion-${deleteTargetId}`;
            const row = document.getElementById(rowId);
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
            if (window.showToast) window.showToast(data.message || 'Error al eliminar el informe.', 'error');
        }
    } catch (err) {
        console.error(err);
        btn.disabled = false;
        btn.textContent = 'CONFIRMAR ELIMINACIÓN';
        closeDeleteModal();
        if (window.showToast) window.showToast('Error de conexión con el servidor.', 'error');
    }
}

const adminDocenteVerBaseUrl = window.route ? window.route("/admin/informes/docente") : "/admin/informes/docente";
const adminCoordVerBaseUrl = window.route ? window.route("/admin/informes/coordinacion") : "/admin/informes/coordinacion";

function openReportPreviewModal(type, id, title, subtitle) {
    const modal = document.getElementById('reportPreviewModal');
    const iframe = document.getElementById('reportPreviewIframe');
    const loader = document.getElementById('previewIframeLoader');
    const titleEl = document.getElementById('previewModalTitle');
    const subtitleEl = document.getElementById('previewModalSubtitle');
    const badgeEl = document.getElementById('previewModalBadge');
    const fullTabBtn = document.getElementById('previewModalFullTabBtn');

    titleEl.textContent = title || 'Informe Oficial';
    subtitleEl.textContent = subtitle || 'FARUSAC • Control Académico';

    if (type === 'docente') {
        badgeEl.textContent = 'Docente';
        badgeEl.className = 'report-badge-type report-badge-docente';
        const verUrl = `${adminDocenteVerBaseUrl}/${id}/ver`;
        iframe.src = `${verUrl}?embed=1`;
        fullTabBtn.href = verUrl;
    } else {
        badgeEl.textContent = 'Coordinación';
        badgeEl.className = 'report-badge-type report-badge-coord';
        const verUrl = `${adminCoordVerBaseUrl}/${id}/ver`;
        iframe.src = `${verUrl}?embed=1`;
        fullTabBtn.href = verUrl;
    }

    if (loader) loader.style.display = 'flex';
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeReportPreviewModal() {
    const modal = document.getElementById('reportPreviewModal');
    modal.classList.remove('active');
    document.body.style.overflow = '';

    setTimeout(() => {
        const iframe = document.getElementById('reportPreviewIframe');
        if (iframe && !modal.classList.contains('active')) {
            iframe.src = 'about:blank';
        }
    }, 300);
}

function hidePreviewLoader() {
    const loader = document.getElementById('previewIframeLoader');
    const iframe = document.getElementById('reportPreviewIframe');
    if (iframe && (iframe.src.includes('about:blank') || !iframe.src)) return;
    if (loader) {
        loader.style.display = 'none';
    }
}

function printReportIframe() {
    const iframe = document.getElementById('reportPreviewIframe');
    if (iframe && iframe.contentWindow) {
        try {
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
        } catch (e) {
            console.error('Error al invocar impresión de iframe:', e);
            window.open(iframe.src.replace('?embed=1', ''), '_blank');
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeReportPreviewModal();
            closeDeleteModal();
            closeBulkDownloadModal();
        }
    });

    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('page_coordinacion') && !urlParams.has('page_docentes')) {
        switchTab('coordinacion');
    } else {
        applyFilters();
    }
});