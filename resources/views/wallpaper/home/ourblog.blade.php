@if(!empty($blogs)&&$blogs->isNotEmpty())
  <section class="hero-blog-section effectFadeIn">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <div class="blog-header">
            <div class="section-badge">
                <i class="fa-solid fa-newspaper"></i>
                <span>Thông tin cập nhật</span>
            </div>
            <h2 class="section-title">TIN TỨC <br> <span class="highlight">MỚI NHẤT</span></h2>
            <p>Cập nhật liên tục những tin tức nóng hổi về các sự kiện, giải đấu, xu hướng mới trong làng thể hình và cử tạ. Tin tức của chúng tôi cung cấp thông tin chi tiết về các hoạt động của Liên đoàn.</p>
          </div>
        </div>
        
        <div class="col-12">
          <div class="row justify-content-center">
            @php $count = 0; @endphp
            @foreach($blogs as $blog)
              @if(!empty($blog->seos))
                @foreach($blog->seos as $seo)
                  @if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$language)
                    @php
                        $titleBlog    = $seo->infoSeo->title ?? '';
                        $slugBlog     = $seo->infoSeo->slug_full ?? '';
                        $image        = \App\Helpers\Image::getUrlImageSmallByUrlImage($blog->seo->image ?? '');
                        // Check date exists
                        $created_at   = $blog->seo->created_at ?? date('Y-m-d');
                        $dateDay      = date('d', strtotime($created_at));
                        $dateMonth    = date('m', strtotime($created_at));
                        $dateYear     = date('Y', strtotime($created_at));
                        $count++;
                    @endphp
                    
                    <div class="col-lg-4 col-md-6 col-12 mb-4">
                      <div class="blog-box">
                        <div class="blog-img">
                           <div class="date-badge">
                              <span class="day">{{ $dateDay }}</span>
                              <span class="month">Thg {{ $dateMonth }}</span>
                           </div>
                           
                           <a href="/{{ $slugBlog }}"> 
                              @if(!empty($blog->seo->image))
                                <img src="{{ $image }}" alt="{{ $titleBlog }}" title="{{ $titleBlog }}" loading="lazy" />
                              @else
                                <img src="https://liendoancutathehinhhcm.storage.googleapis.com/storage/images/default-news.webp" alt="{{ $titleBlog }}" loading="lazy" />
                              @endif
                              <div class="img-overlay"></div>
                           </a> 
                        </div>
                        
                        <div class="blog-content">
                          <div class="meta-info">
                              <span><i class="fa-regular fa-user"></i> Admin</span>
                              <span><i class="fa-regular fa-calendar"></i> {{ $dateYear }}</span>
                          </div>
                          
                          <h2><a href="/{{ $slugBlog }}" class="maxLine_2">{{ $titleBlog }}</a></h2>
                          
                          <div class="blog-desc maxLine_3">
                             {!! !empty($seo->infoSeo->contents[0]->content) ? strip_tags($seo->infoSeo->contents[0]->content) : '' !!}
                          </div>
                          
                          <a href="/{{ $slugBlog }}" class="read-more-link">
                              Xem chi tiết <i class="fa-solid fa-arrow-right-long"></i>
                          </a>
                        </div>
                      </div>
                    </div>
                    
                    @break
                  @endif
                @endforeach
              @endif
              
              @if($count >= 3) @break @endif
            @endforeach
          </div>
        </div>

        <div class="col-12">
          <div class="buttons-container"> 
            <a href="/tin-tuc" class="btn-view-all">
                Xem tất cả tin tức <i class="fa-solid fa-arrow-right"></i>
            </a> 
          </div>
        </div>

      </div>
    </div>
  </section>
@endif