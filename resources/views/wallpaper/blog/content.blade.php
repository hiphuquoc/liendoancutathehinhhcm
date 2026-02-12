<div class="single_blog">
    <!-- danh mục cha -->
    <div class="category_parent">
        @foreach($item->categories as $category)
            {{-- @php
                dd($category->infoCategory);
            @endphp --}}
            @foreach($category->infoCategory->seos as $seo)
                @if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$language)
                    <a href="/{{ $seo->infoSeo->slug_full }}"><i class="fa-solid fa-tags"></i>{{ $seo->infoSeo->title ?? ''}}</a>
                    @break
                @endif
            @endforeach
        @endforeach
    </div>
    <!-- tiêu đề -->
    <div class="title_sub">{{ $itemSeo->title ?? '' }}</div>
    <!-- thông tin bài viết -->
    <div class="date_con">
        <div class="date_box">
            <span><i class="fa-regular fa-clock"></i></span>
            <span>{{ date('d/m/Y', strtotime($itemSeo->created_at)) }}</span>
        </div>
        <div class="time_box">
            <span><i class="fa fa-user" aria-hidden="true"></i></span>
            <span>Đăng bởi Admin</span>
        </div>
    </div>
    <!-- nội dung -->
    <div class="blogInfoContent">
        @php
            $content = '';
            foreach($itemSeo->contents as $c) $content .= $c->content;
        @endphp
        {!! $content !!}
    </div> 
    <!-- kêu gọi -->
    <div class="bottom-box"> <span class="pull-left">
        Cảm ơn bạn đã xem bài viết, để ủng hộ chúng tôi hãy chia sẻ bài viết này tới bạn bè của bạn nhé!
      </span>
      <ul class="pull-right share">
        <li><a href="https://www.facebook.com/sharer/sharer.php?u={{ $itemSeo->slug_full ?? '' }}"><i class="fa-brands fa-facebook-f"></i></a></li>
        <li><a href="https://twitter.com/intent/tweet?url={{ $itemSeo->slug_full ?? '' }}"><i class="fa-brands fa-twitter"></i></a></li>
        <li><a href="https://www.linkedin.com/shareArticle?mini=true&url={{ $itemSeo->slug_full ?? '' }}"><i class="fa-brands fa-linkedin-in"></i></a></li>
      </ul>
    </div>
  </div>