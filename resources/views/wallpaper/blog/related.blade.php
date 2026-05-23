@if(!empty($related) && $related->isNotEmpty())
    <section class="related-section" aria-labelledby="related-title">
        <h2 id="related-title" class="related-section__title">Bài viết liên quan</h2>
        <ul class="related-section__list" role="list">
            @foreach($related as $blog)
                @foreach($blog->seos as $seo)
                    @if(!empty($seo->infoSeo->language) && $seo->infoSeo->language == $language)
                        @php
                            $titleBlog = $seo->infoSeo->title ?? '';
                            $slugBlog  = $seo->infoSeo->slug_full ?? '';
                            $imageUrl  = !empty($blog->seo->image) ? \App\Helpers\Image::getUrlImageSmallByUrlImage($blog->seo->image) : '';
                            $excerpt = '';
                            if (!empty($seo->infoSeo->contents) && isset($seo->infoSeo->contents[0]) && !empty($seo->infoSeo->contents[0]->content)) {
                                $excerpt = Str::limit(strip_tags($seo->infoSeo->contents[0]->content), 90);
                            }
                        @endphp
                        <li class="related-section__item">
                            <a href="/{{ $slugBlog }}" class="related-card">
                                @if($imageUrl)
                                    <span class="related-card__media">
                                        <img class="related-card__img" src="{{ $imageUrl }}" alt="{{ $titleBlog }}" loading="lazy" />
                                    </span>
                                @endif
                                <span class="related-card__body">
                                    <span class="related-card__date"><i class="fa-regular fa-clock" aria-hidden="true"></i> {{ date('d/m/Y', strtotime($blog->seo->created_at)) }}</span>
                                    <span class="related-card__title">{{ $titleBlog }}</span>
                                    @if($excerpt)<span class="related-card__excerpt">{{ $excerpt }}</span>@endif
                                </span>
                            </a>
                        </li>
                        @break
                    @endif
                @endforeach
            @endforeach
        </ul>
    </section>
@endif
