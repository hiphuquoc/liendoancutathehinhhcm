@extends('layouts.admin')

@section('content')
@include('admin.components.loadingOverlay', [
    'id' => 'refereeQrcodeLoadingOverlay',
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
                            Mã QR Trọng tài
                        </h2>
                        <p class="companyManagementPage_section_desc">Xem và tải xuống mã QR code của từng trọng tài</p>
                    </div>
                </div>
                <div class="companyManagementPage_section_header_right">
                    <div class="adminPersonnelPage_stats">
                        <div class="adminPersonnelPage_stats_item">
                            <span class="adminPersonnelPage_stats_label">Tổng số:</span>
                            <span class="adminPersonnelPage_stats_value">{{ $referees->count() ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="companyManagementPage_section_body">
                <!-- Search & Filter Bar -->
                <form id="formSearch" method="get" action="{{ route('admin.refereeQrcode.index') }}" class="adminPersonnelPage_searchBar">
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
                                placeholder="Tìm kiếm theo tên trọng tài..." 
                                value="{{ $search ?? '' }}"
                            >
                        </div>
                        
                        <!-- Filter & Actions -->
                        <div class="adminPersonnelPage_searchBar_controls">
                            <div class="adminPersonnelPage_searchBar_actions">
                                @if($referees->count() > 0)
                                    <a href="{{ route('admin.refereeQrcode.downloadAll', ['search' => $search ?? '']) }}" 
                                       class="adminButton adminButton--primary adminButton--sm qrcode-download-all-btn">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                                        </svg>
                                        <span>Tải QRcode (.zip)</span>
                                    </a>
                                @endif
                                @if(!empty($search))
                                    <a href="{{ route('admin.refereeQrcode.index') }}" class="adminButton adminButton--secondary adminButton--sm">
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
                @if($referees->count() > 0)
                    <div class="adminQrCode_list">
                        @foreach($referees as $referee)
                            <div class="adminQrCode_listItem">
                                <div class="adminQrCode_listItem_qr">
                                    <img src="{{ $referee->qr_code_svg ?? '' }}" alt="QR Code" class="adminQrCode_listItem_qr_img">
                                </div>
                                <div class="adminQrCode_listItem_info">
                                    <div class="adminQrCode_listItem_header">
                                        <div class="adminQrCode_listItem_header_top">
                                            <h3 class="adminQrCode_listItem_name">{{ $referee->name ?? 'N/A' }}</h3>
                                        </div>
                                    </div>
                                    <div class="adminQrCode_listItem_details">
                                        <div class="adminQrCode_listItem_detail adminQrCode_listItem_detail--contact">
                                            @if(!empty($referee->phone))
                                                <div class="adminQrCode_listItem_contactItem">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                                    </svg>
                                                    <span>{{ $referee->phone }}</span>
                                                </div>
                                            @endif
                                        @if(!empty($referee->email))
                                                <div class="adminQrCode_listItem_contactItem">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                </svg>
                                                <span>{{ $referee->email }}</span>
                                            </div>
                                        @endif
                                            </div>
                                        <div class="adminQrCode_listItem_url">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/>
                                            </svg>
                                            <a href="{{ $referee->qr_url ?? '#' }}" target="_blank" class="adminQrCode_listItem_url_link">
                                                {{ $referee->qr_url ?? 'N/A' }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="adminQrCode_listItem_actions">
                                    <a href="{{ route('admin.refereeQrcode.download', ['id' => $referee->id]) }}" 
                                       class="adminButton adminButton--primary adminButton--sm qrcode-download-btn"
                                       data-referee-id="{{ $referee->id }}">
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
                        <p>Không có Trọng tài nào</p>
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
    const loadingId = 'refereeQrcodeLoadingOverlay';
    
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
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                // Clean up object URL
                window.URL.revokeObjectURL(blobUrl);
                
                if (typeof hideAdminLoading === 'function') {
                    hideAdminLoading(loadingId);
                }
            } catch (error) {
                console.error('Download error:', error);
                if (typeof hideAdminLoading === 'function') {
                    hideAdminLoading(loadingId);
                }
                alert('Lỗi khi tải mã QR code: ' + error.message);
            }
        });
    });
    
    // Loading khi download tất cả QR code
    const downloadAllBtn = document.querySelector('.qrcode-download-all-btn');
    if (downloadAllBtn) {
        downloadAllBtn.addEventListener('click', function(e) {
            if (typeof showAdminLoading === 'function') {
                showAdminLoading(loadingId, 'Đang tạo file ZIP...');
            }
            // Loading sẽ được ẩn khi file download xong (browser tự động xử lý)
            setTimeout(function() {
                if (typeof hideAdminLoading === 'function') {
                    hideAdminLoading(loadingId);
                }
            }, 3000); // Timeout sau 3 giây nếu chưa download xong
        });
    }
})();
</script>
@endpush

