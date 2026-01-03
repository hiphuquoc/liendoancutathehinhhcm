@extends('layouts.admin')

@section('content')
<div class="adminVideoAcademy">
    <div class="adminVideoAcademy_content">
        <!-- Back Button -->
        <div class="adminVideoAcademy_back">
            <a href="{{ route('admin.videoAcademy.index') }}" class="adminButton adminButton--secondary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                <span>Quay lại danh sách</span>
            </a>
        </div>

        <!-- Video Player Section -->
        <div class="adminVideoAcademy_playerSection">
            <div class="adminVideoAcademy_player">
                @if(!empty($video->video_url))
                    <video 
                        controls 
                        class="adminVideoAcademy_player_video"
                        poster="{{ $video->thumbnail_url ?? '' }}"
                        preload="metadata"
                    >
                        <source src="{{ $video->video_url }}" type="video/mp4">
                        <source src="{{ $video->video_url }}" type="video/webm">
                        <source src="{{ $video->video_url }}" type="video/quicktime">
                        Trình duyệt của bạn không hỗ trợ video HTML5.
                    </video>
                @else
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; color: #fff;">
                        <p>Video không khả dụng</p>
                    </div>
                @endif
            </div>

            <!-- Video Info -->
            <div class="adminVideoAcademy_info">
                <div class="adminVideoAcademy_info_header">
                    <h1 class="adminVideoAcademy_info_title">{{ $video->title }}</h1>
                    @if(!empty($video->category))
                        <span class="adminVideoAcademy_info_category">{{ $video->category }}</span>
                    @endif
                </div>
                @if(!empty($video->description))
                    <div class="adminVideoAcademy_info_description">
                        {!! nl2br(e($video->description)) !!}
                    </div>
                @endif
                <div class="adminVideoAcademy_info_meta">
                    <div class="adminVideoAcademy_info_metaItem">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        <span>{{ $video->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Videos -->
        @if($relatedVideos->isNotEmpty())
            <div class="adminVideoAcademy_related">
                <h2 class="adminVideoAcademy_related_title">Video liên quan</h2>
                <div class="adminVideoAcademy_related_grid">
                    @foreach($relatedVideos as $relatedVideo)
                        <a href="{{ route('admin.videoAcademy.show', ['id' => $relatedVideo->id]) }}" class="adminVideoAcademy_card adminVideoAcademy_card--small">
                            <div class="adminVideoAcademy_card_thumbnail">
                                @if(!empty($relatedVideo->thumbnail_url))
                                    <img src="{{ $relatedVideo->thumbnail_url }}" alt="{{ $relatedVideo->title }}" />
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
                            </div>
                            <div class="adminVideoAcademy_card_content">
                                <h3 class="adminVideoAcademy_card_title">{{ $relatedVideo->title }}</h3>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

