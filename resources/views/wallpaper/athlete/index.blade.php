@extends('layouts.wallpaper')
@push('cssFirstView')
    @if(env('APP_ENV')=='local')
        @vite('resources/sources/main/teacher-list.scss')
    @else
        @php
            $manifest           = json_decode(file_get_contents(public_path('build/manifest.json')), true);
            $cssFirstView       = $manifest['resources/sources/main/teacher-list.scss']['file'];
        @endphp
        <style type="text/css">
            {!! file_get_contents(public_path('build/' . $cssFirstView)) !!}
        </style>
    @endif
@endpush
@push('headCustom')
    @include('wallpaper.schema.organization')
    @include('wallpaper.schema.article', compact('item'))
    @include('wallpaper.schema.creativeworkseries', compact('item'))
    @include('wallpaper.schema.social', ['item' => $item, 'lowPrice' => 1, 'highPrice' => 5])
    @include('wallpaper.schema.faq', ['data' => $item->faqs])
@endpush
@section('content')
    @include('wallpaper.template.shareSocial')
    @include('wallpaper.snippets.banner', [
        'urlImage' => 'https://liendoancutathehinhhcm.storage.googleapis.com/storage/images/about-bg-img.webp',
    ])
    @include('wallpaper.athlete.athlete')
@endsection
@push('modal')
@endpush
@push('bottom')
@endpush
@push('scriptCustom')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
        });
    </script>
@endpush
