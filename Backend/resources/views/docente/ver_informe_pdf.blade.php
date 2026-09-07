<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe - {{ $informe->curso->nombre_curso ?? 'Curso' }} - {{ $informe->mes }} {{ $informe->curso->anio ?? '' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --color-azul-oscuro: #002D72;
            --color-azul-medio: #003B95;
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
            padding: 40px 20px;
            font-size: 13px;
            line-height: 1.5;
        }

        .no-print-bar {
            max-width: 900px;
            margin: 0 auto 20px auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 20px;
            font-family: 'Outfit', sans-serif;
            font-size: 13.5px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
            border: none;
        }

        .btn-print {
            background-color: var(--color-azul-oscuro);
            color: #ffffff;
            box-shadow: 0 4px 10px rgba(0, 45, 114, 0.2);
        }

        .btn-print:hover {
            background-color: var(--color-azul-medio);
            transform: translateY(-1px);
        }

        .btn-back {
            background-color: #ffffff;
            color: #475569;
            border: 1.5px solid #cbd5e1;
        }

        .btn-back:hover {
            background-color: #f8fafc;
            color: #1e293b;
        }

        /* --- HOJA DEL DOCUMENTO OFICIAL --- */
        .document-page {
            max-width: 900px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 50px 60px;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
        }

        .doc-header {
            border-bottom: 2.5px solid var(--color-azul-oscuro);
            padding-bottom: 20px;
            margin-bottom: 25px;
        }

        .header-logos-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 18px;
            padding-bottom: 16px;
            border-bottom: 1px solid #e2e8f0;
            flex-wrap: nowrap;
        }

        .logo-item {
            object-fit: contain;
            display: block;
        }

        .logo-usac-farusac {
            height: 54px;
            max-width: 240px;
        }

        .logo-acreditadora {
            height: 46px;
            max-width: 140px;
        }

        .doc-title-block {
            text-align: center;
        }

        .doc-institution {
            font-family: 'Outfit', sans-serif;
            font-size: 15px;
            font-weight: 800;
            color: var(--color-azul-oscuro);
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .doc-faculty {
            font-family: 'Outfit', sans-serif;
            font-size: 13px;
            font-weight: 700;
            color: #475569;
            margin-top: 3px;
            margin-bottom: 5px;
            letter-spacing: 0.02em;
        }

        .doc-report-name {
            font-family: 'Outfit', sans-serif;
            font-size: 16px;
            font-weight: 800;
            color: #b91c1c;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* --- SECCIONES Y TABLAS --- */
        .section-box {
            margin-bottom: 24px;
        }

        .section-title {
            font-family: 'Outfit', sans-serif;
            font-size: 13px;
            font-weight: 800;
            color: var(--color-azul-oscuro);
            text-transform: uppercase;
            letter-spacing: 0.04em;
            background-color: #f1f5f9;
            padding: 6px 12px;
            border-left: 4px solid var(--color-azul-oscuro);
            margin-bottom: 12px;
        }

        .grid-info {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px 20px;
            font-size: 12.5px;
        }

        .info-row {
            display: flex;
            gap: 8px;
        }

        .info-label {
            font-weight: 700;
            color: #475569;
            min-width: 140px;
        }

        .info-value {
            font-weight: 500;
            color: #0f172a;
            word-break: break-all;
        }

        .info-value a {
            color: #2563eb;
            text-decoration: none;
        }

        .info-value a:hover {
            text-decoration: underline;
        }

        /* Tabla de semanas */
        .table-semanas {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 12px;
        }

        .table-semanas th {
            background-color: #f8fafc;
            color: var(--color-azul-oscuro);
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            text-align: left;
            padding: 10px 12px;
            border: 1px solid var(--color-borde);
            text-transform: uppercase;
            font-size: 11.5px;
        }

        .table-semanas td {
            padding: 10px 12px;
            border: 1px solid var(--color-borde);
            vertical-align: top;
        }

        .badge-semana {
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            color: var(--color-azul-oscuro);
            white-space: nowrap;
        }

        .text-block-value {
            background-color: #f8fafc;
            border: 1px solid var(--color-borde);
            border-radius: 6px;
            padding: 12px 14px;
            font-size: 12.5px;
            line-height: 1.6;
            color: #334155;
            white-space: pre-line;
        }

        .doc-footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: #64748b;
        }

        /* --- IMPRESIÓN --- */
        @media print {
            body {
                background-color: #ffffff;
                padding: 0;
            }
            .no-print-bar {
                display: none !important;
            }
            .document-page {
                box-shadow: none;
                border: none;
                padding: 0;
                max-width: 100%;
            }
            .table-semanas th {
                background-color: #f1f5f9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .section-title {
                background-color: #f1f5f9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .header-logos-container {
                display: flex !important;
                align-items: center !important;
                justify-content: space-between !important;
                gap: 15px !important;
                padding-bottom: 16px !important;
                border-bottom: 1px solid #cbd5e1 !important;
            }
            .logo-item {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

    <!-- BARRA SUPERIOR DE ACCIONES -->
    <div class="no-print-bar">
        <a href="{{ route('docente.informes') }}" class="btn-action btn-back">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg>
            Volver al Historial
        </a>
        <button type="button" class="btn-action btn-print" onclick="window.print()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
            Imprimir / Guardar PDF
        </button>
    </div>

    <!-- PÁGINA DEL INFORME -->
    <div class="document-page">
        
        <!-- ENCABEZADO OFICIAL FARUSAC -->
        <div class="doc-header">
            <div class="header-logos-container">
                <img src="{{ asset('images/InformePDF/logos-usac-farusac.png') }}" alt="Logo USAC - FARUSAC" class="logo-item logo-usac-farusac">
                <img src="{{ asset('images/InformePDF/LOGOS ACREDITADORAS 2026 HCERES (1).png') }}" alt="Logo Acreditadora HCÉRES" class="logo-item logo-acreditadora">
                <img src="{{ asset('images/InformePDF/LOGOS ACREDITADORAS 2026 CCA (1).png') }}" alt="Logo Acreditadora CCA" class="logo-item logo-acreditadora">
                <img src="{{ asset('images/InformePDF/LOGOS ACREDITADORAS 2026 CEAI (1).png') }}" alt="Logo Acreditadora CEAI" class="logo-item logo-acreditadora">
            </div>
            <div class="doc-title-block">
                <div class="doc-institution">UNIVERSIDAD DE SAN CARLOS DE GUATEMALA</div>
                <div class="doc-faculty">FACULTAD DE ARQUITECTURA</div>
                <div class="doc-report-name">INFORME MENSUAL DE ACTIVIDADES DOCENTES</div>
            </div>
        </div>

        <!-- 1. INFORMACIÓN GENERAL -->
        <div class="section-box">
            <div class="section-title">1. INFORMACIÓN GENERAL DEL CURSO Y DOCENTE</div>
            <div class="grid-info">
                <div class="info-row">
                    <span class="info-label">Docente:</span>
                    <span class="info-value">{{ $user->nombre }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Correo Institucional:</span>
                    <span class="info-value">{{ $user->correo }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Curso:</span>
                    <span class="info-value">{{ $informe->curso->nombre_curso ?? 'Sin asignar' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Código de Curso:</span>
                    <span class="info-value">{{ $informe->curso->codigo_curso ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Sección:</span>
                    <span class="info-value">{{ $informe->curso->seccion ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Carrera / Área:</span>
                    <span class="info-value">{{ $informe->curso->carrera ?? '—' }} ({{ $informe->curso->area ?? '—' }})</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Año y Periodo:</span>
                    <span class="info-value">{{ $informe->curso->anio ?? '' }} - {{ $informe->periodo }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Mes Reportado:</span>
                    <span class="info-value" style="font-weight: 700; color: var(--color-azul-oscuro);">{{ $informe->mes }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Estudiantes Asignados:</span>
                    <span class="info-value">{{ $informe->estudiantes_asignados }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Fecha de Envío:</span>
                    <span class="info-value">{{ date('d/m/Y H:i', strtotime($informe->created_at)) }}</span>
                </div>
            </div>
        </div>

        <!-- 2. ENLACES Y PLATAFORMAS -->
        <div class="section-box">
            <div class="section-title">2. ENLACES DE EVIDENCIA Y PLATAFORMAS VIRTUALES</div>
            <div class="grid-info" style="grid-template-columns: 1fr;">
                <div class="info-row">
                    <span class="info-label">Listado Oficial de Asistencia:</span>
                    <span class="info-value">
                        @if ($informe->listado_asistencia_url)
                            <a href="{{ $informe->listado_asistencia_url }}" target="_blank">{{ $informe->listado_asistencia_url }}</a>
                        @else
                            <span style="color: #94a3b8;">No especificado</span>
                        @endif
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Enlace a Evidencias (Drive):</span>
                    <span class="info-value">
                        @if ($informe->enlace_evidencia_url)
                            <a href="{{ $informe->enlace_evidencia_url }}" target="_blank">{{ $informe->enlace_evidencia_url }}</a>
                        @else
                            <span style="color: #94a3b8;">No especificado</span>
                        @endif
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Enlace de Clases (Meet / Zoom):</span>
                    <span class="info-value">
                        @if ($informe->enlace_meet_zoom_url)
                            <a href="{{ $informe->enlace_meet_zoom_url }}" target="_blank">{{ $informe->enlace_meet_zoom_url }}</a>
                        @else
                            <span style="color: #94a3b8;">No especificado</span>
                        @endif
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Enlace de Aula (Classroom / Drive):</span>
                    <span class="info-value">
                        @if ($informe->enlace_classroom_drive_url)
                            <a href="{{ $informe->enlace_classroom_drive_url }}" target="_blank">{{ $informe->enlace_classroom_drive_url }}</a>
                        @else
                            <span style="color: #94a3b8;">No especificado</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- 3. ESTRATEGIAS DE EVALUACIÓN -->
        @if ($informe->estrategias_evaluacion)
            <div class="section-box">
                <div class="section-title">3. ESTRATEGIAS DE EVALUACIÓN APLICADAS</div>
                <div class="text-block-value">
                    {{ $informe->estrategias_evaluacion }}
                </div>
            </div>
        @endif

        <!-- 4. REGISTRO SEMANAL DE ACTIVIDADES -->
        <div class="section-box">
            <div class="section-title">4. REGISTRO SEMANAL DE ACTIVIDADES Y METODOLOGÍAS</div>
            <table class="table-semanas">
                <thead>
                    <tr>
                        <th style="width: 80px;">Semana</th>
                        <th>Contenido y Actividad Realizada</th>
                        <th style="width: 100px;">Estudiantes Participantes</th>
                        <th>Metodologías Empleadas</th>
                        <th>Medios de Comunicación</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($informe->semanas as $sem)
                        <tr>
                            <td class="badge-semana">Semana {{ $sem->numero_semana }}</td>
                            <td>{{ $sem->actividad_realizada }}</td>
                            <td style="text-align: center; font-weight: 700;">{{ $sem->estudiantes_participaron }}</td>
                            <td>{{ $sem->metodologias ?? '—' }}</td>
                            <td>{{ $sem->medios_comunicacion ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: #94a3b8;">No se registraron semanas de actividad.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PIE DE DOCUMENTO -->
        <div class="doc-footer">
            <span>Generado automáticamente por el Sistema de Informes FARUSAC</span>
            <span>ID Informe: #{{ $informe->id }} | Estado: {{ strtoupper($informe->estado) }}</span>
        </div>

    </div>

</body>
</html>
