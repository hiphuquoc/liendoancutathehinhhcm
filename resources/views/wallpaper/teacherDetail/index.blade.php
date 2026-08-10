@extends('layouts.wallpaper')
@push('cssFirstView')
    <!-- trường hợp là local thì dùng vite để chạy npm run dev lúc code -->
    @if(env('APP_ENV')=='local')
        @vite('resources/sources/main/trainer-detail.scss')
    @else
        @php
            $manifest           = json_decode(file_get_contents(public_path('build/manifest.json')), true);
            $cssFirstView       = $manifest['resources/sources/main/trainer-detail.scss']['file'] ?? '';
        @endphp
        @if(!empty($cssFirstView) && file_exists(public_path('build/' . $cssFirstView)))
            <style type="text/css">
                {!! file_get_contents(public_path('build/' . $cssFirstView)) !!}
            </style>
        @endif
    @endif
    <!-- GLightbox CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" />
@endpush
@push('headCustom')
    <!-- ===== START:: SCHEMA ===== -->
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
    <!-- share social -->
    @include('wallpaper.template.shareSocial')
    <!-- Item Category Grid Box -->
    @include('wallpaper.teacherDetail.banner', [
        'urlImage'   => \App\Helpers\Image::getUrlImageCloud('storage/images/about-bg-img.webp'),
        'breadcrumb' => $breadcrumb ?? [], 
    ])
    @include('wallpaper.teacherDetail.body')
@endsection
@push('modal')
    

@endpush
@push('bottom')
    <script src="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const lightbox = GLightbox({
                touchNavigation: true,
                loop: true,
                autoplayVideos: true,
                selector: '.glightbox'
            });
        });
    </script>
    <!-- === START:: Zalo Ring === -->
    {{-- @include('wallpaper.snippets.zaloRing') --}}
    <!-- === END:: Zalo Ring === -->
@endpush
@push('scriptCustom')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            // Animate skill bars when they come into view
            const skillBars = document.querySelectorAll('.trainerProfile_skill_fill');
            
            const animateSkillBar = (bar) => {
                const percent = bar.getAttribute('data-percent');
                if (percent) {
                    bar.style.width = '0%';
                    setTimeout(() => {
                        bar.style.width = percent + '%';
                    }, 100);
                }
            };
            
            const observerOptions = {
                threshold: 0.3,
                rootMargin: '0px'
            };
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        animateSkillBar(entry.target);
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);
            
            skillBars.forEach(bar => {
                observer.observe(bar);
            });
            
            // Smooth scroll for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        });
    </script>
@endpush
