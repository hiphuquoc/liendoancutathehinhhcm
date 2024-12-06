<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
@if(Route::is('main.confirm'))
    <meta name="robots" content="noindex,nofollow">
@else
    @if(!empty($index)&&$index=='no')
        <meta name="robots" content="noindex,nofollow">
    @else 
        <meta name="robots" content="index,follow">
    @endif
@endif
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="fragment" content="!" />
@if(!empty($language))
    <meta name="language" content="{{ $language }}" />
@endif
{{-- <!-- Dmca -->
<meta name='dmca-site-verification' content='{{ env('DMCA_VALIDATE') }}' />
<!-- Tối ưu hóa việc tải ảnh từ Google Cloud Storage -->
<link rel="preconnect" href="https://namecomvn.storage.googleapis.com" crossorigin>
<link rel="dns-prefetch" href="https://namecomvn.storage.googleapis.com">
<link rel="preconnect" href="https://images.dmca.com">
<link rel="dns-prefetch" href="https://images.dmca.com">
<link rel="preconnect" href="https://cdnjs.cloudflare.com">
<link rel="dns-prefetch" href="https://cdnjs.cloudflare.com"> --}}
<!-- font -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
<!-- Favicon -->
<link rel="shortcut icon" href="/storage/images/upload/logo-type-manager-upload.webp" type="image/x-icon" />
<!-- Font Awesome -->
<link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" as="style" onload="this.rel='stylesheet'" />
<noscript>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</noscript>
<!-- Css cố định -->
<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/main/bootsnav.css') }}?{{ time() }}">
<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/main/bootstrap.min.css') }}?{{ time() }}">
<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/main/owl-2.carousel.min.css') }}?{{ time() }}">
<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/main/owl-2.theme.default.min.css') }}?{{ time() }}">
{{-- <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/main/font-awesome.css') }}?{{ time() }}"> --}}
{{-- <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/main/font-awesome.min.css') }}?{{ time() }}"> --}}
<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/main/sm-clean.css') }}?{{ time() }}">
<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/main/sm-core-css.css') }}?{{ time() }}">
<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/main/style.css') }}?{{ time() }}">
<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/main/responsive.css') }}?{{ time() }}">
<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/main/blog.css') }}?{{ time() }}">
<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/main/trainer.css') }}?{{ time() }}">

<link rel="stylesheet" type="text/css" media="screen" href="https://netizensstore.com/gymfit_theme/html/multipage_8/assets/fonts/flaticon.css">
{{-- <link rel="stylesheet" type="text/css" media="screen" href="https://netizensstore.com/gymfit_theme/html/multipage_8/assets/css/style.css"> --}}
<!-- CSS Khung nhìn đầu tiên - Inline Css -->
@stack('cssFirstView')
<!-- Css tải sau -->
@stack('headCustom')
{{-- @if(env('APP_ENV')=='local')
    <!-- tải font nếu dev -->
    <style type="text/css">
        @font-face {
            font-family: 'Segoe-UI';
            font-style: normal;
            font-weight: 500;
            src: url('/css/font/SegoeUI.ttf');
            font-display: swap;
        }

        @font-face {
            font-family: 'Segoe-UI Semi';
            font-style: normal;
            font-weight: 700;
            font-display: swap;
            src: url('/css/font/SegoeUI-SemiBold.ttf');
        }

        @font-face {
            font-family: 'Segoe-UI Bold';
            font-style: normal;
            font-weight: 700;
            font-display: swap;
            src: url('/css/font/SegoeUI-Bold.ttf');
        } */

        @font-face {
            font-family: 'SVN-Gilroy';
            font-style: normal;
            font-display: swap;
            font-weight: 500;
            src: url('/css/font/svn-gilroy_medium.ttf');
        }

        @font-face {
            font-family: 'SVN-Gilroy Med';
            font-style: normal;
            font-display: swap;
            font-weight: 700;
            src: url('/css/font/svn-gilroy_med.ttf');
        }

        @font-face {
            font-family: 'SVN-Gilroy Semi';
            font-style: normal;
            font-weight: 700;
            font-display: swap;
            src: url('/css/font/svn-gilroy_semibold.ttf');
        }

        @font-face {
            font-family: 'SVN-Gilroy Bold';
            font-style: normal;
            font-weight: 700;
            font-display: swap;
            src: url('/css/font/svn-gilroy_semibold.ttf');
        }
    </style>
@endif --}}