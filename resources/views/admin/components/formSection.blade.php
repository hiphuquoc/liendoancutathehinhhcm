{{-- 
    Component: Admin Form Section
    Usage: @include('admin.components.formSection', ['title' => 'Tiêu đề section', 'icon' => '...'])
--}}
<div class="adminFormSection">
    <div class="adminFormSection_header">
        @if(isset($icon))
            <div class="adminFormSection_header_icon">
                {!! $icon !!}
            </div>
        @endif
        <div class="adminFormSection_header_info">
            <h2 class="adminFormSection_title">{{ $title ?? 'Thông tin' }}</h2>
            @if(isset($description))
                <p class="adminFormSection_description">{{ $description }}</p>
            @endif
        </div>
    </div>
    <div class="adminFormSection_body">
        {{ $slot ?? '' }}
    </div>
</div>

