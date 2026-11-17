/**
 * FARUSAC - SISTEMA DE INFORMES: GESTIÓN DE CURSOS (admin-cursos.js)
 */

let dbCursos = [];
let dbPageOffset = 0;
let deleteTargetCourseId = null;

function initAdminCursos(cursosData, pageOffset = 0) {
    dbPageOffset = Number(pageOffset) || 0;
    const rawList = Array.isArray(cursosData) ? cursosData : (cursosData?.data || []);
    dbCursos = rawList.map(c => ({
        id: c.id,
        carrera: c.carrera,
        area: c.area,
        curso: c.nombre_curso,
        codigo: String(c.codigo_curso),
        seccion: c.seccion,
        jornada: (function(sec) {
            const s = String(sec || '').toUpperCase();
            return (s.startsWith('V') || s.startsWith('F') || s.startsWith('G') || s.startsWith('PG') || s === 'B') ? 'Vespertina' : 'Matutina';
        })(c.seccion),
        semestre: c.semestre,
        anio: String(c.anio)
    }));

    populateYearFilter();
    renderTable();

    const newCursoInput = document.getElementById('newCurso');
    if (newCursoInput) {
        newCursoInput.addEventListener('input', handleCursoInput);
        newCursoInput.addEventListener('focus', handleCursoInput);
    }

    const newAreaInput = document.getElementById('newArea');
    const areaSuggestions = document.getElementById('areaSuggestions');
    if (newAreaInput && areaSuggestions) {
        newAreaInput.addEventListener('input', () => handleGenericSuggestions(newAreaInput, areaSuggestions, 'area'));
        newAreaInput.addEventListener('focus', () => handleGenericSuggestions(newAreaInput, areaSuggestions, 'area'));
    }

    const newSeccionInput = document.getElementById('newSeccion');
    const seccionSuggestions = document.getElementById('seccionSuggestions');
    if (newSeccionInput && seccionSuggestions) {
        newSeccionInput.addEventListener('input', () => handleGenericSuggestions(newSeccionInput, seccionSuggestions, 'seccion'));
        newSeccionInput.addEventListener('focus', () => handleGenericSuggestions(newSeccionInput, seccionSuggestions, 'seccion'));
    }

    const newAnioInput = document.getElementById('newAnio');
    const anioSuggestions = document.getElementById('anioSuggestions');
    if (newAnioInput && anioSuggestions) {
        newAnioInput.addEventListener('input', () => handleGenericSuggestions(newAnioInput, anioSuggestions, 'anio'));
        newAnioInput.addEventListener('focus', () => handleGenericSuggestions(newAnioInput, anioSuggestions, 'anio'));
    }

    document.getElementById('searchInput')?.addEventListener('input', renderTable);
    document.getElementById('carreraFilter')?.addEventListener('change', renderTable);
    document.getElementById('semestreFilter')?.addEventListener('change', renderTable);
    document.getElementById('anioFilter')?.addEventListener('change', renderTable);
    document.getElementById('addCourseBtn')?.addEventListener('click', openAddCourseModal);
}

function populateYearFilter() {
    const anioFilter = document.getElementById('anioFilter');
    if (!anioFilter) return;
    const currentSelected = anioFilter.value;
    const years = [...new Set(dbCursos.map(c => c.anio))];
    years.sort((a, b) => b - a);

    anioFilter.innerHTML = '<option value="todos">Todos</option>';
    years.forEach(year => {
        const opt = document.createElement('option');
        opt.value = year;
        opt.textContent = year;
        anioFilter.appendChild(opt);
    });

    if (years.includes(currentSelected)) {
        anioFilter.value = currentSelected;
    } else {
        anioFilter.value = 'todos';
    }
}

function handleCursoInput(e) {
    const input = e.target;
    const container = document.getElementById('cursosSuggestions');
    if (!container) return;

    const val = input.value.trim().toLowerCase();
    if (val === '') {
        container.innerHTML = '';
        container.style.display = 'none';
        return;
    }

    const matches = [];
    const seen = new Set();
    dbCursos.forEach(c => {
        if (c.curso && c.curso.toLowerCase().includes(val) && !seen.has(c.curso.toLowerCase())) {
            seen.add(c.curso.toLowerCase());
            matches.push({ nombre: c.curso, codigo: c.codigo, area: c.area });
        }
    });

    container.innerHTML = '';
    if (matches.length === 0) {
        container.style.display = 'none';
        return;
    }

    matches.forEach(m => {
        const div = document.createElement('div');
        div.className = 'suggestion-item';
        div.textContent = m.nombre;
        
        div.addEventListener('click', () => {
            input.value = m.nombre;
            document.getElementById('newCodigo').value = m.codigo;
            document.getElementById('newArea').value = m.area;
            container.innerHTML = '';
            container.style.display = 'none';
        });

        container.appendChild(div);
    });

    container.style.display = 'block';
}

function handleGenericSuggestions(inputEl, containerEl, dataField) {
    const val = inputEl.value.trim().toLowerCase();
    if (val === '') {
        containerEl.innerHTML = '';
        containerEl.style.display = 'none';
        return;
    }

    const matches = [];
    const seen = new Set();
    dbCursos.forEach(c => {
        const rawValue = c[dataField];
        if (rawValue) {
            const strValue = String(rawValue).trim();
            if (strValue.toLowerCase().includes(val) && !seen.has(strValue.toLowerCase())) {
                seen.add(strValue.toLowerCase());
                matches.push(strValue);
            }
        }
    });

    containerEl.innerHTML = '';
    if (matches.length === 0) {
        containerEl.style.display = 'none';
        return;
    }

    matches.sort().forEach(match => {
        const div = document.createElement('div');
        div.className = 'suggestion-item';
        div.textContent = match;
        
        div.addEventListener('click', () => {
            inputEl.value = match;
            containerEl.innerHTML = '';
            containerEl.style.display = 'none';
        });

        containerEl.appendChild(div);
    });

    containerEl.style.display = 'block';
}

function renderTable() {
    const tableBody = document.getElementById('coursesTableBody');
    if (!tableBody) return;

    const searchVal = document.getElementById('searchInput')?.value.toLowerCase().trim() || '';
    const carreraVal = document.getElementById('carreraFilter')?.value || 'todos';
    const semestreVal = document.getElementById('semestreFilter')?.value || 'todos';
    const anioVal = document.getElementById('anioFilter')?.value || 'todos';

    const filtered = dbCursos.filter(c => {
        const matchesSearch = (c.curso && c.curso.toLowerCase().includes(searchVal)) || (c.codigo && c.codigo.includes(searchVal));
        const matchesCarrera = (carreraVal === 'todos') || (c.carrera === carreraVal);
        const matchesSemestre = (semestreVal === 'todos') || (c.semestre === semestreVal);
        const matchesAnio = (anioVal === 'todos') || (c.anio === anioVal);

        return matchesSearch && matchesCarrera && matchesSemestre && matchesAnio;
    });

    tableBody.innerHTML = '';

    if (filtered.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="8" class="no-records-row">No se encontraron cursos con los filtros aplicados.</td>
            </tr>
        `;
        return;
    }

    filtered.forEach((c, idx) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="td-id">${dbPageOffset + idx + 1}.</td>
            <td class="td-carrera">${c.carrera}</td>
            <td>${c.area}</td>
            <td class="td-curso">${c.curso}</td>
            <td class="td-codigo">${c.codigo}</td>
            <td>${c.seccion}</td>
            <td>${c.jornada}</td>
            <td>
                <button type="button" class="action-btn" onclick="confirmDeleteCourse(${c.id})" title="Eliminar Curso">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </td>
        `;
        tableBody.appendChild(tr);
    });
}

function openAddCourseModal() {
    window.openModal('addCourseModal');
}

function closeAddCourseModal() {
    window.closeModal('addCourseModal');
    document.getElementById('addCourseForm')?.reset();
}

function handleCreateCourse(e) {
    e.preventDefault();

    const carrera = document.getElementById('newCarrera')?.value;
    const area = document.getElementById('newArea')?.value.trim();
    const nombre_curso = document.getElementById('newCurso')?.value.trim();
    const codigo_curso = document.getElementById('newCodigo')?.value.trim();
    const seccion = document.getElementById('newSeccion')?.value.trim();
    const jornada = document.getElementById('newJornada')?.value;
    const semestre = document.getElementById('newSemestre')?.value;
    const anioRaw = document.getElementById('newAnio')?.value.trim();

    const anioReg = /^[0-9]{4}$/;
    if (!anioReg.test(anioRaw)) {
        window.showToast("Por favor, ingrese un año válido de 4 dígitos (Ej. 2026).", "warning");
        return;
    }
    const anio = parseInt(anioRaw, 10);

    if (!carrera || !area || !nombre_curso || !codigo_curso || !seccion || !jornada || !semestre || isNaN(anio)) return;

    fetch('/admin/cursos', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.getCsrfToken()
        },
        body: JSON.stringify({ carrera, area, nombre_curso, codigo_curso, seccion, semestre, anio })
    })
    .then(res => {
        if (!res.ok) {
            return res.json().then(err => { throw err; });
        }
        return res.json();
    })
    .then(data => {
        if (data.success) {
            const c = data.curso;
            dbCursos.push({
                id: c.id,
                carrera: c.carrera,
                area: c.area,
                curso: c.nombre_curso,
                codigo: String(c.codigo_curso),
                seccion: c.seccion,
                jornada: jornada,
                semestre: c.semestre,
                anio: String(c.anio)
            });
            closeAddCourseModal();
            populateYearFilter();
            renderTable();
            window.showToast('Curso agregado correctamente', 'success');
        } else {
            window.showToast(data.message || 'Error al agregar el curso', 'error');
        }
    })
    .catch(err => {
        console.error(err);
        window.showToast(err.message || 'Ocurrió un error al intentar agregar el curso.', 'error');
    });
}

function confirmDeleteCourse(id) {
    deleteTargetCourseId = id;
    window.openModal('confirmDeleteModal');
}

function closeConfirmDeleteModal() {
    window.closeModal('confirmDeleteModal');
    deleteTargetCourseId = null;
}

function executeDeleteCourse() {
    if (deleteTargetCourseId === null) return;

    fetch(`/admin/cursos/${deleteTargetCourseId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.getCsrfToken()
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            dbCursos = dbCursos.filter(c => c.id !== deleteTargetCourseId);
            closeConfirmDeleteModal();
            populateYearFilter();
            renderTable();
            window.showToast('Curso eliminado correctamente', 'info');
        } else {
            window.showToast(data.message || 'Error al eliminar el curso', 'error');
        }
    })
    .catch(err => {
        console.error(err);
        window.showToast('Ocurrió un error al intentar eliminar el curso.', 'error');
    });
}
