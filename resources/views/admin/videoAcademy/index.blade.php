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
                        <div class="adminVideoAcademy_card">
                            <a href="{{ route('admin.videoAcademy.show', ['id' => $video->id]) }}" class="adminVideoAcademy_card_link">
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
                                    @if(auth()->user()->hasRole('admin'))
                                        <div class="adminVideoAcademy_card_actions">
                                            <a href="{{ route('admin.videoAcademy.view', ['id' => $video->id, 'type' => 'edit']) }}" class="adminVideoAcademy_card_action" title="Chỉnh sửa" onclick="event.preventDefault(); event.stopPropagation(); window.location.href='{{ route('admin.videoAcademy.view', ['id' => $video->id, 'type' => 'edit']) }}';">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                </svg>
                                            </a>
                                            <a href="{{ route('admin.videoAcademy.view', ['id' => $video->id, 'type' => 'copy']) }}" class="adminVideoAcademy_card_action" title="Sao chép" onclick="event.preventDefault(); event.stopPropagation(); window.location.href='{{ route('admin.videoAcademy.view', ['id' => $video->id, 'type' => 'copy']) }}';">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
                                                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                                                </svg>
                                            </a>
                                            <button onclick="event.preventDefault(); event.stopPropagation(); deleteVideoItem({{ $video->id }});" class="adminVideoAcademy_card_action adminVideoAcademy_card_action--danger" title="Xóa">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
                                                </svg>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                <div class="adminVideoAcademy_card_body">
                                    <h3 class="adminVideoAcademy_card_title">{{ $video->title }}</h3>
                                    @if(!empty($video->description))
                                        <p class="adminVideoAcademy_card_desc">{{ Str::limit($video->description, 100) }}</p>
                                    @endif
                                </div>
                            </a>
                        </div>
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

@push('styleCustom')
<style>
.adminVideoAcademy_card_link {
    display: block;
    text-decoration: none;
    color: inherit;
}
</style>
@endpush

@push('scriptCustom')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var searchInput = document.querySelector('.adminVideoAcademy_toolbar_search_input');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') { e.preventDefault(); this.closest('form').submit(); }
        });
    }

    // Ensure hover actions work
    const cards = document.querySelectorAll('.adminVideoAcademy_card');
    cards.forEach(function(card) {
        const actions = card.querySelector('.adminVideoAcademy_card_actions');
        if (!actions) return;
        
        card.addEventListener('mouseenter', function() {
            actions.style.opacity = '1';
            actions.style.visibility = 'visible';
        });
        
        card.addEventListener('mouseleave', function() {
            actions.style.opacity = '0';
            actions.style.visibility = 'hidden';
        });
    });
});

function deleteVideoItem(id) {
    if(confirm('{{ config("admin.alert.confirmRemove") }}')) {
        $.ajax({
            url: "{{ route('admin.videoAcademy.delete') }}",
            type: "get",
            dataType: "html",
            data: { id: id }
        }).done(function(data) {
            if(data == true) {
                location.reload();
            } else {
                alert('Có lỗi xảy ra khi xóa video');
            }
        });
    }
}
</script>
@endpush
@endsection
