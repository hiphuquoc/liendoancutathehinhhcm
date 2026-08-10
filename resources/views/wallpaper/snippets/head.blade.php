<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<script>(function(){var mq=window.matchMedia('(hover: none), (pointer: coarse)');if(mq.matches)document.documentElement.classList.add('no-hover');else document.documentElement.classList.remove('no-hover');mq.addEventListener('change',function(){if(mq.matches)document.documentElement.classList.add('no-hover');else document.documentElement.classList.remove('no-hover');});})();</script>
<style>html.no-hover *:hover{transform:none!important;box-shadow:none!important}</style>
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
<link href="https://fonts.googleapis.com/css2?family=Encode+Sans+SC:wght@100..900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
<!-- Favicon -->
<link rel="shortcut icon" href="{{ \App\Helpers\Image::getUrlImageCloud('storage/images/favicon.webp') }}" type="image/x-icon" />
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
<!-- CSS Khung nhìn đầu tiên - Inline Css -->
@stack('cssFirstView')
<!-- Css tải sau -->
@stack('headCustom')

<!-- START:: ===== GOOGLE FONTS -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Lexend:wght@100..900&display=swap" rel="stylesheet">
<!-- END:: ===== GOOGLE FONTS -->