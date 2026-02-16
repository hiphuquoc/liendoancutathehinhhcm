@if(!empty($categoriesLv2) && $categoriesLv2->isNotEmpty())
    <div class="sidebar-block sidebar-categories">
        <h3 class="sidebar-block__title">Danh mục</h3>
        <nav class="sidebar-categories__nav" aria-label="Danh mục tin tức">
            <ul class="sidebar-categories__list" role="list">
                @foreach($categoriesLv2 as $cLv2)
                    @foreach($cLv2->seos as $seo)
                        @if(!empty($seo->infoSeo->language) && $seo->infoSeo->language == $language)
                            <li class="sidebar-categories__item">
                                <a href="/{{ $seo->infoSeo->slug_full }}" class="sidebar-categories__link">
                                    <span class="sidebar-categories__name">{{ $seo->infoSeo->title ?? '' }}</span>
                                    <span class="sidebar-categories__count">{{ !empty($cLv2->blogs) && $cLv2->blogs->count() > 0 ? $cLv2->blogs->count() : 0 }}</span>
                                </a>
                            </li>
                            @break
                        @endif
                    @endforeach
                @endforeach
            </ul>
        </nav>
    </div>
@endif
