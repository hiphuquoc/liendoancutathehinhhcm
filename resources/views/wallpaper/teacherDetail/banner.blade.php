@php
    $style      = !empty($urlImage) ? 'style="background-image:url('.$urlImage.')"' : '';
    // Get name from trainer_info/referee_info, fallback to seo title
    $title      = $item->name ?? '';
    if(empty($title)) {
    foreach($item->seos as $seo){
      if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$language) {
        $title  = $seo->infoSeo->title;
        break;
      }
    }
    }
    $title      = trim($title) ?? '';
@endphp

<section class="hero-about-banner-section" {!! $style !!}>
  <div class="container">
    <div class="banner-content-section">
      <div class="banner-img-section">
        <h2>{{ $title }}</h2>
      </div>
      <div class="content-section">
        <p>
          <a href="/">Trang chủ</a> 
          @for($i=0;$i<count($breadcrumb);++$i)
            @php
              if($i==count($breadcrumb)-1){
                $title  = trim($breadcrumb[$i]->title) ?? '';
              }else {
                $title  = $breadcrumb[$i]->title;
              }
            @endphp
            <span><i class="fa-solid fa-angle-right" style="padding:0 5px;color:rgb(88, 196, 255);"></i></span> 
            <a href="/{{ $breadcrumb[$i]->slug_full ?? null }}" title="{{ $title }}">{{ $title }}</a>
          @endfor
        </p>
      </div>
    </div>
  </div>
</section>