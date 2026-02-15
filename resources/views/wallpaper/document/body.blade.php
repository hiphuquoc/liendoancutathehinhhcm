<!-- start document section -->
<section class="trainer-list-page" style="background:#fff;"> <!-- Keep white background if prefered, or remove for gray -->
  <div class="container">
    
    <div class="trainer-list-header">
        <div class="section-badge">
            <i class="fa-solid fa-folder-open"></i>
            <span>Kho tài liệu</span>
        </div>
        <h1 class="header-title">TÀI LIỆU <span class="highlight">CỦA LIÊN ĐOÀN</span></h1>
        <p class="header-desc">
            Liên đoàn Cử tạ - Thể hình TP.HCM cung cấp hệ thống tài liệu đào tạo toàn diện, phù hợp với mọi cấp độ từ người mới làm quen đến vận động viên chuyên nghiệp. Với nội dung chuyên sâu và được biên soạn bởi đội ngũ chuyên gia giàu kinh nghiệm, tài liệu của chúng tôi không chỉ giúp bạn nắm vững kỹ thuật mà còn trang bị nền tảng kiến thức chuyên môn vững chắc.
        </p>
    </div>

    <div class="row">
      @include('wallpaper.document.table', compact('documents'))
    </div>

  </div>
</section>
<!-- end document section -->