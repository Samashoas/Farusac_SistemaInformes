<div id="toastNotification" 
     class="toast-notification" 
     role="alert" 
     aria-live="assertive"
     data-success="{{ session('success') }}"
     data-error="{{ session('error') }}"
     data-warning="{{ session('warning') }}"
     data-info="{{ session('info') }}">
    <div id="globalToastIcon" class="toast-icon"></div>
    <div id="globalToastMessage" class="toast-message"></div>
</div>
