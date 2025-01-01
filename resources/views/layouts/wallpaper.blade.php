<!DOCTYPE html>
<html lang="{{ $language ?? 'vi' }}" dir="{{ config('language.'.$language.'.dir') }}">

<!-- === START:: Head === -->
<head>
    @include('wallpaper.snippets.head')
</head>
<!-- === END:: Head === -->

<!-- === START:: Body === -->
<body class="background">
    <div id="js_openCloseModal_blur">
        <!-- LOADER START HERE -->
        <div class="page_loader">
            <img loading='lazy' src="{{ Storage::url('images/svg/loader.svg') }}" alt="loader" title="loader" style="width:70px" />
        </div>

        <!-- header Top -->
        @include('wallpaper.snippets.headerTop')

        <!-- nội dung chính -->
        <section>
            @yield('content')
        </section>
        
        <!-- footer -->
        @include('wallpaper.snippets.footer')

        <div class="bottom">
            <div id="smoothScrollToTop" class="gotoTop" onclick="javascript:smoothScrollToTop();" style="display: block;">
                <i class="fas fa-chevron-up"></i>
            </div>
            @stack('bottom')
        </div>

        {{-- @include('layouts.tmp') --}}

    </div>
    
    <!-- Modal -->
    @stack('modal')

    <!-- login form modal -->
    <div id="js_checkLoginAndSetShow_modal">
        <!-- tải ajaax checkLoginAndSetShow() -->
    </div>

    <!-- === START:: Scripts Default === -->
    @include('wallpaper.snippets.scriptDefault')
    <!-- === END:: Scripts Default === -->

    <!-- === START:: Scripts Custom === -->
    @stack('scriptCustom')
    <!-- === END:: Scripts Custom === -->
    
</body>
<!-- === END:: Body === -->

</html>