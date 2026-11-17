@extends('layouts.app')

@section('title', 'Carga de Datos - Panel Administrador')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endpush

@section('content')
<!-- Notificación de Éxito / Feedback -->
@if (session('exito'))
    <div class="alert-success-banner">
        <svg style="width: 22px; height: 22px; margin-right: 12px; color: #48bb78; flex-shrink: 0;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
            <circle cx="12" cy="12" r="10"></circle>
            <path fill="#fff" d="M10 15.172l-3.293-3.293-1.414 1.414L10 18l8-8-1.414-1.414z"></path>
        </svg>
        <span style="font-size: 13.5px; font-weight: 500; color: #276749;">{{ session('exito') }}</span>
    </div>
@endif

@if ($errors->any())
    <div class="alert-error-banner">
        <svg style="width: 22px; height: 22px; margin-right: 12px; color: #e53e3e; flex-shrink: 0;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
            <circle cx="12" cy="12" r="10"></circle>
            <path fill="#fff" d="M13.414 12l3.293-3.293-1.414-1.414L12 10.586 8.707 7.293 7.293 8.707 10.586 12l-3.293 3.293 1.414 1.414L12 13.414l3.293 3.293 1.414-1.414z"></path>
        </svg>
        <span style="font-size: 13.5px; font-weight: 500; color: #9b2c2c;">{{ $errors->first() }}</span>
    </div>
@endif

<!-- 1. VISTA DE SELECCIÓN INICIAL (DASHBOARD) -->
<div class="selection-grid-container" id="loadSelectionView">
    <h2 class="section-main-title">CARGA MASIVA DE DATOS</h2>

    <div class="cards-selection-wrapper">
        <!-- Tarjeta Principal: Carga Académica Integral -->
        <div class="selection-card selection-card-academica" onclick="showUploadView('academica')">
            <svg class="selection-card-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            <h3 style="font-family: 'Outfit', sans-serif; font-size: 19px; font-weight: 700; color: var(--color-texto-oscuro); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.04em;">
                Carga Académica Completa
            </h3>
            <p class="card-academica-desc">
                Formato oficial de Carga Académica Detallada FARUSAC. Carga cursos y docentes, y realiza la <b>asignación directa</b> en una sola operación. Gestiona la coexistencia de semestres automáticamente.
            </p>
            <button class="selection-btn">CARGAR CARGA ACADÉMICA</button>
        </div>

        <!-- Tarjetas Secundarias -->
        <div class="secondary-cards-grid">
            <div class="selection-card selection-card-usuarios" onclick="showUploadView('usuarios')">
                <svg class="selection-card-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <button class="selection-btn">CARGAR SOLO USUARIOS</button>
            </div>

            <div class="selection-card selection-card-cursos" onclick="showUploadView('cursos')">
                <svg class="selection-card-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <button class="selection-btn">CARGAR SOLO CURSOS</button>
            </div>
        </div>
    </div>
</div>

<!-- 2. PANELES DE FORMULARIO -->
<!-- Formulario: Carga Académica Completa -->
<div class="upload-view-container" id="uploadAcademicaView">
    <div class="back-link-wrapper">
        <button type="button" class="back-link-btn" onclick="showSelectionView()">
            <svg class="back-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Volver a la selección
        </button>
    </div>

    <div class="form-card">
        <div class="card-title-group">
            <h2 class="card-main-title">Carga Masiva de datos</h2>
            <div class="card-main-subtitle" style="color: var(--color-terracota);">CARGA ACADÉMICA (DOCENTES Y CURSOS)</div>
        </div>

        <div class="card-actions-wrapper">
            <a href="{{ route('admin.cursos') }}" class="card-action-btn academica-inst-btn">Gestión de cursos</a>
            <a href="{{ route('admin.usuarios') }}" class="card-action-btn academica-inst-btn">Gestión de usuarios</a>
            <button type="button" class="card-action-btn academica-inst-btn" onclick="downloadExample('academica')">Descargar ejemplo CSV</button>
        </div>

        <!-- Filtros Opcionales de Periodo -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px; text-align: left;">
            <div>
                <label style="display:block; font-size: 11px; font-weight: 700; color: var(--color-texto-claro); text-transform: uppercase; margin-bottom: 5px;">Carrera</label>
                <select id="academica_carrera" style="width: 100%; padding: 10px 12px; border: 1.5px solid #cbd5e0; border-radius: 8px; font-size: 13px; background-color: #fff; color: var(--color-texto-oscuro); outline: none;">
                    <option value="detectar" selected>Auto-detectar del archivo (Arquitectura)</option>
                    <option value="Arquitectura">Arquitectura</option>
                    <option value="Diseño Gráfico">Diseño Gráfico</option>
                </select>
            </div>
            <div>
                <label style="display:block; font-size: 11px; font-weight: 700; color: var(--color-texto-claro); text-transform: uppercase; margin-bottom: 5px;">Semestre / Periodo</label>
                <select id="academica_semestre" style="width: 100%; padding: 10px 12px; border: 1.5px solid #cbd5e0; border-radius: 8px; font-size: 13px; background-color: #fff; color: var(--color-texto-oscuro); outline: none;">
                    <option value="detectar" selected>Auto-detectar del archivo</option>
                    <option value="Primer Semestre">Primer Semestre (Enero - Mayo)</option>
                    <option value="Vacaciones Junio">Vacaciones Junio (Vacaciones 1er Semestre)</option>
                    <option value="Segundo Semestre">Segundo Semestre (Julio - Noviembre)</option>
                    <option value="Vacaciones Diciembre">Vacaciones Diciembre (Vacaciones 2do Semestre)</option>
                </select>
            </div>
            <div>
                <label style="display:block; font-size: 11px; font-weight: 700; color: var(--color-texto-claro); text-transform: uppercase; margin-bottom: 5px;">Año</label>
                <select id="academica_anio" style="width: 100%; padding: 10px 12px; border: 1.5px solid #cbd5e0; border-radius: 8px; font-size: 13px; background-color: #fff; color: var(--color-texto-oscuro); outline: none;">
                    <option value="detectar" selected>Auto-detectar del archivo</option>
                    <option value="2026">2026</option>
                    <option value="2027">2027</option>
                    <option value="2025">2025</option>
                </select>
            </div>
        </div>

        <form id="formAcademicaCSV" onsubmit="event.preventDefault();">
            <div class="file-upload-area" id="dropAreaAcademica" onclick="document.getElementById('archivo_csv_academica').click()">
                <svg class="upload-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" style="color: var(--color-terracota);">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <input type="file" name="archivo_csv" id="archivo_csv_academica" class="file-input" accept=".csv" required style="display:none;" onchange="uploadCSVFile('academica')">
                <div class="upload-text" id="uploadTextAcademica">Arrastra el archivo CSV o haz click para buscar el archivo</div>
                <div class="upload-hint">Formato oficial de Carga Académica Detallada FARUSAC (Máx. 10 MB)</div>
                <div class="file-selected-name" id="fileNameAcademica" style="color: var(--color-terracota);"></div>
            </div>

            <div class="csv-instructions academica-inst">
                <h4 class="instructions-title">Instrucciones y Requisitos de Carga</h4>
                <ul class="instructions-list">
                    <li><b>Asignación Directa:</b> El archivo vincula automáticamente a cada docente con sus cursos y secciones en una sola operación.</li>
                    <li><b>Formato oficial:</b> Archivo delimitado por comas con las columnas: <i>Área, Ciclo, Código, Asignatura, Sección, Jornada, Docente, Registro Personal, Correo</i>, etc.</li>
                    <li><b>Regla de Coexistencia de Periodos:</b>
                        <ul style="margin-top: 5px; padding-left: 15px; list-style-type: circle;">
                            <li><i>Primer Semestre</i> (Enero) y <i>Vacaciones de Junio</i> coexisten al ser del mismo ciclo.</li>
                            <li><i>Segundo Semestre</i> (Julio) y <i>Vacaciones de Diciembre</i> coexisten al ser del mismo ciclo.</li>
                            <li><b>Semestres diferentes no pueden coexistir:</b> Al cargar un nuevo semestre, las asignaciones de semestres de otros ciclos se desasignan y actualizan automáticamente.</li>
                        </ul>
                    </li>
                </ul>
            </div>
        </form>

        <!-- Resultados / Reporte -->
        <div class="report-section" id="reportAcademica">
            <div id="coexistenciaBannerAcademica" style="display: none; background-color: rgba(185, 71, 0, 0.06); border-left: 4px solid var(--color-terracota); border-radius: 8px; padding: 14px 18px; margin-bottom: 20px; text-align: left;">
                <div id="coexistenciaBannerTitulo" style="font-family: 'Outfit', sans-serif; font-size: 14px; font-weight: 700; color: var(--color-terracota); margin-bottom: 4px;"></div>
                <div id="coexistenciaBannerTexto" style="font-size: 12.5px; color: var(--color-texto-claro); line-height: 1.4;"></div>
            </div>

            <div class="report-tabs">
                <button type="button" class="report-tab-btn tab-btn-success active" onclick="switchReportTab('academica', 'success')">
                    Cursos asignados exitosamente: <span id="successCountAcademica">0</span>
                </button>
                <button type="button" class="report-tab-btn tab-btn-error" onclick="switchReportTab('academica', 'error')">
                    Observaciones / con error: <span id="errorCountAcademica">0</span>
                </button>
            </div>

            <div id="reportContentAcademica"></div>
        </div>
    </div>
</div>

<!-- Formulario: Usuarios -->
<div class="upload-view-container" id="uploadUsuariosView">
    <div class="back-link-wrapper">
        <button type="button" class="back-link-btn" onclick="showSelectionView()">
            <svg class="back-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Volver a la selección
        </button>
    </div>

    <div class="form-card">
        <div class="card-title-group">
            <h2 class="card-main-title">Carga Masiva de datos</h2>
            <div class="card-main-subtitle" style="color: var(--color-azul);">USUARIOS</div>
        </div>

        <div class="card-actions-wrapper">
            <a href="{{ route('admin.usuarios') }}" class="card-action-btn">Ir a gestión de usuarios</a>
            <button type="button" class="card-action-btn" onclick="downloadExample('usuarios')">Descargar ejemplo</button>
        </div>

        <form id="formUsuariosCSV" onsubmit="event.preventDefault();">
            <div class="file-upload-area" id="dropAreaUsuarios" onclick="document.getElementById('archivo_csv_usuarios').click()">
                <svg class="upload-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
                <input type="file" name="archivo_csv" id="archivo_csv_usuarios" class="file-input" accept=".csv" required style="display:none;" onchange="uploadCSVFile('usuarios')">
                <div class="upload-text" id="uploadTextUsuarios">Arrastra el archivo CSV o hacer click para buscar el archivo</div>
                <div class="upload-hint">Tamaño máximo admitido: 5 MB</div>
                <div class="file-selected-name" id="fileNameUsuarios"></div>
            </div>

            <div class="csv-instructions usuarios-inst">
                <h4 class="instructions-title">Requisitos del Archivo</h4>
                <ul class="instructions-list">
                    <li>El archivo debe estar delimitado por comas (formato CSV estándar).</li>
                    <li>Debe contener los encabezados exactos en la primera fila: <b>Nombre, Correo, Numero, Plaza</b> (donde <i>Numero</i> corresponde al Registro Personal).</li>
                    <li>El correo electrónico debe pertenecer al dominio institucional <b>@farusac.edu.gt</b>.</li>
                    <li>Las plazas válidas son estrictamente: <b>Titular</b>, <b>Titular+Ampliacion</b> o <b>Interino</b>.</li>
                </ul>
            </div>
        </form>

        <!-- Resultados / Reporte Usuarios -->
        <div class="report-section" id="reportUsuarios">
            <div class="report-tabs">
                <button type="button" class="report-tab-btn tab-btn-success active" onclick="switchReportTab('usuarios', 'success')">
                    Datos cargados exitosamente: <span id="successCountUsuarios">0</span>
                </button>
                <button type="button" class="report-tab-btn tab-btn-error" onclick="switchReportTab('usuarios', 'error')">
                    Datos con error: <span id="errorCountUsuarios">0</span>
                </button>
            </div>

            <div id="reportContentUsuarios"></div>
        </div>
    </div>
</div>

<!-- Formulario: Cursos -->
<div class="upload-view-container" id="uploadCursosView">
    <div class="back-link-wrapper">
        <button type="button" class="back-link-btn" onclick="showSelectionView()">
            <svg class="back-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Volver a la selección
        </button>
    </div>

    <div class="form-card">
        <div class="card-title-group">
            <h2 class="card-main-title">Carga Masiva de datos</h2>
            <div class="card-main-subtitle" style="color: var(--color-oro);">CURSOS</div>
        </div>

        <div class="card-actions-wrapper">
            <a href="{{ route('admin.cursos') }}" class="card-action-btn cursos-inst-btn">Ir a gestión de cursos</a>
            <button type="button" class="card-action-btn cursos-inst-btn" onclick="downloadExample('cursos')">Descargar ejemplo</button>
        </div>

        <form id="formCursosCSV" onsubmit="event.preventDefault();">
            <div class="file-upload-area" id="dropAreaCursos" onclick="document.getElementById('archivo_csv_cursos').click()">
                <svg class="upload-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" style="color: var(--color-oro);">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
                <input type="file" name="archivo_csv" id="archivo_csv_cursos" class="file-input" accept=".csv" required style="display:none;" onchange="uploadCSVFile('cursos')">
                <div class="upload-text" id="uploadTextCursos">Arrastra el archivo CSV o hacer click para buscar el archivo</div>
                <div class="upload-hint">Tamaño máximo admitido: 5 MB</div>
                <div class="file-selected-name" id="fileNameCursos"></div>
            </div>

            <div class="csv-instructions cursos-inst">
                <h4 class="instructions-title">Requisitos del Archivo</h4>
                <ul class="instructions-list">
                    <li>El archivo debe estar delimitado por comas (formato CSV estándar).</li>
                    <li>Debe contener los encabezados exactos en la primera fila: <b>Carrera, Area, Curso, Codigo, Seccion, Anio, Semestre</b>.</li>
                    <li>Las carreras válidas son: <b>Arquitectura</b> o <b>Diseño Gráfico</b>.</li>
                    <li>Los semestres válidos son: <b>Primer Semestre</b>, <b>Segundo Semestre</b>, <b>Vacaciones Junio</b> o <b>Vacaciones Diciembre</b>.</li>
                </ul>
            </div>
        </form>

        <!-- Resultados / Reporte Cursos -->
        <div class="report-section" id="reportCursos">
            <div class="report-tabs">
                <button type="button" class="report-tab-btn tab-btn-success active" onclick="switchReportTab('cursos', 'success')">
                    Datos cargados exitosamente: <span id="successCountCursos">0</span>
                </button>
                <button type="button" class="report-tab-btn tab-btn-error" onclick="switchReportTab('cursos', 'error')">
                    Datos con error: <span id="errorCountCursos">0</span>
                </button>
            </div>

            <div id="reportContentCursos"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/admin-carga.js') }}"></script>
@endpush
