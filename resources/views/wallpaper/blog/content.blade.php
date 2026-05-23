<article class="article-detail">
    @if(!empty($item->categories) && $item->categories->isNotEmpty())
        <div class="article-detail__category">
            @foreach($item->categories as $category)
                @if(empty($category->infoCategory)) @continue @endif
                @foreach($category->infoCategory->seos ?? [] as $seo)
                    @if(!empty($seo->infoSeo->language) && $seo->infoSeo->language == $language)
                        <a href="/{{ $seo->infoSeo->slug_full ?? '#' }}"><i class="fa-solid fa-tags" aria-hidden="true"></i> {{ $seo->infoSeo->title ?? '' }}</a>
                        @break
                    @endif
                @endforeach
            @endforeach
        </div>
    @endif

    <div class="article-detail__body">
        @php
            $content = '';
            if (!empty($itemSeo->contents)) {
                foreach ($itemSeo->contents as $c) {
                    $content .= $c->content ?? '';
                }
            }
        @endphp
        @if($content)
            <div class="article-detail__content prose">
                {!! $content !!}
            </div>
        @endif
    </div>

    <footer class="article-detail__share" aria-label="Chia sẻ bài viết">
        <span class="article-detail__share-label">Chia sẻ bài viết</span>
        <ul class="article-detail__share-list" role="list">
            <li><a href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}" target="_blank" rel="noopener noreferrer" aria-label="Chia sẻ lên Facebook" class="article-detail__share-btn article-detail__share-btn--fb"><i class="fa-brands fa-facebook-f" aria-hidden="true"></i></a></li>
            <li><a href="https://twitter.com/intent/tweet?url={{ url()->current() }}" target="_blank" rel="noopener noreferrer" aria-label="Chia sẻ lên Twitter" class="article-detail__share-btn article-detail__share-btn--tw"><i class="fa-brands fa-twitter" aria-hidden="true"></i></a></li>
            <li><a href="https://www.linkedin.com/shareArticle?mini=true&url={{ url()->current() }}" target="_blank" rel="noopener noreferrer" aria-label="Chia sẻ lên LinkedIn" class="article-detail__share-btn article-detail__share-btn--in"><i class="fa-brands fa-linkedin-in" aria-hidden="true"></i></a></li>
        </ul>
    </footer>
</article>
