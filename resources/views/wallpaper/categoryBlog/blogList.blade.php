@if(!empty($blogs)&&$blogs->isNotEmpty())
    @foreach($blogs as $blog)
        @foreach($blog->seos as $seo)
            @if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$language)
                @php
                    $imageMini  = \App\Helpers\Image::getUrlImageMiniByUrlImage($blog->seo->image);
                    $imageSmall = \App\Helpers\Image::getUrlImageSmallByUrlImage($blog->seo->image);
                    $title      = $seo->infoSeo->title ?? '';
                    $url        = $seo->infoSeo->slug_full ?? '';
                @endphp
                <div class="blog_page_box">
                    <div class="row">
                        <div class="col-lg-6 col-md-6">
                            <div class="blog_img_inner"> 
                                <a href="/{{ $url }}">
                                    <img class="img-fluid lazyload" src="{{ $imageMini }}" data-src="{{ $imageSmall }}" alt="{{ $title }}" loading="lazy" style="filter:blur(5px);-webkit-filter:blur(5px);" />
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="blog_con_inner">
                                <a href="/{{ $url }}">
                                    <h2 class="maxLine_2">
                                        {{ $title }}
                                    </h2>
                                </a>
                                <div class="date_con">
                                    <div class="date_box">
                                        <span><i class="fa-regular fa-clock"></i></span>
                                        <span>{{ date('d/m/Y', strtotime($blog->seo->created_at)) }}</span>
                                    </div>
                                    <div class="time_box">
                                        <span><i class="fa fa-user" aria-hidden="true"></i></span>
                                        <span>Đăng bởi Admin</span>
                                    </div>
                                </div>
                                <p class="maxLine_4">{!! !empty($seo->infoSeo->contents[0]->content) ? strip_tags($seo->infoSeo->contents[0]->content) : '' !!}</p>
                                <div class="read_more_blog"> 
                                    <a href="/{{ $url }}" class="blog_btn">
                                        <span>Xem thêm</span>
                                        <i class="fa fa-angle-right" aria-hidden="true"></i>
                                    </a> 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @break;

            @endif
        @endforeach
    @endforeach
@else 
    <div class="blog_page_box">
        <div class="row">{{ config('language.'.$language.'.data.no_suitable_results_found')}}</div>
    </div>
@endif