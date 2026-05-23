@extends('layouts.wallpaper')
@push('cssFirstView')
    <!-- trường hợp là local thì dùng vite để chạy npm run dev lúc code -->
    @if(env('APP_ENV')=='local')
        @vite('resources/sources/main/category-blog-first-view.scss')
    @else
        @php
            $manifest           = json_decode(file_get_contents(public_path('build/manifest.json')), true);
            $cssFirstView       = $manifest['resources/sources/main/category-blog-first-view.scss']['file'];
        @endphp
        <style type="text/css">
            {!! file_get_contents(public_path('build/' . $cssFirstView)) !!}
        </style>
    @endif
@endpush
@push('headCustom')
<!-- ===== START:: SCHEMA ===== -->
    <!-- STRAT:: Organization Schema -->
    @include('wallpaper.schema.organization')
    <!-- END:: Organization Schema -->

    <!-- STRAT:: Article Schema -->
    @include('wallpaper.schema.article', compact('item'))
    <!-- END:: Article Schema -->

    <!-- STRAT:: Article Schema -->
    @include('wallpaper.schema.creativeworkseries', compact('item'))
    <!-- END:: Article Schema -->
    
    {{-- <!-- STRAT:: FAQ Schema -->
    @include('wallpaper.schema.itemlist', ['data' => $categories])
    <!-- END:: FAQ Schema --> --}}

    <!-- STRAT:: Title - Description - Social -->
    @include('wallpaper.schema.social', ['item' => $item, 'lowPrice' => 1, 'highPrice' => 5])
    <!-- END:: Title - Description - Social -->

    <!-- STRAT:: FAQ Schema -->
    @include('wallpaper.schema.faq', ['data' => $item->faqs])
    <!-- END:: FAQ Schema -->
<!-- ===== END:: SCHEMA ===== -->
@endpush
@section('content')
    @include('wallpaper.template.shareSocial')
    <div class="breadcrumbMobileBox" aria-hidden="true"></div>
    @include('wallpaper.snippets.banner', [
        'urlImage' => 'https://liendoancutathehinhhcm.storage.googleapis.com/storage/images/blog-bg-img.webp',
    ])

    @php
        $blogPageTitle = isset($item) && !empty($item->title) ? $item->title : (isset($item) && !empty($item->name) ? $item->name : 'Tin tức');
    @endphp
    <header class="page-category-blog-hero">
        <div class="container page-category-blog-hero__container">
            <div class="page-category-blog-hero__inner">
                <h1 class="page-category-blog-hero__title">{{ $blogPageTitle }}</h1>
                <p class="page-category-blog-hero__desc">Cập nhật tin tức, sự kiện và bài viết từ Liên Đoàn Cử Tạ Thể Hình TP.HCM</p>
            </div>
        </div>
    </header>

    <section class="page-category-blog" aria-label="Tin tức">
        <div class="container page-category-blog__container">
            <div class="page-category-blog__layout">
                <aside class="page-category-blog__sidebar" aria-label="Sidebar">
                    @include('wallpaper.categoryBlog.search')
                    @include('wallpaper.categoryBlog.blogFeatured')
                    @include('wallpaper.categoryBlog.categoryList')
                    @include('wallpaper.categoryBlog.fanpageFacebook')
                </aside>
                <main class="page-category-blog__main" id="blog-list-main">
                    @include('wallpaper.categoryBlog.blogList')
                    <div class="page-category-blog__pagination-wrap">
                        {{ $blogs->links('wallpaper.categoryBlog.pagination') }}
                    </div>
                </main>
            </div>
        </div>
    </section>

    {{-- @include('wallpaper.home.teacher')
    @include('wallpaper.home.timetable')
    @include('wallpaper.home.video')
    @include('wallpaper.home.ourblog') --}}
    {{-- @include('wallpaper.home.form') --}}
@endsection
@push('modal')
    

@endpush
@push('bottom')
    <!-- === START:: Zalo Ring === -->
    {{-- @include('wallpaper.snippets.zaloRing') --}}
    <!-- === END:: Zalo Ring === -->
@endpush
@push('scriptCustom')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            
        });
    </script>
@endpush
