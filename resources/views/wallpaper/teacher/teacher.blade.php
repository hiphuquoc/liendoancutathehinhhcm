<!-- start trainer section -->
<section class="hero-trainers">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-sm-12 col-xs-12">
                <div class="trainer-header">
                    <h2>HUẤN LUYỆN VIÊN <span>CỦA CHÚNG TÔI</span></h2>
                    <p>Đội ngũ huấn luyện viên của chúng tôi là những chuyên gia hàng đầu, sở hữu kinh nghiệm thực tế và thành tích ấn tượng trong lĩnh vực Cử tạ - Thể hình. Sự tận tâm, chuyên nghiệp và nhiệt huyết của họ sẽ đồng hành cùng bạn trên hành trình chinh phục mọi thử thách và mục tiêu tập luyện.</p>
                </div>
            </div>
            <div class="col-lg-12 col-sm-12 col-xs-12">
            
            
                <div class="trainer_all">
                    @foreach($trainers as $trainer)
                        @foreach($trainer->seos as $seo)
                            @if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$language)
                                @php
                                    $tmp        = explode('|', $seo->infoSeo->title);
                                    $fullName   = !empty($tmp[0]) ? $tmp[0] : '';
                                    $job        = !empty($tmp[1]) ? $tmp[1] : '';
                                    $imageSmall = \App\Helpers\Image::getUrlImageSmallByUrlImage($seo->infoSeo->image);
                                    $imageMini  = \App\Helpers\Image::getUrlImageMiniByUrlImage($seo->infoSeo->image);
                                @endphp
                                <div class="trainer_box">
                                    <div class="img_trainer">
                                        <div class="img_wrapper">
                                            <img class="lazyload" src="{{ $imageMini }}?{{ time() }}" data-src="{{ $imageSmall }}" alt="{{ $fullName }}" title="{{ $fullName }}" loading="lazy" />
                                        </div>
                                        <div class="trainer_social">
                                            <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                                            <a href="#"><i class="fa-brands fa-youtube"></i></a>
                                            <a href="#"><i class="fa-brands fa-instagram"></i></a>
                                        </div>
                                    </div>
                                    <div class="trainer_con">
                                        <a href="/{{ $seo->infoSeo->slug_full }}"><h3>{{ $fullName }}</h3></a>
                                        <p>{{ $job }}</p>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
<!-- start trainer section -->