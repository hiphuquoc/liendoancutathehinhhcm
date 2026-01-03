@extends('layouts.admin')

@section('content')
<div class="adminVideoAcademy">
    <div class="adminVideoAcademy_content">
        <div class="adminVideoAcademy_header">
            <div class="adminVideoAcademy_header_left">
                <div class="adminVideoAcademy_header_icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z"/>
                    </svg>
                </div>
                <div class="adminVideoAcademy_header_info">
                    <h1 class="adminVideoAcademy_title">Video Academy</h1>
                    <p class="adminVideoAcademy_desc">Khóa học trực tuyến dành cho Huấn luyện viên</p>
                </div>
            </div>
            <div class="adminVideoAcademy_header_stats">
                <div class="adminVideoAcademy_stat">
                    <span class="adminVideoAcademy_stat_value">{{ $videos->total() ?? 0 }}</span>
                    <span class="adminVideoAcademy_stat_label">Video</span>
                </div>
            </div>
        </div>

        <!-- Search & Filter Bar -->
        <form id="formSearch" method="get" action="{{ route('admin.videoAcademy.index') }}" class="adminVideoAcademy_searchBar">
            <div class="adminVideoAcademy_searchBar_inputWrapper">
                <svg class="adminVideoAcademy_searchBar_icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.35-4.35"/>
                </svg>
                <input 
                    type="text" 
                    class="adminVideoAcademy_searchBar_input" 
                    name="search" 
                    placeholder="Tìm kiếm video..." 
                    value="{{ $params['search'] ?? '' }}"
                />
            </div>
            @if(!empty($categories))
                <div class="adminVideoAcademy_searchBar_filter">
                    <select class="adminVideoAcademy_searchBar_filterSelect" name="category" onchange="this.form.submit()">
                        <option value="">Tất cả danh mục</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ (isset($params['category']) && $params['category'] == $category) ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
            <button type="submit" class="adminButton adminButton--primary adminButton--sm">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.35-4.35"/>
                </svg>
                <span>Tìm kiếm</span>
            </button>
            @if(!empty($params['search']) || !empty($params['category']))
                <a href="{{ route('admin.videoAcademy.index') }}" class="adminButton adminButton--secondary adminButton--sm">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <span>Xóa bộ lọc</span>
                </a>
            @endif
        </form>

        <!-- Videos Grid -->
        @if(!empty($videos) && $videos->isNotEmpty())
            <div class="adminVideoAcademy_grid">
                @foreach($videos as $video)
                    <a href="{{ route('admin.videoAcademy.show', ['id' => $video->id]) }}" class="adminVideoAcademy_card">
                        <div class="adminVideoAcademy_card_thumbnail">
                                    @if(!empty($video->thumbnail_url))
                                        <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}" />
                                    @else
                                        <div class="adminVideoAcademy_card_placeholder">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z"/>
                                            </svg>
                                        </div>
                                    @endif
                            <div class="adminVideoAcademy_card_playButton">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                            </div>
                            @if(!empty($video->category))
                                <div class="adminVideoAcademy_card_category">
                                    {{ $video->category }}
                                </div>
                            @endif
                        </div>
                        <div class="adminVideoAcademy_card_content">
                            <h3 class="adminVideoAcademy_card_title">{{ $video->title }}</h3>
                            @if(!empty($video->description))
                                <p class="adminVideoAcademy_card_description">{{ Str::limit($video->description, 120) }}</p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($videos->hasPages())
                <div class="adminVideoAcademy_pagination">
                    {{ $videos->appends(request()->query())->links('admin.template.paginate') }}
                </div>
            @endif
        @else
            <div class="adminVideoAcademy_empty">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z"/>
                </svg>
                <h3>Không tìm thấy video nào</h3>
                <p>Vui lòng thử lại với từ khóa hoặc bộ lọc khác</p>
            </div>
        @endif
    </div>
</div>

@push('scriptCustom')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('[name="search"]');
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

