<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Process;

class ChromiumPdfService
{
    /**
     * Resuelve el binario ejecutable de Chromium / Google Chrome / Microsoft Edge
     * de forma dinámica, multiplataforma y segura, sin rutas absolutas hardcodeadas.
     */
    public function getChromiumBinary(): ?string
    {
        // 1. Configuración explícita en config (vía services.chromium.binary / CHROMIUM_PATH en .env)
        $configured = config('services.chromium.binary');
        if (!empty($configured) && file_exists($configured)) {
            return $configured;
        }

        $candidates = [];

        if (PHP_OS_FAMILY === 'Windows') {
            $systemDrive     = $_SERVER['SystemDrive'] ?? 'C:';
            $programFiles    = $_SERVER['ProgramFiles'] ?? ($systemDrive . '\\Program Files');
            $programFilesX86 = $_SERVER['ProgramFiles(x86)'] ?? ($systemDrive . '\\Program Files (x86)');
            $localAppData    = $_SERVER['LOCALAPPDATA'] ?? '';

            $candidates = array_filter([
                $programFiles . '\\Microsoft\\Edge\\Application\\msedge.exe',
                $programFilesX86 . '\\Microsoft\\Edge\\Application\\msedge.exe',
                $programFiles . '\\Google\\Chrome\\Application\\chrome.exe',
                $programFilesX86 . '\\Google\\Chrome\\Application\\chrome.exe',
                $localAppData ? $localAppData . '\\Microsoft\\Edge\\Application\\msedge.exe' : null,
                $localAppData ? $localAppData . '\\Google\\Chrome\\Application\\chrome.exe' : null,
            ]);
        } elseif (PHP_OS_FAMILY === 'Darwin') {
            $candidates = [
                '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
                '/Applications/Microsoft Edge.app/Contents/MacOS/Microsoft Edge',
                '/Applications/Chromium.app/Contents/MacOS/Chromium',
            ];
        } else {
            // Entornos Linux / Docker / Servidores de producción
            $candidates = [
                '/usr/bin/google-chrome',
                '/usr/bin/google-chrome-stable',
                '/usr/bin/chromium',
                '/usr/bin/chromium-browser',
                '/snap/bin/chromium',
            ];
        }

        foreach ($candidates as $path) {
            if (!empty($path) && file_exists($path)) {
                return $path;
            }
        }

        // Búsqueda en PATH del sistema como fallback seguro utilizando Process sin interpolación de shell
        if (PHP_OS_FAMILY === 'Windows') {
            foreach (['msedge.exe', 'chrome.exe'] as $exe) {
                try {
                    $result = Process::timeout(3)->run(['where.exe', $exe]);
                    if ($result->successful()) {
                        $firstLine = trim(explode("\n", trim($result->output()))[0]);
                        if (!empty($firstLine) && file_exists($firstLine)) {
                            return $firstLine;
                        }
                    }
                } catch (\Throwable) {
                    // Silenciar y continuar con el siguiente candidato
                }
            }
        } else {
            foreach (['google-chrome', 'google-chrome-stable', 'chromium', 'chromium-browser'] as $cmd) {
                try {
                    $result = Process::timeout(3)->run(['which', $cmd]);
                    if ($result->successful()) {
                        $firstLine = trim(explode("\n", trim($result->output()))[0]);
                        if (!empty($firstLine) && file_exists($firstLine)) {
                            return $firstLine;
                        }
                    }
                } catch (\Throwable) {
                    // Silenciar y continuar
                }
            }
        }

        return null;
    }

    /**
     * Convierte imágenes a data-URI base64 para que el HTML cargue sin problemas de rutas relativas o permisos locales.
     */
    public function embeberImagenesBase64(string $html): string
    {
        $logos = [
            'logos-usac-farusac.png' => public_path('images/InformePDF/logos-usac-farusac.png'),
            'LOGOS ACREDITADORAS 2026 HCERES (1).png' => public_path('images/InformePDF/LOGOS ACREDITADORAS 2026 HCERES (1).png'),
            'LOGOS ACREDITADORAS 2026 CCA (1).png' => public_path('images/InformePDF/LOGOS ACREDITADORAS 2026 CCA (1).png'),
            'LOGOS ACREDITADORAS 2026 CEAI (1).png' => public_path('images/InformePDF/LOGOS ACREDITADORAS 2026 CEAI (1).png'),
            'FarusacLogo.png' => public_path('images/FarusacLogo.png'),
        ];

        foreach ($logos as $filename => $fullPath) {
            if (file_exists($fullPath)) {
                $type = pathinfo($fullPath, PATHINFO_EXTENSION);
                $data = file_get_contents($fullPath);
                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

                $variants = [
                    asset('images/InformePDF/' . $filename),
                    url('images/InformePDF/' . $filename),
                    'images/InformePDF/' . $filename,
                    asset('images/InformePDF/' . rawurlencode($filename)),
                    url('images/InformePDF/' . rawurlencode($filename)),
                    'images/InformePDF/' . rawurlencode($filename),
                    asset('images/' . $filename),
                    url('images/' . $filename),
                    'images/' . $filename,
                ];

                foreach ($variants as $v) {
                    $html = str_replace($v, $base64, $html);
                }
            }
        }

        return $html;
    }

    /**
     * Renderiza una vista o cadena HTML en PDF vectorizado de alta fidelidad usando el motor nativo Chromium.
     * Sanitiza los argumentos y ejecuta el subproceso sin intermediación de shell para prevenir inyección de comandos.
     */
    public function renderHtmlToPdf(string $html): string
    {
        $chromiumPath = $this->getChromiumBinary();
        if (!$chromiumPath) {
            throw new Exception('No se encontró el ejecutable de Microsoft Edge o Google Chrome para renderizar el PDF. Puede configurar la variable CHROMIUM_PATH en su archivo .env.');
        }

        // Embeber imágenes institucionales en Base64
        $html = $this->embeberImagenesBase64($html);

        // Inyectar regla CSS para impresión exacta carta con margen 0
        $printOptimizationsCss = '<style>
            @page { size: letter portrait; margin: 0 !important; }
            html, body { margin: 0 !important; padding: 0 !important; width: 100% !important; max-width: 100% !important; background-color: #ffffff !important; overflow: visible !important; }
            .no-print-bar { display: none !important; }
            .document-page { width: 100% !important; max-width: 100% !important; margin: 0 !important; padding: 0 !important; border: none !important; box-shadow: none !important; border-radius: 0 !important; }
            .report-table-layout { width: 100% !important; max-width: 100% !important; table-layout: fixed !important; border-collapse: collapse !important; }
            .report-table-layout>thead { display: table-header-group !important; }
            .report-table-layout>tfoot { display: table-footer-group !important; }
            .report-table-layout>tbody { display: table-row-group !important; }
            .report-header-wrapper { padding: 10mm 15mm 0 15mm !important; width: 100% !important; box-sizing: border-box !important; }
            .report-body-wrapper { padding: 4mm 15mm 8mm 15mm !important; width: 100% !important; box-sizing: border-box !important; }
            .report-footer-spacer { display: block !important; height: 50px !important; width: 100% !important; visibility: hidden !important; }
            .report-footer-wrapper { position: fixed !important; bottom: 0 !important; left: 0 !important; right: 0 !important; width: 100% !important; padding: 0 !important; margin: 0 !important; z-index: 9999 !important; background-color: #ffffff !important; }
            .footer-gold-bar { height: 2.5px !important; background-color: #f3b228 !important; width: 100% !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            .footer-navy-bar { background-color: #0b2341 !important; color: #ffffff !important; padding: 5px 20px !important; width: 100% !important; box-sizing: border-box !important; display: flex !important; align-items: center !important; justify-content: space-between !important; flex-wrap: nowrap !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        </style>';
        $html = str_replace('</head>', $printOptimizationsCss . '</head>', $html);

        $tempHtml = tempnam(sys_get_temp_dir(), 'farusac_html_') . '.html';
        $tempPdf = tempnam(sys_get_temp_dir(), 'farusac_pdf_') . '.pdf';

        file_put_contents($tempHtml, $html);

        $flags = [
            '--headless',
            '--disable-gpu',
            '--allow-file-access-from-files',
            '--no-pdf-header-footer',
            '--run-all-compositor-stages-before-draw',
            '--virtual-time-budget=2500',
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-dev-shm-usage',
            "--print-to-pdf={$tempPdf}",
            $tempHtml,
        ];

        // Ejecución segura mediante array de argumentos: no invoca shell, previniendo inyección de comandos
        $command = array_merge([$chromiumPath], $flags);

        try {
            Process::timeout(60)->run($command);
        } catch (\Throwable $e) {
            // Si el runner de Process falla, intentar como fallback con argumentos sanitizados individualmente
            $escapedCmd = escapeshellcmd($chromiumPath) . ' ' . implode(' ', array_map('escapeshellarg', $flags));
            @exec($escapedCmd);
        }

        $pdfBinary = null;
        if (file_exists($tempPdf) && filesize($tempPdf) > 0) {
            $pdfBinary = file_get_contents($tempPdf);
        }

        @unlink($tempHtml);
        @unlink($tempPdf);

        if (!$pdfBinary) {
            throw new Exception('No se pudo generar el archivo PDF con el motor de Chromium.');
        }

        return $pdfBinary;
    }

    /**
     * Genera el binario PDF de un informe docente reutilizando la vista oficial institucional.
     */
    public function generarPdfDocente($informe, $user): string
    {
        $html = view('docente.ver_informe_pdf', [
            'user' => $user,
            'informe' => $informe,
        ])->render();

        return $this->renderHtmlToPdf($html);
    }

    /**
     * Genera el binario PDF de un informe de coordinación reutilizando la vista oficial institucional.
     */
    public function generarPdfCoordinacion($informe, $user): string
    {
        $html = view('coordinador.ver_informe_coordinacion', [
            'user' => $user,
            'informe' => $informe,
        ])->render();

        return $this->renderHtmlToPdf($html);
    }
}
