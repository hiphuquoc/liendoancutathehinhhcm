<div class="container">
    <div class="layoutTeacherDetail">
        <div class="layoutTeacherDetail_info">
            <!-- ảnh đại diện -->
            <div class="teacherDetailImg">
              @php
                /* thông tin trang */
                $fullName     = '';
                $job          = '';
                $summary      = '';
                foreach($item->seos as $seo){
                  if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$language){
                    $tmp        = explode('|', $seo->infoSeo->title);
                    $fullName   = !empty($tmp[0]) ? $tmp[0] : '';
                    $job        = !empty($tmp[1]) ? $tmp[1] : '';
                    $summary    = $seo->infoSeo->seo_description;
                  }
                }
                /* thông tin ảnh */
                $imageSmall = \App\Helpers\Image::getUrlImageSmallByUrlImage($item->seo->image);
                $imageMini  = \App\Helpers\Image::getUrlImageMiniByUrlImage($item->seo->image);
              @endphp
              <img class="lazyload" src="{{ $imageMini }}" data-src="{{ $imageSmall }}" alt="ảnh huấn luyện viên {{ $fullName }}" title="ảnh huấn luyện viên {{ $fullName }}" loading="lazy" />
            </div>
            <!-- thành tích -->
            @if(!empty($item->achievements)&&$item->achievements->isNotEmpty())
              <div class="teacherInfoBox">
                <h2>Thành tích</h2> 
                <ul>
                  @foreach($item->achievements as $achievement)
                    <li>{{ $achievement->content }}</li>
                  @endforeach
                </ul>
              </div>
            @endif
            <!-- thông tin -->
            @if(!empty($item->skills)&&$item->skills->isNotEmpty())
              <div class="teacherInfoBox">
                  <h2>Kỹ năng</h2> 
                  <div class="container-skills">
                    
                      @foreach($item->skills as $skill)
                        <div class="html">
                          <p class="bar-title">{{ $skill->skill }} <span class="percent align-right">{{ $skill->percent }}%</span> </p>
                          <div class="bar">
                            <div class="bar-fill bar-fill-html start"></div>
                          </div>
                        </div>
                      @endforeach

                  </div>
              </div>
            @endif
        </div>
        <div class="layoutTeacherDetail_detail">
            <!-- thông tin -->
            <div class="teacherInfoBox2">
              <div class="teacherInfoBox2_image">
                <img class="lazyload" src="{{ $imageMini }}" data-src="{{ $imageSmall }}" alt="ảnh huấn luyện viên {{ $fullName }}" title="ảnh huấn luyện viên {{ $fullName }}" loading="lazy" />
              </div>
              <div class="teacherInfoBox2_info">
                <h1>{{ $fullName }}</h1> 
                <div class="teacherInfoBox2_info_sub">{{ $job }}</div>
                <div class="teacherInfoBox2_info_contact">
                    <div class="teacherInfoBox2_info_contact_item">
                        <i class="fa fa-phone" aria-hidden="true"></i>{{ $item->phone ?? '' }}
                    </div>
                    <div class="teacherInfoBox2_info_contact_item">
                        <i class="fa fa-phone" aria-hidden="true"></i>{{ $item->email ?? '' }}
                    </div>
                    <div class="teacherInfoBox2_info_contact_item line"></div>
                </div>
              </div>
            </div>
            <!-- Giới thiệu -->
            @if(!empty($summary))
              <div class="teacherInfoBox">
                  <h2>Giới thiệu</h2> 
                  <p class="teacherInfoBox_content">{{ $summary }}</p>
              </div>
            @endif
            <!-- kinh nghiệm -->
            @if(!empty($item->experiences)&&$item->experiences->isNotEmpty())
              <div class="teacherInfoBox">
                  <h2>Tóm tắt sự nghiệp</h2> 
                  @foreach($item->experiences as $experiences)
                    <h3>{{ $experiences->title }}</h3>
                    <div class="teacherInfoBox_company">{{ $experiences->company }}</div>
                    <ul>
                      @foreach($experiences->contents as $content)
                        <li>{{ $content->content }}</li>
                      @endforeach
                    </ul>
                  @endforeach
              </div>
            @endif
            <!-- bằng cấp -->
            @if(!empty($item->degrees)&&$item->degrees->isNotEmpty())
              <div class="teacherInfoBox">
                  <h2>Bằng cấp liên quan</h2> 
                  @foreach($item->degrees as $degree)
                    <h3>{{ $degree->title }}</h3>
                    <div class="teacherInfoBox_company">{{ $degree->school }}</div>
                    <ul>
                      @foreach($degree->contents as $content)
                        <li>{{ $content->content }}</li>
                      @endforeach
                    </ul>
                  @endforeach
              </div>
            @endif
        </div>
    </div>
</div>