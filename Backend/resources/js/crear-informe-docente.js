/**
 * FARUSAC - SISTEMA DE INFORMES: LÓGICA DE CREACIÓN/EDICIÓN DE INFORME DOCENTE (crear-informe-docente.js)
 */

const cfg = window.informeDocenteConfig || {};
const isEditMode = !!cfg.isEditMode;
const targetUrl = cfg.targetUrl || '';
const redirectUrl = cfg.redirectUrl || '';

    function onCursoSelected() {
        const select = document.getElementById('cursoIdSelect');
        const selectedOpt = select.options[select.selectedIndex];
        const inputSeccion = document.getElementById('inputSeccion');
        const inputAnio = document.getElementById('inputAnio');
        const selectPeriodo = document.getElementById('selectPeriodo');

        if (selectedOpt && selectedOpt.value) {
            inputSeccion.value = selectedOpt.getAttribute('data-seccion') || '';
            const anioCurso = selectedOpt.getAttribute('data-anio');
            if (anioCurso) inputAnio.value = anioCurso;

            const semCurso = selectedOpt.getAttribute('data-semestre') || '';
            if (semCurso) {
                Array.from(selectPeriodo.options).forEach(opt => {
                    if (opt.value.toLowerCase().includes(semCurso.toLowerCase()) || semCurso.toLowerCase().includes(opt.value.toLowerCase())) {
                        opt.selected = true;
                    }
                });
            }
        } else {
            inputSeccion.value = '';
        }
    }

    function goToStep(step) {
        if (step === 2) {
            const cursoVal = document.getElementById('cursoIdSelect').value;
            const estudiantesVal = document.getElementById('inputEstudiantesAsignados').value;

            let hasErrors = false;
            if (!cursoVal) {
                document.getElementById('cursoIdSelect').classList.add('input-error');
                hasErrors = true;
            }
            if (!estudiantesVal || parseInt(estudiantesVal) < 1) {
                document.getElementById('inputEstudiantesAsignados').classList.add('input-error');
                hasErrors = true;
            }

            if (hasErrors) {
                window.showToast('Por favor completa los campos obligatorios antes de continuar a la Fase 2.', 'warning');
                return;
            }

            document.getElementById('phase1Panel').style.display = 'none';
            document.getElementById('phase2Panel').style.display = 'block';

            document.getElementById('stepIndicator1').classList.remove('active');
            document.getElementById('stepIndicator1').classList.add('completed');
            document.getElementById('stepIndicator2').classList.add('active');
        } else {
            document.getElementById('phase2Panel').style.display = 'none';
            document.getElementById('phase1Panel').style.display = 'block';

            document.getElementById('stepIndicator2').classList.remove('active');
            document.getElementById('stepIndicator1').classList.add('active');
        }
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function addSemana() {
        const container = document.getElementById('semanasContainer');
        const count = container.querySelectorAll('.semana-card').length + 1;

        const card = document.createElement('div');
        card.className = 'semana-card';
        card.id = `semanaCard-${count}`;
        card.setAttribute('data-semana', count);
        card.innerHTML = `
            <div class="semana-header">
                <span class="semana-badge">SEMANA ${count}</span>
                <button type="button" class="btn-delete-semana" onclick="removeSemana(${count})">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Eliminar
                </button>
            </div>
            <div class="form-row">
                <div class="form-col" style="flex: 2;">
                    <label class="form-label">* Contenido o actividad desarrollada:</label>
                    <textarea class="form-textarea semana-actividad" rows="2" placeholder="Describa el contenido impartido..." required></textarea>
                </div>
                <div class="form-col form-col-small">
                    <label class="form-label">Estudiantes que participaron:</label>
                    <input type="number" class="form-input semana-estudiantes" min="0" placeholder="0" value="0">
                </div>
            </div>
            <div class="form-row">
                <div class="form-col">
                    <label class="form-label">Metodología utilizada:</label>
                    <input type="text" class="form-input semana-metodologias" placeholder="Ej. Clase magistral, ABP...">
                </div>
                <div class="form-col">
                    <label class="form-label">Medios de comunicación utilizados:</label>
                    <input type="text" class="form-input semana-medios" placeholder="Ej. Google Meet, Correo...">
                </div>
            </div>
        `;
        container.appendChild(card);
    }

    function removeSemana(num) {
        const card = document.getElementById(`semanaCard-${num}`);
        if (card) {
            card.remove();
            reindexSemanas();
        }
    }

    function reindexSemanas() {
        const cards = document.querySelectorAll('#semanasContainer .semana-card');
        cards.forEach((card, index) => {
            const newNum = index + 1;
            card.id = `semanaCard-${newNum}`;
            card.setAttribute('data-semana', newNum);
            const badge = card.querySelector('.semana-badge');
            if (badge) badge.textContent = `SEMANA ${newNum}`;
            const delBtn = card.querySelector('.btn-delete-semana');
            if (delBtn) delBtn.setAttribute('onclick', `removeSemana(${newNum})`);
        });
    }

    function openFinishModal() {
        const estudiantesAsignados = parseInt(document.getElementById('inputEstudiantesAsignados').value) || 0;
        let hasError = false;

        document.querySelectorAll('.semana-card').forEach(card => {
            const inputEst = card.querySelector('.semana-estudiantes');
            const num = parseInt(inputEst.value) || 0;
            if (num > estudiantesAsignados) {
                inputEst.classList.add('input-error');
                hasError = true;
            }
        });

        if (hasError) {
            window.showToast('Los estudiantes participantes no pueden exceder los asignados.', 'error');
            return;
        }

        const primeraActividad = document.querySelector('.semana-actividad')?.value.trim();
        if (!primeraActividad) {
            window.showToast('Por favor describe el contenido de al menos una semana.', 'error');
            return;
        }

        document.getElementById('confirmFinishModal').classList.add('active');
    }

    function closeFinishModal() {
        document.getElementById('confirmFinishModal').classList.remove('active');
    }

    function executeSubmitInforme() {
        const btnConfirm = document.getElementById('btnConfirmSubmit');
        const estudiantesAsignados = parseInt(document.getElementById('inputEstudiantesAsignados').value) || 0;

        btnConfirm.disabled = true;
        btnConfirm.textContent = 'ENVIANDO...';

        const anioVal = document.getElementById('inputAnio').value.trim() || new Date().getFullYear();
        const periodoVal = document.getElementById('selectPeriodo').value.trim();

        const payload = {
            curso_id: document.getElementById('cursoIdSelect').value,
            periodo: `${periodoVal} ${anioVal}`,
            mes: document.getElementById('selectMes').value,
            estudiantes_asignados: estudiantesAsignados,
            listado_asistencia_url: document.getElementById('inputListadoAsistencia').value.trim() || null,
            enlace_evidencia_url: document.getElementById('inputEnlaceEvidencia').value.trim() || null,
            enlace_meet_zoom_url: document.getElementById('inputEnlaceMeet').value.trim() || null,
            enlace_classroom_drive_url: document.getElementById('inputEnlaceClassroom').value.trim() || null,
            estrategias_evaluacion: document.getElementById('inputEstrategias').value.trim() || null,
            semanas: []
        };

        const semanaCards = document.querySelectorAll('#semanasContainer .semana-card');
        semanaCards.forEach((card, index) => {
            payload.semanas.push({
                numero_semana: index + 1,
                actividad_realizada: card.querySelector('.semana-actividad').value.trim() || 'Sin descripción',
                estudiantes_participaron: parseInt(card.querySelector('.semana-estudiantes').value) || 0,
                metodologias: card.querySelector('.semana-metodologias').value.trim() || null,
                medios_comunicacion: card.querySelector('.semana-medios').value.trim() || null,
            });
        });

        const targetMethod = isEditMode ? 'PUT' : 'POST';

        fetch(targetUrl, {
            method: targetMethod,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.getCsrfToken(),
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json().then(data => ({ status: res.status, body: data })))
        .then(({ status, body }) => {
            btnConfirm.disabled = false;
            btnConfirm.textContent = 'CONFIRMAR';
            closeFinishModal();

            if (status === 200 && body.success) {
                document.getElementById('successInformeModal').classList.add('active');
            } else {
                window.showToast(body.message || 'Ocurrió un error al registrar el informe.', 'error');
            }
        })
        .catch(err => {
            console.error(err);
            btnConfirm.disabled = false;
            btnConfirm.textContent = 'CONFIRMAR';
            closeFinishModal();
            window.showToast('Error de conexión con el servidor.', 'error');
        });
    }

    function redirectToDashboard() {
        window.location.href = redirectUrl;
    }

    document.addEventListener('DOMContentLoaded', () => {
        ['cursoIdSelect', 'inputEstudiantesAsignados', 'selectPeriodo', 'inputAnio', 'selectMes'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', () => el.classList.remove('input-error'));
                el.addEventListener('change', () => el.classList.remove('input-error'));
            }
        });

        if (!isEditMode) {
            onCursoSelected();
        }
    });