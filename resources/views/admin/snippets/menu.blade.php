<!-- BEGIN: Main Menu - Modern Responsive -->
<aside class="adminDashboard_sidebar" id="adminDashboardSidebar">
    <div class="adminSidebar">
        <!-- Mobile Close Button -->
        <button class="adminSidebar_mobileClose" onclick="toggleAdminMobileMenu()" aria-label="Đóng menu">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 6L6 18M6 6l12 12"/>
            </svg>
        </button>

        <!-- Sidebar Header -->
        <div class="adminSidebar_header">
            {{-- Decorative Background Circles --}}
            <div class="adminSidebar_header_bg">
                <div class="adminSidebar_header_bg_circle adminSidebar_header_bg_circle--1"></div>
                <div class="adminSidebar_header_bg_circle adminSidebar_header_bg_circle--2"></div>
                <div class="adminSidebar_header_bg_circle adminSidebar_header_bg_circle--3"></div>
            </div>
            
            @php
                $user = auth()->user();
                $firstRoute = route('admin.account.profile');
                // Get trainer code if user is trainer
                $trainerCode = null;
                if ($user->hasRole('trainer') && !$user->hasRole('admin')) {
                    $trainer = \App\Models\Trainer::where('user_id', $user->id)->first();
                    if ($trainer && !empty($trainer->trainer_code)) {
                        $trainerCode = $trainer->trainer_code;
                    }
                }
            @endphp
            
            <a href="{{ $firstRoute }}" class="adminSidebar_header_link">
                <div class="adminSidebar_header_avatar">
                    @if($user->avatar)
                        <img src="{{ $user->avatar }}" alt="{{ $user->name }}" />
                    @else
                        <div class="adminSidebar_header_avatar_placeholder">
                            {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <div class="adminSidebar_header_info">
                    <div class="adminSidebar_header_info_name">{{ $user->name }}</div>
                    <div class="adminSidebar_header_info_email">{{ $user->email }}</div>
        </div>
            </a>
            
            @if(!empty($trainerCode))
                <div 
                    class="adminSidebar_header_trainerCode"
                    onclick="copyTrainerCode('{{ $trainerCode }}', this)"
                    data-tooltip="Nhấp để sao chép mã HLV"
                    title="Nhấp để sao chép mã HLV"
                >
                    <span class="adminSidebar_header_trainerCode_value">{{ $trainerCode }}</span>
                </div>
            @endif
    </div>

        <!-- Navigation -->
        <nav class="adminSidebar_nav">
            @php
                $menuSections = \App\Helpers\AdminMenuHelper::getMenuSections();
            @endphp
            
            @foreach($menuSections as $sectionKey => $section)
                <div class="adminSidebar_nav_section">
                    <div class="adminSidebar_nav_section_title">{{ $section['title'] }}</div>
                    
                    @php
                        $menuItems = \App\Helpers\AdminMenuHelper::getMenuItems($sectionKey);
                    @endphp
                    
                    @foreach($menuItems as $item)
                        <a href="{{ $item['url'] }}" 
                           class="adminSidebar_nav_item {{ $item['active'] ? 'active' : '' }}"
                           @if(!empty($item['onclick'])) onclick="{{ $item['onclick'] }}" @endif>
                            <div class="adminSidebar_nav_item_icon">
                                @if($item['svg'])
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        {!! $item['svg'] !!}
                                    </svg>
                                @endif
                            </div>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            @endforeach
        </nav>
    </div>
</aside>

<!-- Mobile Menu Trigger Button -->
<button class="adminDashboard_mobileMenuTrigger" id="adminMobileMenuTrigger" onclick="toggleAdminMobileMenu()">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="M4 6h16" class="adminDashboard_mobileMenuTrigger_line adminDashboard_mobileMenuTrigger_line--1"/>
        <path d="M4 12h16" class="adminDashboard_mobileMenuTrigger_line adminDashboard_mobileMenuTrigger_line--2"/>
        <path d="M4 18h16" class="adminDashboard_mobileMenuTrigger_line adminDashboard_mobileMenuTrigger_line--3"/>
    </svg>
</button>

<!-- END: Main Menu -->
