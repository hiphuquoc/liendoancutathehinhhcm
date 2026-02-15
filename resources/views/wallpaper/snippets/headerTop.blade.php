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
<div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>
<div class="mobile-menu-drawer" id="mobileMenuDrawer">
    <div class="drawer-header">
        <span class="drawer-title">Menu</span>
        <button class="close-btn" id="mobileMenuClose"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="drawer-body">
        <ul class="mobile-nav-list">
            @foreach($dataMenu as $itemMenu)
                @php
                    $slugMenu = $itemMenu['slug'];
                    // Filter out contact and courses for mobile
                    if(in_array($slugMenu, ['lien-he', 'khoa-hoc'])) continue;

                    $active   = in_array($slugMenu, $arraySlugCurrent) ? 'active' : '';
                    $hasSub   = ($slugMenu == 'tin-tuc' && !empty($categoriesLv2) && $categoriesLv2->isNotEmpty());
                @endphp
                
                @if($slugMenu == '' || $slugMenu == 'trang-chu')
                    <li class="mobile-nav-item">
                        <a href="/" class="mobile-nav-link nav-icon-home {{ $active }}">
                            <span class="nav-icon-home">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9.02 2.84005L3.63 7.04005C3.23 7.35005 3 7.86005 3 8.40005V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19V8.40005C21 7.86005 20.77 7.35005 20.37 7.04005L14.98 2.84005C13.25 1.49005 10.75 1.49005 9.02 2.84005Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M12 17V14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span> 
                            <span>Trang chủ</span>
                        </a>
                    </li>
                    @continue
                @endif

                <li class="mobile-nav-item {{ $hasSub ? 'has-submenu' : '' }}">
                   @if($hasSub)
                        <div class="mobile-nav-link-wrapper" style="display: flex; justify-content: space-between; align-items: center;">
                            <a href="/{{ $slugMenu }}" class="mobile-nav-link {{ $active }}">{{ $itemMenu['name'] }}</a>
                            <span class="submenu-toggle" style="cursor: pointer;"><i class="fa-solid fa-chevron-down"></i></span>
                        </div>
                        <ul class="mobile-submenu">
                            @foreach($categoriesLv2 as $cLv2)
                                <li><a href="/{{ $cLv2->slug_full ?? '' }}">{{ $cLv2->title ?? '' }}</a></li>
                            @endforeach
                        </ul>
                   @else
                        <a href="/{{ $slugMenu }}" class="mobile-nav-link {{ $active }}">{{ $itemMenu['name'] }}</a>
                   @endif
                </li>
            @endforeach
        </ul>
    </div>
    <div class="drawer-footer">
        <a href="/lien-he" class="btn btn-primary w-100" style="background: #00adef; border: none;">Liên hệ</a>
    </div>
</div>

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
            document.body.style.overflow = 'hidden';
        }

        function closeMenu() {
            drawer.classList.remove('active');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        if(toggleBtn) toggleBtn.addEventListener('click', openMenu);
        if(toggleBtnSmall) toggleBtnSmall.addEventListener('click', openMenu);
        if(closeBtn) closeBtn.addEventListener('click', closeMenu);
        if(overlay) overlay.addEventListener('click', closeMenu);

        // Mobile Submenu Toggle
        const submenuToggles = document.querySelectorAll('.submenu-toggle');
        submenuToggles.forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                const parent = this.closest('.mobile-nav-item');
                parent.classList.toggle('open');
                
                // Animation logic handled by CSS, just class toggle needed
            });
        });
    });
</script>
@endpush