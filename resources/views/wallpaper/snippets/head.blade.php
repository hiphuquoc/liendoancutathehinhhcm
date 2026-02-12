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
<link href="https://fonts.googleapis.com/css2?family=Encode+Sans+SC:wght@100..900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
<!-- Favicon -->
<link rel="shortcut icon" href="https://liendoancutathehinhhcm.storage.googleapis.com/storage/images/favicon.webp" type="image/x-icon" />
<!-- Font Awesome -->
<link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" as="style" onload="this.rel='stylesheet'" />
<noscript>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</noscript>
{{-- <style type="text/css">
    @font-face {
        font-family: Lyon-Text;
        font-style: normal;
        font-weight: 400;
        src: url(lyon-text-regular-3be84b20b1d9ff1e3456b0a220ae449b.woff) format("woff")
    }

    @font-face {
        font-family: Lyon-Text;
        font-style: italic;
        font-weight: 400;
        src: url(lyon-text-regular-italic-437d32a42fc5b8268bb4a1e0cc8b363f.woff) format("woff")
    }

    @font-face {
        font-family: Lyon-Text;
        font-style: normal;
        font-weight: 600;
        src: url(lyon-text-semibold-acb7f110189034ff6a1afa4b730be0ed.woff) format("woff")
    }

    @font-face {
        font-family: Lyon-Text;
        font-style: italic;
        font-weight: 600;
        src: url(lyon-text-semibold-italic-1f81a2f93060f05edd7f078ac91f25e6.woff) format("woff")
    }

    @font-face {
        font-family: iawriter-mono;
        font-style: normal;
        font-weight: 400;
        src: url("{{ asset('css/font/iawriter-mono-regular-4b73d071988a4f1cd2283524716ad970.woff') }}") format("woff");
    }

    @font-face {
        font-family: iawriter-mono;
        font-style: italic;
        font-weight: 400;
        src: url("{{ asset('css/font/iawriter-mono-italic-d5d3224c1377168e261efc6aa0ce89c6.woff') }}") format("woff");
    }

    @font-face {
        font-family: iawriter-mono;
        font-style: normal;
        font-weight: 600;
        src: url("{{ asset('css/font/iawriter-mono-bold-eb96a5e539892d26cf8b0cb2367e3580.woff') }}") format("woff");
    }

    @font-face {
        font-family: iawriter-mono;
        font-style: italic;
        font-weight: 600;
        src: url("{{ asset('css/font/iawriter-mono-bold-italic-743b231fa82483406c79a00fa1f12fe8.woff') }}") format("woff");
    }

    @font-face {
        font-family: inter;
        font-style: normal;
        font-weight: 400;
        src: url(inter-ui-regular-3ae6a7d3890c33d857fc00bd2e4c4820.woff) format("woff")
    }

    @font-face {
        font-family: inter;
        font-style: normal;
        font-weight: 500;
        src: url(inter-ui-medium-95b8a98959d1af9ab432d7ffe295ef94.woff) format("woff")
    }

    @font-face {
        font-family: inter;
        font-style: normal;
        font-weight: 600;
        src: url(inter-ui-semibold-19b57197b819695d334b9961ee41910e.woff) format("woff")
    }

    @font-face {
        font-family: inter;
        font-style: normal;
        font-weight: 700;
        src: url(inter-ui-bold-001893789f7f342b520f29ac8af7d6ca.woff) format("woff")
    }

    @font-face {
        font-family: permanent-marker;
        font-style: normal;
        font-weight: 400;
        src: url(permanent-marker-a6d62939e7c920a184ddddcf4149e62c.woff) format("woff")
    }

</style> --}}
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