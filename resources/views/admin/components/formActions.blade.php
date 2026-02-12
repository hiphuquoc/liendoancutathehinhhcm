{{-- 
    Component: Admin Form Actions (Submit, Cancel buttons)
    Usage: @include('admin.components.formActions', ['backRoute' => 'admin.document.list'])
--}}
<div class="adminFormActions">
    <div class="adminFormActions_primary">
        <button type="submit" form="formAction" id="adminFormSubmitBtn" class="adminFormActions_button adminFormActions_button--primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                <polyline points="17 21 17 13 7 13 7 21"/>
                <polyline points="7 3 7 8 15 8"/>
            </svg>
            <span>Lưu thay đổi</span>
        </button>
    </div>
    
    <div class="adminFormActions_secondary">
        @if(isset($backRoute))
            <a href="{{ route($backRoute) }}" class="adminFormActions_button adminFormActions_button--secondary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                <span>Quay lại</span>
            </a>
        @endif
        
        @if(isset($viewUrl) && $viewUrl)
            <a href="{{ $viewUrl }}" target="_blank" class="adminFormActions_button adminFormActions_button--view">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
                <span>Xem trang</span>
            </a>
        @endif
    </div>

    @if(isset($showIndexGoogle) && $showIndexGoogle)
        <div class="adminFormActions_options">
            <div class="adminFormActions_checkbox">
                <input type="checkbox" id="index_google" name="index_google" class="adminFormActions_checkbox_input" />
                <label for="index_google" class="adminFormActions_checkbox_label">
                    <!-- <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                    </svg> -->
                    Báo Google index
                </label>
            </div>
        </div>
    @endif
</div>

{{-- Mobile Bottom Bar (Fixed at bottom on mobile) --}}
<div class="adminFormActions_mobile">
    <div class="adminFormActions_mobile_container">
        @if(isset($viewUrl) && $viewUrl)
            <a href="{{ $viewUrl }}" target="_blank" class="adminFormActions_mobile_button adminFormActions_mobile_button--view" title="Xem trang">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
            </a>
        @endif
        
        <div class="adminFormActions_mobile_right">
            @if(isset($backRoute))
                <a href="{{ route($backRoute) }}" class="adminFormActions_mobile_button adminFormActions_mobile_button--back">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                    <span>Quay lại</span>
                </a>
            @endif
            
            <button type="submit" form="formAction" id="adminFormSubmitBtnMobile" class="adminFormActions_mobile_button adminFormActions_mobile_button--primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/>
                    <polyline points="7 3 7 8 15 8"/>
                </svg>
                <span>Lưu</span>
            </button>
        </div>
    </div>
</div>

@push('scriptCustom')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const submitBtn = document.getElementById('adminFormSubmitBtn');
    const submitBtnMobile = document.getElementById('adminFormSubmitBtnMobile');
    const form = document.getElementById('formAction');
    
    function handleSubmit(e) {
        // Check if form is valid (HTML5 validation)
        if (form.checkValidity && !form.checkValidity()) {
            // Find invalid fields
            const invalidFields = Array.from(form.querySelectorAll(':invalid'));
            
            // Show validation errors using validation component
            if (window.adminFormValidation && window.adminFormValidation.show) {
                window.adminFormValidation.show(invalidFields);
            } else {
                // Fallback to browser default
                form.reportValidity();
                
                // Scroll to first invalid field
                if (invalidFields.length > 0) {
                    invalidFields[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                    invalidFields[0].focus();
                }
            }
            
            e.preventDefault();
            return false;
        }
        
        // Hide validation errors if form is valid
        if (window.adminFormValidation && window.adminFormValidation.hide) {
            window.adminFormValidation.hide();
        }
    }
    
    if (submitBtn && form) {
        // Handle desktop button click
        submitBtn.addEventListener('click', handleSubmit);
    }
    
    if (submitBtnMobile && form) {
        // Handle mobile button click
        submitBtnMobile.addEventListener('click', handleSubmit);
    }
    
    if (form) {
        // Monitor form submit event
        form.addEventListener('submit', function(e) {
            // Show loading state if needed
            if (submitBtn) {
                submitBtn.disabled = true;
                const span = submitBtn.querySelector('span');
                if (span) {
                    span.textContent = 'Đang xử lý...';
                }
            }
            if (submitBtnMobile) {
                submitBtnMobile.disabled = true;
            }
        });
    }
});
</script>
@endpush

