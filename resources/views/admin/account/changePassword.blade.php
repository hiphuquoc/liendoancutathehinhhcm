@extends('layouts.admin')

@section('content')
<div class="adminAccountPage">
    <div class="adminAccountPage_content">
        <div class="companyManagementPage_section companyManagementPage_section--tracked">
            <div class="companyManagementPage_section_header companyManagementPage_section_header--changePassword">
                <div class="companyManagementPage_section_header_left">
                    <div class="companyManagementPage_section_header_iconWrapper companyManagementPage_section_header_iconWrapper--changePassword">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                        </svg>
                    </div>
                    <div class="companyManagementPage_section_header_info">
                        <h2 class="companyManagementPage_section_title">
                            Đổi mật khẩu
                        </h2>
                        <p class="companyManagementPage_section_desc">Cập nhật mật khẩu để bảo vệ tài khoản của bạn</p>
                    </div>
                </div>
            </div>
            <div class="companyManagementPage_section_body">
                <div id="changePasswordError" class="adminAccountPage_error" style="display: none;">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm-1.72 6.97a.75.75 0 10-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 101.06 1.06L12 13.06l1.72 1.72a.75.75 0 101.06-1.06L13.06 12l1.72-1.72a.75.75 0 10-1.06-1.06L12 10.94l-1.72-1.72z" clip-rule="evenodd"/>
                    </svg>
                    <span id="changePasswordErrorText"></span>
        </div>

                <div id="changePasswordSuccess" class="adminAccountPage_success" style="display: none;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Đổi mật khẩu thành công!</span>
    </div>
    
    @if ($errors->any())
                <div class="adminAccountPage_error">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm-1.72 6.97a.75.75 0 10-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 101.06 1.06L12 13.06l1.72 1.72a.75.75 0 101.06-1.06L13.06 12l1.72-1.72a.75.75 0 10-1.06-1.06L12 10.94l-1.72-1.72z" clip-rule="evenodd"/>
                    </svg>
                    <ul style="list-style: none; padding: 0; margin: 0;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

                <form id="formChangePassword" class="adminAccountPage_form" method="POST" action="{{ route('admin.account.updatePassword') }}">
                    @csrf
                    
                    <!-- Current Password -->
                    <div class="adminAccountPage_field">
                        <label for="currentPassword" class="adminAccountPage_label">
                    Mật khẩu hiện tại
                            <span class="adminAccountPage_label_required">*</span>
                </label>
                        <div class="adminAccountPage_inputWrapper">
                            <svg class="adminAccountPage_inputIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                            </svg>
                    <input 
                        type="password" 
                                id="currentPassword" 
                        name="current_password" 
                                class="adminAccountPage_input" 
                        placeholder="Nhập mật khẩu hiện tại"
                        required
                                autocomplete="current-password"
                            />
                            <button type="button" class="adminAccountPage_passwordToggle" onclick="togglePasswordVisibility('currentPassword', this)" aria-label="Hiển thị mật khẩu">
                                <svg class="adminAccountPage_passwordToggle_icon--hide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                <svg class="adminAccountPage_passwordToggle_icon--show" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: none;">
                                    <path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/>
                                    <path d="M1 1l22 22"/>
                                </svg>
                    </button>
                </div>
                        <div class="adminAccountPage_fieldError" id="currentPasswordError" style="display: none;"></div>
            </div>
            
                    <!-- New Password -->
                    <div class="adminAccountPage_field">
                        <label for="newPassword" class="adminAccountPage_label">
                            Mật khẩu mới
                            <span class="adminAccountPage_label_required">*</span>
                        </label>
                        <div class="adminAccountPage_inputWrapper">
                            <svg class="adminAccountPage_inputIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                            </svg>
                            <input 
                                type="password" 
                                id="newPassword" 
                                name="password" 
                                class="adminAccountPage_input" 
                                placeholder="Nhập mật khẩu mới (tối thiểu 6 ký tự)"
                                required
                                minlength="6"
                                autocomplete="new-password"
                            />
                            <button type="button" class="adminAccountPage_passwordToggle" onclick="togglePasswordVisibility('newPassword', this)" aria-label="Hiển thị mật khẩu">
                                <svg class="adminAccountPage_passwordToggle_icon--hide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                <svg class="adminAccountPage_passwordToggle_icon--show" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: none;">
                                    <path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/>
                                    <path d="M1 1l22 22"/>
                                </svg>
                            </button>
                        </div>
                        <div class="adminAccountPage_fieldError" id="newPasswordError" style="display: none;"></div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="adminAccountPage_field">
                        <label for="confirmPassword" class="adminAccountPage_label">
                            Xác nhận mật khẩu mới
                            <span class="adminAccountPage_label_required">*</span>
                        </label>
                        <div class="adminAccountPage_inputWrapper">
                            <svg class="adminAccountPage_inputIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                            </svg>
                            <input 
                                type="password" 
                                id="confirmPassword" 
                                name="password_confirmation" 
                                class="adminAccountPage_input" 
                                placeholder="Nhập lại mật khẩu mới"
                                required
                                minlength="6"
                                autocomplete="new-password"
                            />
                            <button type="button" class="adminAccountPage_passwordToggle" onclick="togglePasswordVisibility('confirmPassword', this)" aria-label="Hiển thị mật khẩu">
                                <svg class="adminAccountPage_passwordToggle_icon--hide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                <svg class="adminAccountPage_passwordToggle_icon--show" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: none;">
                                    <path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/>
                                    <path d="M1 1l22 22"/>
                                </svg>
                            </button>
                        </div>
                        <div class="adminAccountPage_fieldError" id="confirmPasswordError" style="display: none;"></div>
                        </div>

                    <!-- Submit Button -->
                    <div class="adminAccountPage_actions">
                        <button type="submit" class="adminAccountPage_submit" id="changePasswordSubmitBtn">
                            <span class="adminAccountPage_submit_text">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Cập nhật mật khẩu
                            </span>
                            <span class="adminAccountPage_submit_loading" style="display: none;">
                                <svg class="adminAccountPage_spinner" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10" stroke-opacity="0.25"/>
                                    <path d="M12 2a10 10 0 0 1 10 10" stroke-linecap="round"/>
                                </svg>
                                Đang xử lý...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function togglePasswordVisibility(inputId, button) {
        const input = document.getElementById(inputId);
    const hideIcon = button.querySelector('.adminAccountPage_passwordToggle_icon--hide');
    const showIcon = button.querySelector('.adminAccountPage_passwordToggle_icon--show');
        
        if (input.type === 'password') {
            input.type = 'text';
        hideIcon.style.display = 'none';
        showIcon.style.display = 'block';
        button.setAttribute('aria-label', 'Ẩn mật khẩu');
        } else {
            input.type = 'password';
        hideIcon.style.display = 'block';
        showIcon.style.display = 'none';
        button.setAttribute('aria-label', 'Hiển thị mật khẩu');
    }
}

    (function() {
        'use strict';
    
    const form = document.getElementById('formChangePassword');
    if (!form) return;
    
    const currentPasswordInput = document.getElementById('currentPassword');
    const newPasswordInput = document.getElementById('newPassword');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    const submitBtn = document.getElementById('changePasswordSubmitBtn');
    const errorContainer = document.getElementById('changePasswordError');
    const errorText = document.getElementById('changePasswordErrorText');
    const successContainer = document.getElementById('changePasswordSuccess');

    function setSubmitLoading(isLoading) {
        submitBtn.disabled = isLoading;
        submitBtn.querySelector('.adminAccountPage_submit_text').style.display = isLoading ? 'none' : 'flex';
        submitBtn.querySelector('.adminAccountPage_submit_loading').style.display = isLoading ? 'flex' : 'none';
    }
    
    function showError(message) {
        errorText.textContent = message;
        errorContainer.style.display = 'flex';
        successContainer.style.display = 'none';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    
    function hideError() {
        errorContainer.style.display = 'none';
    }
    
    function showSuccess() {
        successContainer.style.display = 'flex';
        errorContainer.style.display = 'none';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    
    function showFieldError(fieldId, message) {
        const errorEl = document.getElementById(fieldId + 'Error');
        if (errorEl) {
            errorEl.textContent = message;
            errorEl.style.display = 'block';
        }
    }
    
    function hideFieldError(fieldId) {
        const errorEl = document.getElementById(fieldId + 'Error');
        if (errorEl) {
            errorEl.style.display = 'none';
        }
    }
    
    // Real-time validation
    newPasswordInput.addEventListener('input', function() {
        if (this.value.length > 0 && this.value.length < 6) {
            showFieldError('newPassword', 'Mật khẩu phải có ít nhất 6 ký tự');
        } else {
            hideFieldError('newPassword');
        }
        
        if (confirmPasswordInput.value.length > 0) {
            if (this.value !== confirmPasswordInput.value) {
                showFieldError('confirmPassword', 'Mật khẩu xác nhận không khớp');
            } else {
                hideFieldError('confirmPassword');
            }
        }
    });
    
    confirmPasswordInput.addEventListener('input', function() {
        if (this.value !== newPasswordInput.value) {
            showFieldError('confirmPassword', 'Mật khẩu xác nhận không khớp');
        } else {
            hideFieldError('confirmPassword');
        }
    });
    
    // Form submission
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        hideError();
        hideFieldError('currentPassword');
        hideFieldError('newPassword');
        hideFieldError('confirmPassword');
        
        // Validate
        if (!currentPasswordInput.value) {
            showError('Vui lòng nhập mật khẩu hiện tại');
            showFieldError('currentPassword', 'Vui lòng nhập mật khẩu hiện tại');
            return;
        }
        
        if (!newPasswordInput.value) {
            showError('Vui lòng nhập mật khẩu mới');
            showFieldError('newPassword', 'Vui lòng nhập mật khẩu mới');
            return;
            }
        
        if (newPasswordInput.value.length < 6) {
            showError('Mật khẩu mới phải có ít nhất 6 ký tự');
            showFieldError('newPassword', 'Mật khẩu mới phải có ít nhất 6 ký tự');
            return;
            }
        
        if (newPasswordInput.value !== confirmPasswordInput.value) {
            showError('Mật khẩu xác nhận không khớp');
            showFieldError('confirmPassword', 'Mật khẩu xác nhận không khớp');
            return;
            }
        
        // Disable submit button and show loading
        setSubmitLoading(true);
        
        const formData = new FormData(form);
        
        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
            });
            
            const contentType = response.headers.get('content-type') || '';
            if (!contentType.includes('application/json')) {
                throw new Error('Invalid server response');
            }

            const data = await response.json();
            
            if (data.success) {
                showSuccess();
                form.reset();
                setSubmitLoading(false);
                
                setTimeout(() => {
                    successContainer.style.display = 'none';
                }, 3000);
            } else {
                showError(data.message || 'Có lỗi xảy ra khi đổi mật khẩu');
                
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        const fieldId = field === 'current_password' ? 'currentPassword' : 
                                       field === 'password' ? 'newPassword' : 
                                       field === 'password_confirmation' ? 'confirmPassword' : field;
                        showFieldError(fieldId, data.errors[field][0]);
                    });
                }
                
                setSubmitLoading(false);
            }
        } catch (error) {
            console.error('Error:', error);
            showError('Có lỗi xảy ra. Vui lòng thử lại sau.');
            setSubmitLoading(false);
        }
    });
    })();
</script>
@endsection
