{{-- 
    Component: Admin Form Message (Flash Message)
    Usage: @include('admin.components.formMessage')
    
    This component displays flash messages from session and automatically removes them after display.
--}}
@if(session('message'))
    @php
        $message = session('message');
        $messageType = $message['type'] ?? 'info';
        $messageContent = $message['message'] ?? '';
    @endphp
    <div class="adminFormPage_message adminFormPage_message--{{ $messageType }}">
        <div class="adminFormPage_message_icon">
            @if($messageType === 'success')
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
            {!! $messageContent !!}
        </div>
    </div>
    @php
        // Remove message after displaying (flash message - only show once)
        session()->forget('message');
    @endphp
@endif

