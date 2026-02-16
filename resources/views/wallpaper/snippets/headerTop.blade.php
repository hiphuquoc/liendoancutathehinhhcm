@php
  // Retrieve menu and other data
  $dataMenu         = config('main_'.env('APP_NAME').'.menu_public');
  $slugCurrent      = \App\Helpers\Url::getSlugCurrent();
  $arraySlugCurrent = explode('/', $slugCurrent);

  // Retrieve Lv2 Categories (keeping existing logic)
  $categoriesLv2  = \App\Models\CategoryBlog::select('*')
                      ->join('seo', 'seo.id', '=', 'category_blog.seo_id')
                      ->whereHas('seos.infoSeo', function ($query) use ($language) {
                          $query->where('language', $language)
                              ->where('level', 2);
                      })
                      ->with(['seos.infoSeo' => function($query) use ($language) {
                          $query->where('language', $language);
                      }, 'seo', 'seos', 'blogs'])
                      ->orderBy('seo.ordering', 'DESC')
                      ->orderBy('seo.id', 'DESC')
                      ->get();
@endphp

<!-- Vite Style Loader for Header -->
@if(env('APP_ENV')=='local')
    @vite('resources/sources/main/global-header.scss')
@else
    @php
        $manifestH = json_decode(file_get_contents(public_path('build/manifest.json')), true);
        $cssHeader = $manifestH['resources/sources/main/global-header.scss']['file'] ?? '';
    @endphp
    @if(!empty($cssHeader) && file_exists(public_path('build/' . $cssHeader)))
        <style type="text/css">
            {!! file_get_contents(public_path('build/' . $cssHeader)) !!}
        </style>
    @endif
@endif

<header class="header-main" id="mainHeader">
    <div class="container">
        <div class="header-inner">
            <!-- Logo -->
            <a href="/" class="header-logo" aria-label="Trang chủ">
                <img src="https://liendoancutathehinhhcm.storage.googleapis.com/storage/images/logo-liendoancuta-1.webp" alt="Liên Đoàn Cử Tạ Thể Hình TP.HCM">
            </a>

            <!-- Desktop Navigation -->
            <nav class="desktop-nav">
                <ul class="nav-list">
                    @foreach($dataMenu as $itemMenu)
                        @php
                            $slugMenu = $itemMenu['slug'];
                            // Filter out contact and courses
                            if(in_array($slugMenu, ['lien-he', 'khoa-hoc'])) continue;
                            
                            $active   = in_array($slugMenu, $arraySlugCurrent) ? 'active' : '';
                            $hasSub   = ($slugMenu == 'tin-tuc' && !empty($categoriesLv2) && $categoriesLv2->isNotEmpty());
                        @endphp
                        
                        @if($slugMenu == '' || $slugMenu == 'trang-chu')
                            <li class="nav-item">
                                <a href="/" class="nav-link nav-icon-home {{ $active }}" aria-label="Trang chủ">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9.02 2.84005L3.63 7.04005C3.23 7.35005 3 7.86005 3 8.40005V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19V8.40005C21 7.86005 20.77 7.35005 20.37 7.04005L14.98 2.84005C13.25 1.49005 10.75 1.49005 9.02 2.84005Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M12 17V14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </a>
                            </li>
                            @continue
                        @endif

                        <li class="nav-item">
                            <a href="/{{ $slugMenu }}" class="nav-link {{ $active }}">
                                {{ $itemMenu['name'] }}
                                @if($hasSub) <i class="fa-solid fa-chevron-down"></i> @endif
                            </a>
                            
                            @if($hasSub)
                                <ul class="dropdown-menu">
                                    @foreach($categoriesLv2 as $cLv2)
                                        <li><a href="/{{ $cLv2->slug_full ?? '' }}">{{ $cLv2->title ?? '' }}</a></li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                </ul>
                
                <!-- CTA Button -->
                <div class="header-actions">
                     <a href="/lien-he" class="btn-cta">
                         <span>Liên hệ</span>
                         <div class="icon-circle">
                             <i class="fa-solid fa-arrow-right"></i>
                         </div>
                     </a>
                     
                     <!-- Mobile Toggle (Only visible on small screens) -->
                    <button class="mobile-toggle" id="mobileMenuToggle" aria-label="Toggle Navigation">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                </div>
            </nav>

            <!-- Mobile Toggle Wrapper (Visible only when desktop nav hidden) -->
            <div class="header-actions d-lg-none">
                <button class="mobile-toggle" id="mobileMenuToggleSmall" onclick="document.getElementById('mobileMenuToggle').click()" aria-label="Toggle Navigation">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
        </div>
    </div>
</header>

<!-- Mobile Menu Drawer -->
<div class="mobile-menu-overlay" id="mobileMenuOverlay" aria-hidden="true"></div>
<aside class="mobile-menu-drawer" id="mobileMenuDrawer" role="dialog" aria-modal="true" aria-label="Menu điều hướng" aria-hidden="true">
    <div class="drawer-header">
        <a href="/" class="drawer-logo" aria-label="Về trang chủ">
            <img src="https://liendoancutathehinhhcm.storage.googleapis.com/storage/images/logo-liendoancuta-1.webp" alt="Liên Đoàn Cử Tạ Thể Hình TP.HCM">
        </a>
        <button type="button" class="close-btn" id="mobileMenuClose" aria-label="Đóng menu">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>
    <nav class="drawer-body" aria-label="Menu chính">
        <p class="drawer-nav-label">Điều hướng</p>
        <ul class="mobile-nav-list" role="list">
            @foreach($dataMenu as $itemMenu)
                @php
                    $slugMenu = $itemMenu['slug'];
                    if(in_array($slugMenu, ['lien-he', 'khoa-hoc'])) continue;
                    $active   = in_array($slugMenu, $arraySlugCurrent) ? 'active' : '';
                    $hasSub   = ($slugMenu == 'tin-tuc' && !empty($categoriesLv2) && $categoriesLv2->isNotEmpty());
                @endphp
                
                @if($slugMenu == '' || $slugMenu == 'trang-chu')
                    <li class="mobile-nav-item">
                        <a href="/" class="mobile-nav-link {{ $active }}">
                            <span class="link-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                            </span>
                            <span class="link-text">Trang chủ</span>
                        </a>
                    </li>
                    @continue
                @endif

                <li class="mobile-nav-item {{ $hasSub ? 'has-submenu' : '' }}">
                   @if($hasSub)
                        <div class="mobile-nav-link-wrapper">
                            <a href="/{{ $slugMenu }}" class="mobile-nav-link {{ $active }}">
                                <span class="link-text">{{ $itemMenu['name'] }}</span>
                            </a>
                            <button type="button" class="submenu-toggle" aria-expanded="false" aria-controls="mobile-submenu-{{ $slugMenu }}" aria-label="Mở menu {{ $itemMenu['name'] }}">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </button>
                        </div>
                        <ul class="mobile-submenu" id="mobile-submenu-{{ $slugMenu }}" role="list">
                            @foreach($categoriesLv2 as $cLv2)
                                <li>
                                    <a href="/{{ $cLv2->slug_full ?? '' }}">
                                        <span class="dot"></span>
                                        {{ $cLv2->title ?? '' }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                   @else
                        <a href="/{{ $slugMenu }}" class="mobile-nav-link {{ $active }}">
                            <span class="link-text">{{ $itemMenu['name'] }}</span>
                        </a>
                   @endif
                </li>
            @endforeach
        </ul>
    </nav>
    <div class="drawer-footer">
        <div class="footer-contact-info">
            <p>Liên Đoàn Cử Tạ Thể Hình TP.HCM</p>
        </div>
        <a href="/lien-he" class="btn-drawer-cta">
            <span>Liên hệ ngay</span>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
        </a>
    </div>
</aside>

@push('scriptCustom')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sticky Header
        const header = document.getElementById('mainHeader');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });

        // Mobile Menu
        const toggleBtn = document.getElementById('mobileMenuToggle');
        const toggleBtnSmall = document.getElementById('mobileMenuToggleSmall'); /* Added for mobile view */
        const closeBtn = document.getElementById('mobileMenuClose');
        const overlay = document.getElementById('mobileMenuOverlay');
        const drawer = document.getElementById('mobileMenuDrawer');

        function openMenu() {
            drawer.classList.add('active');
            overlay.classList.add('active');
            drawer.setAttribute('aria-hidden', 'false');
            overlay.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }

        function closeMenu() {
            drawer.classList.remove('active');
            overlay.classList.remove('active');
            drawer.setAttribute('aria-hidden', 'true');
            overlay.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }

        if (toggleBtn) toggleBtn.addEventListener('click', openMenu);
        if (toggleBtnSmall) toggleBtnSmall.addEventListener('click', openMenu);
        if (closeBtn) closeBtn.addEventListener('click', closeMenu);
        if (overlay) overlay.addEventListener('click', closeMenu);

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && drawer.classList.contains('active')) closeMenu();
        });

        // Mobile Submenu Toggle
        const submenuToggles = document.querySelectorAll('.submenu-toggle');
        submenuToggles.forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                const parent = this.closest('.mobile-nav-item');
                const isOpen = parent.classList.toggle('open');
                this.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });
        });
    });
</script>
@endpush