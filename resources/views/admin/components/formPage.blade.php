{{-- 
    Component: Admin Form Page Wrapper
    Usage: @include('admin.components.formPage', ['title' => 'Tiêu đề trang'])
--}}
<div class="adminFormPage">
    <div class="adminFormPage_content">
        @if(isset($title))
            <div class="adminFormPage_header">
                <h1 class="adminFormPage_title">{{ $title }}</h1>
            </div>
        @endif
        
        @if ($errors->any())
            <div class="adminFormPage_errors">
                <div class="adminFormPage_errors_icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                </div>
                <div class="adminFormPage_errors_content">
                    <h3>Có lỗi xảy ra:</h3>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

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

        <div class="adminFormPage_body">
            {{ $slot ?? '' }}
        </div>
    </div>
</div>

