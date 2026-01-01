@extends('layouts.admin')

@section('content')
@include('admin.components.loadingOverlay', [
    'id' => 'qrcodeLoadingOverlay',
    'message' => 'Đang tải mã QR code...'
])
<div class="adminPersonnelPage">
    <div class="adminPersonnelPage_content">
        <div class="companyManagementPage_section companyManagementPage_section--tracked">
            <div class="companyManagementPage_section_header companyManagementPage_section_header--trainer">
                <div class="companyManagementPage_section_header_left">
                    <div class="companyManagementPage_section_header_iconWrapper companyManagementPage_section_header_iconWrapper--trainer">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3.375 4.5C2.339 4.5 1.5 5.34 1.5 6.375V13.5h12V6.375c0-1.036-.84-1.875-1.875-1.875h-8.25zM13.5 15h-12v2.625c0 1.035.84 1.875 1.875 1.875h8.25c1.035 0 1.875-.84 1.875-1.875V15zM1.5 19.125c0 1.035.84 1.875 1.875 1.875h8.25c1.035 0 1.875-.84 1.875-1.875v-4.5H1.5v4.5zM17.25 4.5c-1.036 0-1.875.84-1.875 1.875v15.75c0 1.035.84 1.875 1.875 1.875h2.625c1.035 0 1.875-.84 1.875-1.875V6.375c0-1.036-.84-1.875-1.875-1.875h-2.625zM22.5 10.5v-4.5c0-1.036-.84-1.875-1.875-1.875h-2.625c-1.036 0-1.875.84-1.875 1.875v4.5h6.375zM22.5 15h-6.375v4.5c0 1.035.84 1.875 1.875 1.875h2.625c1.035 0 1.875-.84 1.875-1.875V15z"/>
                        </svg>
                    </div>
                    <div class="companyManagementPage_section_header_info">
                        <h2 class="companyManagementPage_section_title">
                            Mã QR HLV
                        </h2>
                        <p class="companyManagementPage_section_desc">Xem và tải xuống mã QR code của từng huấn luyện viên</p>
                    </div>
                </div>
                <div class="companyManagementPage_section_header_right">
                    <div class="adminPersonnelPage_stats">
                        <div class="adminPersonnelPage_stats_item">
                            <span class="adminPersonnelPage_stats_label">Tổng số:</span>
                            <span class="adminPersonnelPage_stats_value">{{ $trainers->count() ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="companyManagementPage_section_body">
                <!-- Search & Filter Bar -->
                <form id="formSearch" method="get" action="{{ route('admin.qrcode.index') }}" class="adminPersonnelPage_searchBar">
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
                                placeholder="Tìm kiếm theo tên hoặc mã HLV..." 
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
                                    @if($trainers->count() > 0)
                                        <a href="{{ route('admin.qrcode.downloadAll', ['course' => $courseFilter ?? '', 'search' => $search ?? '']) }}" 
                                           class="adminButton adminButton--primary adminButton--sm qrcode-download-all-btn">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                                            </svg>
                                            <span>Tải QRcode (.zip)</span>
                                        </a>
                                    @endif
                                    <a href="{{ route('admin.qrcode.index') }}" class="adminButton adminButton--secondary adminButton--sm">
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

                <!-- QR Code List -->
                @if(!empty($courseFilter) || !empty($search))
                    @if($trainers->count() > 0)
                        <div class="adminQrCode_list">
                            @foreach($trainers as $trainer)
                                <div class="adminQrCode_listItem">
                                    <div class="adminQrCode_listItem_qr">
                                        <img src="{{ $trainer->qr_code_svg ?? '' }}" alt="QR Code" class="adminQrCode_listItem_qr_img">
                                    </div>
                                    <div class="adminQrCode_listItem_info">
                                        <div class="adminQrCode_listItem_header">
                                            <div class="adminQrCode_listItem_header_top">
                                                <h3 class="adminQrCode_listItem_name">{{ $trainer->name ?? 'N/A' }}</h3>
                                                @if(!empty($trainer->trainer_code))
                                                    <div class="adminQrCode_listItem_code">
                                                        <span class="adminQrCode_listItem_code_value">{{ $trainer->trainer_code }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="adminQrCode_listItem_details">
                                            <div class="adminQrCode_listItem_detail adminQrCode_listItem_detail--contact">
                                                @if(!empty($trainer->phone))
                                                    <div class="adminQrCode_listItem_contactItem">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                                        </svg>
                                                        <span>{{ $trainer->phone }}</span>
                                                    </div>
                                                @endif
                                                @if(!empty($trainer->email))
                                                    <div class="adminQrCode_listItem_contactItem">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                        </svg>
                                                        <span>{{ $trainer->email }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="adminQrCode_listItem_url">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/>
                                                </svg>
                                                <a href="{{ $trainer->qr_url ?? '#' }}" target="_blank" class="adminQrCode_listItem_url_link">
                                                    {{ $trainer->qr_url ?? 'N/A' }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="adminQrCode_listItem_actions">
                                        <a href="{{ route('admin.qrcode.download', ['id' => $trainer->id]) }}" 
                                           class="adminButton adminButton--primary adminButton--sm qrcode-download-btn"
                                           data-trainer-id="{{ $trainer->id }}">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                                            </svg>
                                            <span>Tải PNG</span>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="adminPersonnelPage_empty">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3.375 4.5C2.339 4.5 1.5 5.34 1.5 6.375V13.5h12V6.375c0-1.036-.84-1.875-1.875-1.875h-8.25zM13.5 15h-12v2.625c0 1.035.84 1.875 1.875 1.875h8.25c1.035 0 1.875-.84 1.875-1.875V15zM1.5 19.125c0 1.035.84 1.875 1.875 1.875h8.25c1.035 0 1.875-.84 1.875-1.875v-4.5H1.5v4.5zM17.25 4.5c-1.036 0-1.875.84-1.875 1.875v15.75c0 1.035.84 1.875 1.875 1.875h2.625c1.035 0 1.875-.84 1.875-1.875V6.375c0-1.036-.84-1.875-1.875-1.875h-2.625zM22.5 10.5v-4.5c0-1.036-.84-1.875-1.875-1.875h-2.625c-1.036 0-1.875.84-1.875 1.875v4.5h6.375zM22.5 15h-6.375v4.5c0 1.035.84 1.875 1.875 1.875h2.625c1.035 0 1.875-.84 1.875-1.875V15z"/>
                            </svg>
                            <p>Không tìm thấy HLV nào</p>
                        </div>
                    @endif
                @else
                    <div class="adminPersonnelPage_empty">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v12.75c0 .621.504 1.125 1.125 1.125h11.25c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/>
                        </svg>
                        <h3>Chưa có bộ lọc</h3>
                        <p>Vui lòng chọn khóa học hoặc nhập từ khóa tìm kiếm để xem mã QR code</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('scriptCustom')
<script>
(function() {
    'use strict';
    
    const formSearch = document.getElementById('formSearch');
    if (!formSearch) return;
    
    const searchInput = formSearch.querySelector('[name="search"]');
    const loadingId = 'qrcodeLoadingOverlay';
    
    // Show loading khi submit form
    formSearch.addEventListener('submit', function() {
        if (typeof showAdminLoading === 'function') {
            showAdminLoading(loadingId, 'Đang tải mã QR code...');
        }
    });
    
    // Loading khi download QR code (single)
    document.querySelectorAll('.qrcode-download-btn').forEach(function(btn) {
        btn.addEventListener('click', async function(e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            
            if (typeof showAdminLoading === 'function') {
                showAdminLoading(loadingId, 'Đang tạo mã QR code...');
            }
            
            try {
                // Dùng fetch để theo dõi tiến trình download
                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!response.ok) {
                    throw new Error('Không thể tải mã QR code');
                }
                
                // Lấy blob từ response
                const blob = await response.blob();
                
                // Tạo object URL và trigger download
                const blobUrl = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = blobUrl;
                
                // Lấy filename từ Content-Disposition header hoặc dùng tên mặc định
                const contentDisposition = response.headers.get('Content-Disposition');
                let filename = 'qrcode.png';
                if (contentDisposition) {
                    const filenameMatch = contentDisposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
                    if (filenameMatch && filenameMatch[1]) {
                        filename = filenameMatch[1].replace(/['"]/g, '');
                    }
                }
                
                link.download = filename;
                link.style.display = 'none';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                // Giải phóng object URL sau khi download
                setTimeout(function() {
                    window.URL.revokeObjectURL(blobUrl);
                }, 100);
                
                // Tắt loading sau khi download bắt đầu
                if (typeof hideAdminLoading === 'function') {
                    hideAdminLoading(loadingId);
                }
                
            } catch (error) {
                console.error('Lỗi khi tải mã QR code:', error);
                
                // Tắt loading và hiển thị thông báo lỗi
                if (typeof hideAdminLoading === 'function') {
                    hideAdminLoading(loadingId);
                }
                
                alert('Có lỗi xảy ra khi tải mã QR code. Vui lòng thử lại.');
            }
        });
    });
    
    // Loading khi download tất cả QR code
    const downloadAllBtn = document.querySelector('.qrcode-download-all-btn');
    if (downloadAllBtn) {
        downloadAllBtn.addEventListener('click', async function(e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            
            if (typeof showAdminLoading === 'function') {
                showAdminLoading(loadingId, 'Đang tạo file ZIP với tất cả mã QR code...\nVui lòng đợi, quá trình này có thể mất vài phút...');
            }
            
            try {
                // Dùng fetch để theo dõi tiến trình download
                // Fetch sẽ chờ đến khi server hoàn tất việc tạo ZIP
                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!response.ok) {
                    throw new Error('Không thể tải file ZIP');
                }
                
                // Lấy blob từ response
                const blob = await response.blob();
                
                // Tạo object URL và trigger download
                const blobUrl = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = blobUrl;
                
                // Lấy filename từ Content-Disposition header hoặc dùng tên mặc định
                const contentDisposition = response.headers.get('Content-Disposition');
                let filename = 'qrcode_trainers.zip';
                if (contentDisposition) {
                    const filenameMatch = contentDisposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
                    if (filenameMatch && filenameMatch[1]) {
                        filename = filenameMatch[1].replace(/['"]/g, '');
                    }
                }
                
                link.download = filename;
                link.style.display = 'none';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                // Giải phóng object URL sau khi download
                setTimeout(function() {
                    window.URL.revokeObjectURL(blobUrl);
                }, 100);
                
                // Tắt loading sau khi download bắt đầu
                if (typeof hideAdminLoading === 'function') {
                    hideAdminLoading(loadingId);
                }
                
            } catch (error) {
                console.error('Lỗi khi tải file ZIP:', error);
                
                // Tắt loading và hiển thị thông báo lỗi
                if (typeof hideAdminLoading === 'function') {
                    hideAdminLoading(loadingId);
                }
                
                alert('Có lỗi xảy ra khi tải file ZIP. Vui lòng thử lại.');
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
                showAdminLoading(loadingId, 'Đang tải mã QR code...');
            }
            formSearch.submit();
        }
        
        // Sử dụng setInterval để kiểm tra thay đổi (fallback)
        const checkInterval = setInterval(function() {
            const currentValue = hiddenInput.value || '';
            if (currentValue !== lastValue) {
                lastValue = currentValue;
                clearInterval(checkInterval);
                setTimeout(submitForm, 100);
            }
        }, 100);
        
        // Lắng nghe click trên option để submit ngay
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
        
        // MutationObserver để theo dõi thay đổi value
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
    
    // Khởi tạo khi DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            setupCourseSelectAutoSubmit();
            setupSearchAutoSubmit();
        });
    } else {
        // DOM đã sẵn sàng, nhưng đợi một chút để custom select được khởi tạo
        setTimeout(function() {
            setupCourseSelectAutoSubmit();
            setupSearchAutoSubmit();
        }, 300);
    }
})();
</script>
@endpush

