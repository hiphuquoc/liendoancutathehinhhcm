@extends('layouts.admin')

@section('content')
<div class="adminVideoAcademy">
    <div class="adminVideoAcademy_content">
        {{-- Hero / Header Academy --}}
        <header class="adminVideoAcademy_hero">
            <div class="adminVideoAcademy_hero_inner">
                <div class="adminVideoAcademy_hero_icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z"/>
                    </svg>
                </div>
                <div class="adminVideoAcademy_hero_text">
                    <h1 class="adminVideoAcademy_hero_title">Video Academy</h1>
                    <p class="adminVideoAcademy_hero_desc">Khóa học video dành cho Huấn luyện viên & Trọng tài</p>
                </div>
                @if(!empty($videos) && $videos->total() > 0)
                    <div class="adminVideoAcademy_hero_stat">
                        <span class="adminVideoAcademy_hero_stat_value">{{ $videos->total() }}</span>
                        <span class="adminVideoAcademy_hero_stat_label">bài học</span>
                    </div>
                @endif
            </div>
        </header>

        {{-- Search & Filter --}}
        <form method="get" action="{{ route('admin.videoAcademy.index') }}" class="adminVideoAcademy_toolbar">
            <div class="adminVideoAcademy_toolbar_search">
                <svg class="adminVideoAcademy_toolbar_search_icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
                <input type="text" name="search" class="adminVideoAcademy_toolbar_search_input" placeholder="Tìm kiếm video..." value="{{ $params['search'] ?? '' }}" />
            </div>
            @if(!empty($categories))
                <div class="adminVideoAcademy_toolbar_filter">
                    <select name="category" class="adminVideoAcademy_toolbar_filter_select" onchange="this.form.submit()">
                        <option value="">Tất cả danh mục</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ (isset($params['category']) && $params['category'] == $category) ? 'selected' : '' }}>{{ $category }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <button type="submit" class="adminButton adminButton--primary adminVideoAcademy_toolbar_btn">Tìm kiếm</button>
            @if(!empty($params['search']) || !empty($params['category']))
                <a href="{{ route('admin.videoAcademy.index') }}" class="adminButton adminButton--secondary adminVideoAcademy_toolbar_btn">Xóa bộ lọc</a>
            @endif
        </form>

        {{-- Danh sách video --}}
        @if(!empty($videos) && $videos->isNotEmpty())
            <section class="adminVideoAcademy_list">
                <div class="adminVideoAcademy_grid">
                    @foreach($videos as $video)
                        <a href="{{ route('admin.videoAcademy.show', ['id' => $video->id]) }}" class="adminVideoAcademy_card">
                            <div class="adminVideoAcademy_card_thumb">
                                @if(!empty($video->thumbnail_url))
                                    <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}" />
                                @else
                                    <div class="adminVideoAcademy_card_placeholder">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z"/>
                                        </svg>
                                    </div>
                                @endif
                                <div class="adminVideoAcademy_card_play">
                                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
                                </div>
                                @if(!empty($video->category))
                                    <span class="adminVideoAcademy_card_category">{{ $video->category }}</span>
                                @endif
                            </div>
                            <div class="adminVideoAcademy_card_body">
                                <h3 class="adminVideoAcademy_card_title">{{ $video->title }}</h3>
                                @if(!empty($video->description))
                                    <p class="adminVideoAcademy_card_desc">{{ Str::limit($video->description, 100) }}</p>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>

                @if($videos->hasPages())
                    <div class="adminVideoAcademy_pagination">
                        {{ $videos->appends(request()->query())->links('admin.template.paginate') }}
                    </div>
                @endif
            </section>
        @else
            <div class="adminVideoAcademy_empty">
                <div class="adminVideoAcademy_empty_icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z"/>
                    </svg>
                </div>
                <h3 class="adminVideoAcademy_empty_title">Chưa có video nào</h3>
                <p class="adminVideoAcademy_empty_text">Vui lòng thử lại với từ khóa hoặc bộ lọc khác.</p>
            </div>
        @endif
    </div>
</div>

@push('scriptCustom')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var searchInput = document.querySelector('.adminVideoAcademy_toolbar_search_input');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') { e.preventDefault(); this.closest('form').submit(); }
        });
    }
});
</script>
@endpush
@endsection
