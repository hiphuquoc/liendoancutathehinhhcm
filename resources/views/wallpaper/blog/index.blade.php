@extends('layouts.wallpaper')
@push('cssFirstView')
    @if(env('APP_ENV')=='local')
        @vite('resources/sources/main/category-blog-first-view.scss')
    @else
        @php
            $manifest     = json_decode(file_get_contents(public_path('build/manifest.json')), true);
            $cssFirstView = $manifest['resources/sources/main/category-blog-first-view.scss']['file'] ?? '';
        @endphp
        @if(!empty($cssFirstView))
        <style type="text/css">{!! file_get_contents(public_path('build/' . $cssFirstView)) !!}</style>
        @endif
    @endif
@endpush
@push('headCustom')
    @include('wallpaper.schema.organization')
    @include('wallpaper.schema.article', compact('item'))
    @include('wallpaper.schema.creativeworkseries', compact('item'))
    @include('wallpaper.schema.social', ['item' => $item, 'lowPrice' => 1, 'highPrice' => 5])
    @include('wallpaper.schema.faq', ['data' => $item->faqs ?? []])
@endpush
@section('content')
    @include('wallpaper.template.shareSocial')
    <div class="breadcrumbMobileBox" aria-hidden="true"></div>
    @include('wallpaper.snippets.banner', [
        'urlImage' => \App\Helpers\Image::getUrlImageCloud('storage/images/blog-bg-img.webp'),
    ])

    @php
        $blogDetailTitle = $itemSeo->title ?? 'Bài viết';
    @endphp
    <header class="page-category-blog-hero page-blog-detail-hero">
        <div class="container page-category-blog-hero__container">
            <div class="page-category-blog-hero__inner">
                <h1 class="page-category-blog-hero__title">{{ $blogDetailTitle }}</h1>
                <p class="page-category-blog-hero__desc">
                    <span><i class="fa-regular fa-clock" aria-hidden="true"></i> {{ date('d/m/Y', strtotime($itemSeo->created_at ?? 'now')) }}</span>
                    <span class="sep">·</span>
                    <span>Đăng bởi Admin</span>
                </p>
            </div>
        </div>
    </header>

    <section class="page-category-blog page-blog-detail" aria-label="Nội dung bài viết">
        <div class="container page-category-blog__container">
            <div class="page-category-blog__layout">
                <aside class="page-category-blog__sidebar" aria-label="Sidebar">
                    @include('wallpaper.categoryBlog.search')
                    @include('wallpaper.categoryBlog.blogFeatured')
                    @include('wallpaper.categoryBlog.categoryList')
                    @include('wallpaper.categoryBlog.fanpageFacebook')
                </aside>
                <main class="page-category-blog__main page-blog-detail__main">
                    @include('wallpaper.blog.content')
                    @include('wallpaper.blog.related')
                </main>
            </div>
        </div>
    </section>
@endsection
