<section class="hero-sponsors effectFadeIn">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="sponsor-header">
          <div class="section-badge">
            <i class="fa-solid fa-handshake"></i>
            <span>Đồng hành cùng chúng tôi</span>
          </div>
          <h2 class="section-title">ĐỐI TÁC <br> <span class="highlight">NHÀ TÀI TRỢ</span></h2>
          <p>Chúng tôi trân trọng sự đồng hành của các đối tác và nhà tài trợ – những thương hiệu, doanh nghiệp và tổ chức uy tín đã góp phần tạo nên sự phát triển bền vững của Liên đoàn Cử tạ – Thể hình TP.HCM.</p>
        </div>
      </div>
      
      <div class="col-12">
        <div class="row">
          @for($i=0;$i<6;++$i)
            <div class="col-lg-4 col-md-6 col-12 mb-4">
                <div class="sponsor-card">
                    <div class="card-img">
                        <!-- Partner Level Badge -->
                        <div class="card-badge">
                            <i class="fa-solid fa-gem"></i> Đối tác kim cương
                        </div>
                        
                        <a href="/doi-tac-nha-tai-tro/test" class="img-link">
                            <img class="lazyload" 
                                 src="{{ \App\Helpers\Image::getUrlImageCloud('storage/images/phong-tap-mau-mini.webp') }}" 
                                 data-src="{{ \App\Helpers\Image::getUrlImageCloud('storage/images/phong-tap-mau-large.webp') }}" 
                                 alt="Gym Center Q3" 
                                 loading="lazy">
                        </a>
                    </div>
                    
                    <div class="card-content">
                        <div class="content-top">
                            <h3><a href="/doi-tac-nha-tai-tro/test">Gym Center Q3</a></h3>
                            <p>Hệ thống phòng tập đạt chuẩn quốc tế với trang thiết bị hiện đại hàng đầu Việt Nam.</p>
                        </div>
                        
                        <div class="card-info">
                            <div class="info-item">
                                <i class="fa-solid fa-location-dot"></i>
                                <span>219 Lý Thường Kiệt, P.15, Q.11, TP.HCM</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
          @endfor
        </div>
      </div>
    
      <div class="col-12">
        <div class="buttons-container"> 
            <a href="/doi-tac-nha-tai-tro" class="btn-view-all">
                Xem tất cả đối tác <i class="fa-solid fa-arrow-right"></i>
            </a> 
        </div>
      </div>
    </div>
  </div>
</section>