<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consolidado de Informes FARUSAC - {{ $mes ?? 'Mensual' }} {{ $anio ?? '' }}</title>

    <!-- Google Fonts: Outfit y Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --color-azul: #002D72;
            --color-oro: #AC8400;
            --color-terracota: #B94700;
            --color-borde: #cbd5e1;
            --color-texto: #1e293b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--color-texto);
            background-color: #f1f5f9;
            padding: 30px 20px 60px 20px;
            font-size: 12.5px;
            line-height: 1.5;
        }

        /* --- BARRA FLOTANTE DE CONTROL (NO IMPRIMIBLE) --- */
        .no-print-bar {
            max-width: 1000px;
            margin: 0 auto 25px auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #ffffff;
            padding: 14px 24px;
            border-radius: 14px;
            border: 1.5px solid #e2e8f0;
            box-shadow: 0 4px 16px rgba(0, 45, 114, 0.06);
            position: sticky;
            top: 15px;
            z-index: 1000;
        }

        .bar-left-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .bar-title {
            font-family: 'Outfit', sans-serif;
            font-size: 16px;
            font-weight: 700;
            color: var(--color-azul);
            text-transform: uppercase;
        }

        .bar-badge {
            background-color: rgba(185, 71, 0, 0.1);
            color: var(--color-terracota);
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }

        .bar-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 20px;
            font-family: 'Outfit', sans-serif;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
            border: none;
        }

        .btn-back {
            background-color: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
        }

        .btn-back:hover {
            background-color: #e2e8f0;
            color: #1e293b;
        }

        .btn-print {
            background-color: var(--color-terracota);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(185, 71, 0, 0.25);
        }

        .btn-print:hover {
            background-color: #9f3c00;
            transform: translateY(-1px);
        }

        /* --- CONTENEDOR DE CADA INFORME INDIVIDUAL --- */
        .report-page-wrapper {
            max-width: 1000px;
            margin: 0 auto 40px auto;
            background-color: #ffffff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border-radius: 12px;
            padding: 40px;
            position: relative;
        }

        /* MASTER TABLE LAYOUT PARA PAGINACIÓN */
        .report-table-layout {
            width: 100%;
            border-collapse: collapse;
        }

        .report-header-wrapper {
            padding-bottom: 12px;
        }

        .report-body-wrapper {
            padding: 10px 0;
            width: 100%;
        }

        .report-footer-spacer {
            height: 48px;
            display: block;
            visibility: hidden;
        }

        /* LOGOS Y CABECERA */
        .header-logos-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            padding-bottom: 10px;
            margin-bottom: 10px;
            border-bottom: 1px solid #e2e8f0;
            flex-wrap: nowrap;
        }

        .logo-item {
            object-fit: contain;
            display: block;
        }

        .logo-usac-farusac {
            height: 46px;
            max-width: 210px;
            width: auto;
        }

        .logo-acreditadora {
            height: 36px;
            max-width: 105px;
            width: auto;
        }

        .doc-title-block {
            text-align: center;
        }

        .doc-institution {
            font-family: 'Outfit', sans-serif;
            font-size: 15px;
            font-weight: 700;
            color: var(--color-azul);
            text-transform: uppercase;
            letter-spacing: 0.03em;
            line-height: 1.2;
        }

        .doc-faculty {
            font-family: 'Outfit', sans-serif;
            font-size: 12.5px;
            font-weight: 600;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            margin-top: 1px;
            line-height: 1.2;
        }

        .doc-report-name {
            font-family: 'Outfit', sans-serif;
            font-size: 16px;
            font-weight: 800;
            color: var(--color-azul);
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-top: 6px;
            line-height: 1.2;
        }

        .doc-header {
            border-bottom: 2px solid var(--color-azul);
            padding-bottom: 10px;
            margin-bottom: 14px;
        }

        /* CAJA DE INFORMACIÓN GENERAL */
        .general-info-box {
            border: 1px solid var(--color-borde);
            border-radius: 6px;
            padding: 10px 16px;
            margin-bottom: 15px;
            background-color: #f8fafc;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .info-row {
            display: flex;
            align-items: baseline;
            gap: 16px;
            flex-wrap: wrap;
        }

        .info-field-group {
            display: inline-flex;
            align-items: baseline;
            gap: 5px;
        }

        .field-label {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            color: var(--color-azul);
            font-size: 11px;
            text-transform: uppercase;
        }

        .field-val {
            color: var(--color-texto);
            font-weight: 500;
            font-size: 13px;
        }

        .field-val-link {
            color: #0369a1;
            word-break: break-all;
            text-decoration: underline;
        }

        /* TABLAS INTERNAS */
        .doc-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            table-layout: fixed;
            margin-bottom: 12px;
        }

        .doc-table th,
        .doc-table td {
            border: 1px solid var(--color-borde);
            padding: 6px 8px;
            vertical-align: top;
            word-break: break-word;
        }

        .doc-table th {
            background-color: #f1f5f9;
            color: var(--color-azul);
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 10px;
        }

        .section-header-title {
            font-family: 'Outfit', sans-serif;
            font-size: 13.5px;
            font-weight: 800;
            color: var(--color-azul);
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        /* PIE DE PÁGINA FARUSAC */
        .report-footer-wrapper {
            margin-top: 20px;
            width: 100%;
        }

        .footer-address-line {
            font-size: 9.5px;
            color: #475569;
            text-align: center;
            line-height: 1.3;
            margin-bottom: 4px;
        }

        .footer-gold-bar {
            height: 3px;
            background-color: #f3b228;
            width: 100%;
        }

        .footer-navy-bar {
            background-color: #0b2341;
            color: #ffffff;
            padding: 5px 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 10px;
        }

        /* --- REGLAS DE IMPRESIÓN (@media print) --- */
        @media print {
            @page {
                size: letter portrait;
                margin: 8mm 8mm 8mm 8mm;
            }

            body {
                background-color: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
                font-size: 11px !important;
            }

            .no-print-bar {
                display: none !important;
            }

            .report-page-wrapper {
                box-shadow: none !important;
                border-radius: 0 !important;
                padding: 0 !important;
                margin: 0 !important;
                max-width: 100% !important;
                width: 100% !important;
                page-break-before: always;
                break-before: page;
            }

            .report-page-wrapper:first-of-type {
                page-break-before: auto !important;
                break-before: auto !important;
            }

            .report-footer-wrapper {
                position: fixed !important;
                bottom: 0 !important;
                left: 0 !important;
                right: 0 !important;
                width: 100% !important;
                background-color: #ffffff !important;
                z-index: 9999 !important;
            }

            .report-footer-spacer {
                display: block !important;
                height: 48px !important;
                visibility: hidden !important;
            }

            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>

<body>

    <!-- BARRA SUPERIOR DE ACCIONES -->
    <div class="no-print-bar">
        <div class="bar-left-info">
            <h1 class="bar-title">Consolidado FARUSAC - {{ $mes ?? 'Todos los Meses' }} {{ $anio ?? '' }}</h1>
            <span class="bar-badge">{{ $totalCount }} Informes</span>
        </div>
        <div class="bar-actions">
            <a href="{{ route('admin.informes') }}" class="btn-action btn-back">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
                <span>Volver al Historial</span>
            </a>
            <button type="button" class="btn-action btn-print" onclick="window.print()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect x="6" y="14" width="12" height="8"></rect>
                </svg>
                <span>Imprimir / Guardar Todo en PDF</span>
            </button>
        </div>
    </div>

    <!-- 1. ITERACIÓN DE INFORMES DE DOCENTES -->
    @foreach ($informesDocentes as $inf)
        <div class="report-page-wrapper">
            <table class="report-table-layout">
                <thead>
                    <tr>
                        <td>
                            <div class="report-header-wrapper">
                                <header class="doc-header">
                                    <div class="header-logos-container">
                                        <img src="{{ asset('images/InformePDF/logos-usac-farusac.png') }}"
                                            alt="Logo USAC y FARUSAC" class="logo-item logo-usac-farusac">
                                        <img src="{{ asset('images/InformePDF/LOGOS ACREDITADORAS 2026 HCERES (1).png') }}"
                                            alt="Logo Acreditadora HCÉRES" class="logo-item logo-acreditadora">
                                        <img src="{{ asset('images/InformePDF/LOGOS ACREDITADORAS 2026 CCA (1).png') }}"
                                            alt="Logo Acreditadora CCA" class="logo-item logo-acreditadora">
                                        <img src="{{ asset('images/InformePDF/LOGOS ACREDITADORAS 2026 CEAI (1).png') }}"
                                            alt="Logo Acreditadora CEAI" class="logo-item logo-acreditadora">
                                    </div>
                                    <div class="doc-title-block">
                                        <div class="doc-institution">Universidad de San Carlos de Guatemala</div>
                                        <div class="doc-faculty">Facultad de Arquitectura</div>
                                        <div class="doc-report-name">INFORME MENSUAL DE ACTIVIDADES DOCENTES</div>
                                    </div>
                                </header>
                            </div>
                        </td>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <td><div class="report-footer-spacer"></div></td>
                    </tr>
                </tfoot>
                <tbody>
                    <tr>
                        <td>
                            <div class="report-body-wrapper">
                                <!-- Datos Generales -->
                                <div class="general-info-box">
                                    <div class="info-row">
                                        <div class="info-field-group">
                                            <span class="field-label">Docente:</span>
                                            <span class="field-val">{{ $inf->usuario->nombre ?? 'Docente' }}</span>
                                        </div>
                                        <div class="info-field-group">
                                            <span class="field-label">Carrera:</span>
                                            <span class="field-val">{{ $inf->curso->carrera ?? 'Arquitectura' }}</span>
                                        </div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-field-group">
                                            <span class="field-label">Asignatura:</span>
                                            <span class="field-val">{{ $inf->curso->nombre_curso ?? '' }}</span>
                                        </div>
                                        <div class="info-field-group">
                                            <span class="field-label">Código:</span>
                                            <span class="field-val">{{ $inf->curso->codigo_curso ?? '—' }}</span>
                                        </div>
                                        <div class="info-field-group">
                                            <span class="field-label">Sección:</span>
                                            <span class="field-val">{{ $inf->curso->seccion ?? '—' }}</span>
                                        </div>
                                        <div class="info-field-group">
                                            <span class="field-label">Mes y año:</span>
                                            <span class="field-val">{{ $inf->mes }} {{ $inf->curso->anio ?? date('Y', strtotime($inf->created_at)) }}</span>
                                        </div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-field-group">
                                            <span class="field-label">Estudiantes asignados:</span>
                                            <span class="field-val">{{ $inf->estudiantes_asignados ?? 0 }}</span>
                                        </div>
                                        <div class="info-field-group">
                                            <span class="field-label">Listado Asistencia:</span>
                                            <span class="field-val">{{ $inf->listado_asistencia_url ?? '—' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Semanas de Actividades -->
                                <table class="doc-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 10%;">No. Sem.</th>
                                            <th style="width: 25%;">Tema Planificado</th>
                                            <th style="width: 35%;">Actividades Desarrolladas</th>
                                            <th style="width: 12%; text-align: center;">Cumplimiento</th>
                                            <th style="width: 18%;">Observaciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($inf->semanas as $s)
                                            <tr>
                                                <td style="text-align: center; font-weight: 700;">Sem. {{ $s->numero_semana }}</td>
                                                <td>{{ $s->tema_planificado ?? '—' }}</td>
                                                <td>{{ $s->actividades_desarrolladas ?? '—' }}</td>
                                                <td style="text-align: center; font-weight: 600;">
                                                    {{ $s->cumplimiento === 'si' ? 'Sí' : ($s->cumplimiento === 'no' ? 'No' : '—') }}
                                                </td>
                                                <td>{{ $s->observaciones ?? '—' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" style="text-align: center; font-style: italic;">Sin semanas registradas.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                @if($inf->estrategias_evaluacion)
                                    <div style="margin-top: 10px;">
                                        <span class="field-label">Estrategias de Evaluación:</span>
                                        <p style="font-size: 11px; margin-top: 2px;">{{ $inf->estrategias_evaluacion }}</p>
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <footer class="report-footer-wrapper">
                <div class="footer-address-line">
                    Ciudad Universitaria zona 12, Edificio T-2, 3er Nivel, Guatemala, Guatemala.
                </div>
                <div class="footer-gold-bar"></div>
                <div class="footer-navy-bar">
                    <span>farusac.edu.gt</span>
                    <span>PBX: (+502) 2418-8000</span>
                </div>
            </footer>
        </div>
    @endforeach

    <!-- 2. ITERACIÓN DE INFORMES DE COORDINACIÓN -->
    @foreach ($informesCoordinacion as $infC)
        <div class="report-page-wrapper">
            <table class="report-table-layout">
                <thead>
                    <tr>
                        <td>
                            <div class="report-header-wrapper">
                                <header class="doc-header">
                                    <div class="header-logos-container">
                                        <img src="{{ asset('images/InformePDF/logos-usac-farusac.png') }}"
                                            alt="Logo USAC y FARUSAC" class="logo-item logo-usac-farusac">
                                        <img src="{{ asset('images/InformePDF/LOGOS ACREDITADORAS 2026 HCERES (1).png') }}"
                                            alt="Logo Acreditadora HCÉRES" class="logo-item logo-acreditadora">
                                        <img src="{{ asset('images/InformePDF/LOGOS ACREDITADORAS 2026 CCA (1).png') }}"
                                            alt="Logo Acreditadora CCA" class="logo-item logo-acreditadora">
                                        <img src="{{ asset('images/InformePDF/LOGOS ACREDITADORAS 2026 CEAI (1).png') }}"
                                            alt="Logo Acreditadora CEAI" class="logo-item logo-acreditadora">
                                    </div>
                                    <div class="doc-title-block">
                                        <div class="doc-institution">Universidad de San Carlos de Guatemala</div>
                                        <div class="doc-faculty">Facultad de Arquitectura</div>
                                        <div class="doc-report-name">INFORME MENSUAL DE ACTIVIDADES DE COORDINACIÓN</div>
                                    </div>
                                </header>
                            </div>
                        </td>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <td><div class="report-footer-spacer"></div></td>
                    </tr>
                </tfoot>
                <tbody>
                    <tr>
                        <td>
                            <div class="report-body-wrapper">
                                <!-- Datos Generales -->
                                <div class="general-info-box">
                                    <div class="info-row">
                                        <div class="info-field-group">
                                            <span class="field-label">Área:</span>
                                            <span class="field-val">{{ $infC->area }}</span>
                                        </div>
                                        <div class="info-field-group">
                                            <span class="field-label">Mes y año:</span>
                                            <span class="field-val">{{ $infC->mes }} {{ $infC->anio }}</span>
                                        </div>
                                        <div class="info-field-group">
                                            <span class="field-label">Coordinador(a):</span>
                                            <span class="field-val">{{ $infC->usuario->nombre ?? 'Coordinador' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- 1. Asignaturas y Programas -->
                                @if($infC->programas && $infC->programas->count() > 0)
                                    <h2 class="section-header-title">1. Programas de Asignaturas:</h2>
                                    <table class="doc-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 40%;">Asignatura</th>
                                                <th style="width: 60%;">Enlace a Programa</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($infC->programas as $p)
                                                <tr>
                                                    <td><strong>{{ $p->asignatura }}</strong></td>
                                                    <td>{{ $p->enlace_programa ?? '—' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif

                                <!-- 2. Información General Asignaturas -->
                                @if($infC->asignaturas && $infC->asignaturas->count() > 0)
                                    <h2 class="section-header-title">2. Información General de Asignaturas:</h2>
                                    <table class="doc-table">
                                        <thead>
                                            <tr>
                                                <th>Docente</th>
                                                <th>Asignatura</th>
                                                <th style="width: 45px; text-align: center;">Sec.</th>
                                                <th style="width: 60px; text-align: center;">Informe</th>
                                                <th style="width: 60px; text-align: center;">Meet</th>
                                                <th style="width: 60px; text-align: center;">Enlace</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($infC->asignaturas as $a)
                                                <tr>
                                                    <td>{{ $a->docente_nombre }}</td>
                                                    <td>{{ $a->asignatura }}</td>
                                                    <td style="text-align: center;">{{ $a->seccion }}</td>
                                                    <td style="text-align: center;">{{ $a->presento_informe ? 'Sí' : 'No' }}</td>
                                                    <td style="text-align: center;">{{ $a->tiene_sala_reuniones ? 'Sí' : 'No' }}</td>
                                                    <td style="text-align: center;">{{ $a->funciona_enlace_virtual ? 'Sí' : 'No' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif

                                <!-- 3.1 Avance de Cursos -->
                                @if($infC->avances && $infC->avances->count() > 0)
                                    <h2 class="section-header-title">3.1 Avance del Curso:</h2>
                                    <table class="doc-table">
                                        <thead>
                                            <tr>
                                                <th>Docente</th>
                                                <th>Asignatura</th>
                                                <th style="width: 45px; text-align: center;">Sec.</th>
                                                <th style="width: 65px; text-align: center;">% Avance</th>
                                                <th>Observaciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($infC->avances as $av)
                                                <tr>
                                                    <td>{{ $av->docente_nombre }}</td>
                                                    <td>{{ $av->asignatura }}</td>
                                                    <td style="text-align: center;">{{ $av->seccion }}</td>
                                                    <td style="text-align: center; font-weight: 700;">{{ $av->porcentaje_avance }}%</td>
                                                    <td>{{ $av->observaciones ?? '—' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <footer class="report-footer-wrapper">
                <div class="footer-address-line">
                    Ciudad Universitaria zona 12, Edificio T-2, 3er Nivel, Guatemala, Guatemala.
                </div>
                <div class="footer-gold-bar"></div>
                <div class="footer-navy-bar">
                    <span>farusac.edu.gt</span>
                    <span>PBX: (+502) 2418-8000</span>
                </div>
            </footer>
        </div>
    @endforeach

</body>

</html>
