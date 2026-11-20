/**
 * FARUSAC - SISTEMA DE INFORMES: CARGA MASIVA DE DATOS (admin-carga.js)
 */

let lastReportData = {
    academica: null,
    usuarios: null,
    cursos: null
};

function showUploadView(type) {
    const sel = document.getElementById('loadSelectionView');
    const acad = document.getElementById('uploadAcademicaView');
    const user = document.getElementById('uploadUsuariosView');
    const curs = document.getElementById('uploadCursosView');

    if (sel) sel.style.display = 'none';
    if (acad) acad.style.display = (type === 'academica') ? 'block' : 'none';
    if (user) user.style.display = (type === 'usuarios') ? 'block' : 'none';
    if (curs) curs.style.display = (type === 'cursos') ? 'block' : 'none';

    document.getElementById('reportAcademica')?.style && (document.getElementById('reportAcademica').style.display = 'none');
    document.getElementById('reportUsuarios')?.style && (document.getElementById('reportUsuarios').style.display = 'none');
    document.getElementById('reportCursos')?.style && (document.getElementById('reportCursos').style.display = 'none');
}

function showSelectionView() {
    const sel = document.getElementById('loadSelectionView');
    const acad = document.getElementById('uploadAcademicaView');
    const user = document.getElementById('uploadUsuariosView');
    const curs = document.getElementById('uploadCursosView');

    if (sel) sel.style.display = 'flex';
    if (acad) acad.style.display = 'none';
    if (user) user.style.display = 'none';
    if (curs) curs.style.display = 'none';
}

function downloadExample(type) {
    let csvContent = "";
    let filename = "";
    if (type === 'academica') {
        csvContent = "UNIVERSIDAD DE SAN CARLOS DE GUATEMALA,,,,,,,,,,,,,,,,\n" +
            "FACULTAD DE ARQUITECTURA - ,,,,,,,,,,,,,,,,\n" +
            "CARGA ACADÉMICA DEL SEGUNDO SEMESTRE 2026,,,,,,,,,,,,,,,,\n" +
            "Reporte generado el 22 de octubre de 2026,,,,,,,,,,,,,,,,\n" +
            "#,Área,Ciclo,Código,Asignatura,Sección,Jornada,Salón,Día,Inicio,Fin,Docente,Carga,Registro Personal,Grado Académico,CUI,Correo\n" +
            "1,Diseño Arquitectónico,1,1.01.1,Fundamentos del Diseño,M1,M,Virtual,MI,11:00,13:00,de la Maza Castellanos Osmin Isidro,22,20141721,Arq.,2337701510101,osmin.maza@farusac.edu.gt";
        filename = "ejemplo_carga_academica_farusac.csv";
    } else if (type === 'usuarios') {
        csvContent = "Nombre,Correo,Numero,Plaza\nJuan Perez,juan.perez@farusac.edu.gt,20141721,Titular\nMaria Gomez,maria.gomez@farusac.edu.gt,19982341,Titular+Ampliacion\nCarlos Lopez,carlos.lopez@farusac.edu.gt,20054321,Interino";
        filename = "ejemplo_usuarios.csv";
    } else if (type === 'cursos') {
        csvContent = "Carrera,Area,Curso,Codigo,Seccion,Anio,Semestre\nArquitectura,Área de Tecnología y Expresión,Fotografía,30313,A,2026,Primer Semestre\nDiseño Gráfico,Área de Comunicación,Diseño Web I,10542,B,2026,Segundo Semestre\nArquitectura,Área de Diseño,Diseño Arquitectónico I,20015,C,2026,Vacaciones Junio";
        filename = "ejemplo_cursos.csv";
    }

    const blob = new Blob([new Uint8Array([0xEF, 0xBB, 0xBF]), csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.setAttribute("download", filename);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function uploadCSVFile(type) {
    let fileInputId = 'archivo_csv_academica';
    let uploadTextId = 'uploadTextAcademica';
    let fileNameId = 'fileNameAcademica';
    let dropAreaId = 'dropAreaAcademica';
    let url = '/admin/carga-datos/academica';

    if (type === 'usuarios') {
        fileInputId = 'archivo_csv_usuarios';
        uploadTextId = 'uploadTextUsuarios';
        fileNameId = 'fileNameUsuarios';
        dropAreaId = 'dropAreaUsuarios';
        url = '/admin/importar-usuarios';
    } else if (type === 'cursos') {
        fileInputId = 'archivo_csv_cursos';
        uploadTextId = 'uploadTextCursos';
        fileNameId = 'fileNameCursos';
        dropAreaId = 'dropAreaCursos';
        url = '/admin/importar-cursos';
    }

    const fileInput = document.getElementById(fileInputId);
    if (!fileInput || fileInput.files.length === 0) return;

    const file = fileInput.files[0];
    const formData = new FormData();
    formData.append('archivo_csv', file);

    if (type === 'academica') {
        const carreraEl = document.getElementById('academica_carrera');
        const semestreEl = document.getElementById('academica_semestre');
        const anioEl = document.getElementById('academica_anio');

        if (carreraEl && carreraEl.value !== 'detectar') formData.append('carrera', carreraEl.value);
        if (semestreEl && semestreEl.value !== 'detectar') formData.append('semestre', semestreEl.value);
        if (anioEl && anioEl.value !== 'detectar') formData.append('anio', anioEl.value);
    }

    const uploadText = document.getElementById(uploadTextId);
    const fileName = document.getElementById(fileNameId);
    const dropArea = document.getElementById(dropAreaId);

    if (uploadText) uploadText.textContent = "Procesando y validando archivo CSV...";
    if (fileName) fileName.textContent = `Analizando: ${file.name}`;
    if (dropArea) dropArea.style.opacity = '0.7';

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': window.getCsrfToken()
        },
        body: formData
    })
    .then(res => {
        if (!res.ok) {
            return res.json().then(errData => {
                throw new Error(errData.message || 'Error de comunicación con el servidor.');
            }).catch(e => {
                throw new Error(e.message || 'Error en la respuesta del servidor.');
            });
        }
        return res.json();
    })
    .then(data => {
        if (uploadText) {
            uploadText.textContent = (type === 'academica')
                ? "Arrastra el archivo CSV o haz click para buscar el archivo"
                : "Arrastra el archivo CSV o hacer click para buscar el archivo";
        }
        if (fileName) fileName.textContent = "";
        if (dropArea) dropArea.style.opacity = '1';
        fileInput.value = "";

        displayReport(type, data);
        window.showToast('Archivo importado con éxito', 'success');
    })
    .catch(err => {
        console.error(err);
        window.showToast(err.message || 'Ocurrió un error al subir e importar el archivo CSV.', 'error');
        if (uploadText) {
            uploadText.textContent = (type === 'academica')
                ? "Arrastra el archivo CSV o haz click para buscar el archivo"
                : "Arrastra el archivo CSV o hacer click para buscar el archivo";
        }
        if (fileName) fileName.textContent = "";
        if (dropArea) dropArea.style.opacity = '1';
        fileInput.value = "";
    });
}

function displayReport(type, data) {
    lastReportData[type] = data;

    let reportSectionId = 'reportAcademica';
    let successCountId = 'successCountAcademica';
    let errorCountId = 'errorCountAcademica';

    if (type === 'usuarios') {
        reportSectionId = 'reportUsuarios';
        successCountId = 'successCountUsuarios';
        errorCountId = 'errorCountUsuarios';
    } else if (type === 'cursos') {
        reportSectionId = 'reportCursos';
        successCountId = 'successCountCursos';
        errorCountId = 'errorCountCursos';
    }

    const reportSection = document.getElementById(reportSectionId);
    if (reportSection) reportSection.style.display = 'block';

    const sEl = document.getElementById(successCountId);
    const eEl = document.getElementById(errorCountId);
    if (sEl) sEl.textContent = data.success_count;
    if (eEl) eEl.textContent = data.error_count;

    if (type === 'academica') {
        const bannerEl = document.getElementById('coexistenciaBannerAcademica');
        const tituloEl = document.getElementById('coexistenciaBannerTitulo');
        const textoEl = document.getElementById('coexistenciaBannerTexto');
        if (bannerEl && data.coexistencia_info) {
            bannerEl.style.display = 'block';
            if (tituloEl) tituloEl.textContent = `Periodo Activo: ${data.periodo || 'Carga Exitosa'}`;
            if (textoEl) textoEl.innerHTML = `<b>Regla de Coexistencia:</b> ${data.coexistencia_info} <br><b>Resumen:</b> ${data.total_docentes || 0} docentes procesados, ${data.total_cursos || 0} cursos registrados y ${data.success_count} asignaciones activadas.`;
        }
    }

    switchReportTab(type, 'success');
}

function switchReportTab(type, tab) {
    const data = lastReportData[type];
    if (!data) return;

    let reportContentId = 'reportContentAcademica';
    let reportSectionId = 'reportAcademica';

    if (type === 'usuarios') {
        reportContentId = 'reportContentUsuarios';
        reportSectionId = 'reportUsuarios';
    } else if (type === 'cursos') {
        reportContentId = 'reportContentCursos';
        reportSectionId = 'reportCursos';
    }

    const reportContent = document.getElementById(reportContentId);
    if (!reportContent) return;

    const tabsContainer = document.getElementById(reportSectionId);
    if (!tabsContainer) return;
    const successBtn = tabsContainer.querySelector('.tab-btn-success');
    const errorBtn = tabsContainer.querySelector('.tab-btn-error');

    if (tab === 'success') {
        if (successBtn) successBtn.classList.add('active');
        if (errorBtn) errorBtn.classList.remove('active');

        if (!data.success_records || data.success_records.length === 0) {
            reportContent.innerHTML = `
                <div class="report-title">Datos Cargados exitosamente</div>
                <p style="font-size: 13px; color: var(--color-texto-claro); text-align: left; padding: 10px 0;">
                    No se cargó ningún registro nuevo en esta sesión.
                </p>`;
            return;
        }

        let html = '<div class="report-title">Datos Cargados exitosamente</div>';
        html += '<div class="report-table-wrapper"><table class="report-table"><thead><tr>';

        if (type === 'academica') {
            html += '<th>#</th><th>Docente</th><th>Correo</th><th>Registro Personal</th><th>Asignatura</th><th>Código</th><th>Sección</th><th>Jornada</th><th>Área</th><th>Periodo</th>';
            html += '</tr></thead><tbody>';
            data.success_records.forEach((r, idx) => {
                html += `<tr>
                    <td>${idx + 1}.</td>
                    <td style="font-weight: 600; color: var(--color-texto-principal);">${r.docente}</td>
                    <td>${r.correo}</td>
                    <td>${r.numero}</td>
                    <td style="font-weight: 600; color: var(--color-texto-principal);">${r.curso}</td>
                    <td style="font-family: monospace; font-weight: 600;">${r.codigo}</td>
                    <td><span style="font-weight: 700; color: var(--color-terracota);">${r.seccion}</span></td>
                    <td>${r.jornada}</td>
                    <td>${r.area}</td>
                    <td><span style="background-color: rgba(0, 45, 114, 0.08); padding: 3px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; color: var(--color-azul);">${r.semestre} ${r.anio}</span></td>
                </tr>`;
            });
        } else if (type === 'usuarios') {
            html += '<th>ID</th><th>Nombre</th><th>Correo</th><th>Registro Personal</th><th>Rol</th><th>Plaza</th><th>Estado</th>';
            html += '</tr></thead><tbody>';
            data.success_records.forEach((r, idx) => {
                html += `<tr>
                    <td>${idx + 1}.</td>
                    <td style="font-weight: 600; color: var(--color-texto-principal);">${r.nombre}</td>
                    <td>${r.correo}</td>
                    <td>${r.numero}</td>
                    <td style="text-transform: capitalize;">${r.rol}</td>
                    <td>${r.plaza}</td>
                    <td><span style="color: #48bb78; font-weight: 600;">${r.estado}</span></td>
                </tr>`;
            });
        } else {
            html += '<th>ID</th><th>Carrera</th><th>Área</th><th>Curso</th><th>Código</th><th>Sección</th><th>Jornada</th>';
            html += '</tr></thead><tbody>';
            data.success_records.forEach((r, idx) => {
                html += `<tr>
                    <td>${idx + 1}.</td>
                    <td>${r.carrera}</td>
                    <td>${r.area}</td>
                    <td style="font-weight: 600; color: var(--color-texto-principal);">${r.curso}</td>
                    <td>${r.codigo}</td>
                    <td>${r.seccion}</td>
                    <td>${r.jornada}</td>
                </tr>`;
            });
        }
        html += '</tbody></table></div>';
        reportContent.innerHTML = html;

    } else if (tab === 'error') {
        if (successBtn) successBtn.classList.remove('active');
        if (errorBtn) errorBtn.classList.add('active');

        if (!data.error_lines || data.error_lines.length === 0) {
            reportContent.innerHTML = `
                <div class="report-title">Observaciones / Errores</div>
                <p style="font-size: 13px; color: var(--color-texto-claro); text-align: left; padding: 10px 0;">
                    No se detectaron errores en este archivo. Todos los registros fueron procesados exitosamente.
                </p>`;
            return;
        }

        let html = '<div class="report-title">Observaciones y Filas con Error</div>';
        html += '<ul class="error-log-list">';
        data.error_lines.forEach(line => {
            html += `<li class="error-log-item">- ${line}</li>`;
        });
        html += '</ul>';
        reportContent.innerHTML = html;
    }
}

function initDragAndDrop(dropAreaId, fileInputId, uploadType) {
    const fileInput = document.getElementById(fileInputId);
    const dropArea = document.getElementById(dropAreaId);

    if (!dropArea || !fileInput) return;

    dropArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropArea.classList.add('dragover');
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, () => {
            dropArea.classList.remove('dragover');
        });
    });

    dropArea.addEventListener('drop', (e) => {
        e.preventDefault();
        if (e.dataTransfer.files.length > 0) {
            fileInput.files = e.dataTransfer.files;
            uploadCSVFile(uploadType);
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    showSelectionView();
    initDragAndDrop('dropAreaAcademica', 'archivo_csv_academica', 'academica');
    initDragAndDrop('dropAreaUsuarios', 'archivo_csv_usuarios', 'usuarios');
    initDragAndDrop('dropAreaCursos', 'archivo_csv_cursos', 'cursos');
});
