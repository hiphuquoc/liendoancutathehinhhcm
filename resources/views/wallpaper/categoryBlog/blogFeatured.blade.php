@if(!empty($blogFeatured)&&$blogFeatured->isNotEmpty())
    <div class="popular_box">
        <div class="blog_title">
            <h3>Bài viết nổi bật</h3>
        </div>
        @php
            $i = 0;
        @endphp
        @foreach($blogFeatured as $blog)
            @foreach($blog->seos as $seo)
                @if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$language)
                    @php
                        $imageMini  = \App\Helpers\Image::getUrlImageMiniByUrlImage($blog->seo->image);
                        $imageSmall = \App\Helpers\Image::getUrlImageSmallByUrlImage($blog->seo->image);
                        $title      = $seo->infoSeo->title ?? '';
                        $url        = $seo->infoSeo->slug_full ?? '';
                        $style      = $i==0 ? 'style="margin-top:0;"' : '';
                        ++$i;
                    @endphp
                    {{-- <div class="post">
                        <div class="post-img"> 
                            <img class="img-fluid lazyload" src="{{ $imageMini }}" data-src="{{ $imageSmall }}" alt="{{ $title }}" loading="lazy" style="filter:blur(5px);-webkit-filter:blur(5px);" />
                        </div>
                        <div class="post-content">
                            <h5 class="maxLine_1">{{ $title }}</h5>
                            <p class="maxLine_2">Sed cursus sed dui a aliquet. Mauris purus nunc.</p>
                        </div>
                    </div> --}}

                    <div class="post_item" {!! $style !!}>
                        <div class="post_img">
                            <a href="/{{ $url }}">
                                <img class="img-fluid lazyload" src="{{ $imageMini }}" data-src="{{ $imageSmall }}" alt="{{ $title }}" loading="lazy" style="filter:blur(5px);-webkit-filter:blur(5px);" />
                            </a>
                        </div>
                        <div class="post_con">
                            <a href="/{{ $url }}">
                                <h3 class="maxLine_2">{{ $title }}</h3>
                            </a>
                            <p><span><i class="fa-regular fa-clock"></i> {{ date('d/m/Y', strtotime($blog->seo->created_at)) }}</p>
                        </div>
                    </div>
                    
                    @break
                @endif
            @endforeach
        @endforeach

    </div>
@endif