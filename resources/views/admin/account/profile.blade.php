@extends('layouts.admin')

@section('content')
<div class="adminAccountPage">
    <div class="adminAccountPage_content">
        <div class="companyManagementPage_section companyManagementPage_section--tracked">
            <div class="companyManagementPage_section_header companyManagementPage_section_header--profile">
                <div class="companyManagementPage_section_header_left">
                    <div class="companyManagementPage_section_header_iconWrapper companyManagementPage_section_header_iconWrapper--profile">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                        </svg>
                    </div>
                    <div class="companyManagementPage_section_header_info">
                        <h2 class="companyManagementPage_section_title">
                            Thông tin cá nhân
                        </h2>
                        <p class="companyManagementPage_section_desc">Cập nhật thông tin cá nhân để quản lý tài khoản của bạn</p>
                    </div>
                </div>
            </div>
            <div class="companyManagementPage_section_body">
                <!-- Validation Errors Banner -->
                @include('admin.components.formValidationErrors')

                <!-- Message -->
                @php
                    $sessionMessage = session('message');
                    $showMessage = !empty($sessionMessage);
                @endphp
                @if($showMessage)
                    <div class="adminFormPage_message adminFormPage_message--{{ $sessionMessage['type'] ?? 'info' }}">
                        <div class="adminFormPage_message_icon">
                            @if(($sessionMessage['type'] ?? 'info') === 'success')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                    <polyline points="22 4 12 14.01 9 11.01"/>
                                </svg>
                            @else
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="12" y1="8" x2="12" y2="12"/>
                                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                                </svg>
                            @endif
                        </div>
                        <div class="adminFormPage_message_content">
                            {!! $sessionMessage['message'] ?? '' !!}
                        </div>
                    </div>
                    @php
                        session()->forget('message'); // Clear the message after displaying
                    @endphp
                @endif

                <form id="formAction" class="adminAccountPage_form" method="POST" action="{{ route('admin.account.updateProfile') }}">
                    @csrf
                    
                    @include('admin.components.profileFormFields', [
                        'user' => $user,
                        'trainerCode' => $trainerCode ?? null,
                        'hideAddress' => false,
                        'formType' => 'account'
                    ])

                    <!-- Submit Button -->
                    <div class="adminAccountPage_actions">
                        <button type="submit" class="adminAccountPage_submit" id="profileSubmitBtn">
                            <span class="adminAccountPage_submit_text">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Cập nhật thông tin
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

@push('scriptCustom')
<script>
    (function() {
        'use strict';
    
    const form = document.getElementById('formAction');
    const submitBtn = document.getElementById('profileSubmitBtn');
    
    if (!form || !submitBtn) return;
    
    // Form submission
    form.addEventListener('submit', function(e) {
        submitBtn.disabled = true;
        submitBtn.querySelector('.adminAccountPage_submit_text').style.display = 'none';
        submitBtn.querySelector('.adminAccountPage_submit_loading').style.display = 'flex';
    });
    })();
    
    // Copy trainer code function
    function copyTrainerCode(code, element) {
        // element can be button or the entire code box
        var codeBox = element.classList.contains('adminPersonnelPage_card_code') ? element : element.closest('.adminPersonnelPage_card_code');
        if (!codeBox) codeBox = element;
        
        // Copy to clipboard
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(code).then(function() {
                // Show success state
                codeBox.classList.add('adminPersonnelPage_card_code--copied');
                codeBox.setAttribute('data-tooltip', 'Đã sao chép!');
                codeBox.setAttribute('title', 'Đã sao chép!');
                
                // Reset after 2 seconds
                setTimeout(function() {
                    codeBox.classList.remove('adminPersonnelPage_card_code--copied');
                    codeBox.setAttribute('data-tooltip', 'Nhấp để sao chép mã HLV');
                    codeBox.setAttribute('title', 'Nhấp để sao chép mã HLV');
                }, 2000);
            }).catch(function(err) {
                console.error('Failed to copy:', err);
                alert('Không thể sao chép mã số. Vui lòng thử lại hoặc sao chép thủ công.');
            });
        } else {
            // Fallback for older browsers
            var textArea = document.createElement("textarea");
            textArea.value = code;
            textArea.style.position = "fixed";
            textArea.style.left = "-999999px";
            textArea.style.top = "-999999px";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                document.execCommand('copy');
                codeBox.classList.add('adminPersonnelPage_card_code--copied');
                codeBox.setAttribute('data-tooltip', 'Đã sao chép!');
                codeBox.setAttribute('title', 'Đã sao chép!');
                setTimeout(function() {
                    codeBox.classList.remove('adminPersonnelPage_card_code--copied');
                    codeBox.setAttribute('data-tooltip', 'Nhấp để sao chép mã HLV');
                    codeBox.setAttribute('title', 'Nhấp để sao chép mã HLV');
                }, 2000);
            } catch (err) {
                console.error('Failed to copy:', err);
                alert('Không thể sao chép mã số. Vui lòng thử lại hoặc sao chép thủ công.');
            }
            document.body.removeChild(textArea);
        }
    }
</script>
@endpush
@endsection
