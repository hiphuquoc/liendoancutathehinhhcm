@if(!empty($blogFeatured) && $blogFeatured->isNotEmpty())
    <div class="sidebar-block sidebar-featured">
        <h3 class="sidebar-block__title">Bài viết nổi bật</h3>
        <ul class="sidebar-featured__list" role="list">
            @foreach($blogFeatured as $blog)
                @foreach($blog->seos as $seo)
                    @if(!empty($seo->infoSeo->language) && $seo->infoSeo->language == $language)
                        @php
                            $imageMini  = \App\Helpers\Image::getUrlImageMiniByUrlImage($blog->seo->image);
                            $imageSmall = \App\Helpers\Image::getUrlImageSmallByUrlImage($blog->seo->image);
                            $title      = $seo->infoSeo->title ?? '';
                            $url        = $seo->infoSeo->slug_full ?? '';
                        @endphp
                        <li class="sidebar-featured__item">
                            <a href="/{{ $url }}" class="sidebar-featured__card">
                                <span class="sidebar-featured__media">
                                    <img class="sidebar-featured__img lazyload" src="{{ $imageMini }}" data-src="{{ $imageSmall }}" alt="{{ $title }}" loading="lazy" />
                                </span>
                                <span class="sidebar-featured__body">
                                    <span class="sidebar-featured__title">{{ $title }}</span>
                                    <span class="sidebar-featured__date"><i class="fa-regular fa-clock" aria-hidden="true"></i> {{ date('d/m/Y', strtotime($blog->seo->created_at)) }}</span>
                                </span>
                            </a>
                        </li>
                        @break
                    @endif
                @endforeach
            @endforeach
        </ul>
    </div>
@endif
