/**
 * FARUSAC - SISTEMA DE INFORMES: LÓGICA DASHBOARD DOCENTE & COORDINADOR (dashboard.js)
 */

let pendingUnassignCursoId = null;
let pendingDeleteCoordReportId = null;
let currentConfirmAction = null;

function getDashboardCfg() {
    return window.dashboardConfig || {};
}

function getCatalogoCursos() {
    return getDashboardCfg().catalogoCursos || [];
}

function getRolePrefix() {
    return getDashboardCfg().rolePrefix || 'docente';
}

function switchView(viewName) {
    const btnDocente = document.getElementById('btnSwitchDocente');
    const btnCoord = document.getElementById('btnSwitchCoordinador');
    const sectionDocente = document.getElementById('docenteViewSection');
    const sectionCoord = document.getElementById('coordinadorViewSection');

    if (!btnDocente || !btnCoord) return;

    if (viewName === 'docente') {
        btnDocente.classList.add('active');
        btnCoord.classList.remove('active');
        if (sectionDocente) sectionDocente.style.display = 'flex';
        if (sectionCoord) sectionCoord.style.display = 'none';
    } else {
        btnCoord.classList.add('active');
        btnDocente.classList.remove('active');
        if (sectionDocente) sectionDocente.style.display = 'none';
        if (sectionCoord) sectionCoord.style.display = 'block';
    }
}

function onCarreraModalChange() {
    const cfg = getDashboardCfg();
    const carreraSelect = document.getElementById('selectCarreraModal');
    const areaSelect = document.getElementById('selectAreaModal');
    const areaError = document.getElementById('areaModalError');
    if (areaError) areaError.style.display = 'none';

    const selectedCarrera = carreraSelect ? carreraSelect.value : '';
    if (!areaSelect) return;
    areaSelect.innerHTML = '<option value="">Selecciona un área...</option>';

    const todasLasAreasList = cfg.todasLasAreasList || [];
    const areasPorCarreraMap = cfg.areasPorCarreraMap || {};
    const currentSelectedArea = cfg.currentSelectedArea || '';

    if (!selectedCarrera) {
        todasLasAreasList.forEach(area => {
            const opt = document.createElement('option');
            opt.value = area;
            opt.textContent = area;
            areaSelect.appendChild(opt);
        });
        return;
    }

    let areas = areasPorCarreraMap[selectedCarrera] || [];
    if (areas.length === 0) {
        areas = todasLasAreasList;
    }

    areas.forEach(area => {
        const opt = document.createElement('option');
        opt.value = area;
        opt.textContent = area;
        if (currentSelectedArea && currentSelectedArea === area) {
            opt.selected = true;
        }
        areaSelect.appendChild(opt);
    });
}

function onAreaModalChange() {
    const areaError = document.getElementById('areaModalError');
    if (areaError) areaError.style.display = 'none';
}

async function confirmarSeleccionArea() {
    const selectCarreraModal = document.getElementById('selectCarreraModal');
    const selectAreaModal = document.getElementById('selectAreaModal');
    const areaError = document.getElementById('areaModalError');
    const btn = document.getElementById('btnConfirmArea');

    const carreraVal = selectCarreraModal ? selectCarreraModal.value : '';
    const areaVal = selectAreaModal ? selectAreaModal.value : '';

    if (!carreraVal) {
        if (areaError) {
            areaError.textContent = 'Por favor, seleccione la carrera a la que pertenece.';
            areaError.style.display = 'block';
        }
        return;
    }

    if (!areaVal) {
        if (areaError) {
            areaError.textContent = 'Por favor, seleccione un área válida.';
            areaError.style.display = 'block';
        }
        return;
    }

    if (areaError) areaError.style.display = 'none';
    if (btn) {
        btn.disabled = true;
        btn.textContent = 'Guardando...';
    }

    try {
        const url = window.route ? window.route('/coordinador/area/seleccionar') : '/coordinador/area/seleccionar';
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.getCsrfToken(),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ carrera: carreraVal, area: areaVal })
        });

        const data = await response.json();
        if (data.success) {
            window.showToast('Área guardada correctamente', 'success');
            setTimeout(() => window.location.reload(), 500);
        } else {
            if (btn) {
                btn.disabled = false;
                btn.textContent = 'Confirmar Área';
            }
            if (areaError) {
                areaError.textContent = data.message || 'No se pudo guardar el área seleccionada.';
                areaError.style.display = 'block';
            }
        }
    } catch (err) {
        console.error(err);
        if (btn) {
            btn.disabled = false;
            btn.textContent = 'Confirmar Área';
        }
        if (areaError) {
            areaError.textContent = 'Error de conexión con el servidor.';
            areaError.style.display = 'block';
        }
    }
}

function openAddCourseModal() {
    resetModalForm();
    const modalOverlay = document.getElementById('addCourseModalOverlay');
    if (modalOverlay) modalOverlay.classList.add('active');
}

function closeAddCourseModal() {
    const modalOverlay = document.getElementById('addCourseModalOverlay');
    if (modalOverlay) modalOverlay.classList.remove('active');
}

function handleBackdropClick(event) {
    const modalOverlay = document.getElementById('addCourseModalOverlay');
    if (event.target === modalOverlay) {
        closeAddCourseModal();
    }
}

function resetModalForm() {
    const selectCarrera = document.getElementById('selectCarrera');
    const selectArea = document.getElementById('selectArea');
    const selectCurso = document.getElementById('selectCurso');
    const selectSeccion = document.getElementById('selectSeccion');
    const btnSubmitCourse = document.getElementById('btnSubmitCourse');
    const modalErrorMessage = document.getElementById('modalErrorMessage');

    if (selectCarrera) selectCarrera.value = '';
    if (selectArea) {
        selectArea.innerHTML = '<option value="">Selecciona primero una carrera...</option>';
        selectArea.disabled = true;
    }
    if (selectCurso) {
        selectCurso.innerHTML = '<option value="">Selecciona primero un área...</option>';
        selectCurso.disabled = true;
    }
    if (selectSeccion) {
        selectSeccion.innerHTML = '<option value="">Selecciona primero un curso...</option>';
        selectSeccion.disabled = true;
    }
    if (btnSubmitCourse) btnSubmitCourse.disabled = true;
    if (modalErrorMessage) {
        modalErrorMessage.style.display = 'none';
        modalErrorMessage.textContent = '';
    }
}

function onCarreraChange() {
    const selectCarrera = document.getElementById('selectCarrera');
    const selectArea = document.getElementById('selectArea');
    const selectCurso = document.getElementById('selectCurso');
    const selectSeccion = document.getElementById('selectSeccion');
    const btnSubmitCourse = document.getElementById('btnSubmitCourse');

    const carrera = selectCarrera ? selectCarrera.value : '';

    if (selectArea) {
        selectArea.innerHTML = '<option value="">Selecciona un área...</option>';
        selectArea.disabled = true;
    }
    if (selectCurso) {
        selectCurso.innerHTML = '<option value="">Selecciona primero un área...</option>';
        selectCurso.disabled = true;
    }
    if (selectSeccion) {
        selectSeccion.innerHTML = '<option value="">Selecciona primero un curso...</option>';
        selectSeccion.disabled = true;
    }
    if (btnSubmitCourse) btnSubmitCourse.disabled = true;

    if (!carrera) return;

    const catalogo = getCatalogoCursos();
    const areasUnicas = [...new Set(
        catalogo.filter(c => c.carrera === carrera && c.area).map(c => c.area)
    )].sort();

    if (selectArea) {
        if (areasUnicas.length === 0) {
            selectArea.innerHTML = '<option value="">No hay cursos registrados para esta carrera...</option>';
            selectArea.disabled = true;
        } else {
            areasUnicas.forEach(area => {
                const opt = document.createElement('option');
                opt.value = area;
                opt.textContent = area;
                selectArea.appendChild(opt);
            });
            selectArea.disabled = false;
        }
    }
}

function onAreaChange() {
    const selectCarrera = document.getElementById('selectCarrera');
    const selectArea = document.getElementById('selectArea');
    const selectCurso = document.getElementById('selectCurso');
    const selectSeccion = document.getElementById('selectSeccion');
    const btnSubmitCourse = document.getElementById('btnSubmitCourse');

    const carrera = selectCarrera ? selectCarrera.value : '';
    const area = selectArea ? selectArea.value : '';

    if (selectCurso) {
        selectCurso.innerHTML = '<option value="">Selecciona un curso...</option>';
        selectCurso.disabled = true;
    }
    if (selectSeccion) {
        selectSeccion.innerHTML = '<option value="">Selecciona primero un curso...</option>';
        selectSeccion.disabled = true;
    }
    if (btnSubmitCourse) btnSubmitCourse.disabled = true;

    if (!carrera || !area || !selectCurso) return;

    const catalogo = getCatalogoCursos();
    const cursosUnicos = [...new Set(
        catalogo.filter(c => c.carrera === carrera && c.area === area).map(c => c.nombre_curso)
    )].sort();

    cursosUnicos.forEach(nombre => {
        const opt = document.createElement('option');
        opt.value = nombre;
        opt.textContent = nombre;
        selectCurso.appendChild(opt);
    });

    selectCurso.disabled = false;
}

function onCursoChange() {
    const selectCarrera = document.getElementById('selectCarrera');
    const selectArea = document.getElementById('selectArea');
    const selectCurso = document.getElementById('selectCurso');
    const selectSeccion = document.getElementById('selectSeccion');
    const btnSubmitCourse = document.getElementById('btnSubmitCourse');

    const carrera = selectCarrera ? selectCarrera.value : '';
    const area = selectArea ? selectArea.value : '';
    const nombreCurso = selectCurso ? selectCurso.value : '';
    if (!selectSeccion) return;
    selectSeccion.innerHTML = '<option value="">Selecciona una sección...</option>';
    if (btnSubmitCourse) btnSubmitCourse.disabled = true;

    if (!nombreCurso) {
        selectSeccion.disabled = true;
        return;
    }

    const catalogo = getCatalogoCursos();
    const secciones = catalogo
        .filter(c => c.carrera === carrera && (!area || c.area === area) && c.nombre_curso === nombreCurso)
        .sort((a, b) => (a.seccion || '').localeCompare(b.seccion || ''));

    secciones.forEach(c => {
        const opt = document.createElement('option');
        opt.value = c.id;
        opt.textContent = `Sección ${c.seccion} (${c.semestre} ${c.anio})`;
        selectSeccion.appendChild(opt);
    });

    selectSeccion.disabled = false;
}

function onSeccionChange() {
    const selectSeccion = document.getElementById('selectSeccion');
    const btnSubmitCourse = document.getElementById('btnSubmitCourse');
    if (btnSubmitCourse && selectSeccion) {
        btnSubmitCourse.disabled = !selectSeccion.value;
    }
}

async function handleCourseSubmit(event) {
    if (event) event.preventDefault();
    const selectSeccion = document.getElementById('selectSeccion');
    const btnSubmitCourse = document.getElementById('btnSubmitCourse');
    const modalErrorMessage = document.getElementById('modalErrorMessage');

    const cursoId = selectSeccion ? selectSeccion.value : null;
    if (!cursoId) return;

    if (btnSubmitCourse) {
        btnSubmitCourse.disabled = true;
        btnSubmitCourse.textContent = 'Asignando...';
    }
    if (modalErrorMessage) modalErrorMessage.style.display = 'none';

    const rolePrefix = getRolePrefix();
    const url = window.route ? window.route(`/${rolePrefix}/cursos/asignar`) : `/${rolePrefix}/cursos/asignar`;

    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.getCsrfToken(),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ curso_id: cursoId })
        });

        const data = await response.json();
        if (btnSubmitCourse) {
            btnSubmitCourse.disabled = false;
            btnSubmitCourse.textContent = 'Agregar Curso';
        }

        if (response.ok && data.success) {
            closeAddCourseModal();
            window.showToast(data.message, 'success');
            setTimeout(() => window.location.reload(), 400);
        } else {
            if (modalErrorMessage) {
                modalErrorMessage.textContent = data.message || 'Error al asignar el curso.';
                modalErrorMessage.style.display = 'block';
            }
        }
    } catch (err) {
        console.error(err);
        if (btnSubmitCourse) {
            btnSubmitCourse.disabled = false;
            btnSubmitCourse.textContent = 'Agregar Curso';
        }
        if (modalErrorMessage) {
            modalErrorMessage.textContent = 'Error de conexión con el servidor.';
            modalErrorMessage.style.display = 'block';
        }
    }
}

function unassignCourse(cursoId, nombreCurso, seccion) {
    pendingUnassignCursoId = cursoId;
    const title = document.getElementById('confirmModalTitle');
    const desc = document.getElementById('confirmModalDesc');
    const overlay = document.getElementById('confirmDeleteModalOverlay');

    if (title) title.textContent = '¿ESTÁ SEGURO QUE DESEA REMOVER EL CURSO?';
    if (desc) desc.textContent = `El curso "${nombreCurso} - Sección ${seccion}" será eliminado de su lista de cursos asignados.`;
    currentConfirmAction = executeUnassignCourse;
    if (overlay) overlay.classList.add('active');
}

function confirmDeleteCoordReport(id, mesAnio) {
    pendingDeleteCoordReportId = id;
    const title = document.getElementById('confirmModalTitle');
    const desc = document.getElementById('confirmModalDesc');
    const overlay = document.getElementById('confirmDeleteModalOverlay');

    if (title) title.textContent = '¿DESEA ELIMINAR EL INFORME DE COORDINACIÓN?';
    if (desc) desc.textContent = `El informe de ${mesAnio} será eliminado permanentemente.`;
    currentConfirmAction = executeDeleteCoordReport;
    if (overlay) overlay.classList.add('active');
}

function closeConfirmDeleteModal() {
    const overlay = document.getElementById('confirmDeleteModalOverlay');
    if (overlay) overlay.classList.remove('active');
    pendingUnassignCursoId = null;
    pendingDeleteCoordReportId = null;
    currentConfirmAction = null;
}

function handleConfirmBackdropClick(event) {
    const overlay = document.getElementById('confirmDeleteModalOverlay');
    if (event.target === overlay) {
        closeConfirmDeleteModal();
    }
}

async function executeUnassignCourse() {
    if (!pendingUnassignCursoId) return;
    const cursoId = pendingUnassignCursoId;
    const btn = document.getElementById('btnConfirmDelete');
    if (btn) {
        btn.disabled = true;
        btn.textContent = 'Removiendo...';
    }

    const rolePrefix = getRolePrefix();
    const url = window.route ? window.route(`/${rolePrefix}/cursos/${cursoId}`) : `/${rolePrefix}/cursos/${cursoId}`;

    try {
        const response = await fetch(url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': window.getCsrfToken(),
                'Accept': 'application/json'
            }
        });

        const data = await response.json();
        if (btn) {
            btn.disabled = false;
            btn.textContent = 'REMOVER';
        }
        closeConfirmDeleteModal();

        if (data.success) {
            window.showToast(data.message, 'success');
            const el = document.getElementById(`curso-item-${cursoId}`);
            if (el) el.remove();
        } else {
            window.showToast(data.message || 'No se pudo remover el curso.', 'error');
        }
    } catch (err) {
        console.error(err);
        if (btn) {
            btn.disabled = false;
            btn.textContent = 'REMOVER';
        }
        closeConfirmDeleteModal();
        window.showToast('Error de conexión al intentar remover el curso.', 'error');
    }
}

async function executeDeleteCoordReport() {
    if (!pendingDeleteCoordReportId) return;
    const id = pendingDeleteCoordReportId;
    const btn = document.getElementById('btnConfirmDelete');
    if (btn) {
        btn.disabled = true;
        btn.textContent = 'Eliminando...';
    }

    try {
        const url = window.route ? window.route(`/coordinador/informes-coordinacion/${id}`) : `/coordinador/informes-coordinacion/${id}`;
        const response = await fetch(url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': window.getCsrfToken(),
                'Accept': 'application/json'
            }
        });

        const data = await response.json();
        if (btn) {
            btn.disabled = false;
            btn.textContent = 'REMOVER';
        }
        closeConfirmDeleteModal();

        if (data.success) {
            window.showToast(data.message, 'success');
            const row = document.getElementById(`coord-report-row-${id}`);
            if (row) row.remove();
        } else {
            window.showToast(data.message || 'No se pudo eliminar el informe.', 'error');
        }
    } catch (err) {
        console.error(err);
        if (btn) {
            btn.disabled = false;
            btn.textContent = 'REMOVER';
        }
        closeConfirmDeleteModal();
        window.showToast('Error de conexión al intentar eliminar el informe.', 'error');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const btnConfirmDelete = document.getElementById('btnConfirmDelete');
    if (btnConfirmDelete) {
        btnConfirmDelete.addEventListener('click', () => {
            if (typeof currentConfirmAction === 'function') {
                currentConfirmAction();
            }
        });
    }
});