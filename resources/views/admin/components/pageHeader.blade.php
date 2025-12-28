{{-- 
    Page Header Component - Unified Design for Admin
    Usage: @include('admin.components.pageHeader', [
        'title' => 'Tiêu đề trang',
        'desc' => 'Mô tả',
        'icon' => '<path d="..."/>',
        'backUrl' => route('...'),
        'backText' => 'Quay lại',
        'actionUrl' => route('...'),
        'actionText' => 'Hành động',
        'meta' => ['Meta 1', 'Meta 2']
    ])
--}}
@php
    $backText = $backText ?? 'Quay lại';
    $showIcon = $showIcon ?? true;
    $actionIcon = $actionIcon ?? '<path d="M12 4.5v15m7.5-7.5h-15"/>';
@endphp

<div class="adminPageHeader {{ !empty($backUrl) ? 'adminPageHeader--withBack' : '' }}">
    @if(!empty($backUrl))
    <a href="{{ $backUrl }}" class="adminPageHeader_back">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
        <span>{{ $backText }}</span>
    </a>
    @endif
    
    <div class="adminPageHeader_main">
        <div class="adminPageHeader_left">
            @if($showIcon && !empty($icon))
            <div class="adminPageHeader_iconWrapper">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    {!! $icon !!}
                </svg>
            </div>
            @endif
            <div class="adminPageHeader_info">
                <h1 class="adminPageHeader_title">{{ $title }}</h1>
                @if(!empty($desc))
                <p class="adminPageHeader_desc">{!! $desc !!}</p>
                @endif
                @if(!empty($meta) && is_array($meta))
                <div class="adminPageHeader_meta">
                    @foreach($meta as $item)
                        @if(is_array($item))
                            @if(!empty($item['badge']))
                            <span class="adminPageHeader_badge {{ $item['badgeClass'] ?? 'adminPageHeader_badge--success' }}">{{ $item['text'] }}</span>
                            @else
                            <span>{!! $item['text'] !!}</span>
                            @endif
                        @else
                            <span>{!! $item !!}</span>
                        @endif
                    @endforeach
                </div>
                @endif
            </div>
        </div>
        
        @if(!empty($actionUrl) && !empty($actionText))
        <a href="{{ $actionUrl }}" class="adminPageHeader_action">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                {!! $actionIcon !!}
            </svg>
            <span>{{ $actionText }}</span>
        </a>
        @endif
    </div>
</div>

