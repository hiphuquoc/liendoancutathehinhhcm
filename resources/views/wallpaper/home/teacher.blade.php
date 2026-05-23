<!-- start trainer section -->
<section class="hero-trainers">
  <div class="container effectFadeIn">
    <div class="row">
      <div class="col-lg-12 col-sm-12 col-xs-12">
        <div class="trainer-header-shared"> <!-- Use shared header class -->
          <div class="section-badge">
              <i class="fa-solid fa-users-gear"></i>
              <span>Đội ngũ chuyên gia</span>
          </div>
          <h2 class="section-title">HUẤN LUYỆN VIÊN <br> <span class="highlight">CHUYÊN NGHIỆP</span></h2>
          <p class="header-desc">Đội ngũ huấn luyện viên của chúng tôi là những chuyên gia hàng đầu, sở hữu kinh nghiệm thực tế và thành tích ấn tượng trong lĩnh vực Cử tạ - Thể hình. Sự tận tâm, chuyên nghiệp và nhiệt huyết của họ sẽ đồng hành cùng bạn trên hành trình chinh phục mọi thử thách.</p>
          
          <!-- Shared Stats Interface -->
          <div class="header-stats">
              <div class="stat-badge">
                  <div class="stat-icon">
                      <i class="fa-solid fa-user-graduate"></i>
                  </div>
                  <div class="stat-text">
                      <span class="stat-number">1000+</span>
                      <span class="stat-label">Huấn luyện viên</span>
                  </div>
              </div>
              <div class="stat-badge">
                  <div class="stat-icon">
                      <i class="fa-solid fa-users"></i>
                  </div>
                  <div class="stat-text">
                      <span class="stat-number">50+</span>
                      <span class="stat-label">Câu lạc bộ</span>
                  </div>
              </div>
              <div class="stat-badge">
                  <div class="stat-icon">
                      <i class="fa-solid fa-medal"></i>
                  </div>
                  <div class="stat-text">
                      <span class="stat-number">10+</span>
                      <span class="stat-label">Năm kinh nghiệm</span>
                  </div>
              </div>
          </div>
        </div>
      </div>
      <div class="traninerFlex col-lg-12 col-sm-12 col-xs-12">
        <div class="carousel owl-carousel owl-theme ss_carousel owl-loaded owl-drag" id="slider1">
          @foreach($trainers as $trainer)
            @foreach($trainer->seos as $seo)
                @if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$language)
                    @php
                      $fullName   = $trainer->name ?? $seo->infoSeo->title ?? '';
                      $job        = $trainer->position ?? '';
                      $imageSmall = \App\Helpers\Image::getUrlImageSmallByUrlImage($seo->infoSeo->image);
                      $imageMini  = \App\Helpers\Image::getUrlImageMiniByUrlImage($seo->infoSeo->image);
                    @endphp
                    <!-- Wrapper with padding for spacing -->
                    <div class="px-3 h-100 py-3"> 
                        <div class="trainer-card">
                            <div class="card-image">
                                <a href="/{{ $seo->infoSeo->slug_full }}" class="d-block w-100 h-100">
                                    <img class="lazyload" src="{{ $imageMini }}" data-src="{{ $imageSmall }}" alt="{{ $fullName }}" title="{{ $fullName }}" loading="lazy" />
                                </a>
                                <div class="card-overlay">
                                    <a href="/{{ $seo->infoSeo->slug_full }}" class="overlay-btn">
                                        <i class="fa-solid fa-arrow-right"></i> Xem hồ sơ
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <h3 class="trainer-name">
                                    <a href="/{{ $seo->infoSeo->slug_full }}">{{ $fullName }}</a>
                                </h3>
                                <p class="trainer-role">{{ $job }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        @endforeach
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12"> 
        <div class="buttons-container">
            <a href="/huan-luyen-vien" class="btn-brand-outline">
                <span>Xem tất cả HLV</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a> 
        </div>
      </div>
    </div>
  </div>
</section>
<!-- start trainer section -->