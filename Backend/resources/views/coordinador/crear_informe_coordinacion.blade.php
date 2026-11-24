@extends('layouts.app')

@section('title', isset($informe) ? 'Edición de Informe de Coordinación' : 'Creación de Informe de Coordinación')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/coordinador/informe-coordinacion.css') }}">
@endpush

@section('content')
            <div class="wizard-container">
                <!-- Botón de Ayuda Top-Right -->
                <a href="{{ route('coordinador.dashboard') }}" class="help-btn" title="Volver al Panel">✕</a>

                <h2 class="wizard-title">
                    {{ isset($informe) ? 'Edición de Informe de Coordinación' : 'Creación de Informe de Coordinación' }}
                </h2>

                @if (isset($isLocked) && $isLocked)
                    <div
                        style="background-color: #fee2e2; border: 1.5px solid #ef4444; color: #991b1b; padding: 14px 20px; border-radius: 12px; margin-bottom: 24px; font-weight: 600; font-size: 13.5px; display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 20px;">🔒</span>
                        <span>Este informe se encuentra <b>bloqueado para edición</b> porque ha vencido el plazo máximo de 3
                            días desde su envío original.</span>
                    </div>
                @endif

                <form id="formInformeCoordinacion" onsubmit="event.preventDefault();">

                    <!-- ========================================== -->
                    <!-- DATOS GENERALES DEL INFORME -->
                    <!-- ========================================== -->
                    <div class="form-card-panel">
                        <div class="panel-header-title">
                            <div class="panel-title-text">
                                <span class="badge-num">📋</span>
                                <span>Datos Generales de la Coordinación</span>
                            </div>
                        </div>

                        <!-- Fila 1: Área, Coordinador, Periodo Semestral -->
                        <div class="form-row">
                            <div class="form-col">
                                <label class="form-label">*Área a Cargo:</label>
                                <input type="text" class="form-input input-readonly" value="{{ $area }}" readonly>
                            </div>

                            <div class="form-col">
                                <label class="form-label">*Coordinador(a):</label>
                                <input type="text" class="form-input input-readonly" value="{{ $user->nombre }}"
                                    readonly>
                            </div>

                            <div class="form-col">
                                <label class="form-label" for="selectPeriodo">*Periodo Semestral:</label>
                                <div class="select-container">
                                    @php
                                        $currentPeriodo = isset($informe) ? $informe->periodo : ($periodoDefecto ?? ('Segundo Semestre ' . date('Y')));
                                    @endphp
                                    <select id="selectPeriodo" class="form-select" required>
                                        <option value="Primer Semestre {{ date('Y') }}" {{ str_contains($currentPeriodo, 'Primer Semestre') ? 'selected' : '' }}>Primer Semestre {{ date('Y') }}
                                        </option>
                                        <option value="Segundo Semestre {{ date('Y') }}" {{ str_contains($currentPeriodo, 'Segundo Semestre') ? 'selected' : '' }}>
                                            Segundo Semestre {{ date('Y') }}</option>
                                        <option value="Vacaciones Junio {{ date('Y') }}" {{ str_contains($currentPeriodo, 'Vacaciones Junio') ? 'selected' : '' }}>
                                            Vacaciones Junio {{ date('Y') }}</option>
                                        <option value="Vacaciones Diciembre {{ date('Y') }}" {{ str_contains($currentPeriodo, 'Vacaciones Diciembre') ? 'selected' : '' }}>
                                            Vacaciones Diciembre {{ date('Y') }}</option>
                                    </select>
                                    <svg class="select-custom-arrow" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Fila 2: Mes, Año -->
                        <div class="form-row">
                            <div class="form-col">
                                <label class="form-label" for="selectMes">*Mes del Informe:</label>
                                <div class="select-container">
                                    @php
                                        $currentMes = isset($informe) ? $informe->mes : ($mesDefecto ?? \Carbon\Carbon::now()->locale('es')->translatedFormat('F'));
                                        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                                    @endphp
                                    <select id="selectMes" class="form-select" required>
                                        @foreach ($meses as $m)
                                            <option value="{{ $m }}" {{ strcasecmp($currentMes, $m) == 0 ? 'selected' : '' }}>
                                                {{ $m }}</option>
                                        @endforeach
                                    </select>
                                    <svg class="select-custom-arrow" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>

                            <div class="form-col form-col-small">
                                <label class="form-label" for="inputAnio">*Año:</label>
                                <input type="number" id="inputAnio" class="form-input"
                                    value="{{ isset($informe) ? $informe->anio : date('Y') }}" required>
                            </div>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- 1. COMPETENCIA DEL ÁREA -->
                    <!-- ========================================== -->
                    <div class="form-card-panel">
                        <div class="panel-header-title">
                            <div class="panel-title-text">
                                <span class="badge-num">1</span>
                                <span>Competencia del Área</span>
                            </div>
                        </div>

                        <p class="form-hint-desc" style="margin-top: 4px;"><strong>Programas de las
                                asignaturas:</strong> (Solo en el primer mes del semestre. Se cargan automáticamente las
                            asignaturas registradas del área)</p>

                        <div class="table-responsive">
                            <table class="custom-report-table" id="tablaProgramas">
                                <thead>
                                    <tr>
                                        <th style="width: 40%;">Asignatura</th>
                                        <th style="width: 60%;">Enlace a programa (URL Drive / Cloud)</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyProgramas">
                                    @php
                                        $listaProgramas = [];
                                        if (isset($programasCompletos) && count($programasCompletos) > 0) {
                                            $listaProgramas = $programasCompletos;
                                        } elseif (isset($programasIniciales) && count($programasIniciales) > 0) {
                                            $listaProgramas = $programasIniciales;
                                        } elseif (isset($informe) && $informe->programas && $informe->programas->count() > 0) {
                                            $listaProgramas = $informe->programas->map(function ($p) {
                                                return [
                                                    'asignatura' => $p->asignatura,
                                                    'enlace_programa' => $p->enlace_programa
                                                ];
                                            });
                                        } elseif (isset($asignaturasUnicas) && count($asignaturasUnicas) > 0) {
                                            $listaProgramas = $asignaturasUnicas->map(function ($asig) {
                                                return [
                                                    'asignatura' => $asig,
                                                    'enlace_programa' => ''
                                                ];
                                            });
                                        }
                                    @endphp

                                    @forelse ($listaProgramas as $p)
                                        @php
                                            $asigName = is_array($p) ? ($p['asignatura'] ?? '') : (is_object($p) ? ($p->asignatura ?? '') : $p);
                                            $asigLink = is_array($p) ? ($p['enlace_programa'] ?? '') : (is_object($p) ? ($p->enlace_programa ?? '') : '');
                                        @endphp
                                        <tr class="fila-programa" data-asig-name="{{ $asigName }}">
                                            <td>
                                                <input type="text" class="table-input prog-asignatura"
                                                    value="{{ $asigName }}" readonly
                                                    style="background-color: #f8fafc; font-weight: 600; color: #1e293b; cursor: default;">
                                            </td>
                                            <td>
                                                <input type="url" class="table-input prog-enlace" value="{{ $asigLink }}"
                                                    placeholder="https://drive.google.com/...">
                                            </td>
                                        </tr>
                                    @empty
                                        <tr id="rowEmptyProgramas">
                                            <td colspan="2" style="text-align: center; color: #94a3b8; padding: 18px;">
                                                No se encontraron asignaturas registradas para esta área en el sistema.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- 2. INFORMACIÓN GENERAL DE ASIGNATURAS -->
                    <!-- ========================================== -->
                    <div class="form-card-panel">
                        <div class="panel-header-title">
                            <div class="panel-title-text">
                                <span class="badge-num">2</span>
                                <span>Información General de Asignaturas Impartidas en el Área</span>
                            </div>
                        </div>

                        <p class="form-hint-desc">Se coloca una fila por cada sección de asignatura impartida en el
                            área.</p>

                        <div class="table-responsive">
                            <table class="custom-report-table" id="tablaAsignaturas">
                                <thead>
                                    <tr>
                                        <th style="width: 125px; text-align: center;">Informe Docente</th>
                                        <th>Docente a cargo</th>
                                        <th>Asignatura</th>
                                        <th style="width: 65px; text-align: center;">Sección</th>
                                        <th style="width: 95px; text-align: center;">Presentó informe</th>
                                        <th style="width: 95px; text-align: center;">Sala reuniones</th>
                                        <th style="width: 95px; text-align: center;">Enlace Classroom</th>
                                        <th style="width: 95px; text-align: center;">Enlaces evaluación</th>
                                        <th style="width: 95px; text-align: center;">Evidencias grales</th>
                                        <th style="min-width: 170px;">Observaciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyAsignaturas">
                                    @php
                                        $itemsAsignaturas = (isset($informe) && $informe->asignaturas && $informe->asignaturas->isNotEmpty()) ? $informe->asignaturas : ($filasAsignaturas ?? collect());
                                    @endphp
                                    @foreach ($itemsAsignaturas as $index => $asig)
                                        @php
                                            $cursoId = is_array($asig) ? ($asig['curso_id'] ?? null) : $asig->curso_id;
                                            $docenteNombre = is_array($asig) ? ($asig['docente_nombre'] ?? '') : $asig->docente_nombre;
                                            $nombreAsignatura = is_array($asig) ? ($asig['asignatura'] ?? '') : $asig->asignatura;
                                            $seccion = is_array($asig) ? ($asig['seccion'] ?? '') : $asig->seccion;
                                            $presento = is_array($asig) ? ($asig['presento_informe'] ?? false) : $asig->presento_informe;
                                            $sala = is_array($asig) ? ($asig['tiene_sala_reuniones'] ?? false) : $asig->tiene_sala_reuniones;
                                            $virtual = is_array($asig) ? ($asig['funciona_enlace_virtual'] ?? false) : $asig->funciona_enlace_virtual;
                                            $eval = is_array($asig) ? ($asig['funciona_enlace_evaluacion'] ?? false) : $asig->funciona_enlace_evaluacion;
                                            $evid = is_array($asig) ? ($asig['evidencias_generales'] ?? false) : $asig->evidencias_generales;
                                            $obs = is_array($asig) ? ($asig['observaciones'] ?? '') : $asig->observaciones;

                                            // Buscar informe docente correspondiente
                                            $docenteInformeId = is_array($asig) ? ($asig['docente_informe_id'] ?? null) : null;
                                            if (!$docenteInformeId && isset($docenteInformes)) {
                                                $docenteInf = $docenteInformes->first(function ($item) use ($cursoId, $docenteNombre) {
                                                    return (int)$item->curso_id === (int)$cursoId
                                                        && (!$docenteNombre || mb_strtolower(trim($item->usuario?->nombre ?? '')) === mb_strtolower(trim($docenteNombre)));
                                                });
                                                $docenteInformeId = $docenteInf ? $docenteInf->id : null;
                                            }
                                            $docenteId = is_array($asig) ? ($asig['docente_id'] ?? '') : '';
                                        @endphp
                                        <tr class="fila-asig-general" data-curso-id="{{ $cursoId }}" data-docente-id="{{ $docenteId }}"
                                            data-row-index="{{ $index }}" data-asig-nombre="{{ $nombreAsignatura }}"
                                            data-asig-seccion="{{ $seccion }}" data-asig-docente="{{ $docenteNombre }}">
                                            <td style="text-align: center;">
                                                <div class="col-docente-report-action" data-curso-id="{{ $cursoId }}">
                                                    @if ($docenteInformeId)
                                                        <button type="button" class="btn-ver-docente"
                                                            onclick="abrirModalInformeDocente({{ $docenteInformeId }})"
                                                            title="Visualizar informe entregado por el docente">
                                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2.2">
                                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                                <circle cx="12" cy="12" r="3"></circle>
                                                            </svg>
                                                            <span>Ver Informe</span>
                                                        </button>
                                                    @else
                                                        <span class="badge-sin-informe">Sin informe</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" class="table-input asig-docente"
                                                    value="{{ $docenteNombre }}" readonly
                                                    style="background-color: #f8fafc; font-weight: 600; color: #1e293b; cursor: default;">
                                            </td>
                                            <td>
                                                <input type="text" class="table-input asig-nombre"
                                                    value="{{ $nombreAsignatura }}" readonly
                                                    style="background-color: #f8fafc; font-weight: 600; color: #1e293b; cursor: default;">
                                            </td>
                                            <td style="text-align: center;">
                                                <input type="text" class="table-input asig-seccion" value="{{ $seccion }}"
                                                    readonly
                                                    style="text-align: center; width: 55px; background-color: #f8fafc; font-weight: 600; color: #1e293b; cursor: default;">
                                            </td>
                                            <td style="text-align: center;">
                                                <select
                                                    class="table-select asig-presento {{ $presento ? 'val-si' : 'val-no' }}"
                                                    onchange="actualizarColorSelect(this)">
                                                    <option value="1" {{ $presento ? 'selected' : '' }}>Sí</option>
                                                    <option value="0" {{ !$presento ? 'selected' : '' }}>No</option>
                                                </select>
                                            </td>
                                            <td style="text-align: center;">
                                                <select class="table-select asig-sala {{ $sala ? 'val-si' : 'val-no' }}"
                                                    onchange="actualizarColorSelect(this)">
                                                    <option value="1" {{ $sala ? 'selected' : '' }}>Sí</option>
                                                    <option value="0" {{ !$sala ? 'selected' : '' }}>No</option>
                                                </select>
                                            </td>
                                            <td style="text-align: center;">
                                                <select
                                                    class="table-select asig-virtual {{ $virtual ? 'val-si' : 'val-no' }}"
                                                    onchange="actualizarColorSelect(this)">
                                                    <option value="1" {{ $virtual ? 'selected' : '' }}>Sí</option>
                                                    <option value="0" {{ !$virtual ? 'selected' : '' }}>No</option>
                                                </select>
                                            </td>
                                            <td style="text-align: center;">
                                                <select class="table-select asig-eval {{ $eval ? 'val-si' : 'val-no' }}"
                                                    onchange="actualizarColorSelect(this)">
                                                    <option value="1" {{ $eval ? 'selected' : '' }}>Sí</option>
                                                    <option value="0" {{ !$eval ? 'selected' : '' }}>No</option>
                                                </select>
                                            </td>
                                            <td style="text-align: center;">
                                                <select class="table-select asig-evid {{ $evid ? 'val-si' : 'val-no' }}"
                                                    onchange="actualizarColorSelect(this)">
                                                    <option value="1" {{ $evid ? 'selected' : '' }}>Sí</option>
                                                    <option value="0" {{ !$evid ? 'selected' : '' }}>No</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="hidden" class="asig-obs" value="{{ $obs }}">
                                                <button type="button"
                                                    class="btn-obs-trigger {{ !empty($obs) ? 'filled' : 'empty' }}"
                                                    onclick="abrirModalObservacion(this, 'asig')">
                                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2.2">
                                                        <path
                                                            d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7">
                                                        </path>
                                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z">
                                                        </path>
                                                    </svg>
                                                    <span
                                                        class="obs-btn-text">{{ !empty($obs) ? Str::limit($obs, 18) : '+ Observación' }}</span>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Herramientas virtuales -->
                        <div class="form-row" style="margin-top: 22px; margin-bottom: 0;">
                            <div class="form-col form-col-full">
                                <label class="form-label" for="inputHerramientasVirtuales">Herramientas virtuales
                                    utilizadas para la docencia en línea:</label>
                                <textarea id="inputHerramientasVirtuales" class="form-textarea"
                                    placeholder="Ej. Google Classroom, Google Meet, Zoom, Campus Virtual FARUSAC, etc.">{{ isset($informe) && !empty($informe->herramientas_virtuales) ? $informe->herramientas_virtuales : 'Google Classroom, Google Meet, Zoom, Campus Virtual FARUSAC' }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- 3. SÍNTESIS DE LOS INFORMES PRESENTADOS -->
                    <!-- ========================================== -->
                    <div class="form-card-panel">
                        <div class="panel-header-title">
                            <div class="panel-title-text">
                                <span class="badge-num">3</span>
                                <span>Síntesis de los Informes Presentados por los Docentes</span>
                            </div>
                        </div>

                        <!-- 3.1 Avance del curso -->
                        <div style="margin-bottom: 30px;">
                            <h3
                                style="font-family: 'Outfit', sans-serif; font-size: 15px; font-weight: 700; color: var(--color-azul); margin-bottom: 6px;">
                                3.1. Avance del curso con relación a la programación mensual
                            </h3>
                            <p class="form-hint-desc">Se coloca una fila por cada sección de asignatura. Si existe
                                alguna complicación en cumplir el porcentaje de avance al impartir los contenidos
                                programados para el mes, indicar la razón en la columna de “Observaciones”.</p>

                            <div class="table-responsive">
                                <table class="custom-report-table" id="tablaAvances">
                                    <thead>
                                        <tr>
                                            <th>Docente a cargo</th>
                                            <th>Asignatura</th>
                                            <th style="width: 65px; text-align: center;">Sección</th>
                                            <th style="width: 110px; text-align: center;">% de Avance</th>
                                            <th style="min-width: 170px;">Observaciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyAvances">
                                        @php
                                            $itemsAvances = (isset($informe) && $informe->avances && $informe->avances->isNotEmpty()) ? $informe->avances : ($filasAsignaturas ?? collect());
                                        @endphp
                                        @foreach ($itemsAvances as $av)
                                            @php
                                                $cursoId = is_array($av) ? ($av['curso_id'] ?? null) : $av->curso_id;
                                                $docenteNombre = is_array($av) ? ($av['docente_nombre'] ?? '') : $av->docente_nombre;
                                                $nombreAsignatura = is_array($av) ? ($av['asignatura'] ?? '') : $av->asignatura;
                                                $seccion = is_array($av) ? ($av['seccion'] ?? '') : $av->seccion;
                                                $porcentaje = is_array($av) ? ($av['porcentaje_avance'] ?? 100) : $av->porcentaje_avance;
                                                $obs = is_array($av) ? ($av['observaciones'] ?? '') : $av->observaciones;
                                                $docenteId = is_array($av) ? ($av['docente_id'] ?? '') : '';
                                            @endphp
                                            <tr class="fila-avance" data-curso-id="{{ $cursoId }}" data-docente-id="{{ $docenteId }}"
                                                data-asig-nombre="{{ $nombreAsignatura }}"
                                                data-asig-seccion="{{ $seccion }}" data-asig-docente="{{ $docenteNombre }}">
                                                <td>
                                                    <input type="text" class="table-input avance-docente"
                                                        value="{{ $docenteNombre }}" readonly
                                                        style="background-color: #f8fafc; font-weight: 600; color: #1e293b; cursor: default;">
                                                </td>
                                                <td>
                                                    <input type="text" class="table-input avance-asignatura"
                                                        value="{{ $nombreAsignatura }}" readonly
                                                        style="background-color: #f8fafc; font-weight: 600; color: #1e293b; cursor: default;">
                                                </td>
                                                <td style="text-align: center;">
                                                    <input type="text" class="table-input avance-seccion"
                                                        value="{{ $seccion }}" readonly
                                                        style="text-align: center; width: 55px; background-color: #f8fafc; font-weight: 600; color: #1e293b; cursor: default;">
                                                </td>
                                                <td style="text-align: center;">
                                                    <div style="display: inline-flex; align-items: center; gap: 4px;">
                                                        <input type="number" min="0" max="100"
                                                            class="table-input avance-porcentaje" value="{{ $porcentaje }}"
                                                            style="text-align: center; width: 70px;">
                                                        <span
                                                            style="font-weight: 700; color: var(--color-texto-secundario);">%</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="hidden" class="avance-obs" value="{{ $obs }}">
                                                    <button type="button"
                                                        class="btn-obs-trigger {{ !empty($obs) ? 'filled' : 'empty' }}"
                                                        onclick="abrirModalObservacion(this, 'avance')">
                                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2.2">
                                                            <path
                                                                d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7">
                                                            </path>
                                                            <path
                                                                d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z">
                                                            </path>
                                                        </svg>
                                                        <span
                                                            class="obs-btn-text">{{ !empty($obs) ? Str::limit($obs, 18) : '+ Observación' }}</span>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- 3.2 Reporte de estudiantes con problemas -->
                        <div>
                            <div
                                style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; flex-wrap: wrap; gap: 10px;">
                                <h3
                                    style="font-family: 'Outfit', sans-serif; font-size: 15px; font-weight: 700; color: var(--color-azul);">
                                    3.2. Reporte de estudiantes con problemas para recibir el curso en línea
                                </h3>
                                <button type="button" class="btn-add-row" onclick="addEstudianteRow()">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2.5">
                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                    <span>Agregar Fila de Estudiantes</span>
                                </button>
                            </div>
                            <p class="form-hint-desc">Reporte de alumnos con inconvenientes técnicos, de conectividad o
                                académicos reportados por los docentes.</p>

                            <div class="table-responsive">
                                <table class="custom-report-table" id="tablaEstudiantes">
                                    <thead>
                                        <tr>
                                            <th style="width: 30%;">Asignatura</th>
                                            <th style="width: 12%; text-align: center;">Sección</th>
                                            <th style="width: 18%; text-align: center;">Cantidad con inconvenientes</th>
                                            <th style="width: 35%;">Carné de los estudiantes</th>
                                            <th style="width: 5%; text-align: center;">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyEstudiantes">
                                        @if (isset($informe) && $informe->estudiantes->count() > 0)
                                            @foreach ($informe->estudiantes as $est)
                                                <tr>
                                                    <td>
                                                        <input type="text" class="table-input est-asignatura"
                                                            value="{{ $est->asignatura }}"
                                                            placeholder="Nombre de la Asignatura">
                                                    </td>
                                                    <td style="text-align: center;">
                                                        <input type="text" class="table-input est-seccion"
                                                            value="{{ $est->seccion }}" style="text-align: center;"
                                                            placeholder="Sección">
                                                    </td>
                                                    <td style="text-align: center;">
                                                        <input type="number" min="0" class="table-input est-cantidad"
                                                            value="{{ $est->cantidad_estudiantes }}" style="text-align: center;"
                                                            placeholder="0">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="table-input est-carne"
                                                            value="{{ $est->carne_estudiantes }}"
                                                            placeholder="Ej. 202100123, 202204567">
                                                    </td>
                                                    <td style="text-align: center;">
                                                        <button type="button" class="btn-delete-row"
                                                            onclick="deleteTableRow(this)" title="Eliminar fila">✕</button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td>
                                                    <input type="text" class="table-input est-asignatura"
                                                        placeholder="Nombre de la Asignatura">
                                                </td>
                                                <td style="text-align: center;">
                                                    <input type="text" class="table-input est-seccion"
                                                        style="text-align: center;" placeholder="Sección">
                                                </td>
                                                <td style="text-align: center;">
                                                    <input type="number" min="0" class="table-input est-cantidad" value="0"
                                                        style="text-align: center;" placeholder="0">
                                                </td>
                                                <td>
                                                    <input type="text" class="table-input est-carne"
                                                        placeholder="Ej. 202100123, 202204567">
                                                </td>
                                                <td style="text-align: center;">
                                                    <button type="button" class="btn-delete-row"
                                                        onclick="deleteTableRow(this)" title="Eliminar fila">✕</button>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- 4. INFORME DE ACTIVIDADES DE COORDINACIÓN DE ÁREA Y OBSERVACIONES GENERALES -->
                    <!-- ========================================== -->
                    <div class="form-card-panel">
                        <div class="panel-header-title">
                            <div class="panel-title-text">
                                <span class="badge-num">4</span>
                                <span>Informe de Actividades de Coordinación de Área y Observaciones Generales</span>
                            </div>
                        </div>
                        <p class="form-hint-desc">Adjunte el enlace al documento o carpeta en la nube (Google Drive, OneDrive, etc.) con el informe de actividades de coordinación y observaciones generales.</p>

                        <div class="form-row" style="margin-top: 14px; margin-bottom: 0;">
                            <div class="form-col form-col-full">
                                <label class="form-label" for="inputEnlaceActividadesCoordinacion">Enlace de actividades de coordinación y observaciones (Drive / Nube):</label>
                                <input type="url" id="inputEnlaceActividadesCoordinacion" class="form-input" placeholder="https://drive.google.com/..." value="{{ isset($informe) ? $informe->enlace_actividades_coordinacion : '' }}">
                            </div>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- 5. INFORME DE AUXILIARES (DE HABERLOS) -->
                    <!-- ========================================== -->
                    <div class="form-card-panel">
                        <div class="panel-header-title">
                            <div class="panel-title-text">
                                <span class="badge-num">5</span>
                                <span>Informe de Auxiliares (De haberlos)</span>
                            </div>
                        </div>
                        <p class="form-hint-desc">Adjunte el enlace al informe mensual o carpeta con las constancias de los auxiliares de cátedra asignados al área (de haberlos).</p>

                        <div class="form-row" style="margin-top: 14px; margin-bottom: 0;">
                            <div class="form-col form-col-full">
                                <label class="form-label" for="inputEnlaceInformeAuxiliares">Enlace de informe de auxiliares (Drive / Nube):</label>
                                <input type="url" id="inputEnlaceInformeAuxiliares" class="form-input" placeholder="https://drive.google.com/..." value="{{ isset($informe) ? $informe->enlace_informe_auxiliares : '' }}">
                            </div>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- 6. INFORME DE DOCENTES CON PERMISO LABORAL, SUSPENSIÓN Y/O INASISTENCIA -->
                    <!-- ========================================== -->
                    <div class="form-card-panel">
                        <div class="panel-header-title">
                            <div class="panel-title-text">
                                <span class="badge-num">6</span>
                                <span>Informe de Docentes con Permiso Laboral, Suspensión y/o Inasistencia</span>
                            </div>
                        </div>
                        <p class="form-hint-desc">Adjunte el enlace al documento o carpeta con las justificaciones, permisos laborales, suspensiones del IGSS o reportes de inasistencia de los docentes del área.</p>

                        <div class="form-row" style="margin-top: 14px; margin-bottom: 0;">
                            <div class="form-col form-col-full">
                                <label class="form-label" for="inputEnlaceDocentesPermisos">Enlace de docentes con permiso, suspensión o inasistencia (Drive / Nube):</label>
                                <input type="url" id="inputEnlaceDocentesPermisos" class="form-input" placeholder="https://drive.google.com/..." value="{{ isset($informe) ? $informe->enlace_docentes_permisos : '' }}">
                            </div>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- BARRA DE ACCIONES INFERIOR -->
                    <!-- ========================================== -->
                    <div class="bottom-actions-container">
                        <a href="{{ route('coordinador.dashboard') }}" class="btn-back-action">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5">
                                <line x1="19" y1="12" x2="5" y2="12"></line>
                                <polyline points="12 19 5 12 12 5"></polyline>
                            </svg>
                            <span>Volver al Panel</span>
                        </a>

                        <div class="actions-right-group">
                            <button type="button" class="btn-draft-action"
                                onclick="submitInformeCoordinacion('borrador')">
                                Guardar Borrador
                            </button>
                            <button type="button" class="btn-submit-action"
                                onclick="submitInformeCoordinacion('enviado')">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.5">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                <span>{{ isset($informe) ? 'Actualizar y Enviar' : 'Enviar Informe Oficial' }}</span>
                            </button>
                        </div>
                    </div>

                </form>
            </div>



    <div class="custom-modal-overlay" id="modalVerInformeDocente"
        onclick="cerrarModalFuera(event, 'modalVerInformeDocente')">
        <div class="custom-modal-container"
            style="max-width: 980px; width: 95vw; height: 90vh; display: flex; flex-direction: column;">
            <div class="modal-accent-bar"></div>
            <div class="modal-header-box" style="padding: 14px 24px; flex-shrink: 0;">
                <div>
                    <div class="modal-title-main">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                        <span>Informe Mensual del Docente (Formato Institucional Oficial)</span>
                    </div>
                    <div style="font-size: 12px; color: #64748b; margin-top: 2px;" id="modalDocenteSubtitulo">
                        Visualización en formato de documento oficial FARUSAC
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <button type="button" class="btn-submit-action" onclick="imprimirIframeDocente()"
                        style="padding: 7px 16px; font-size: 12px; display: inline-flex; align-items: center; gap: 6px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <polyline points="6 9 6 2 18 2 18 9"></polyline>
                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                            <rect x="6" y="14" width="12" height="8"></rect>
                        </svg>
                        <span>Imprimir / PDF</span>
                    </button>
                    <button type="button" class="modal-close-btn" onclick="cerrarModal('modalVerInformeDocente')"
                        title="Cerrar (Esc)">✕</button>
                </div>
            </div>

            <div style="flex: 1; position: relative; overflow: hidden; background: #f8fafc; padding: 0;">
                <div id="modalDocenteLoader"
                    style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: #ffffff; display: flex; flex-direction: column; align-items: center; justify-content: center; z-index: 10;">
                    <div class="spinner-border"
                        style="display: inline-block; width: 32px; height: 32px; border: 3px solid #cbd5e1; border-top-color: var(--color-azul); border-radius: 50%; animation: spin 0.8s linear infinite;">
                    </div>
                    <p style="margin-top: 12px; font-weight: 600; color: #475569; font-size: 13.5px;">Cargando documento
                        oficial del docente...</p>
                </div>
                <iframe id="iframeDocenteInforme" src="about:blank"
                    style="width: 100%; height: 100%; border: none; display: block;"
                    onload="const l = document.getElementById('modalDocenteLoader'); if(l) l.style.display='none';"></iframe>
            </div>

            <div class="modal-footer-box" style="padding: 12px 24px; flex-shrink: 0;">
                <button type="button" class="btn-draft-action" onclick="cerrarModal('modalVerInformeDocente')">Cerrar
                    Visualización</button>
            </div>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL 2: VENTANA EMERGENTE DE OBSERVACIONES -->
    <!-- ======================================================== -->
    <div class="custom-modal-overlay" id="modalObservaciones" onclick="cerrarModalFuera(event, 'modalObservaciones')">
        <div class="custom-modal-container" style="max-width: 580px;">
            <div class="modal-accent-bar"></div>
            <div class="modal-header-box">
                <div>
                    <div class="modal-title-main">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                        <span>Observaciones de la Asignatura</span>
                    </div>
                    <div style="font-size: 12px; color: #64748b; margin-top: 2px;" id="modalObsInfoCurso">
                        Curso — Sección — Docente
                    </div>
                </div>
                <button type="button" class="modal-close-btn" onclick="cerrarModal('modalObservaciones')"
                    title="Cerrar">✕</button>
            </div>

            <div class="modal-body-scroll" style="padding: 20px 24px;">
                <label for="modalObsTextarea"
                    style="font-size: 12.5px; font-weight: 700; color: #334155; display: block; margin-bottom: 8px;">
                    Detalle de Observaciones para el Informe:
                </label>
                <textarea id="modalObsTextarea" class="form-textarea"
                    style="min-height: 170px; font-size: 13.5px; line-height: 1.6; border-radius: 12px;"
                    placeholder="Ingrese aquí las observaciones detalladas sobre la entrega del informe, salas virtuales, avances, o situaciones particulares de esta asignatura..."></textarea>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 8px;">
                    <span style="font-size: 11.5px; color: #94a3b8;" id="modalObsCharCount">0 caracteres</span>
                    <button type="button"
                        style="background: none; border: none; font-size: 11.5px; color: #dc2626; cursor: pointer;"
                        onclick="document.getElementById('modalObsTextarea').value=''; actualizarContadorObs();">Limpiar
                        texto</button>
                </div>
            </div>

            <div class="modal-footer-box">
                <button type="button" class="btn-draft-action"
                    onclick="cerrarModal('modalObservaciones')">Cancelar</button>
                <button type="button" class="btn-submit-action" onclick="guardarObservacionModal()"
                    style="padding: 9px 22px;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <span>Guardar Observación</span>
                </button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    window.informeCoordConfig = {
        informeId: "{{ isset($informe) ? $informe->id : '' }}"
    };
</script>
<script src="{{ asset('js/crear-informe-coordinacion.js') }}"></script>
@endpush
