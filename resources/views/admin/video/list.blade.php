@extends('layouts.admin')

@section('content')
<div class="adminContentPage">
    <div class="adminContentPage_content">
        <div class="companyManagementPage_section companyManagementPage_section--tracked">
            <div class="companyManagementPage_section_header companyManagementPage_section_header--video">
                <div class="companyManagementPage_section_header_left">
                    <div class="companyManagementPage_section_header_iconWrapper companyManagementPage_section_header_iconWrapper--video">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z"/>
                        </svg>
                    </div>
                    <div class="companyManagementPage_section_header_info">
                        <h2 class="companyManagementPage_section_title">
                            Danh sách Video
                        </h2>
                        <p class="companyManagementPage_section_desc">Quản lý video trong hệ thống</p>
                    </div>
                </div>
                <div class="companyManagementPage_section_header_right">
                    <div class="adminContentPage_stats">
                        <div class="adminContentPage_stats_item">
                            <span class="adminContentPage_stats_label">Tổng số:</span>
                            <span class="adminContentPage_stats_value">{{ $list->total() ?? 0 }}</span>
                        </div>
                        <div class="adminContentPage_stats_item adminContentPage_stats_viewPerPage">
                            <label class="adminContentPage_stats_viewPerPage_label">Hiển thị:</label>
                            <select class="adminContentPage_stats_viewPerPage_select" onchange="settingView('viewVideoInfo', this.value);">
                                @foreach(config('setting.admin_array_number_view') as $item)
                                    <option value="{{ $item }}" {{ $viewPerPage == $item ? 'selected' : '' }}>{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>
                        <a href="{{ route('admin.video.view', ['type' => 'create']) }}" class="adminButton adminButton--primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            <span>Thêm Video</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="companyManagementPage_section_body">
                <!-- Search & Filter Bar -->
                <form id="formSearch" method="get" action="{{ route('admin.video.list') }}" class="adminContentPage_searchBar adminContentPage_searchBar--withFilter">
                    <div class="adminContentPage_searchBar_grid">
                        <!-- Search Input -->
                        <div class="adminContentPage_searchBar_inputWrapper">
                            <svg class="adminContentPage_searchBar_icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"/>
                                <path d="m21 21-4.35-4.35"/>
                            </svg>
                            <input 
                                type="text" 
                                class="adminContentPage_searchBar_input" 
                                name="search_name" 
                                id="videoSearchInput"
                                placeholder="Tìm kiếm theo tiêu đề..." 
                                value="{{ $params['search_name'] ?? '' }}"
                            />
                        </div>
                        
                        <!-- Category Filter -->
                        @if(!empty($categories))
                            <div class="adminContentPage_searchBar_filter">
                                @php
                                    $categoryOptions = [];
                                    foreach($categories as $category) {
                                        $categoryOptions[$category] = $category;
                                    }
                                @endphp
                                @include('admin.components.formSelect', [
                                    'name' => 'category',
                                    'value' => $params['category'] ?? '',
                                    'options' => $categoryOptions,
                                    'placeholder' => 'Tất cả danh mục',
                                    'class' => 'adminContentPage_searchBar_filterSelect'
                                ])
                            </div>
                        @endif
                    </div>
                </form>

                <!-- Message -->
                @include('admin.components.formMessage')

                <!-- Cards Grid -->
                @if(!empty($list) && $list->isNotEmpty())
                    <div class="adminContentPage_grid">
                        @foreach($list as $item)
                            <div class="adminContentPage_card" id="oneItem-{{ $item->id }}">
                                <div class="adminContentPage_card_imageWrapper">
                                    @if(!empty($item->thumbnail_url))
                                        <img src="{{ $item->thumbnail_url }}" alt="{{ $item->title }}" />
                                    @else
                                        <div class="adminContentPage_card_placeholder">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="adminContentPage_card_status">
                                        @if($item->status)
                                            <span class="adminBadge adminBadge--success">Đang hiển thị</span>
                                        @else
                                            <span class="adminBadge adminBadge--secondary">Ẩn</span>
                                        @endif
                                    </div>
                                    <div class="adminContentPage_card_actions">
                                        @if(!empty($item->file_cloud))
                                            <a href="{{ route('admin.video.watch', ['id' => $item->id]) }}" class="adminContentPage_card_action" title="Xem video">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                    <circle cx="12" cy="12" r="3"/>
                                                </svg>
                                            </a>
                                        @endif
                                        <a href="{{ route('admin.video.view', ['id' => $item->id, 'type' => 'edit']) }}" class="adminContentPage_card_action" title="Chỉnh sửa">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                            </svg>
                                        </a>
                                        <button onclick="deleteItem({{ $item->id }})" class="adminContentPage_card_action adminContentPage_card_action--danger" title="Xóa">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <div class="adminContentPage_card_body">
                                    <h3 class="adminContentPage_card_title">{{ $item->title }}</h3>
                                    @if(!empty($item->category))
                                        <div class="adminContentPage_card_meta">
                                            <span class="adminContentPage_card_category">{{ $item->category }}</span>
                                        </div>
                                    @endif
                                    @if(!empty($item->description))
                                        <p class="adminContentPage_card_description">{{ Str::limit($item->description, 100) }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if($list->hasPages())
                        <div class="adminContentPage_pagination">
                            {{ $list->appends(request()->query())->links('admin.template.paginate') }}
                        </div>
                    @endif
                @else
                    <div class="adminContentPage_empty">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z"/>
                        </svg>
                        <h3>Chưa có video nào</h3>
                        <p>Bắt đầu bằng cách thêm video mới vào hệ thống</p>
                        <a href="{{ route('admin.video.view', ['type' => 'create']) }}" class="adminButton adminButton--primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            <span>Thêm Video</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scriptCustom')
<script>
function deleteItem(id) {
    if (!confirm('Bạn có chắc chắn muốn xóa video này không?')) {
        return;
    }
    
    window.location.href = '{{ route("admin.video.delete") }}?id=' + id;
}

// Auto submit khi custom selectbox thay đổi
function setupCategorySelectAutoSubmit() {
    const categorySelectContainer = document.querySelector('.adminContentPage_searchBar_filterSelect .adminCustomSelect');
    if (!categorySelectContainer) return;
    
    const hiddenInput = categorySelectContainer.querySelector('input[type="hidden"][name="category"]');
    if (!hiddenInput) return;
    
    let lastValue = hiddenInput.value || '';
    
    function submitForm() {
        document.getElementById('formSearch').submit();
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
    const optionsContainer = categorySelectContainer.querySelector('.adminCustomSelect_options');
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
    const searchInput = document.getElementById('videoSearchInput');
    if (!searchInput) return;
    
    let searchTimeout;
    
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            document.getElementById('formSearch').submit();
        }, 500); // 500ms debounce
    });
    
    // Submit on Enter
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            clearTimeout(searchTimeout);
            document.getElementById('formSearch').submit();
        }
    });
}

// Initialize khi DOM ready
document.addEventListener('DOMContentLoaded', function() {
    setupCategorySelectAutoSubmit();
    setupSearchAutoSubmit();
});
</script>
@endpush
@endsection

