@extends('layouts.app')

@section('title', isset($modoEdicion) && $modoEdicion ? 'FARUSAC - Edición de Informe' : 'FARUSAC - Creación de Informe')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/informes.css') }}">
@endpush

@section('content')
<div class="wizard-container">
    <button type="button" class="help-btn" title="Ayuda y Requisitos">?</button>

    <h2 class="wizard-title">{{ isset($modoEdicion) && $modoEdicion ? 'Edición de Informe' : 'Creación de Informe' }}</h2>

    @if (isset($estaBloqueado) && $estaBloqueado)
        <div style="background-color: #fee2e2; border: 1.5px solid #ef4444; color: #991b1b; padding: 14px 20px; border-radius: 12px; margin-bottom: 20px; font-weight: 600; font-size: 13.5px; display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 20px;">🔒</span>
            <span>Este informe se encuentra <b>bloqueado para edición</b> porque ha vencido el plazo máximo de 3 días desde su envío original.</span>
        </div>
    @endif

    <!-- STEPPER -->
    <div class="stepper-wrapper">
        <div class="step-item active" id="stepIndicator1" onclick="goToStep(1)">
            <div class="step-circle">1</div>
            <div class="step-label">Información General</div>
        </div>

        <div class="stepper-arrow">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="5" y1="12" x2="19" y2="12"></line>
                <polyline points="12 5 19 12 12 19"></polyline>
            </svg>
        </div>

        <div class="step-item" id="stepIndicator2" onclick="goToStep(2)">
            <div class="step-circle">2</div>
            <div class="step-label">Registro Semanal</div>
        </div>
    </div>

    <!-- FORMULARIO -->
    <form id="formCrearInforme" onsubmit="event.preventDefault();">
        <!-- FASE 1: INFORMACIÓN GENERAL -->
        <div class="form-card-panel" id="phase1Panel">
            <div class="form-row">
                <div class="form-col">
                    <label class="form-label">*Nombre del docente:</label>
                    <input type="text" id="inputDocenteNombre" class="form-input input-readonly" value="{{ $user->nombre }}" readonly>
                </div>

                <div class="form-col">
                    <label class="form-label">*Correo:</label>
                    <input type="text" id="inputDocenteCorreo" class="form-input input-readonly" value="{{ $user->correo }}" readonly>
                </div>

                <div class="form-col">
                    <label class="form-label" for="cursoIdSelect">*Curso:</label>
                    <div class="select-container">
                        <select id="cursoIdSelect" class="form-select" onchange="onCursoSelected()" required>
                            <option value="">Seleccione un curso...</option>
                            @foreach ($cursosAsignados as $c)
                                @php
                                    $isSelected = false;
                                    if (isset($informe) && $informe->curso_id == $c->id) {
                                        $isSelected = true;
                                    } elseif (isset($selectedCurso) && $selectedCurso->id == $c->id) {
                                        $isSelected = true;
                                    }
                                @endphp
                                <option value="{{ $c->id }}" 
                                    data-seccion="{{ $c->seccion }}" 
                                    data-anio="{{ $c->anio }}"
                                    data-semestre="{{ $c->semestre }}"
                                    {{ $isSelected ? 'selected' : '' }}>
                                    {{ $c->nombre_curso }}
                                </option>
                            @endforeach
                        </select>
                        <svg class="select-custom-arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>

                <div class="form-col form-col-small">
                    <label class="form-label">*Sección:</label>
                    <input type="text" id="inputSeccion" class="form-input input-readonly" value="{{ isset($informe) ? $informe->curso->seccion : (isset($selectedCurso) ? $selectedCurso->seccion : '') }}" readonly placeholder="—">
                </div>

                <div class="form-col form-col-small">
                    <label class="form-label" for="inputEstudiantesAsignados">*Estudiantes Asignados:</label>
                    <input type="number" id="inputEstudiantesAsignados" class="form-input" min="1" placeholder="Ej. 35" value="{{ isset($informe) ? $informe->estudiantes_asignados : '' }}" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col form-col-small">
                    <label class="form-label" for="inputAnio">* Año:</label>
                    <input type="text" id="inputAnio" class="form-input input-readonly" value="{{ isset($informe) ? ($informe->curso->anio ?? date('Y')) : (isset($selectedCurso) ? $selectedCurso->anio : date('Y')) }}" readonly placeholder="2026">
                </div>

                <div class="form-col">
                    <label class="form-label" for="selectPeriodo">* Periodo</label>
                    <div class="select-container">
                        @php
                            $currentPeriodo = isset($informe) ? trim(preg_replace('/\d{4}/', '', $informe->periodo)) : '';
                        @endphp
                        <select id="selectPeriodo" class="form-select" required>
                            <option value="Primer Semestre" {{ $currentPeriodo == 'Primer Semestre' ? 'selected' : '' }}>Primer Semestre</option>
                            <option value="Segundo Semestre" {{ $currentPeriodo == 'Segundo Semestre' ? 'selected' : '' }}>Segundo Semestre</option>
                            <option value="Vacaciones Junio" {{ $currentPeriodo == 'Vacaciones Junio' ? 'selected' : '' }}>Vacaciones Junio</option>
                            <option value="Vacaciones Diciembre" {{ $currentPeriodo == 'Vacaciones Diciembre' ? 'selected' : '' }}>Vacaciones Diciembre</option>
                        </select>
                        <svg class="select-custom-arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>

                <div class="form-col">
                    <label class="form-label" for="selectMes">* Mes</label>
                    <div class="select-container">
                        @php
                            $currentMes = isset($informe) ? $informe->mes : '';
                        @endphp
                        <select id="selectMes" class="form-select" required>
                            @foreach (['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'] as $m)
                                <option value="{{ $m }}" {{ $currentMes == $m ? 'selected' : '' }}>{{ $m }}</option>
                            @endforeach
                        </select>
                        <svg class="select-custom-arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Alerta interactiva de informe duplicado -->
            <div id="duplicateInformeAlert" style="display: none; background-color: #fee2e2; border: 1.5px solid #ef4444; color: #991b1b; padding: 12px 18px; border-radius: 10px; margin-bottom: 20px; font-weight: 600; font-size: 13.5px; align-items: center; gap: 10px;">
                <span style="font-size: 18px;">⚠️</span>
                <span id="duplicateInformeAlertMsg">Ya existe un informe registrado para este curso en el mes seleccionado.</span>
            </div>

            <div class="form-row">
                <div class="form-col">
                    <label class="form-label" for="inputListadoAsistencia">Listado de asistencia:</label>
                    <input type="url" id="inputListadoAsistencia" class="form-input" placeholder="https://drive.google.com/..." value="{{ isset($informe) ? $informe->listado_asistencia_url : '' }}">
                </div>

                <div class="form-col">
                    <label class="form-label" for="inputEnlaceEvidencia">Enlace de evidencia:</label>
                    <input type="url" id="inputEnlaceEvidencia" class="form-input" placeholder="https://drive.google.com/..." value="{{ isset($informe) ? $informe->enlace_evidencia_url : '' }}">
                </div>

                <div class="form-col">
                    <label class="form-label" for="inputEnlaceMeet">Enlace de Meet o Zoom:</label>
                    <input type="url" id="inputEnlaceMeet" class="form-input" placeholder="https://meet.google.com/..." value="{{ isset($informe) ? $informe->enlace_meet_zoom_url : '' }}">
                </div>
            </div>

            <div class="form-row">
                <div class="form-col-full">
                    <label class="form-label" for="inputEnlaceClassroom">Enlace de classroom, campus virtual o google drive</label>
                    <input type="url" id="inputEnlaceClassroom" class="form-input" placeholder="https://classroom.google.com/..." value="{{ isset($informe) ? $informe->enlace_classroom_drive_url : '' }}">
                </div>
            </div>

            <div class="form-row">
                <div class="form-col-full">
                    <label class="form-label" for="inputEstrategias">Describa herramientas y/o estrategias de evaluación que utiliza dentro de su asignatura:</label>
                    <textarea id="inputEstrategias" class="form-textarea" rows="3" placeholder="Escriba las herramientas y estrategias utilizadas...">{{ isset($informe) ? $informe->estrategias_evaluacion : '' }}</textarea>
                </div>
            </div>

            <div class="wizard-actions">
                <button type="button" class="btn-wizard btn-wizard-next" onclick="goToStep(2)">
                    SIGUIENTE -&gt;
                </button>
            </div>
        </div>

        <!-- FASE 2: REGISTRO SEMANAL -->
        <div class="form-card-panel" id="phase2Panel" style="display: none;">
            <div class="semanas-list" id="semanasContainer">
                @if (isset($informe) && count($informe->semanas) > 0)
                    @foreach ($informe->semanas as $index => $sem)
                        @php $numSem = $index + 1; @endphp
                        <div class="semana-card" id="semanaCard-{{ $numSem }}" data-semana="{{ $numSem }}">
                            <div class="semana-header">
                                <span class="semana-badge">SEMANA {{ $numSem }}</span>
                                @if ($numSem > 1)
                                    <button type="button" class="btn-delete-semana" onclick="removeSemana({{ $numSem }})">
                                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Eliminar
                                    </button>
                                @endif
                            </div>

                            <div class="form-row">
                                <div class="form-col" style="flex: 2;">
                                    <label class="form-label">* Contenido o actividad desarrollada:</label>
                                    <textarea class="form-textarea semana-actividad" rows="2" placeholder="Describa el contenido impartido...">{{ $sem->actividad_realizada }}</textarea>
                                </div>
                                <div class="form-col form-col-small">
                                    <label class="form-label">Estudiantes que participaron:</label>
                                    <input type="number" class="form-input semana-estudiantes" min="0" placeholder="0" value="{{ $sem->estudiantes_participaron }}">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label class="form-label">Metodología utilizada:</label>
                                    <input type="text" class="form-input semana-metodologias" placeholder="Ej. Clase magistral, ABP..." value="{{ $sem->metodologias }}">
                                </div>
                                <div class="form-col">
                                    <label class="form-label">Medios de comunicación utilizados:</label>
                                    <input type="text" class="form-input semana-medios" placeholder="Ej. Google Meet, Correo..." value="{{ $sem->medios_comunicacion }}">
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="semana-card" id="semanaCard-1" data-semana="1">
                        <div class="semana-header">
                            <span class="semana-badge">SEMANA 1</span>
                        </div>

                        <div class="form-row">
                            <div class="form-col" style="flex: 2;">
                                <label class="form-label">* Contenido o actividad desarrollada:</label>
                                <textarea class="form-textarea semana-actividad" rows="2" placeholder="Describa el contenido impartido o la actividad realizada durante esta semana..." required></textarea>
                            </div>
                            <div class="form-col form-col-small">
                                <label class="form-label">Estudiantes que participaron:</label>
                                <input type="number" class="form-input semana-estudiantes" min="0" placeholder="0" value="0">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-col">
                                <label class="form-label">Metodología utilizada:</label>
                                <input type="text" class="form-input semana-metodologias" placeholder="Ej. Clase magistral activa, Aprendizaje basado en problemas...">
                            </div>
                            <div class="form-col">
                                <label class="form-label">Medios de comunicación utilizados:</label>
                                <input type="text" class="form-input semana-medios" placeholder="Ej. Google Meet, Classroom, Correo institucional...">
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="add-semana-wrapper">
                <button type="button" class="btn-add-circle" onclick="addSemana()" title="Agregar otra semana">
                    <div class="btn-add-circle-dashed">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                </button>
                <span class="add-semana-hint">Haga clic para agregar otra semana</span>
            </div>

            <div class="wizard-actions">
                <button type="button" class="btn-wizard btn-wizard-prev" onclick="goToStep(1)">
                    &lt;- ANTERIOR
                </button>
                <button type="button" class="btn-wizard btn-wizard-finish" onclick="{{ (isset($estaBloqueado) && $estaBloqueado && !Auth::user()->isAdministrador()) ? 'return false;' : 'openFinishModal()' }}" {{ (isset($estaBloqueado) && $estaBloqueado && !Auth::user()->isAdministrador()) ? 'disabled style="opacity:0.5;cursor:not-allowed;"' : '' }}>
                    {{ isset($modoEdicion) && $modoEdicion ? 'ACTUALIZAR INFORME' : 'FINALIZAR Y ENVIAR' }}
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Modal de Advertencia Antes de Enviar -->
<div class="modal-overlay" id="confirmFinishModal">
    <div class="confirm-status-card">
        <div class="warning-triangle">⚠</div>
        <h3 class="alert-title">¡ADVERTENCIA DE ENVÍO!</h3>
        <p class="alert-desc">
            Al enviar el informe, tendrás un plazo máximo de <b>3 días</b> para editarlo o modificarlo. 
            Tras este periodo, el informe se <b>bloqueará definitivamente</b> y no podrá ser alterado sin autorización de la coordinación.
        </p>
        <div class="modal-footer-btns">
            <button type="button" class="modal-action-btn btn-action-cancel" onclick="closeFinishModal()">Revisar</button>
            <button type="button" class="modal-action-btn btn-action-danger" id="btnConfirmSubmit" onclick="executeSubmitInforme()">Confirmar</button>
        </div>
    </div>
</div>

<!-- Modal de Notificación de Éxito -->
<div class="modal-overlay" id="successInformeModal">
    <div class="confirm-status-card success-style">
        <div class="success-check-icon">✓</div>
        <h3 class="alert-title">¡INFORME {{ isset($modoEdicion) && $modoEdicion ? 'ACTUALIZADO' : 'ENVIADO' }} CON ÉXITO!</h3>
        <p class="alert-desc">
            Tu informe ha sido guardado y entregado correctamente en la plataforma. Recuerda que tienes 3 días por si necesitas corregir alguna información.
        </p>
        <div class="modal-footer-btns">
            <button type="button" class="modal-action-btn btn-action-success" onclick="redirectToDashboard()">
                CONFIRMAR
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    window.informeDocenteConfig = {
        isEditMode: {{ (isset($modoEdicion) && $modoEdicion) || isset($informe) ? 'true' : 'false' }},
        currentInformeId: {{ isset($informe) ? $informe->id : 'null' }},
        estaBloqueado: {{ isset($estaBloqueado) && $estaBloqueado && !Auth::user()->isAdministrador() ? 'true' : 'false' }},
        targetUrl: '{{ (isset($modoEdicion) && $modoEdicion) || isset($informe) ? ($user->isCoordinador() ? url('/coordinador/informes/' . ($informe->id ?? 0)) : url('/docente/informes/' . ($informe->id ?? 0))) : ($user->isCoordinador() ? route('coordinador.informes.guardar') : route('docente.informes.guardar')) }}',
        redirectUrl: '{{ (isset($modoEdicion) && $modoEdicion) || isset($informe) ? ($user->isCoordinador() ? route('coordinador.informes') : route('docente.informes')) : ($user->isCoordinador() ? route('coordinador.dashboard') : route('docente.dashboard')) }}',
        informesExistentes: @json($informesExistentes ?? [])
    };
</script>
<script src="{{ asset('js/crear-informe-docente.js') }}"></script>
@endpush
