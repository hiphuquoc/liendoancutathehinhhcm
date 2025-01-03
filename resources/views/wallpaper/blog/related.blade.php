

@if(!empty($related)&&$related->isNotEmpty())
  <section class="hero-blog-section related">
    <div class="container">
      <div class="row">
        <div class="col-lg-12 col-sm-12 col-xs-12">
          <div class="blog-header related-header">
            <h3>BÀI VIẾT LIÊN QUAN</h3>
            {{-- <p>Cập nhật liên tục những tin tức nóng hổi về các sự kiện, giải đấu, xu hướng mới trong làng thể hình và cử tạ. Tin tức của chúng tôi cung cấp thông tin chi tiết về các hoạt động của Liên đoàn, các bài viết chuyên môn, cũng như những sự kiện thể thao nổi bật, giúp bạn luôn được cập nhật và đồng hành cùng cộng đồng thể thao.</p> --}}
          </div>
        </div>
        <div class="col-lg-12 col-sm-12 col-xs-12">
          <div class="owl-carousel owl-theme ss_carousel" id="slider5">
            @foreach($related as $blog)
              @foreach($blog->seos as $seo)
                @if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$language)
                  @php
                      $titleBlog    = $seo->infoSeo->title ?? '';
                      $slugBlog     = $seo->infoSeo->slug_full ?? '';
                  @endphp
                  <div class="item effectFadeIn">
                    <div class="blog-box">
                      <div class="blog-img"> <a href="/{{ $slugBlog }}"> 
                          @if(!empty($blog->seo->image))
                            <img src="{{ \App\Helpers\Image::getUrlImageSmallByUrlImage($blog->seo->image) }}" alt="{{ $titleBlog }}" title="{{ $titleBlog }}" />
                          @endif
                          <div class="img-overlay"></div>
                        </a> </div>
                      <div class="blog-content">
                        <p>{{ date('d/m/Y', strtotime($blog->seo->created_at)) }} Đăng bởi Admin</p>
                        <h2><a href="/{{ $slugBlog }}" class="maxLine_3">{{ $titleBlog }}</a></h2>
                        <p class="maxLine_5">{!! !empty($seo->infoSeo->contents[0]->content) ? strip_tags($seo->infoSeo->contents[0]->content) : '' !!}</p>
                      </div>
                    </div>
                  </div>
                  @break
                @endif
              @endforeach
            @endforeach
          </div>
        </div>

      </div>
    </div>
  </section>
@endif

@pushonce('scriptCustom')
  <script type="text/javascript">

    document.addEventListener('DOMContentLoaded', function() {

      var owl = $('#slider5');
      owl.owlCarousel({
        loop:true,
        margin: 0,
        // center:true,
        autoplayTimeout:5000,
        smartSpeed:450,
        dots:true,
        nav: true,
        navText: ["<i class='fa fa-angle-left'></i>","<i class='fa fa-angle-right'></i>"],
        responsive: {
          0: {
            items: 1
          },
          1024: {
            items: 1.7
          },
          1199: {
            items: 2.2
          },
          1499: {
            items: 2.5
          }
        }
      })

    });

  </script>

@endpushonce