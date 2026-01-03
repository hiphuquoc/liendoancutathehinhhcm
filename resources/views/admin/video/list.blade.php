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
                <form id="formSearch" method="get" action="{{ route('admin.video.list') }}" class="adminContentPage_searchBar">
                    <div class="adminContentPage_searchBar_row">
                        <div class="adminContentPage_searchBar_inputWrapper">
                            <svg class="adminContentPage_searchBar_icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"/>
                                <path d="m21 21-4.35-4.35"/>
                            </svg>
                            <input 
                                type="text" 
                                class="adminContentPage_searchBar_input" 
                                name="search_name" 
                                placeholder="Tìm kiếm theo tiêu đề..." 
                                value="{{ $params['search_name'] ?? '' }}"
                            />
                        </div>
                        <div class="adminContentPage_searchBar_controls">
                            @if(!empty($categories))
                                <div class="adminContentPage_searchBar_filter">
                                    <select class="adminContentPage_searchBar_filterSelect" name="category" onchange="this.form.submit()">
                                        <option value="">Tất cả danh mục</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category }}" {{ (isset($params['category']) && $params['category'] == $category) ? 'selected' : '' }}>
                                                {{ $category }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <div class="adminContentPage_searchBar_actions">
                                <button type="submit" class="adminButton adminButton--primary adminButton--sm">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="11" cy="11" r="8"/>
                                        <path d="m21 21-4.35-4.35"/>
                                    </svg>
                                    <span>Tìm kiếm</span>
                                </button>
                                @if(!empty($params['search_name']) || !empty($params['category']))
                                    <a href="{{ route('admin.video.list') }}" class="adminButton adminButton--secondary adminButton--sm">
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

                <!-- Message -->
                @if(session('message'))
                    <div class="adminFormPage_message adminFormPage_message--{{ session('message')['type'] ?? 'info' }}">
                        <div class="adminFormPage_message_icon">
                            @if((session('message')['type'] ?? 'info') === 'success')
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
                            {!! session('message')['message'] ?? '' !!}
                        </div>
                    </div>
                @endif

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

// Auto submit search form on enter
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('[name="search_name"]');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('formSearch').submit();
            }
        });
    }
});
</script>
@endpush
@endsection

