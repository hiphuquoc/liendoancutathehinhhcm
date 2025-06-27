<section class="hero-about">
    <div class="container">
      <div class="row">
        <div class="col-lg-6 col-sm-12 col-xs-12">
          <div class="about-img"> <!-- effectDropdown -->
            <img src="https://liendoancutathehinhhcm.storage.googleapis.com/storage/images/about-img-1.webp" alt="Gymfit" class="img-fluid" />
          </div>
        </div>
        <div class="col-lg-6 col-sm-12 col-xs-12">
          <div class="hero-about-content">
            <div class="about-header">
              <h2>LỜI <span>GIỚI THIỆU</span></h2>
            </div>
            <div class="about-content">
                @php
                    $content        = '';
                    foreach($itemSeo->contents as $c){
                        $content    .= $c->content;
                    }
                @endphp
              {!! $content !!}
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>