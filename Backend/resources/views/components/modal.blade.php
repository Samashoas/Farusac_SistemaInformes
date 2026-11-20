@props([
    'id',
    'title' => '',
    'maxWidth' => '600px',
    'subtitle' => null,
])

<div class="custom-modal-overlay" id="{{ $id }}" onclick="if(event.target === this) closeModal('{{ $id }}')">
    <div class="custom-modal-container" style="max-width: {{ $maxWidth }};">
        <div class="modal-accent-bar"></div>
        <div class="modal-header-box">
            <div>
                <div class="modal-title-main">
                    @isset($icon)
                        {{ $icon }}
                    @endisset
                    <span>{{ $title }}</span>
                </div>
                @if($subtitle)
                    <div style="font-size: 12px; color: #64748b; margin-top: 2px;">{{ $subtitle }}</div>
                @endif
            </div>
            <button type="button" class="modal-close-btn" onclick="closeModal('{{ $id }}')" title="Cerrar (Esc)">✕</button>
        </div>

        <div class="modal-body-scroll">
            {{ $slot }}
        </div>

        @isset($footer)
            <div class="modal-footer-box">
                {{ $footer }}
            </div>
        @endisset
    </div>
</div>
