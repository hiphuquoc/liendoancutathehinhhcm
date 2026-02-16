@if(!empty($blogs) && $blogs->isNotEmpty())
    <ul class="blog-list" role="list">
        @foreach($blogs as $blog)
            @foreach($blog->seos as $seo)
                @if(!empty($seo->infoSeo->language) && $seo->infoSeo->language == $language)
                    @php
                        $imageMini  = \App\Helpers\Image::getUrlImageMiniByUrlImage($blog->seo->image);
                        $imageSmall = \App\Helpers\Image::getUrlImageSmallByUrlImage($blog->seo->image);
                        $title      = $seo->infoSeo->title ?? '';
                        $url        = $seo->infoSeo->slug_full ?? '';
                        $excerpt = '';
                        if (!empty($seo->infoSeo->contents) && isset($seo->infoSeo->contents[0]) && !empty($seo->infoSeo->contents[0]->content)) {
                            $excerpt = strip_tags($seo->infoSeo->contents[0]->content);
                        }
                    @endphp
                    <li class="blog-list__item">
                        <article class="blog-card">
                            <a href="/{{ $url }}" class="blog-card__media">
                                <img class="blog-card__img lazyload" src="{{ $imageMini }}" data-src="{{ $imageSmall }}" alt="{{ $title }}" loading="lazy" />
                            </a>
                            <div class="blog-card__body">
                                <h2 class="blog-card__title">
                                    <a href="/{{ $url }}">{{ $title }}</a>
                                </h2>
                                <div class="blog-card__meta">
                                    <span class="blog-card__date"><i class="fa-regular fa-clock" aria-hidden="true"></i> {{ date('d/m/Y', strtotime($blog->seo->created_at)) }}</span>
                                    <span class="blog-card__author"><i class="fa-solid fa-user" aria-hidden="true"></i> Đăng bởi Admin</span>
                                </div>
                                @if($excerpt)
                                    <p class="blog-card__excerpt">{{ Str::limit($excerpt, 120) }}</p>
                                @endif
                                <a href="/{{ $url }}" class="blog-card__link">
                                    <span>Xem thêm</span>
                                    <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                                </a>
                            </div>
                        </article>
                    </li>
                    @break
                @endif
            @endforeach
        @endforeach
    </ul>
@else
    <div class="blog-list__empty">
        <p>{{ config('language.'.$language.'.data.no_suitable_results_found') }}</p>
    </div>
@endif
