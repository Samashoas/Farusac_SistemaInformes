<!-- MODAL SUPERPUESTO DE VISUALIZACIÓN DE INFORME OFICIAL (LIGHTBOX IFRAME) -->
<div class="report-modal-overlay" id="reportPreviewModal" onclick="closeReportPreviewModal()">
    <div class="report-modal-window" onclick="event.stopPropagation()">
        <div class="report-modal-header">
            <div class="report-modal-header-left">
                <span class="report-badge-type report-badge-docente" id="previewModalBadge">Docente</span>
                <div>
                    <h3 class="report-modal-title" id="previewModalTitle">Cargando informe...</h3>
                    <p class="report-modal-subtitle" id="previewModalSubtitle">FARUSAC • Control Académico</p>
                </div>
            </div>
            <div class="report-modal-header-actions">
                <button type="button" class="report-btn-tool btn-tool-print" onclick="printReportIframe()"
                    title="Imprimir o Exportar PDF">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.2">
                        <polyline points="6 9 6 2 18 2 18 9"></polyline>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                        <rect x="6" y="14" width="12" height="8"></rect>
                    </svg>
                    <span>Imprimir / PDF</span>
                </button>

                <a href="#" id="previewModalFullTabBtn" target="_blank" class="report-btn-tool btn-tool-tab"
                    title="Abrir en pestaña completa del navegador">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.2">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                        <polyline points="15 3 21 3 21 9"></polyline>
                        <line x1="10" y1="14" x2="21" y2="3"></line>
                    </svg>
                    <span>Pestaña</span>
                </a>

                <button type="button" class="report-btn-close" onclick="closeReportPreviewModal()"
                    title="Cerrar ventana (Esc)">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
        </div>

        <div class="report-modal-body">
            <div class="report-modal-loader" id="previewIframeLoader">
                <div class="report-loader-spinner"></div>
                <span class="report-loader-text">Cargando informe institucional oficial...</span>
            </div>
            <iframe id="reportPreviewIframe" class="report-preview-iframe" src="about:blank"
                onload="if (typeof window.hidePreviewLoader === 'function') window.hidePreviewLoader();"></iframe>
        </div>
    </div>
</div>
