<!-- start trainer section -->
<section class="hero-trainers">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-sm-12 col-xs-12">
                <div class="trainer-header">
                    <h2>ĐỐI TÁC – NHÀ TÀI TRỢ</h2>
                    <p>Chúng tôi trân trọng sự đồng hành của các đối tác và nhà tài trợ – những thương hiệu, doanh nghiệp và tổ chức uy tín đã góp phần tạo nên sự phát triển bền vững của Liên đoàn Cử tạ – Thể hình TP.HCM. Thông qua hợp tác và tài trợ, quý đối tác không chỉ nâng cao hình ảnh thương hiệu mà còn cùng chúng tôi lan tỏa giá trị thể thao, sức khỏe và tinh thần vươn lên đến cộng đồng.</p>
                </div>
            </div>

            {{-- <!-- Search box -->
            <div class="col-lg-12 col-sm-12 col-xs-12">
                <div class="trainer-search-container">
                    <input 
                        type="text" 
                        id="trainerSearchInput" 
                        placeholder="Tìm kiếm huấn luyện viên..." 
                        class="trainer-search-input"
                    >
                </div>
            </div> --}}

            <div class="col-lg-12 col-sm-12 col-xs-12">
                <div class="trainer_all">
                    @for($i=0;$i<6;++$i)
                        <div class="trainer_box">
                            <div class="img_trainer">
                                <a href="/doi-tac-nha-tai-tro/test" class="img_wrapper">
                                    <img class="lazyload" src="https://liendoancutathehinhhcm.storage.googleapis.com/storage/images/phong-tap-mau-mini.webp" data-src="https://liendoancutathehinhhcm.storage.googleapis.com/storage/images/phong-tap-mau-large.webp" alt="Trần Tuấn Anh " title="Trần Tuấn Anh " loading="lazy" style="">
                                    <div class="sponsor_sign">Đối tác kim cương</div>
                                </a>
                            </div>
                            <div class="trainer_con">
                                <a href="/doi-tac-nha-tai-tro/test"><h3>Gym Center Q3</h3></a>
                                <p>219 Lý Thường Kiệt, Phường 15, Quận 11, TP. Hồ Chí Minh</p>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
</section>
<!-- end trainer section -->

@push('scriptCustom')
<script type="text/javascript">

</script>
@endpush


