@if(!empty($categoriesLv2)&&$categoriesLv2->isNotEmpty())
    <div class="categories_box">
        <div class="blog_title">
        <h3>Danh mục</h3>
        </div>
        <div class="categories_con">
        <div class="sidebar-info blog-categories">
            @foreach($categoriesLv2 as $cLv2)
                @foreach($cLv2->seos as $seo)
                @if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$language)
                    {{-- <li><a href="#">Business <span class="categories_left">15</span></a></li> --}}
                    <a href="/{{ $seo->infoSeo->slug_full }}">
                        <span class="tag-1">{{ $seo->infoSeo->title ?? '' }}</span>
                        <span class="tag-2">{{ !empty($cLv2->blogs)&&$cLv2->blogs->count()>0 ? $cLv2->blogs->count() : 0 }}</span>
                    </a>
                    @break
                @endif
                @endforeach
            @endforeach
        </div>
        </div>
    </div>
@endif