<head>
  <meta name="robots" content="noindex,nofollow">
  {{-- <meta name="csrf-token" content="LdjJ2w20tGUL3y60AsF4ToCMKWgnRVrAypJ4E78v"> --}}
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
  <meta name="description" content="Trang quản trị nội dung ®Websitekiengiang">
  <meta name="keywords" content="Trang quản trị nội dung ®Websitekiengiang">
  <meta name="author" content="Hitour">
  <title>Trang quản trị nội dung {{ config('main_'.env('APP_NAME').'.info.'.env('APP_NAME').'.company_name') }}</title>
  <link rel="shortcut icon" href="/storage/images/upload/hoaanhtuc-favicon-type-manager-upload.webp" type="image/x-icon">
  
  <!-- BEGIN: Custom CSS - Single SCSS File -->
  <style type="text/css">
    /* font */
    @font-face{
        font-family:'SVN-Gilroy Bold';
        font-style:normal;
        font-weight:700;
        src:url("{{ asset('fonts/svn-gilroy_semibold.ttf') }}")
    }
    @font-face{
        font-family:'SVN-Gilroy';
        font-style:normal;
        font-weight:500;
        src:url("{{ asset('fonts/svn-gilroy_medium.ttf') }}")
    }
</style>
  @vite('resources/sources/admin/style.scss')
  <!-- END: Custom CSS-->
  
  <!-- BEGIN: FONT AWESOME -->
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
  <!-- END: FONT AWESOME -->

  <!-- BEGIN: SLICK -->
  {{-- <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/> --}}
  <!-- END: SLICK -->

 </head>