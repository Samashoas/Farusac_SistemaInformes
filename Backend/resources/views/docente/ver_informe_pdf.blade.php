<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe - {{ $informe->curso->nombre_curso ?? 'Curso' }} - {{ $informe->mes }}
        {{ $informe->curso->anio ?? '' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap"
        rel="stylesheet">

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
            padding: 0;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
            overflow: hidden;
            position: relative;
        }

        /* --- ESTRUCTURA MASTER PARA REPETICIÓN DE HEADER Y FOOTER EN CADA PÁGINA --- */
        .report-table-layout {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            border: none;
            margin: 0;
            padding: 0;
            table-layout: fixed;
        }

        .report-table-layout>thead {
            display: table-header-group;
        }

        .report-table-layout>tfoot {
            display: table-footer-group;
        }

        .report-table-layout>tbody {
            display: table-row-group;
        }

        .report-table-layout>thead>tr>td,
        .report-table-layout>tfoot>tr>td,
        .report-table-layout>tbody>tr>td {
            border: none;
            padding: 0;
            margin: 0;
            background: transparent;
        }

        .report-header-wrapper {
            padding: 35px 45px 0 45px;
        }

        .report-body-wrapper {
            padding: 10px 45px 25px 45px;
        }

        .report-footer-spacer {
            display: none;
        }

        .report-footer-wrapper {
            padding: 0;
            width: 100%;
        }

        .doc-header {
            border-bottom: 2.5px solid var(--color-azul-oscuro);
            padding-bottom: 12px;
            margin-bottom: 14px;
        }

        .header-logos-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid #e2e8f0;
            flex-wrap: nowrap;
            width: 100%;
        }

        .logo-item {
            object-fit: contain;
            display: block;
        }

        .logo-usac-farusac {
            height: 48px;
            max-width: 220px;
            width: auto;
        }

        .logo-acreditadora {
            height: 38px;
            max-width: 110px;
            width: auto;
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
            line-height: 1.2;
        }

        .doc-faculty {
            font-family: 'Outfit', sans-serif;
            font-size: 12.5px;
            font-weight: 700;
            color: #475569;
            margin-top: 2px;
            margin-bottom: 4px;
            letter-spacing: 0.02em;
            line-height: 1.2;
        }

        .doc-report-name {
            font-family: 'Outfit', sans-serif;
            font-size: 16px;
            font-weight: 800;
            color: #b91c1c;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            line-height: 1.2;
        }

        /* --- SECCIONES Y TABLAS --- */
        .section-box {
            margin-bottom: 20px;
            break-inside: auto;
            page-break-inside: auto;
        }

        .section-title {
            font-family: 'Outfit', sans-serif;
            font-size: 13px;
            font-weight: 800;
            color: var(--color-azul-oscuro);
            text-transform: uppercase;
            letter-spacing: 0.03em;
            background-color: #f1f5f9;
            padding: 6px 12px;
            border-left: 4px solid var(--color-azul-oscuro);
            margin-bottom: 12px;
        }

        .grid-info {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px 16px;
            font-size: 12.5px;
            width: 100%;
        }

        .info-row {
            display: flex;
            gap: 6px;
            min-width: 0;
            align-items: baseline;
        }

        .info-label {
            font-weight: 700;
            color: #475569;
            white-space: nowrap;
            flex-shrink: 0;
            font-size: 11.5px;
        }

        .info-value {
            font-weight: 500;
            color: #0f172a;
            word-break: break-word;
            overflow-wrap: break-word;
            min-width: 0;
            font-size: 12.5px;
        }

        .info-value a {
            color: #2563eb;
            text-decoration: none;
            word-break: break-all;
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
            table-layout: fixed;
            border: 1px solid var(--color-borde);
        }

        .table-semanas th {
            background-color: #f8fafc;
            color: var(--color-azul-oscuro);
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            text-align: left;
            padding: 8px 10px;
            border: 1px solid var(--color-borde);
            text-transform: uppercase;
            font-size: 11px;
            word-break: break-word;
            overflow-wrap: break-word;
        }

        .table-semanas td {
            padding: 8px 10px;
            border: 1px solid var(--color-borde);
            vertical-align: top;
            word-break: break-word;
            overflow-wrap: break-word;
            line-height: 1.4;
        }

        .table-semanas tr {
            page-break-inside: avoid;
            break-inside: avoid;
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
            line-height: 1.55;
            color: #334155;
            white-space: pre-line;
            word-break: break-word;
        }

        /* --- PIE DE PÁGINA INSTITUCIONAL FARUSAC --- */
        .doc-footer-official {
            margin: 0;
            width: 100%;
        }

        .footer-top-text {
            text-align: center;
            font-family: 'Outfit', sans-serif;
            font-size: 12.5px;
            font-weight: 700;
            color: #0b2341;
            padding-bottom: 8px;
            background-color: #ffffff;
            letter-spacing: 0.01em;
        }

        .footer-gold-bar {
            height: 3px;
            background-color: #f3b228;
            width: 100%;
        }

        .footer-navy-bar {
            background-color: #0b2341;
            color: #ffffff;
            padding: 8px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: nowrap;
            gap: 10px;
            width: 100%;
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .footer-left-content {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .footer-social-icons {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
        }

        .social-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            background-color: #ffffff;
            color: #0b2341;
            border-radius: 50%;
            flex-shrink: 0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .social-icon svg {
            width: 13px;
            height: 13px;
        }

        .footer-links-text {
            display: flex;
            flex-direction: column;
            gap: 1px;
        }

        .footer-web {
            color: #ffffff;
            font-family: 'Inter', sans-serif;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.02em;
            white-space: nowrap;
        }

        .footer-handle {
            color: #ffffff;
            font-family: 'Inter', sans-serif;
            font-size: 10px;
            font-weight: 400;
            opacity: 0.95;
            letter-spacing: 0.01em;
            white-space: nowrap;
        }

        .footer-right-content {
            font-family: 'Inter', sans-serif;
            font-size: 11.5px;
            font-weight: 600;
            color: #ffffff;
            letter-spacing: 0.02em;
            white-space: nowrap;
            flex-shrink: 0;
        }

        /* --- IMPRESIÓN --- */
        @media print {
            @page {
                size: letter portrait;
                margin: 8mm 10mm 12mm 10mm;
            }

            html,
            body {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                background-color: #ffffff !important;
                overflow: visible !important;
            }

            .no-print-bar {
                display: none !important;
            }

            .document-page {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                border-radius: 0 !important;
                position: static !important;
            }

            .report-table-layout {
                width: 100% !important;
                max-width: 100% !important;
                table-layout: fixed !important;
                border-collapse: collapse !important;
            }

            .report-table-layout>thead {
                display: table-header-group !important;
            }

            .report-table-layout>tfoot {
                display: table-footer-group !important;
            }

            .report-table-layout>tbody {
                display: table-row-group !important;
            }

            .report-header-wrapper {
                padding: 0 0 6px 0 !important;
                width: 100% !important;
            }

            .report-body-wrapper {
                padding: 4px 0 6px 0 !important;
                width: 100% !important;
            }

            /* El espaciador reserva la altura en cada página para no sobreescribir el pie fijo */
            .report-footer-spacer {
                display: block !important;
                height: 48px !important;
                width: 100% !important;
                visibility: hidden !important;
            }

            /* El pie de página siempre va al final de cada página */
            .report-footer-wrapper {
                position: fixed !important;
                bottom: 0 !important;
                left: 0 !important;
                right: 0 !important;
                width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
                z-index: 9999 !important;
                background-color: #ffffff !important;
            }

            .header-logos-container {
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                gap: 14px !important;
                padding-bottom: 6px !important;
                margin-bottom: 6px !important;
                border-bottom: 1px solid #cbd5e1 !important;
                width: 100% !important;
            }

            .logo-usac-farusac {
                height: 38px !important;
                max-width: 170px !important;
                width: auto !important;
            }

            .logo-acreditadora {
                height: 30px !important;
                max-width: 88px !important;
                width: auto !important;
            }

            .doc-institution {
                font-size: 13px !important;
            }

            .doc-faculty {
                font-size: 11px !important;
            }

            .doc-report-name {
                font-size: 14px !important;
            }

            .doc-header {
                border-bottom: 1.5px solid #002D72 !important;
                padding-bottom: 6px !important;
                margin-bottom: 8px !important;
            }

            .section-box {
                margin-bottom: 12px !important;
            }

            .section-title {
                font-size: 11.5px !important;
                padding: 4px 8px !important;
                margin-bottom: 8px !important;
                background-color: #f1f5f9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .grid-info {
                display: grid !important;
                grid-template-columns: 1fr 1fr !important;
                gap: 4px 12px !important;
                width: 100% !important;
                font-size: 11px !important;
            }

            .info-row {
                display: flex !important;
                gap: 4px !important;
                min-width: 0 !important;
                align-items: baseline !important;
            }

            .info-label {
                font-size: 11px !important;
                font-weight: 700 !important;
                color: #475569 !important;
                white-space: nowrap !important;
                flex-shrink: 0 !important;
            }

            .info-value {
                font-size: 11px !important;
                font-weight: 500 !important;
                color: #0f172a !important;
                word-break: break-word !important;
                overflow-wrap: break-word !important;
                min-width: 0 !important;
            }

            .info-value a {
                word-break: break-all !important;
                font-size: 10px !important;
            }

            .table-semanas {
                font-size: 10px !important;
                width: 100% !important;
                table-layout: fixed !important;
                border-collapse: collapse !important;
            }

            .table-semanas th {
                background-color: #f1f5f9 !important;
                padding: 4px 5px !important;
                font-size: 10px !important;
                line-height: 1.2 !important;
                word-break: break-word !important;
                overflow-wrap: break-word !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .table-semanas td {
                padding: 4px 5px !important;
                font-size: 10px !important;
                line-height: 1.3 !important;
                word-break: break-word !important;
                overflow-wrap: break-word !important;
            }

            .table-semanas tr {
                page-break-inside: avoid;
                break-inside: avoid;
            }

            .text-block-value {
                padding: 8px 10px !important;
                font-size: 10.5px !important;
            }

            .footer-top-text {
                font-size: 10.5px !important;
                padding-bottom: 2px !important;
                color: #0b2341 !important;
            }

            .footer-gold-bar {
                height: 2px !important;
                background-color: #f3b228 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .footer-navy-bar {
                background-color: #0b2341 !important;
                color: #ffffff !important;
                padding: 4px 10px !important;
                gap: 6px !important;
                width: 100% !important;
                box-sizing: border-box !important;
                display: flex !important;
                align-items: center !important;
                justify-content: space-between !important;
                flex-wrap: nowrap !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .footer-left-content {
                display: flex !important;
                align-items: center !important;
                gap: 8px !important;
            }

            .footer-social-icons {
                display: flex !important;
                align-items: center !important;
                gap: 4px !important;
            }

            .social-icon {
                width: 18px !important;
                height: 18px !important;
                background-color: #ffffff !important;
                color: #0b2341 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .social-icon svg {
                width: 10px !important;
                height: 10px !important;
            }

            .footer-web {
                font-size: 9.5px !important;
            }

            .footer-handle {
                font-size: 8.5px !important;
            }

            .footer-right-content {
                font-size: 10px !important;
                white-space: nowrap !important;
            }

            .footer-web,
            .footer-handle,
            .footer-right-content {
                color: #ffffff !important;
            }

            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }

        @if(request()->has('embed'))
            body {
                padding: 15px 12px;
                background-color: #f8fafc;
            }

            .document-page {
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
                border-radius: 12px;
                margin: 0 auto;
                padding: 0;
            }

            .report-header-wrapper {
                padding: 20px 20px 0 20px;
            }

            .report-body-wrapper {
                padding: 10px 20px 20px 20px;
            }

            .report-footer-wrapper {
                padding: 0;
            }
        @endif

        @if(isset($isPdf))
            @page {
                size: letter portrait;
                margin: 8mm 10mm 8mm 10mm;
            }

            body {
                font-family: Helvetica, Arial, sans-serif !important;
                font-size: 11px !important;
                color: #1e293b !important;
                background-color: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
                line-height: 1.4 !important;
            }

            .no-print-bar {
                display: none !important;
            }

            .document-page {
                max-width: 100% !important;
                margin: 0 !important;
                border: none !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                background-color: #ffffff !important;
            }

            .report-table-layout {
                width: 100% !important;
                border-collapse: collapse !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .report-header-wrapper {
                padding: 0 0 6px 0 !important;
                width: 100% !important;
            }

            .report-body-wrapper {
                padding: 0 !important;
                width: 100% !important;
            }

            .report-footer-spacer {
                display: none !important;
            }

            .doc-header {
                border-bottom: 2.5px solid #002D72 !important;
                padding-bottom: 6px !important;
                margin-bottom: 8px !important;
            }

            .header-logos-container {
                display: block !important;
                text-align: center !important;
                width: 100% !important;
                margin-bottom: 6px !important;
                padding-bottom: 6px !important;
                border-bottom: 1px solid #e2e8f0 !important;
                white-space: nowrap !important;
            }

            .header-logos-container .logo-item {
                display: inline-block !important;
                vertical-align: middle !important;
                margin: 0 4px !important;
            }

            .logo-usac-farusac {
                height: 42px !important;
                width: auto !important;
                max-width: 200px !important;
            }

            .logo-acreditadora {
                height: 32px !important;
                width: auto !important;
                max-width: 90px !important;
            }

            .doc-title-block {
                text-align: center !important;
            }

            .doc-report-name {
                font-family: Helvetica, Arial, sans-serif !important;
                font-size: 13.5px !important;
                font-weight: bold !important;
                color: #b91c1c !important;
                text-transform: uppercase !important;
                letter-spacing: 0.5px !important;
            }

            .section-box {
                margin-bottom: 10px !important;
            }

            .section-title {
                font-family: Helvetica, Arial, sans-serif !important;
                font-size: 11px !important;
                font-weight: bold !important;
                color: #002D72 !important;
                text-transform: uppercase !important;
                background-color: #f1f5f9 !important;
                padding: 4px 8px !important;
                border-left: 4px solid #002D72 !important;
                margin-top: 8px !important;
                margin-bottom: 6px !important;
            }

            .grid-info {
                display: block !important;
                width: 100% !important;
                margin-bottom: 6px !important;
            }

            .info-row {
                display: inline-block !important;
                width: 48.5% !important;
                vertical-align: top !important;
                margin-bottom: 4px !important;
                font-size: 11px !important;
            }

            .info-label {
                display: inline !important;
                font-weight: bold !important;
                color: #475569 !important;
                font-size: 10.5px !important;
            }

            .info-value {
                display: inline !important;
                font-weight: normal !important;
                color: #0f172a !important;
                font-size: 11px !important;
            }

            .table-semanas {
                width: 100% !important;
                border-collapse: collapse !important;
                font-size: 10.5px !important;
                margin-top: 6px !important;
                border: 1px solid #cbd5e1 !important;
            }

            .table-semanas th {
                background-color: #f8fafc !important;
                color: #002D72 !important;
                font-weight: bold !important;
                padding: 5px 6px !important;
                border: 1px solid #cbd5e1 !important;
                font-size: 10px !important;
                text-transform: uppercase !important;
            }

            .table-semanas td {
                padding: 5px 6px !important;
                border: 1px solid #cbd5e1 !important;
                vertical-align: top !important;
                font-size: 10.5px !important;
                line-height: 1.35 !important;
            }

            .table-semanas tr {
                page-break-inside: avoid !important;
            }

            .badge-semana {
                font-weight: bold !important;
                color: #002D72 !important;
                white-space: nowrap !important;
            }

            .text-block-value {
                background-color: #f8fafc !important;
                border: 1px solid #cbd5e1 !important;
                padding: 8px 10px !important;
                font-size: 11px !important;
                color: #334155 !important;
            }

            .report-footer-wrapper {
                padding: 0 !important;
                width: 100% !important;
                margin-top: 15px !important;
                page-break-inside: avoid !important;
            }

            .doc-footer-official {
                margin: 0 !important;
                width: 100% !important;
            }

            .footer-top-text {
                text-align: center !important;
                font-size: 10px !important;
                font-weight: bold !important;
                color: #0b2341 !important;
                padding-bottom: 4px !important;
                background-color: #ffffff !important;
            }

            .footer-gold-bar {
                height: 3px !important;
                background-color: #f3b228 !important;
                width: 100% !important;
            }

            .footer-navy-bar {
                background-color: #0b2341 !important;
                color: #ffffff !important;
                padding: 6px 15px !important;
                width: 100% !important;
                display: table !important;
            }

            .footer-left-content {
                display: table-cell !important;
                text-align: left !important;
                vertical-align: middle !important;
                color: #ffffff !important;
                font-size: 9.5px !important;
            }

            .footer-social-icons {
                display: none !important;
            }

            .footer-links-text {
                display: inline !important;
                color: #ffffff !important;
            }

            .footer-web {
                display: inline !important;
                color: #ffffff !important;
                font-weight: bold !important;
                font-size: 9.5px !important;
                margin-right: 8px !important;
            }

            .footer-handle {
                display: inline !important;
                color: #ffffff !important;
                font-size: 9px !important;
                opacity: 0.9 !important;
            }

            .footer-right-content {
                display: table-cell !important;
                text-align: right !important;
                vertical-align: middle !important;
                color: #ffffff !important;
                font-size: 9.5px !important;
                font-weight: bold !important;
            }
        @endif
    </style>
</head>

<body>

    @if(!request()->has('embed') && !isset($isPdf))
        <!-- BARRA SUPERIOR DE ACCIONES -->
        <div class="no-print-bar">
            @php
                $userRole = Auth::user() ? Auth::user()->rol : '';
                if ($userRole === 'administrador') {
                    $backRoute = route('admin.informes');
                } elseif ($userRole === 'coordinador') {
                    $backRoute = route('coordinador.informes');
                } else {
                    $backRoute = route('docente.informes');
                }
            @endphp
            <a href="{{ $backRoute }}" class="btn-action btn-back">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
                Volver al Historial
            </a>
            <button type="button" class="btn-action btn-print" onclick="window.print()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect x="6" y="14" width="12" height="8"></rect>
                </svg>
                Imprimir / Guardar PDF
            </button>
        </div>
    @endif

    <!-- PÁGINA DEL INFORME -->
    <div class="document-page">
        <table class="report-table-layout">
            <thead>
                <tr>
                    <td>
                        <div class="report-header-wrapper">
                            <!-- ENCABEZADO OFICIAL FARUSAC -->
                            <div class="doc-header">
                                <div class="header-logos-container">
                                    <img src="{{ asset('images/InformePDF/logos-usac-farusac.png') }}"
                                        alt="Logo USAC - FARUSAC" class="logo-item logo-usac-farusac">
                                    <img src="{{ asset('images/InformePDF/LOGOS ACREDITADORAS 2026 HCERES (1).png') }}"
                                        alt="Logo Acreditadora HCÉRES" class="logo-item logo-acreditadora">
                                    <img src="{{ asset('images/InformePDF/LOGOS ACREDITADORAS 2026 CCA (1).png') }}"
                                        alt="Logo Acreditadora CCA" class="logo-item logo-acreditadora">
                                    <img src="{{ asset('images/InformePDF/LOGOS ACREDITADORAS 2026 CEAI (1).png') }}"
                                        alt="Logo Acreditadora CEAI" class="logo-item logo-acreditadora">
                                </div>
                                <div class="doc-title-block">
                                    <div class="doc-report-name">INFORME MENSUAL DE ACTIVIDADES DOCENTES</div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <td>
                        <!-- Espaciador que reserva espacio al pie de cada página para el footer fijo -->
                        <div class="report-footer-spacer"></div>
                    </td>
                </tr>
            </tfoot>
            <tbody>
                <tr>
                    <td>
                        <div class="report-body-wrapper">
                            <!-- 1. INFORMACIÓN GENERAL -->
                            <div class="section-box">
                                <div class="section-title">1. INFORMACIÓN GENERAL DEL CURSO Y DOCENTE</div>
                                <div class="grid-info">
                                    <div class="info-row">
                                        <span class="info-label">Docente:</span>
                                        <span class="info-value">{{ $informe->usuario->nombre ?? $user->nombre }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Correo Institucional:</span>
                                        <span class="info-value">{{ $informe->usuario->correo ?? $user->correo }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Curso:</span>
                                        <span
                                            class="info-value">{{ $informe->curso->nombre_curso ?? 'Sin asignar' }}</span>
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
                                        <span class="info-value">{{ $informe->curso->carrera ?? '—' }}
                                            ({{ $informe->curso->area ?? '—' }})</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Año y Periodo:</span>
                                        <span class="info-value">{{ $informe->curso->anio ?? '' }} -
                                            {{ $informe->periodo }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Mes Reportado:</span>
                                        <span class="info-value"
                                            style="font-weight: 700; color: var(--color-azul-oscuro);">{{ $informe->mes }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Estudiantes Asignados:</span>
                                        <span class="info-value">{{ $informe->estudiantes_asignados }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Fecha de Envío:</span>
                                        <span
                                            class="info-value">{{ date('d/m/Y H:i', strtotime($informe->created_at)) }}</span>
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
                                                <a href="{{ $informe->listado_asistencia_url }}"
                                                    target="_blank">{{ $informe->listado_asistencia_url }}</a>
                                            @else
                                                <span style="color: #94a3b8;">No especificado</span>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Enlace a Evidencias (Drive):</span>
                                        <span class="info-value">
                                            @if ($informe->enlace_evidencia_url)
                                                <a href="{{ $informe->enlace_evidencia_url }}"
                                                    target="_blank">{{ $informe->enlace_evidencia_url }}</a>
                                            @else
                                                <span style="color: #94a3b8;">No especificado</span>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Enlace de Clases (Meet / Zoom):</span>
                                        <span class="info-value">
                                            @if ($informe->enlace_meet_zoom_url)
                                                <a href="{{ $informe->enlace_meet_zoom_url }}"
                                                    target="_blank">{{ $informe->enlace_meet_zoom_url }}</a>
                                            @else
                                                <span style="color: #94a3b8;">No especificado</span>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Enlace de Aula (Classroom / Drive):</span>
                                        <span class="info-value">
                                            @if ($informe->enlace_classroom_drive_url)
                                                <a href="{{ $informe->enlace_classroom_drive_url }}"
                                                    target="_blank">{{ $informe->enlace_classroom_drive_url }}</a>
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
                                            <th style="width: 13%;">Semana</th>
                                            <th style="width: 35%;">Contenido y Actividad Realizada</th>
                                            <th style="width: 12%; text-align: center;">Estudiantes Participantes</th>
                                            <th style="width: 23%;">Metodologías Empleadas</th>
                                            <th style="width: 17%;">Medios de Comunicación</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($informe->semanas as $sem)
                                            <tr>
                                                <td class="badge-semana">Semana {{ $sem->numero_semana }}</td>
                                                <td>{{ $sem->actividad_realizada }}</td>
                                                <td style="text-align: center; font-weight: 700;">
                                                    {{ $sem->estudiantes_participaron }}</td>
                                                <td>{{ $sem->metodologias ?? '—' }}</td>
                                                <td>{{ $sem->medios_comunicacion ?? '—' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" style="text-align: center; color: #94a3b8;">No se
                                                    registraron semanas de actividad.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                        </div> <!-- /report-body-wrapper -->
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- PIE DE PÁGINA INSTITUCIONAL FARUSAC (Fijo al final de cada página en impresión) -->
        <div class="report-footer-wrapper">
            <footer class="doc-footer-official">
                <div class="footer-top-text">
                    Ciudad Universitaria, Zona 12, Facultad de Arquitectura, Edificio T2
                </div>
                <div class="footer-gold-bar"></div>
                <div class="footer-navy-bar">
                    <div class="footer-left-content">
                        <div class="footer-social-icons">
                            <!-- Facebook -->
                            <span class="social-icon">
                                <svg viewBox="0 0 24 24" fill="#0b2341">
                                    <path
                                        d="M14 13.5h2.5l1-4H14v-2c0-1.03 0-2 2-2h1.5V2.14c-.326-.043-1.557-.14-2.857-.14C11.928 2 10 3.657 10 6.7v2.8H7v4h3V22h4v-8.5z" />
                                </svg>
                            </span>
                            <!-- Instagram -->
                            <span class="social-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="#0b2341" stroke-width="2.2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2.5" y="2.5" width="19" height="19" rx="5" ry="5"></rect>
                                    <circle cx="12" cy="12" r="4.2"></circle>
                                    <circle cx="17.2" cy="6.8" r="1" fill="#0b2341" stroke="none"></circle>
                                </svg>
                            </span>
                            <!-- YouTube -->
                            <span class="social-icon">
                                <svg viewBox="0 0 24 24" fill="#0b2341">
                                    <path
                                        d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                                </svg>
                            </span>
                        </div>
                        <div class="footer-links-text">
                            <div class="footer-web">www.farusac.edu.gt</div>
                            <div class="footer-handle">@divulgacionfarusac</div>
                        </div>
                    </div>
                    <div class="footer-right-content">
                        <span>Tel: (502) 24189000</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

</body>

</html>