@extends('layouts.admin')

@section('content')
<div class="academyWatch">
    <div class="academyWatch_container">
        {{-- Header: Breadcrumb --}}
        <nav class="academyWatch_breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('admin.videoAcademy.index') }}" class="academyWatch_breadcrumb_item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>Video Academy</span>
            </a>
            @if(!empty($video->category))
                <span class="academyWatch_breadcrumb_sep" aria-hidden="true">/</span>
                <span class="academyWatch_breadcrumb_item academyWatch_breadcrumb_item--current">{{ $video->category }}</span>
            @endif
            <span class="academyWatch_breadcrumb_sep" aria-hidden="true">/</span>
            <span class="academyWatch_breadcrumb_item academyWatch_breadcrumb_item--current academyWatch_breadcrumb_item--truncate" title="{{ $video->title }}">{{ Str::limit($video->title, 40) }}</span>
        </nav>

        <div class="academyWatch_layout">
            {{-- Cột chính --}}
            <main class="academyWatch_main">
                {{-- Player --}}
                <section class="academyWatch_playerSection">
                    <div class="academyWatch_playerWrapper">
                        @if(!empty($video->video_url))
                            <video
                                id="academyVideoPlayer"
                                class="academyWatch_video"
                                controls
                                controlsList="nodownload"
                                poster="{{ $video->thumbnail_url ?? '' }}"
                                preload="metadata"
                                playsinline
                            >
                                <source src="{{ $video->video_url }}" type="video/mp4">
                                <source src="{{ $video->video_url }}" type="video/webm">
                                Trình duyệt của bạn không hỗ trợ video HTML5.
                            </video>
                        @else
                            <div class="academyWatch_playerPlaceholder">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z"/>
                                </svg>
                                <p>Video không khả dụng</p>
                            </div>
                        @endif
                    </div>
                </section>

                {{-- Thông tin bài học --}}
                <section class="academyWatch_infoSection">
                    <div class="academyWatch_infoHeader">
                        <h1 class="academyWatch_infoTitle">{{ $video->title }}</h1>
                        <div class="academyWatch_infoBadges">
                            @if(!empty($video->category))
                                <span class="academyWatch_infoCategory">{{ $video->category }}</span>
                            @endif
                            <span class="academyWatch_infoDate">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                    <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                                </svg>
                                {{ $video->created_at->format('d/m/Y') }}
                            </span>
                        </div>
                    </div>
                    @if(!empty($video->description))
                        <div class="academyWatch_infoDescription">
                            <h3 class="academyWatch_infoDescriptionTitle">Mô tả</h3>
                            <div class="academyWatch_infoDescriptionContent">{!! nl2br(e($video->description)) !!}</div>
                        </div>
                    @endif
                </section>

                {{-- Điều hướng: Video trước / Tiếp theo --}}
                <nav class="academyWatch_nav" aria-label="Điều hướng video">
                    <div class="academyWatch_nav_inner">
                        @if($prevVideo)
                            <a href="{{ route('admin.videoAcademy.show', ['id' => $prevVideo->id]) }}" class="academyWatch_navBtn academyWatch_navBtn--prev">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                                <span>Video trước</span>
                                <span class="academyWatch_navBtn_title">{{ Str::limit($prevVideo->title, 30) }}</span>
                            </a>
                        @else
                            <span class="academyWatch_navBtn academyWatch_navBtn--disabled" aria-disabled="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                                <span>Video trước</span>
                            </span>
                        @endif

                        <a href="{{ route('admin.videoAcademy.index') }}" class="academyWatch_navBtn academyWatch_navBtn--list">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                            <span>Danh sách</span>
                        </a>

                        @if($nextVideo)
                            <a href="{{ route('admin.videoAcademy.show', ['id' => $nextVideo->id]) }}" class="academyWatch_navBtn academyWatch_navBtn--next">
                                <span>Video tiếp theo</span>
                                <span class="academyWatch_navBtn_title">{{ Str::limit($nextVideo->title, 30) }}</span>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                            </a>
                        @else
                            <span class="academyWatch_navBtn academyWatch_navBtn--disabled" aria-disabled="true">
                                <span>Video tiếp theo</span>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                            </span>
                        @endif
                    </div>
                </nav>
            </main>

            {{-- Sidebar: Danh sách bài học / Video liên quan --}}
            <aside class="academyWatch_sidebar">
                <div class="academyWatch_sidebar_header">
                    <h2 class="academyWatch_sidebar_title">Video liên quan</h2>
                    @if(!empty($video->category))
                        <span class="academyWatch_sidebar_subtitle">{{ $video->category }}</span>
                    @endif
                </div>
                @if($relatedVideos->isNotEmpty())
                    <ul class="academyWatch_sidebar_list" role="list">
                        @foreach($relatedVideos as $relatedVideo)
                            <li>
                                <a href="{{ route('admin.videoAcademy.show', ['id' => $relatedVideo->id]) }}" class="academyWatch_sidebar_item">
                                    <span class="academyWatch_sidebar_item_thumb">
                                        @if(!empty($relatedVideo->thumbnail_url))
                                            <img src="{{ $relatedVideo->thumbnail_url }}" alt="" loading="lazy"/>
                                        @else
                                            <span class="academyWatch_sidebar_item_placeholder">
                                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
                                            </span>
                                        @endif
                                        <span class="academyWatch_sidebar_item_play" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
                                        </span>
                                    </span>
                                    <span class="academyWatch_sidebar_item_body">
                                        <span class="academyWatch_sidebar_item_title">{{ $relatedVideo->title }}</span>
                                        @if(!empty($relatedVideo->category))
                                            <span class="academyWatch_sidebar_item_category">{{ $relatedVideo->category }}</span>
                                        @endif
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="academyWatch_sidebar_empty">Chưa có video liên quan.</p>
                @endif
            </aside>
        </div>
    </div>
</div>

@push('scriptCustom')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var player = document.getElementById('academyVideoPlayer');
    if (player) {
        document.addEventListener('keydown', function(e) {
            if (e.target.closest('input') || e.target.closest('textarea')) return;
            switch (e.key) {
                case ' ': e.preventDefault(); player.paused ? player.play() : player.pause(); break;
                case 'ArrowLeft': e.preventDefault(); player.currentTime = Math.max(0, player.currentTime - 10); break;
                case 'ArrowRight': e.preventDefault(); player.currentTime = Math.min(player.duration, player.currentTime + 10); break;
            }
        });
    }
});
</script>
@endpush
@endsection
