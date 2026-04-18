@extends('layouts.admin')

@section('content')
@include('admin.components.loadingOverlay', [
    'id' => 'athleteEmailLoadingOverlay',
    'message' => 'Đang gửi email...'
])
<div class="adminPersonnelPage">
    <div class="adminPersonnelPage_content">
        <div class="companyManagementPage_section companyManagementPage_section--tracked">
            <div class="companyManagementPage_section_header companyManagementPage_section_header--trainer">
                <div class="companyManagementPage_section_header_left">
                    <div class="companyManagementPage_section_header_iconWrapper companyManagementPage_section_header_iconWrapper--trainer">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                        </svg>
                    </div>
                    <div class="companyManagementPage_section_header_info">
                        <h2 class="companyManagementPage_section_title">
                            Gửi email tài khoản VĐV
                        </h2>
                        <p class="companyManagementPage_section_desc">Chọn vận động viên và gửi thông tin tài khoản đăng nhập qua email</p>
                    </div>
                </div>
                <div class="companyManagementPage_section_header_right">
                    <div class="adminPersonnelPage_stats">
                        <div class="adminPersonnelPage_stats_item">
                            <span class="adminPersonnelPage_stats_label">Tổng số:</span>
                            <span class="adminPersonnelPage_stats_value">{{ $athletes->count() ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="companyManagementPage_section_body">
                <!-- Search & Filter Bar -->
                <form id="formSearch" method="get" action="{{ route('admin.athleteEmail.index') }}" class="adminPersonnelPage_searchBar">
                    <div class="adminPersonnelPage_searchBar_row">
                        <!-- Search Input -->
                        <div class="adminPersonnelPage_searchBar_inputWrapper">
                            <svg class="adminPersonnelPage_searchBar_icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"/>
                                <path d="m21 21-4.35-4.35"/>
                            </svg>
                            <input 
                                type="text" 
                                class="adminPersonnelPage_searchBar_input" 
                                name="search" 
                                placeholder="Tìm kiếm theo tên, mã VĐV hoặc email..." 
                                value="{{ $search ?? '' }}"
                            >
                        </div>
                        
                        <!-- Filter & Actions -->
                        <div class="adminPersonnelPage_searchBar_controls">
                            <div class="adminPersonnelPage_searchBar_filter">
                                @php
                                    $courseOptions = [];
                                    foreach($courses ?? [] as $course) {
                                        $courseOptions[$course] = 'Khóa ' . $course;
                                    }
                                @endphp
                                @include('admin.components.formSelect', [
                                    'name' => 'course',
                                    'value' => $courseFilter ?? '',
                                    'options' => $courseOptions,
                                    'placeholder' => 'Chọn khóa học',
                                    'class' => 'adminPersonnelPage_searchBar_filterSelect'
                                ])
                            </div>
                            
                            <div class="adminPersonnelPage_searchBar_actions">
                                @if(!empty($courseFilter) || !empty($search))
                                    <a href="{{ route('admin.athleteEmail.index') }}" class="adminButton adminButton--secondary adminButton--sm">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        <span>Xóa bộ lọc</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Trainer List with Checkbox -->
                @if(!empty($courseFilter) || !empty($search))
                    @if($athletes->count() > 0)
                        <form id="formSendEmail" class="adminTrainerEmail_form">
                            @csrf
                            <div class="adminTrainerEmail_actions">
                                <div class="adminTrainerEmail_actions_left">
                                    <label class="adminCheckbox">
                                        <input type="checkbox" id="selectAll">
                                        <span class="adminCheckbox_label">Chọn tất cả</span>
                                    </label>
                                    <span class="adminTrainerEmail_selectedCount" id="selectedCount">Đã chọn: 0</span>
                                </div>
                                <div class="adminTrainerEmail_actions_right">
                                    <button type="button" id="sendSelectedBtn" class="adminButton adminButton--primary adminButton--sm" disabled>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                                        </svg>
                                        <span>Gửi email đã chọn</span>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="adminQrCode_list adminQrCode_list--email">
                                @foreach($athletes as $athlete)
                                    <div class="adminQrCode_listItem adminQrCode_listItem--email">
                                        <div class="adminQrCode_listItem_checkbox">
                                            <label class="adminCheckbox">
                                                <input type="checkbox" name="athlete_ids[]" value="{{ $athlete->id }}" class="athlete-checkbox">
                                                <span class="adminCheckbox_label"></span>
                                            </label>
                                        </div>
                                        <div class="adminQrCode_listItem_info">
                                            <div class="adminQrCode_listItem_header">
                                                <div class="adminQrCode_listItem_header_top">
                                                    <h3 class="adminQrCode_listItem_name">{{ $athlete->name ?? 'N/A' }}</h3>
                                                    @if(!empty($athlete->athlete_code))
                                                        <div class="adminQrCode_listItem_code">
                                                            <span class="adminQrCode_listItem_code_value">{{ $athlete->athlete_code }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="adminQrCode_listItem_details">
                                            <div class="adminQrCode_listItem_detail adminQrCode_listItem_detail--contact">
                                                @if(!empty($athlete->phone))
                                                    <div class="adminQrCode_listItem_contactItem">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                                        </svg>
                                                        <span>{{ $athlete->phone }}</span>
                                                    </div>
                                                @endif
                                                @if(!empty($athlete->email))
                                                    <div class="adminQrCode_listItem_contactItem">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                        </svg>
                                                        <span>{{ $athlete->email }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                                @if(!empty($athlete->profile_url))
                                                    <div class="adminQrCode_listItem_url">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/>
                                                        </svg>
                                                        <a href="{{ $athlete->profile_url }}" target="_blank" class="adminQrCode_listItem_url_link">
                                                            {{ $athlete->profile_url }}
                                                        </a>
                                                    </div>
                                                @endif
                                                @if(!empty($athlete->user))
                                                    <div class="adminQrCode_listItem_detail">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                                                        </svg>
                                                        <span>Tên đăng nhập: <strong>{{ $athlete->user->username }}</strong></span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="adminQrCode_listItem_actions">
                                                <button type="button" class="adminButton adminButton--secondary adminButton--sm js-athleteTestEmailBtn" data-athlete-id="{{ $athlete->id }}" data-athlete-name="{{ e($athlete->name ?? 'N/A') }}" title="Gửi email test thông tin tài khoản VĐV này đến địa chỉ tùy chọn">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                                                        <path d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                                                    </svg>
                                                    <span>Gửi email test</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </form>
                    @else
                        <div class="adminPersonnelPage_empty">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                            </svg>
                            <p>Không tìm thấy VĐV nào</p>
                        </div>
                    @endif
                @else
                    <div class="adminPersonnelPage_empty">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                        </svg>
                        <h3>Chưa có bộ lọc</h3>
                        <p>Vui lòng chọn khóa học hoặc nhập từ khóa tìm kiếm để xem danh sách VĐV</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('modal')
    @include('admin.modal.athleteTestEmail')
@endpush

@endsection

@push('scriptCustom')
<script>
(function() {
    'use strict';
    
    const formSearch = document.getElementById('formSearch');
    const formSendEmail = document.getElementById('formSendEmail');
    const selectAllCheckbox = document.getElementById('selectAll');
    const athleteCheckboxes = document.querySelectorAll('.athlete-checkbox');
    const sendSelectedBtn = document.getElementById('sendSelectedBtn');
    const selectedCount = document.getElementById('selectedCount');
    const loadingId = 'athleteEmailLoadingOverlay';
    
    // Update selected count
    function updateSelectedCount() {
        const checked = document.querySelectorAll('.athlete-checkbox:checked').length;
        selectedCount.textContent = `Đã chọn: ${checked}`;
        sendSelectedBtn.disabled = checked === 0;
    }
    
    // Select all checkbox
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            athleteCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectedCount();
        });
    }
    
    // Individual checkbox change
    athleteCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            // Update select all checkbox state
            if (selectAllCheckbox) {
                const allChecked = document.querySelectorAll('.athlete-checkbox:checked').length === athleteCheckboxes.length;
                selectAllCheckbox.checked = allChecked && athleteCheckboxes.length > 0;
                selectAllCheckbox.indeterminate = !allChecked && document.querySelectorAll('.athlete-checkbox:checked').length > 0;
            }
            updateSelectedCount();
        });
    });
    
    // Send email button
    if (sendSelectedBtn && formSendEmail) {
        sendSelectedBtn.addEventListener('click', async function(e) {
            e.preventDefault();
            
            const checkedIds = Array.from(document.querySelectorAll('.athlete-checkbox:checked'))
                .map(cb => cb.value);
            
            if (checkedIds.length === 0) {
                alert('Vui lòng chọn ít nhất một VĐV');
                return;
            }
            
            if (!confirm(`Bạn có chắc chắn muốn gửi email cho ${checkedIds.length} VĐV đã chọn?`)) {
                return;
            }
            
            // Show loading
            if (typeof showAdminLoading === 'function') {
                showAdminLoading(loadingId, 'Đang gửi email...', true);
            }
            
            sendSelectedBtn.disabled = true;
            sendSelectedBtn.innerHTML = '<span>Đang gửi...</span>';
            
            try {
                const formData = new FormData();
                formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}');
                checkedIds.forEach(id => {
                    formData.append('athlete_ids[]', id);
                });
                
                const response = await fetch('{{ route("admin.athleteEmail.sendEmails") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                // Hide loading sớm
                if (typeof hideAdminLoading === 'function') {
                    hideAdminLoading(loadingId);
                }

                const contentType = response.headers.get('Content-Type') || '';
                if (!contentType.includes('application/json')) {
                    const text = await response.text();
                    if (response.status === 419) {
                        alert('Phiên đăng nhập hết hạn. Vui lòng tải lại trang (F5) và thử lại.');
                    } else if (response.status >= 500) {
                        alert('Lỗi máy chủ. Vui lòng thử lại sau.');
                    } else {
                        alert('Phản hồi không hợp lệ. Vui lòng tải lại trang và thử lại.');
                    }
                    return;
                }

                const data = await response.json();

                if (data.success) {
                    alert(data.message);
                    athleteCheckboxes.forEach(cb => cb.checked = false);
                    if (selectAllCheckbox) selectAllCheckbox.checked = false;
                    updateSelectedCount();
                } else {
                    alert('Lỗi: ' + (data.message || 'Có lỗi xảy ra khi gửi email'));
                    if (data.errors && data.errors.length > 0) {
                        console.error('Errors:', data.errors);
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                if (typeof hideAdminLoading === 'function') {
                    hideAdminLoading(loadingId);
                }
                alert('Lỗi: ' + (error.message || 'Không thể kết nối. Vui lòng thử lại.'));
            } finally {
                sendSelectedBtn.disabled = false;
                sendSelectedBtn.innerHTML = `
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                    </svg>
                    <span>Gửi email đã chọn</span>
                `;
            }
        });
    }
    
    // Auto submit khi custom selectbox thay đổi
    function setupCourseSelectAutoSubmit() {
        const courseSelectContainer = document.querySelector('.adminPersonnelPage_searchBar_filterSelect .adminCustomSelect');
        if (!courseSelectContainer) return;
        
        const hiddenInput = courseSelectContainer.querySelector('input[type="hidden"][name="course"]');
        if (!hiddenInput) return;
        
        let lastValue = hiddenInput.value || '';
        
        function submitForm() {
            if (typeof showAdminLoading === 'function') {
                showAdminLoading(loadingId, 'Đang tải danh sách VĐV...');
            }
            formSearch.submit();
        }
        
        // Sử dụng setInterval để kiểm tra thay đổi
        const checkInterval = setInterval(function() {
            const currentValue = hiddenInput.value || '';
            if (currentValue !== lastValue) {
                lastValue = currentValue;
                clearInterval(checkInterval);
                setTimeout(submitForm, 100);
            }
        }, 100);
        
        // Lắng nghe click trên option
        const optionsContainer = courseSelectContainer.querySelector('.adminCustomSelect_options');
        if (optionsContainer) {
            optionsContainer.addEventListener('click', function(e) {
                const option = e.target.closest('.adminCustomSelect_option');
                if (option) {
                    setTimeout(() => {
                        const newValue = hiddenInput.value || '';
                        if (newValue !== lastValue) {
                            lastValue = newValue;
                            clearInterval(checkInterval);
                            submitForm();
                        }
                    }, 150);
                }
            });
        }
        
        // MutationObserver
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                    const currentValue = hiddenInput.value || '';
                    if (currentValue !== lastValue) {
                        lastValue = currentValue;
                        clearInterval(checkInterval);
                        setTimeout(submitForm, 100);
                    }
                }
            });
        });
        
        observer.observe(hiddenInput, {
            attributes: true,
            attributeFilter: ['value']
        });
    }
    
    // Debounce cho search input
    function setupSearchAutoSubmit() {
        const searchInput = formSearch.querySelector('[name="search"]');
        if (!searchInput) return;
        
        let searchTimeout;
        
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (typeof showAdminLoading === 'function') {
                    showAdminLoading(loadingId, 'Đang tìm kiếm...');
                }
                formSearch.submit();
            }, 500);
        });
        
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(searchTimeout);
                if (typeof showAdminLoading === 'function') {
                    showAdminLoading(loadingId, 'Đang tìm kiếm...');
                }
                formSearch.submit();
            }
        });
    }
    
    // Show loading khi submit form search
    if (formSearch) {
        formSearch.addEventListener('submit', function() {
            if (typeof showAdminLoading === 'function') {
                showAdminLoading(loadingId, 'Đang tải danh sách VĐV...');
            }
        });
    }
    
    // --- Modal gửi email test ---
    let athleteTestEmailModalAthleteId = null;

    function openAthleteTestEmailModal(athleteId, athleteName) {
        athleteTestEmailModalAthleteId = athleteId;
        const modal = document.getElementById('adminAthleteTestEmailModal');
        const label = document.getElementById('adminAthleteTestEmailModal_athleteLabel');
        const input = document.getElementById('adminAthleteTestEmailModal_emailInput');
        const errEl = document.getElementById('adminAthleteTestEmailModal_error');
        if (modal) {
            if (label) label.textContent = 'Gửi nội dung email thông tin tài khoản của VĐV \"' + (athleteName || '') + '\" đến địa chỉ bên dưới (gửi ngay, không qua hàng đợi).';
            if (input) { input.value = ''; input.removeAttribute('disabled'); }
            if (errEl) { errEl.style.display = 'none'; errEl.textContent = ''; }
            modal.classList.add('adminClearCacheModal--open');
            setTimeout(function() { if (input) input.focus(); }, 150);
        }
    }

    function closeAthleteTestEmailModal() {
        const modal = document.getElementById('adminAthleteTestEmailModal');
        const confirmBtn = document.getElementById('adminAthleteTestEmailModal_confirmBtn');
        const errEl = document.getElementById('adminAthleteTestEmailModal_error');
        if (modal) modal.classList.remove('adminClearCacheModal--open');
        athleteTestEmailModalAthleteId = null;
        if (confirmBtn) {
            confirmBtn.disabled = false;
            const text = confirmBtn.querySelector('.adminClearCacheModal_button_text');
            const loader = confirmBtn.querySelector('.adminClearCacheModal_button_loader');
            if (text) text.style.display = '';
            if (loader) loader.style.display = 'none';
        }
        if (errEl) { errEl.style.display = 'none'; errEl.textContent = ''; }
    }

    async function confirmSendAthleteTestEmail() {
        const input = document.getElementById('adminAthleteTestEmailModal_emailInput');
        const errEl = document.getElementById('adminAthleteTestEmailModal_error');
        const confirmBtn = document.getElementById('adminAthleteTestEmailModal_confirmBtn');
        const sendUrl = '{{ route("admin.athleteEmail.sendTest") }}';
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

        if (errEl) { errEl.style.display = 'none'; errEl.textContent = ''; }

        const email = input && input.value ? input.value.trim() : '';
        if (!email) {
            if (errEl) { errEl.textContent = 'Vui lòng nhập email nhận test.'; errEl.style.display = 'block'; }
            return;
        }
        const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRe.test(email)) {
            if (errEl) { errEl.textContent = 'Địa chỉ email không hợp lệ.'; errEl.style.display = 'block'; }
            return;
        }

        if (!athleteTestEmailModalAthleteId) {
            alert('Phiên thao tác hết hạn. Vui lòng đóng modal và chọn lại VĐV.');
            return;
        }

        if (confirmBtn) {
            confirmBtn.disabled = true;
            const text = confirmBtn.querySelector('.adminClearCacheModal_button_text');
            const loader = confirmBtn.querySelector('.adminClearCacheModal_button_loader');
            if (text) text.style.display = 'none';
            if (loader) loader.style.display = 'inline-flex';
        }
        if (input) input.setAttribute('disabled', 'disabled');

        try {
            const formData = new FormData();
            formData.append('_token', csrf);
            formData.append('athlete_id', athleteTestEmailModalAthleteId);
            formData.append('test_email', email);

            const response = await fetch(sendUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const contentType = response.headers.get('Content-Type') || '';
            if (!contentType.includes('application/json')) {
                if (response.status === 419) alert('Phiên đăng nhập hết hạn. Vui lòng tải lại trang và thử lại.');
                else alert('Lỗi máy chủ. Vui lòng thử lại sau.');
                return;
            }

            const data = await response.json();
            if (data.success) {
                alert(data.message);
                closeAthleteTestEmailModal();
            } else {
                if (errEl) { errEl.textContent = data.message || 'Có lỗi xảy ra.'; errEl.style.display = 'block'; }
                if (confirmBtn) confirmBtn.disabled = false;
                const text = confirmBtn ? confirmBtn.querySelector('.adminClearCacheModal_button_text') : null;
                const loader = confirmBtn ? confirmBtn.querySelector('.adminClearCacheModal_button_loader') : null;
                if (text) text.style.display = '';
                if (loader) loader.style.display = 'none';
                if (input) input.removeAttribute('disabled');
            }
        } catch (e) {
            console.error(e);
            alert('Lỗi kết nối. Vui lòng thử lại.');
            if (confirmBtn) confirmBtn.disabled = false;
            const text = confirmBtn ? confirmBtn.querySelector('.adminClearCacheModal_button_text') : null;
            const loader = confirmBtn ? confirmBtn.querySelector('.adminClearCacheModal_button_loader') : null;
            if (text) text.style.display = '';
            if (loader) loader.style.display = 'none';
            if (input) input.removeAttribute('disabled');
        }
    }

    window.openAthleteTestEmailModal = openAthleteTestEmailModal;
    window.closeAthleteTestEmailModal = closeAthleteTestEmailModal;
    window.confirmSendAthleteTestEmail = confirmSendAthleteTestEmail;

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.js-athleteTestEmailBtn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-athlete-id');
                const name = this.getAttribute('data-athlete-name') || '';
                if (id) openAthleteTestEmailModal(id, name);
            });
        });
        document.getElementById('adminAthleteTestEmailModal')?.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeAthleteTestEmailModal();
        });
    });

    // Initialize
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            setupCourseSelectAutoSubmit();
            setupSearchAutoSubmit();
            updateSelectedCount();
        });
    } else {
        setTimeout(function() {
            setupCourseSelectAutoSubmit();
            setupSearchAutoSubmit();
            updateSelectedCount();
        }, 300);
    }
})();
</script>
@endpush

