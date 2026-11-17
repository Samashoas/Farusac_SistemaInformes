@props([
    'id',
    'title' => 'Confirmar Acción',
    'message' => '¿Está seguro de que desea realizar esta acción?',
    'confirmText' => 'Confirmar',
    'cancelText' => 'Cancelar',
    'type' => 'danger' // danger, primary, warning
])

<div class="custom-modal-overlay" id="{{ $id }}" onclick="if(event.target === this) closeModal('{{ $id }}')">
    <div class="custom-modal-container" style="max-width: 450px;">
        <div class="modal-accent-bar" style="background: {{ $type === 'danger' ? '#ef4444' : ($type === 'warning' ? '#f59e0b' : 'var(--color-azul)') }};"></div>
        <div class="modal-header-box">
            <div class="modal-title-main" style="color: {{ $type === 'danger' ? '#b91c1c' : '#1e293b' }};">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <span>{{ $title }}</span>
            </div>
            <button type="button" class="modal-close-btn" onclick="closeModal('{{ $id }}')">✕</button>
        </div>
        <div style="padding: 20px 25px; font-size: 14px; color: #475569; line-height: 1.6;">
            <p id="{{ $id }}Message">{{ $message }}</p>
        </div>
        <div class="modal-footer-box">
            <button type="button" class="btn-cancel" onclick="closeModal('{{ $id }}')">{{ $cancelText }}</button>
            <button type="button" class="btn-confirm {{ $type }}" id="{{ $id }}ConfirmBtn">{{ $confirmText }}</button>
        </div>
    </div>
</div>
