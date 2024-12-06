<!-- start our courses -->
<section class="hero-courses section">
    <div class="container">
      <div class="row">
        <div class="col-lg-12 col-sm-12 col-xs-12">
          <div class="our-courses-header">
            <h2>KHÓA HỌC <span>CỦA CHÚNG TÔI</span></h2>
            <p>Các khóa học tại Liên đoàn Cử tạ - Thể hình TP.HCM được thiết kế dành cho mọi cấp độ, từ người mới bắt đầu đến vận động viên chuyên nghiệp. Với nội dung chuyên sâu và đội ngũ giảng viên giàu kinh nghiệm, chúng tôi mang đến lộ trình học tập bài bản, giúp bạn phát triển toàn diện cả về kỹ thuật lẫn kiến thức chuyên môn.</p>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-4 col-sm-6 col-xs-12">
          <div class="our-courses-box effectFadeIn">
            <div class="course-img"> <img src="https://liendoancutathehinhhcm.storage.googleapis.com/storage/images/icon-1-small.webp" alt="Gymfit" class="img-fluid">
            </div>
            <div class="course-content">
              <h2>Khóa học Kettlebells</h2>
              <p>Khóa học Kettlebells giúp bạn rèn luyện sức mạnh, cải thiện độ linh hoạt và tăng cường sức bền một cách toàn diện. Thông qua các bài tập đa dạng, bạn sẽ phát triển khả năng kiểm soát cơ thể, giảm nguy cơ chấn thương và đạt hiệu quả tối ưu trong thời gian ngắn.</p>
            </div>
          </div>
        </div>
        @for($i=2;$i<16;++$i)
          <div class="col-lg-4 col-sm-6 col-xs-12">
            <div class="our-courses-box effectFadeIn">
              <div class="course-img"> <img src="https://liendoancutathehinhhcm.storage.googleapis.com/storage/images/icon-{{ $i }}-small.webp" alt="Gymfit" class="img-fluid">
              </div>
              <div class="course-content">
                <h2>Khóa học Weightlifting</h2>
                <p>Khóa học Weightlifting tập trung vào kỹ thuật nâng tạ chuẩn xác, giúp bạn tăng cường sức mạnh, cải thiện khả năng thăng bằng và phát triển toàn diện nhóm cơ. Tham gia khóa học không chỉ giúp bạn vượt qua giới hạn bản thân mà còn rèn luyện ý chí và sự tự tin trong từng động tác.</p>
              </div>
            </div>
          </div>
        @endfor
      </div>

    </div>
  </section>
  <!-- end our courses -->