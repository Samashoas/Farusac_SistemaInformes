/**
 * FARUSAC - SISTEMA DE INFORMES: LÓGICA INFORME COORDINACIÓN (crear-informe-coordinacion.js)
 */

const informeCoordCfg = window.informeCoordConfig || {};
const informeId = informeCoordCfg.informeId || "";

        // Variables de control de observaciones
        let currentObsTriggerBtn = null;
        let currentObsHiddenInput = null;


        // Estilos interactivos para selects Sí/No
        function toggleSelectStyle(selectEl) {
            if (selectEl.value === '1') {
                selectEl.classList.add('val-si');
                selectEl.classList.remove('val-no');
            } else {
                selectEl.classList.add('val-no');
                selectEl.classList.remove('val-si');
            }
        }

        // Eliminar fila de tabla dinámica
        function deleteTableRow(btn) {
            const tr = btn.closest('tr');
            if (tr) tr.remove();
        }



        // Agregar fila a Estudiantes con problemas (Inciso 3.2)
        function addEstudianteRow() {
            const tbody = document.getElementById('tbodyEstudiantes');
            const tr = document.createElement('tr');
            tr.innerHTML = `
            <td>
                <input type="text" class="table-input est-asignatura" placeholder="Nombre de la Asignatura">
            </td>
            <td style="text-align: center;">
                <input type="text" class="table-input est-seccion" style="text-align: center;" placeholder="Sección">
            </td>
            <td style="text-align: center;">
                <input type="number" min="0" class="table-input est-cantidad" value="0" style="text-align: center;" placeholder="0">
            </td>
            <td>
                <input type="text" class="table-input est-carne" placeholder="Ej. 202100123, 202204567">
            </td>
            <td style="text-align: center;">
                <button type="button" class="btn-delete-row" onclick="deleteTableRow(this)" title="Eliminar fila">✕</button>
            </td>
        `;
            tbody.appendChild(tr);
        }

        // Modal Control Helpers
        function abrirModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        }

        function cerrarModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            }
        }

        function cerrarModalFuera(event, modalId) {
            if (event.target && event.target.id === modalId) {
                cerrarModal(modalId);
            }
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                cerrarModal('modalVerInformeDocente');
                cerrarModal('modalObservaciones');
            }
        });

        // Abrir Modal de Observaciones
        function abrirModalObservacion(btn, tipo) {
            currentObsTriggerBtn = btn;
            const tr = btn.closest('tr');
            if (!tr) return;

            const cursoNombre = tr.getAttribute('data-asig-nombre') || tr.querySelector('.asig-nombre, .avance-asignatura')?.value || 'Asignatura';
            const seccion = tr.getAttribute('data-asig-seccion') || tr.querySelector('.asig-seccion, .avance-seccion')?.value || '—';
            const docente = tr.getAttribute('data-asig-docente') || tr.querySelector('.asig-docente, .avance-docente')?.value || '—';

            currentObsHiddenInput = tr.querySelector(tipo === 'asig' ? '.asig-obs' : '.avance-obs');
            const currentVal = currentObsHiddenInput ? currentObsHiddenInput.value : '';

            document.getElementById('modalObsInfoCurso').innerHTML = `<strong>${cursoNombre}</strong> (Sección <b>${seccion}</b>) &bull; Docente: <b>${docente}</b>`;
            const textarea = document.getElementById('modalObsTextarea');
            textarea.value = currentVal;
            actualizarContadorObs();

            abrirModal('modalObservaciones');
            setTimeout(() => textarea.focus(), 150);
        }

        // Actualizar contador de caracteres
        function actualizarContadorObs() {
            const textarea = document.getElementById('modalObsTextarea');
            const count = textarea ? textarea.value.length : 0;
            const el = document.getElementById('modalObsCharCount');
            if (el) el.textContent = `${count} caracteres`;
        }
        document.getElementById('modalObsTextarea')?.addEventListener('input', actualizarContadorObs);

        // Guardar Observación desde el Modal
        function guardarObservacionModal() {
            const textarea = document.getElementById('modalObsTextarea');
            const val = textarea.value.trim();

            if (currentObsHiddenInput) {
                currentObsHiddenInput.value = val;
            }

            if (currentObsTriggerBtn) {
                const btnText = currentObsTriggerBtn.querySelector('.obs-btn-text');
                if (val) {
                    currentObsTriggerBtn.classList.remove('empty');
                    currentObsTriggerBtn.classList.add('filled');
                    if (btnText) btnText.textContent = val.length > 20 ? val.substring(0, 18) + '...' : val;
                } else {
                    currentObsTriggerBtn.classList.remove('filled');
                    currentObsTriggerBtn.classList.add('empty');
                    if (btnText) btnText.textContent = '+ Observación';
                }
            }

            cerrarModal('modalObservaciones');
            showToast('✓ Observación registrada en el informe', 'success');
        }

        // Abrir Modal de Informe de Docente (En la misma ventana con formato oficial de PDF)
        function abrirModalInformeDocente(docenteInformeId, docenteNombre = '', cursoNombre = '') {
            if (!docenteInformeId) return;

            const rawUrl = `/coordinador/informes/${docenteInformeId}/ver`;
            const urlInforme = window.route ? window.route(rawUrl) : rawUrl;

            // Si el modal institucional global está disponible, usarlo preferentemente
            if (typeof window.openReportPreviewModal === 'function') {
                const title = cursoNombre ? `Informe Docente - ${cursoNombre}` : 'Informe Mensual del Docente';
                const subtitle = docenteNombre ? `${docenteNombre} • Documento Institucional FARUSAC` : 'Documento institucional oficial FARUSAC';
                window.openReportPreviewModal('docente', docenteInformeId, title, subtitle, urlInforme);
                return;
            }

            // Fallback con modal local
            abrirModal('modalVerInformeDocente');

            const loader = document.getElementById('modalDocenteLoader');
            const iframe = document.getElementById('iframeDocenteInforme');
            const subtitulo = document.getElementById('modalDocenteSubtitulo');
            const tabBtn = document.getElementById('modalDocenteTabBtn');

            if (loader) loader.style.display = 'flex';

            if (iframe) {
                iframe.src = `${urlInforme}?embed=1`;
            }

            if (subtitulo) {
                subtitulo.textContent = docenteNombre ? `${docenteNombre} • Documento oficial FARUSAC` : 'Documento institucional oficial FARUSAC';
            }

            if (tabBtn) {
                tabBtn.href = urlInforme;
            }
        }
        window.abrirModalInformeDocente = abrirModalInformeDocente;

        // Imprimir directamente el documento oficial desde el modal
        function imprimirIframeDocente() {
            const iframe = document.getElementById('iframeDocenteInforme');
            if (iframe && iframe.contentWindow) {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
            }
        }

        // Actualizar estilo visual del select según su valor
        function actualizarColorSelect(select) {
            if (!select) return;
            if (select.value === '1') {
                select.classList.remove('val-no');
                select.classList.add('val-si');
            } else {
                select.classList.remove('val-si');
                select.classList.add('val-no');
            }
        }

        // Cargar dinámicamente los datos de docentes cuando cambia periodo o mes
        async function cargarDatosDocentes(mostrarToast = true) {
            const periodo = document.getElementById('selectPeriodo')?.value;
            const mes = document.getElementById('selectMes')?.value;

            if (!periodo || !mes) return;

            try {
                const endpoint = `/coordinador/informes-coordinacion/datos-docentes?periodo=${encodeURIComponent(periodo)}&mes=${encodeURIComponent(mes)}`;
                const fetchUrl = window.route ? window.route(endpoint) : endpoint;
                const token = window.getCsrfToken ? window.getCsrfToken() : '';

                const res = await fetch(fetchUrl, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    }
                });
                const data = await res.json();

                if (res.ok && data.success) {
                    // 1. Sincronizar Programas de Asignaturas (Inciso 1)
                    if (data.programas && Array.isArray(data.programas) && data.programas.length > 0) {
                        const tbodyProg = document.getElementById('tbodyProgramas');
                        if (tbodyProg) {
                            const existingRows = tbodyProg.querySelectorAll('tr.fila-programa');

                            if (existingRows.length === 0) {
                                tbodyProg.innerHTML = '';
                                data.programas.forEach(p => {
                                    const tr = document.createElement('tr');
                                    tr.className = 'fila-programa';
                                    tr.setAttribute('data-asig-name', p.asignatura);
                                    tr.innerHTML = `
                                    <td>
                                        <input type="text" class="table-input prog-asignatura" value="${p.asignatura}" readonly style="background-color: #f8fafc; font-weight: 600; color: #1e293b; cursor: default;">
                                    </td>
                                    <td>
                                        <input type="url" class="table-input prog-enlace" value="${p.enlace_programa || ''}" placeholder="https://drive.google.com/...">
                                    </td>
                                `;
                                    tbodyProg.appendChild(tr);
                                });
                            } else {
                                data.programas.forEach(p => {
                                    let found = false;
                                    existingRows.forEach(tr => {
                                        const inputAsig = tr.querySelector('.prog-asignatura');
                                        const inputLink = tr.querySelector('.prog-enlace');
                                        if (inputAsig && inputAsig.value.trim().toLowerCase() === p.asignatura.trim().toLowerCase()) {
                                            found = true;
                                            // Actualizar el enlace si el docente entregó programa en este mes
                                            if (p.enlace_programa) {
                                                inputLink.value = p.enlace_programa;
                                            }
                                        }
                                    });

                                    if (!found) {
                                        const tr = document.createElement('tr');
                                        tr.className = 'fila-programa';
                                        tr.setAttribute('data-asig-name', p.asignatura);
                                        tr.innerHTML = `
                                        <td>
                                            <input type="text" class="table-input prog-asignatura" value="${p.asignatura}" readonly style="background-color: #f8fafc; font-weight: 600; color: #1e293b; cursor: default;">
                                        </td>
                                        <td>
                                            <input type="url" class="table-input prog-enlace" value="${p.enlace_programa || ''}" placeholder="https://drive.google.com/...">
                                        </td>
                                    `;
                                        tbodyProg.appendChild(tr);
                                    }
                                });
                            }
                        }
                    }

                    // 2. Sincronizar Información General de Asignaturas (Inciso 2) y Avances (Inciso 3.1)
                    if (data.datos && Array.isArray(data.datos)) {
                        const mapByKey = new Map();
                        const listByCurso = new Map();

                        data.datos.forEach(d => {
                            if (d.curso_id && d.docente_id) {
                                mapByKey.set(`${d.curso_id}_id_${d.docente_id}`, d);
                            }
                            if (d.curso_id && d.docente_nombre) {
                                mapByKey.set(`${d.curso_id}_name_${d.docente_nombre.trim().toLowerCase()}`, d);
                            }
                            if (d.curso_id) {
                                const cKey = String(d.curso_id);
                                if (!listByCurso.has(cKey)) {
                                    listByCurso.set(cKey, []);
                                }
                                listByCurso.get(cKey).push(d);
                            }
                        });

                        const existingAsigRows = document.querySelectorAll('#tbodyAsignaturas tr.fila-asig-general');
                        if (existingAsigRows.length === 0) {
                            // Generar filas dinámicamente si el contenedor estaba vacío
                            const tbodyAsig = document.getElementById('tbodyAsignaturas');
                            const tbodyAv = document.getElementById('tbodyAvances');
                            if (tbodyAsig) tbodyAsig.innerHTML = '';
                            if (tbodyAv) tbodyAv.innerHTML = '';

                            data.datos.forEach((d, idx) => {
                                if (tbodyAsig) {
                                    const trAsig = document.createElement('tr');
                                    trAsig.className = 'fila-asig-general';
                                    trAsig.setAttribute('data-curso-id', d.curso_id || '');
                                    trAsig.setAttribute('data-docente-id', d.docente_id || '');
                                    trAsig.setAttribute('data-row-index', idx);
                                    trAsig.setAttribute('data-asig-nombre', d.asignatura || '');
                                    trAsig.setAttribute('data-asig-seccion', d.seccion || '');
                                    trAsig.setAttribute('data-asig-docente', d.docente_nombre || '');
                                    trAsig.innerHTML = `
                                        <td style="text-align: center;">
                                            <div class="col-docente-report-action" data-curso-id="${d.curso_id || ''}">
                                                ${d.docente_informe_id ? `
                                                    <button type="button" class="btn-ver-docente" onclick="abrirModalInformeDocente(${d.docente_informe_id}, '${(d.docente_nombre || '').replace(/'/g, "\\'")}', '${(d.asignatura || '').replace(/'/g, "\\'")}')" title="Visualizar informe entregado por el docente en ${mes}">
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                                        <span>Ver Informe</span>
                                                    </button>
                                                ` : `<span class="badge-sin-informe">Sin informe</span>`}
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" class="table-input asig-docente" value="${d.docente_nombre || ''}" readonly style="background-color: #f8fafc; font-weight: 600; color: #1e293b; cursor: default;">
                                        </td>
                                        <td>
                                            <input type="text" class="table-input asig-nombre" value="${d.asignatura || ''}" readonly style="background-color: #f8fafc; font-weight: 600; color: #1e293b; cursor: default;">
                                        </td>
                                        <td style="text-align: center;">
                                            <input type="text" class="table-input asig-seccion" value="${d.seccion || ''}" readonly style="text-align: center; width: 55px; background-color: #f8fafc; font-weight: 600; color: #1e293b; cursor: default;">
                                        </td>
                                        <td style="text-align: center;">
                                            <select class="table-select asig-presento ${d.presento_informe ? 'val-si' : 'val-no'}" onchange="actualizarColorSelect(this)">
                                                <option value="1" ${d.presento_informe ? 'selected' : ''}>Sí</option>
                                                <option value="0" ${!d.presento_informe ? 'selected' : ''}>No</option>
                                            </select>
                                        </td>
                                        <td style="text-align: center;">
                                            <select class="table-select asig-sala ${d.tiene_sala_reuniones ? 'val-si' : 'val-no'}" onchange="actualizarColorSelect(this)">
                                                <option value="1" ${d.tiene_sala_reuniones ? 'selected' : ''}>Sí</option>
                                                <option value="0" ${!d.tiene_sala_reuniones ? 'selected' : ''}>No</option>
                                            </select>
                                        </td>
                                        <td style="text-align: center;">
                                            <select class="table-select asig-virtual ${d.funciona_enlace_virtual ? 'val-si' : 'val-no'}" onchange="actualizarColorSelect(this)">
                                                <option value="1" ${d.funciona_enlace_virtual ? 'selected' : ''}>Sí</option>
                                                <option value="0" ${!d.funciona_enlace_virtual ? 'selected' : ''}>No</option>
                                            </select>
                                        </td>
                                        <td style="text-align: center;">
                                            <select class="table-select asig-eval ${d.funciona_enlace_evaluacion ? 'val-si' : 'val-no'}" onchange="actualizarColorSelect(this)">
                                                <option value="1" ${d.funciona_enlace_evaluacion ? 'selected' : ''}>Sí</option>
                                                <option value="0" ${!d.funciona_enlace_evaluacion ? 'selected' : ''}>No</option>
                                            </select>
                                        </td>
                                        <td style="text-align: center;">
                                            <select class="table-select asig-evid ${d.evidencias_generales ? 'val-si' : 'val-no'}" onchange="actualizarColorSelect(this)">
                                                <option value="1" ${d.evidencias_generales ? 'selected' : ''}>Sí</option>
                                                <option value="0" ${!d.evidencias_generales ? 'selected' : ''}>No</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="hidden" class="asig-obs" value="${d.observaciones || ''}">
                                            <button type="button" class="btn-obs-trigger ${d.observaciones ? 'filled' : 'empty'}" onclick="abrirModalObservacion(this, 'asig')">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                <span class="btn-obs-label">${d.observaciones ? 'Ver / Editar' : 'Agregar'}</span>
                                            </button>
                                        </td>
                                    `;
                                    tbodyAsig.appendChild(trAsig);
                                }

                                if (tbodyAv) {
                                    const trAv = document.createElement('tr');
                                    trAv.className = 'fila-avance';
                                    trAv.setAttribute('data-curso-id', d.curso_id || '');
                                    trAv.setAttribute('data-docente-id', d.docente_id || '');
                                    trAv.setAttribute('data-asig-nombre', d.asignatura || '');
                                    trAv.setAttribute('data-asig-seccion', d.seccion || '');
                                    trAv.setAttribute('data-asig-docente', d.docente_nombre || '');
                                    trAv.innerHTML = `
                                        <td>
                                            <input type="text" class="table-input avance-docente" value="${d.docente_nombre || ''}" readonly style="background-color: #f8fafc; font-weight: 600; color: #1e293b; cursor: default;">
                                        </td>
                                        <td>
                                            <input type="text" class="table-input avance-asignatura" value="${d.asignatura || ''}" readonly style="background-color: #f8fafc; font-weight: 600; color: #1e293b; cursor: default;">
                                        </td>
                                        <td style="text-align: center;">
                                            <input type="text" class="table-input avance-seccion" value="${d.seccion || ''}" readonly style="text-align: center; width: 55px; background-color: #f8fafc; font-weight: 600; color: #1e293b; cursor: default;">
                                        </td>
                                        <td style="text-align: center;">
                                            <div style="display: flex; align-items: center; justify-content: center; gap: 4px;">
                                                <input type="number" min="0" max="100" class="table-input avance-porcentaje" value="${d.porcentaje_avance || 100}" style="text-align: center; width: 60px; font-weight: 600;">
                                                <span style="font-weight: 700; color: #64748b; font-size: 0.85rem;">%</span>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="hidden" class="avance-obs" value="${d.observaciones || ''}">
                                            <button type="button" class="btn-obs-trigger ${d.observaciones ? 'filled' : 'empty'}" onclick="abrirModalObservacion(this, 'avance')">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                <span class="btn-obs-label">${d.observaciones ? 'Ver / Editar' : 'Agregar'}</span>
                                            </button>
                                        </td>
                                    `;
                                    tbodyAv.appendChild(trAv);
                                }
                            });
                        } else {
                            existingAsigRows.forEach(tr => {
                                const cId = tr.getAttribute('data-curso-id');
                                const docId = tr.getAttribute('data-docente-id');
                                const docNombre = (tr.getAttribute('data-asig-docente') || tr.querySelector('.asig-docente')?.value || '').trim().toLowerCase();

                                let d = null;
                                if (cId && docId && mapByKey.has(`${cId}_id_${docId}`)) {
                                    d = mapByKey.get(`${cId}_id_${docId}`);
                                } else if (cId && docNombre && mapByKey.has(`${cId}_name_${docNombre}`)) {
                                    d = mapByKey.get(`${cId}_name_${docNombre}`);
                                } else if (cId && listByCurso.has(String(cId))) {
                                    const pool = listByCurso.get(String(cId));
                                    if (pool.length === 1) {
                                        d = pool[0];
                                    }
                                }

                                if (d) {
                                    if (d.docente_id) {
                                        tr.setAttribute('data-docente-id', d.docente_id);
                                    }
                                    const inPresento = tr.querySelector('.asig-presento');
                                    if (inPresento) {
                                        inPresento.value = d.presento_informe ? '1' : '0';
                                        actualizarColorSelect(inPresento);
                                    }

                                    const inSala = tr.querySelector('.asig-sala');
                                    if (inSala) {
                                        inSala.value = d.tiene_sala_reuniones ? '1' : '0';
                                        actualizarColorSelect(inSala);
                                    }

                                    const inVirtual = tr.querySelector('.asig-virtual');
                                    if (inVirtual) {
                                        inVirtual.value = d.funciona_enlace_virtual ? '1' : '0';
                                        actualizarColorSelect(inVirtual);
                                    }

                                    const inEval = tr.querySelector('.asig-eval');
                                    if (inEval) {
                                        inEval.value = d.funciona_enlace_evaluacion ? '1' : '0';
                                        actualizarColorSelect(inEval);
                                    }

                                    const inEvid = tr.querySelector('.asig-evid');
                                    if (inEvid) {
                                        inEvid.value = d.evidencias_generales ? '1' : '0';
                                        actualizarColorSelect(inEvid);
                                    }

                                    // Actualizar botón de visualización de informe docente
                                    const actionCol = tr.querySelector('.col-docente-report-action');
                                    if (actionCol) {
                                        if (d.docente_informe_id) {
                                            actionCol.innerHTML = `
                                            <button type="button" class="btn-ver-docente" onclick="abrirModalInformeDocente(${d.docente_informe_id}, '${(d.docente_nombre || '').replace(/'/g, "\\'")}', '${(d.asignatura || '').replace(/'/g, "\\'")}')" title="Visualizar informe entregado por el docente en ${mes}">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                                <span>Ver Informe</span>
                                            </button>
                                        `;
                                        } else {
                                            actionCol.innerHTML = `<span class="badge-sin-informe">Sin informe</span>`;
                                        }
                                    }
                                }
                            });
                        }
                    }

                    if (mostrarToast) {
                        showToast(`✓ Filtro aplicado: datos de docentes sincronizados para ${mes}`, 'success');
                    }
                }
            } catch (err) {
                console.error('Error al sincronizar datos de docentes:', err);
            }
        }

        document.getElementById('selectPeriodo')?.addEventListener('change', () => cargarDatosDocentes(true));
        document.getElementById('selectMes')?.addEventListener('change', () => cargarDatosDocentes(true));

        document.addEventListener('DOMContentLoaded', () => {
            // En modo creación, sincronizar con el mes seleccionado inicialmente
            if (!informeId) {
                cargarDatosDocentes(false);
            }
        });

        // Toast de Notificación
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toastNotification');
            if (!toast) return;
            toast.textContent = message;
            toast.className = `toast-notification show ${type}`;
            setTimeout(() => {
                toast.classList.remove('show');
            }, 4000);
        }

        // Envío / Guardado del Informe
        async function submitInformeCoordinacion(estado = 'enviado') {
            const periodo = document.getElementById('selectPeriodo').value;
            const mes = document.getElementById('selectMes').value;
            const anio = document.getElementById('inputAnio').value;
            const herramientasVirtuales = document.getElementById('inputHerramientasVirtuales').value;

            // Recopilar Programas (Inciso 1)
            const programas = [];
            document.querySelectorAll('#tbodyProgramas tr').forEach(tr => {
                const asig = tr.querySelector('.prog-asignatura')?.value.trim();
                const link = tr.querySelector('.prog-enlace')?.value.trim();
                if (asig) {
                    programas.push({
                        asignatura: asig,
                        enlace_programa: link || null
                    });
                }
            });

            // Recopilar Asignaturas (Inciso 2)
            const asignaturas = [];
            document.querySelectorAll('#tbodyAsignaturas tr.fila-asig-general').forEach(tr => {
                const cursoId = tr.getAttribute('data-curso-id');
                const docente = tr.querySelector('.asig-docente')?.value.trim();
                const nombre = tr.querySelector('.asig-nombre')?.value.trim();
                const seccion = tr.querySelector('.asig-seccion')?.value.trim();
                const presento = tr.querySelector('.asig-presento')?.value === '1';
                const sala = tr.querySelector('.asig-sala')?.value === '1';
                const virtual = tr.querySelector('.asig-virtual')?.value === '1';
                const evaluacion = tr.querySelector('.asig-eval')?.value === '1';
                const evidencias = tr.querySelector('.asig-evid')?.value === '1';
                const obs = tr.querySelector('.asig-obs')?.value.trim();

                if (nombre) {
                    asignaturas.push({
                        curso_id: cursoId || null,
                        docente_nombre: docente || '—',
                        asignatura: nombre,
                        seccion: seccion || '—',
                        presento_informe: presento,
                        tiene_sala_reuniones: sala,
                        funciona_enlace_virtual: virtual,
                        funciona_enlace_evaluacion: evaluacion,
                        evidencias_generales: evidencias,
                        observaciones: obs || null
                    });
                }
            });

            // Recopilar Avances (Inciso 3.1)
            const avances = [];
            document.querySelectorAll('#tbodyAvances tr.fila-avance').forEach(tr => {
                const cursoId = tr.getAttribute('data-curso-id');
                const docente = tr.querySelector('.avance-docente')?.value.trim();
                const nombre = tr.querySelector('.avance-asignatura')?.value.trim();
                const seccion = tr.querySelector('.avance-seccion')?.value.trim();
                const porcentaje = parseInt(tr.querySelector('.avance-porcentaje')?.value || '0', 10);
                const obs = tr.querySelector('.avance-obs')?.value.trim();

                if (nombre) {
                    avances.push({
                        curso_id: cursoId || null,
                        docente_nombre: docente || '—',
                        asignatura: nombre,
                        seccion: seccion || '—',
                        porcentaje_avance: porcentaje,
                        observaciones: obs || null
                    });
                }
            });

            // Recopilar Estudiantes con problemas (Inciso 3.2)
            const estudiantes = [];
            document.querySelectorAll('#tbodyEstudiantes tr').forEach(tr => {
                const asig = tr.querySelector('.est-asignatura')?.value.trim();
                const seccion = tr.querySelector('.est-seccion')?.value.trim();
                const cantidad = parseInt(tr.querySelector('.est-cantidad')?.value || '0', 10);
                const carne = tr.querySelector('.est-carne')?.value.trim();

                if (asig) {
                    estudiantes.push({
                        asignatura: asig,
                        seccion: seccion || '—',
                        cantidad_estudiantes: cantidad,
                        carne_estudiantes: carne || null
                    });
                }
            });

            // Recopilar Enlaces de Secciones 4, 5 y 6
            const enlaceActividadesCoordinacion = document.getElementById('inputEnlaceActividadesCoordinacion')?.value.trim() || null;
            const enlaceInformeAuxiliares = document.getElementById('inputEnlaceInformeAuxiliares')?.value.trim() || null;
            const enlaceDocentesPermisos = document.getElementById('inputEnlaceDocentesPermisos')?.value.trim() || null;

            const payload = {
                periodo: periodo,
                mes: mes,
                anio: parseInt(anio, 10),
                herramientas_virtuales: herramientasVirtuales,
                enlace_actividades_coordinacion: enlaceActividadesCoordinacion,
                enlace_informe_auxiliares: enlaceInformeAuxiliares,
                enlace_docentes_permisos: enlaceDocentesPermisos,
                estado: estado,
                programas: programas,
                asignaturas: asignaturas,
                avances: avances,
                estudiantes: estudiantes
            };

            const basePath = informeId
                ? `/coordinador/informes-coordinacion/${informeId}`
                : '/coordinador/informes-coordinacion';
            const url = window.route ? window.route(basePath) : basePath;

            const method = informeId ? 'PUT' : 'POST';
            const token = window.getCsrfToken ? window.getCsrfToken() : '';

            try {
                const res = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();

                if (res.ok && data.success) {
                    showToast(data.message || 'Informe guardado exitosamente.', 'success');
                    setTimeout(() => {
                        window.location.href = data.redirect || data.redirect_url || (window.route ? window.route('/coordinador/dashboard') : '/coordinador/dashboard');
                    }, 1200);
                } else {
                    showToast(data.message || 'Error al guardar el informe.', 'error');
                }
            } catch (err) {
                showToast('Error de conexión con el servidor: ' + err.message, 'error');
            }
        }