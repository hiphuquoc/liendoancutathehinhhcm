{{-- 
    Component: Admin Loading Overlay (dùng chung)
    Usage: @include('admin.components.loadingOverlay', [
        'id' => 'loadingId', // optional, default: 'adminLoadingOverlay'
        'message' => 'Đang tải...' // optional
    ])
--}}
@php
    $loadingId = $id ?? 'adminLoadingOverlay';
    $message = $message ?? 'Đang tải dữ liệu...';
@endphp

<div id="{{ $loadingId }}" class="adminLoadingOverlay" style="display: none;">
    <div class="adminLoadingOverlay_backdrop"></div>
    <div class="adminLoadingOverlay_content">
        <div class="adminLoadingOverlay_spinner">
            <svg class="adminLoadingOverlay_spinner_svg" viewBox="0 0 50 50">
                <circle class="adminLoadingOverlay_spinner_path" cx="25" cy="25" r="20" fill="none" stroke-width="4"></circle>
            </svg>
        </div>
        <p class="adminLoadingOverlay_message">{{ $message }}</p>
        <div id="{{ $loadingId }}_progress" class="adminLoadingOverlay_progress" style="display: none;">
            <div class="adminLoadingOverlay_progress_bar">
                <div id="{{ $loadingId }}_progress_fill" class="adminLoadingOverlay_progress_fill"></div>
            </div>
            <p id="{{ $loadingId }}_progress_text" class="adminLoadingOverlay_progress_text"></p>
        </div>
    </div>
</div>

@pushonce('scriptCustom')
<script>
(function() {
    'use strict';
    
    // Helper functions để show/hide loading
    window.showAdminLoading = function(id, message, showProgress) {
        const loadingId = id || 'adminLoadingOverlay';
        const loading = document.getElementById(loadingId);
        if (loading) {
            if (message) {
                const messageEl = loading.querySelector('.adminLoadingOverlay_message');
                if (messageEl) {
                    messageEl.textContent = message;
                }
            }
            
            // Show/hide progress
            const progressContainer = document.getElementById(loadingId + '_progress');
            if (progressContainer) {
                progressContainer.style.display = showProgress ? 'block' : 'none';
                if (showProgress) {
                    const progressFill = document.getElementById(loadingId + '_progress_fill');
                    if (progressFill) {
                        progressFill.style.width = '0%';
                    }
                }
            }
            
            loading.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    };
    
    window.updateAdminLoadingProgress = function(id, percent, text) {
        const loadingId = id || 'adminLoadingOverlay';
        const progressFill = document.getElementById(loadingId + '_progress_fill');
        const progressText = document.getElementById(loadingId + '_progress_text');
        
        if (progressFill) {
            progressFill.style.width = percent + '%';
        }
        
        if (progressText && text) {
            progressText.textContent = text;
        }
    };
    
    window.hideAdminLoading = function(id) {
        const loadingId = id || 'adminLoadingOverlay';
        const loading = document.getElementById(loadingId);
        if (loading) {
            loading.style.display = 'none';
            document.body.style.overflow = '';
            
            // Reset progress
            const progressFill = document.getElementById(loadingId + '_progress_fill');
            const progressText = document.getElementById(loadingId + '_progress_text');
            if (progressFill) progressFill.style.width = '0%';
            if (progressText) progressText.textContent = '';
        }
    };
})();
</script>
@endpushonce

