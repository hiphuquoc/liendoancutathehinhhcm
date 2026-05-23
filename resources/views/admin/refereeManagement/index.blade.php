@extends('layouts.admin')

@section('content')
@include('admin.components.loadingOverlay', [
    'id' => 'refereeManagementLoadingOverlay',
    'message' => 'Đang xử lý file Excel...'
])
<div class="adminPersonnelPage">
    <div class="adminPersonnelPage_content">
        <div class="companyManagementPage_section companyManagementPage_section--tracked">
            <div class="companyManagementPage_section_header companyManagementPage_section_header--trainer">
                <div class="companyManagementPage_section_header_left">
                    <div class="companyManagementPage_section_header_iconWrapper companyManagementPage_section_header_iconWrapper--trainer">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                        </svg>
                    </div>
                    <div class="companyManagementPage_section_header_info">
                        <h2 class="companyManagementPage_section_title">
                            Cập nhật Trọng tài từ Excel
                        </h2>
                        <p class="companyManagementPage_section_desc">Tải lên file Excel để tạo hàng loạt trọng tài và tài khoản tương ứng</p>
                    </div>
                </div>
            </div>
            <div class="companyManagementPage_section_body">
                <form id="refereeUploadForm" enctype="multipart/form-data" class="adminTrainerManagement_form">
                    @csrf
                    <div class="adminTrainerManagement_form_body">
                        @include('admin.components.formFileUpload', [
                            'name' => 'excel_file',
                            'label' => 'File Excel',
                            'required' => true,
                            'accept' => '.xlsx,.xls',
                            'tooltip' => 'Định dạng file: .xlsx hoặc .xls (tối đa 10MB). File Excel phải có cấu trúc: Cột 1 (STT), Cột 2 (Họ và Tên - BẮT BUỘC), Cột 3 (Ngày tháng năm sinh - tùy chọn), Cột 4 (Số CCCD - tùy chọn), Cột 5 (Phone - tùy chọn), Cột 6 (Email - BẮT BUỘC), Cột 7 (Địa chỉ - tùy chọn)'
                        ])
                        <div class="adminFormField_hint" style="margin-top: 0.5rem;">
                            <p style="font-size: 0.8125rem; color: var(--admin-gray-600); margin: 0.25rem 0;">Định dạng file: .xlsx hoặc .xls (tối đa 10MB)</p>
                            <p style="font-size: 0.8125rem; color: var(--admin-gray-600); margin: 0.25rem 0;">File Excel phải có cấu trúc: Cột 1 (STT), Cột 2 (Họ và Tên - <strong>BẮT BUỘC</strong>), Cột 3 (Ngày tháng năm sinh - tùy chọn), Cột 4 (Số CCCD - tùy chọn), Cột 5 (Phone - tùy chọn), Cột 6 (Email - <strong>BẮT BUỘC</strong>), Cột 7 (Địa chỉ - tùy chọn)</p>
                            <p style="font-size: 0.8125rem; color: var(--admin-danger); margin: 0.25rem 0; font-weight: 500;">⚠️ Lưu ý: Nếu thiếu Họ và Tên hoặc Email, dòng đó sẽ được đánh dấu là lỗi và bỏ qua.</p>
                        </div>
                    </div>

                    <div class="adminTrainerManagement_form_actions">
                        <button type="submit" class="adminButton adminButton--primary" id="submitBtn">
                            <span>Tải lên và xử lý</span>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                            </svg>
                        </button>
                    </div>
                </form>

                        <!-- Results Container (Gom progress và results) -->
                        <div id="resultsContainer" class="adminTrainerManagement_results" style="display: none;">
                            <!-- Header -->
                            <div class="adminTrainerManagement_results_header">
                                <h3 class="adminTrainerManagement_results_title">Kết quả xử lý</h3>
                                <div class="adminTrainerManagement_results_stats" id="resultsStats">
                                    <span id="progressStats"></span>
                                </div>
                            </div>
                            
                            <!-- Content -->
                            <div class="adminTrainerManagement_results_content">
                                <!-- Summary -->
                                <div id="resultsSummary"></div>
                                
                                <!-- Results List -->
                                <div id="resultsList" class="adminQrCode_list"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scriptCustom')
<script>
(function() {
    'use strict';
    
    // Đợi DOM load xong
    function initUploadForm() {
        const form = document.getElementById('refereeUploadForm');
        if (!form) {
            console.error('Form refereeUploadForm not found');
            return;
        }
        
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('Form submitted');
            
            const formData = new FormData(this);
            const submitBtn = document.getElementById('submitBtn');
            const resultsContainer = document.getElementById('resultsContainer');
            const progressStats = document.getElementById('progressStats');
            
            // Show loading overlay with progress
            if (typeof showAdminLoading === 'function') {
                showAdminLoading('refereeManagementLoadingOverlay', 'Đang xử lý file Excel...', true);
            }
            
            // Disable submit button
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span>Đang xử lý...</span>';
            
            // Hide form and show results container
            const uploadForm = document.getElementById('refereeUploadForm');
            if (uploadForm) {
                uploadForm.style.display = 'none';
            }
            
            // Show results container (must be shown before accessing child elements)
            if (resultsContainer) {
                resultsContainer.style.display = 'block';
            }
            
            // Reset stats (sẽ được cập nhật sau khi có kết quả)
            if (progressStats) progressStats.textContent = '';
            
            // Update loading progress
            if (typeof updateAdminLoadingProgress === 'function') {
                updateAdminLoadingProgress('refereeManagementLoadingOverlay', 10, 'Đang tải file lên server...');
            }
    
    try {
            const response = await fetch('{{ route("admin.refereeManagement.uploadExcel") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
                }
            });
            
            console.log('Response status:', response.status);
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error('Response error:', errorText);
                throw new Error('Lỗi từ server: ' + response.status);
            }
            
            // Update progress
            if (typeof updateAdminLoadingProgress === 'function') {
                updateAdminLoadingProgress('refereeManagementLoadingOverlay', 30, 'Đang đọc file Excel...');
            }
            
            // Simulate progress while processing
            let progressPercent = 30;
            const progressInterval = setInterval(() => {
                if (progressPercent < 90) {
                    progressPercent += 3;
                    if (typeof updateAdminLoadingProgress === 'function') {
                        updateAdminLoadingProgress('refereeManagementLoadingOverlay', progressPercent, 'Đang xử lý dữ liệu...');
                    }
                }
            }, 200);
            
            const data = await response.json();
            console.log('Response data:', data);
            
            clearInterval(progressInterval);
            
            // Update loading progress to 100%
            if (typeof updateAdminLoadingProgress === 'function') {
                updateAdminLoadingProgress('refereeManagementLoadingOverlay', 100, 'Hoàn thành!');
            }
            
            // Hide loading overlay after a short delay
            setTimeout(() => {
                if (typeof hideAdminLoading === 'function') {
                    hideAdminLoading('refereeManagementLoadingOverlay');
                }
            }, 500);
            
            // Update stats
            if (progressStats && data.results && data.results.length > 0) {
                    progressStats.textContent = `${data.success_count || 0} thành công / ${data.results.length} tổng`;
            }
            
            // Populate details tab - get elements after container is shown
            const resultsSummary = document.getElementById('resultsSummary');
            const resultsList = document.getElementById('resultsList');
            
            if (!resultsSummary || !resultsList) {
                console.error('Results containers not found', { resultsSummary, resultsList });
                return;
            }
            
            resultsSummary.innerHTML = '';
            resultsList.innerHTML = '';
            
            if (data.status) {
            // Summary
            const summaryHtml = `
                <div class="adminTrainerManagement_summary">
                    <div class="adminTrainerManagement_summary_header">
                        <svg class="adminTrainerManagement_summary_icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 6L9 17l-5-5"/>
                        </svg>
                        <h3 class="adminTrainerManagement_summary_title">${data.message}</h3>
                    </div>
                    <div class="adminTrainerManagement_summary_stats">
                        <div class="adminTrainerManagement_summary_stat adminTrainerManagement_summary_stat--success">
                            <span class="adminTrainerManagement_summary_stat_label">Thành công</span>
                            <span class="adminTrainerManagement_summary_stat_value">${data.success_count || 0}</span>
                        </div>
                        <div class="adminTrainerManagement_summary_stat adminTrainerManagement_summary_stat--duplicate">
                            <span class="adminTrainerManagement_summary_stat_label">Trùng</span>
                            <span class="adminTrainerManagement_summary_stat_value">${data.duplicate_count || 0}</span>
                        </div>
                        <div class="adminTrainerManagement_summary_stat adminTrainerManagement_summary_stat--error">
                            <span class="adminTrainerManagement_summary_stat_label">Lỗi</span>
                            <span class="adminTrainerManagement_summary_stat_value">${data.error_count || 0}</span>
                        </div>
                    </div>
                </div>
            `;
            if (resultsSummary) {
                resultsSummary.innerHTML = summaryHtml;
            }
            
            // Results list
            if (data.results && data.results.length > 0) {
                data.results.forEach(result => {
                    const item = document.createElement('div');
                    item.className = 'adminQrCode_listItem';
                    
                    let statusClass = '';
                    let statusIcon = '';
                    let statusText = '';
                    
                    if (result.status === 'success') {
                        statusClass = 'adminQrCode_listItem--success';
                        statusIcon = '<svg style="width: 16px; height: 16px; color: var(--admin-success); flex-shrink: 0;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>';
                        statusText = 'Thành công';
                    } else if (result.status === 'duplicate') {
                        statusClass = 'adminQrCode_listItem--duplicate';
                        statusIcon = '<svg style="width: 16px; height: 16px; color: var(--admin-warning); flex-shrink: 0;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
                        statusText = 'Trùng';
                    } else {
                        statusClass = 'adminQrCode_listItem--error';
                        statusIcon = '<svg style="width: 16px; height: 16px; color: var(--admin-danger); flex-shrink: 0;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg>';
                        statusText = 'Lỗi';
                    }
                    
                    item.classList.add(statusClass);
                    
                    let qrCodeHtml = '';
                    if (result.qr_code) {
                        qrCodeHtml = `
                            <div class="adminQrCode_listItem_qr">
                                <div class="adminQrCode_listItem_qr_img">
                                    ${result.qr_code}
                                </div>
                            </div>
                        `;
                    } else {
                        qrCodeHtml = `
                            <div class="adminQrCode_listItem_qr adminQrCode_listItem_qr--empty">
                                <div class="adminQrCode_listItem_qr_empty">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                        <line x1="9" y1="9" x2="15" y2="15"/>
                                        <line x1="15" y1="9" x2="9" y2="15"/>
                                    </svg>
                                    <p>Không có QR</p>
                                </div>
                            </div>
                        `;
                    }
                    
                    let detailsHtml = '';
                    if (result.status === 'success') {
                        detailsHtml = `
                            <div class="adminQrCode_listItem_details">
                                <div class="adminQrCode_listItem_nameRow">
                                    ${statusIcon}
                                    <h3 class="adminQrCode_listItem_name">${result.name}</h3>
                                    <span class="adminQrCode_listItem_statusText adminQrCode_listItem_statusText--${result.status}">${statusText}</span>
                                </div>
                                <div class="adminQrCode_listItem_detail adminQrCode_listItem_detail--contact">
                                    <div class="adminQrCode_listItem_contactItem">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    <span>${result.phone || 'N/A'}</span>
                                </div>
                                    <div class="adminQrCode_listItem_contactItem">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <span>${result.email || 'N/A'}</span>
                                </div>
                                </div>
                                ${result.url ? `
                                <div class="adminQrCode_listItem_url">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/>
                                    </svg>
                                    <a href="${result.url}" target="_blank" class="adminQrCode_listItem_url_link">
                                        ${result.url}
                                    </a>
                                </div>
                                ` : ''}
                            </div>
                        `;
                    } else if (result.status === 'duplicate') {
                        detailsHtml = `
                            <div class="adminQrCode_listItem_details">
                                <div class="adminQrCode_listItem_nameRow">
                                    ${statusIcon}
                                    <h3 class="adminQrCode_listItem_name">${result.name}</h3>
                                    <span class="adminQrCode_listItem_statusText adminQrCode_listItem_statusText--${result.status}">${statusText}</span>
                                </div>
                                <div class="adminQrCode_listItem_detail adminQrCode_listItem_detail--contact">
                                    <div class="adminQrCode_listItem_contactItem">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    <span>${result.phone || 'N/A'}</span>
                                </div>
                                    <div class="adminQrCode_listItem_contactItem">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <span>${result.email || 'N/A'}</span>
                                    </div>
                                </div>
                                <div class="adminQrCode_listItem_reason">
                                    <strong class="adminQrCode_listItem_reason_title">Lý do trùng:</strong>
                                    <ul class="adminQrCode_listItem_reason_list">
                                        ${result.reasons.map(r => `<li>${r}</li>`).join('')}
                                    </ul>
                                </div>
                            </div>
                        `;
                    } else {
                        // Hiển thị lỗi trong statusText
                        const errorMessage = result.error || 'Không xác định';
                        detailsHtml = `
                            <div class="adminQrCode_listItem_details">
                                <div class="adminQrCode_listItem_nameRow">
                                    ${statusIcon}
                                    <h3 class="adminQrCode_listItem_name">${result.name}</h3>
                                    <span class="adminQrCode_listItem_statusText adminQrCode_listItem_statusText--${result.status}" title="${errorMessage}">${statusText}: ${errorMessage}</span>
                                </div>
                                <div class="adminQrCode_listItem_detail adminQrCode_listItem_detail--contact">
                                    <div class="adminQrCode_listItem_contactItem">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    <span>${result.phone || 'N/A'}</span>
                                </div>
                                    <div class="adminQrCode_listItem_contactItem">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <span>${result.email || 'N/A'}</span>
                                </div>
                                </div>
                            </div>
                        `;
                    }
                    
                    item.innerHTML = qrCodeHtml + detailsHtml;
                    resultsList.appendChild(item);
                });
            }
        } else {
            if (resultsSummary) {
                resultsSummary.innerHTML = `
                    <div class="adminTrainerManagement_summary adminTrainerManagement_summary--error">
                        <div class="adminTrainerManagement_summary_header">
                            <svg class="adminTrainerManagement_summary_icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            <h3 class="adminTrainerManagement_summary_title">${data.message}</h3>
                        </div>
                    </div>
                `;
            }
        }
        
            } catch (error) {
                console.error('Error:', error);
                
                // Hide loading overlay
                if (typeof hideAdminLoading === 'function') {
                    hideAdminLoading('refereeManagementLoadingOverlay');
                }
                
                // Show results container
                resultsContainer.style.display = 'block';
                
                const resultsSummary = document.getElementById('resultsSummary');
                if (resultsSummary) {
                    resultsSummary.innerHTML = `
                        <div class="adminTrainerManagement_summary adminTrainerManagement_summary--error">
                            <div class="adminTrainerManagement_summary_header">
                                <svg class="adminTrainerManagement_summary_icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                <h3 class="adminTrainerManagement_summary_title">Lỗi: ${error.message}</h3>
                            </div>
                        </div>
                    `;
                }
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = `
                    <span>Tải lên và xử lý</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                    </svg>
                `;
            }
        });
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initUploadForm);
    } else {
        initUploadForm();
    }
})();
</script>
@endpush
@endsection

