<!-- start trainer section -->
<section class="hero-trainers">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-sm-12 col-xs-12">
                <div class="trainer-header">
                    <h2>TRỌNG TÀI <span>CỦA CHÚNG TÔI</span></h2>
                    <p>Đội ngũ trọng tài của chúng tôi là những chuyên gia uy tín, được đào tạo bài bản và giàu kinh nghiệm trong lĩnh vực Cử tạ – Thể hình. Với tinh thần công tâm, chuyên nghiệp và kiến thức chuyên sâu, họ đảm bảo mọi giải đấu diễn ra công bằng, minh bạch và đúng chuẩn quốc gia – quốc tế.</p>
                </div>
            </div>

            <!-- Search box -->
            <div class="col-lg-12 col-sm-12 col-xs-12">
                <div class="trainer-search-container">
                    <input 
                        type="text" 
                        id="trainerSearchInput" 
                        placeholder="Tìm kiếm Trọng tài..." 
                        class="trainer-search-input"
                    >
                </div>
            </div>

            <div class="col-lg-12 col-sm-12 col-xs-12">
                <div class="trainer_all">
                    @foreach($trainers as $trainer)
                        @foreach($trainer->seos as $seo)
                            @if(!empty($seo->infoSeo->language) && $seo->infoSeo->language == $language)
                                @php
                                    $fullName   = $trainer->name ?? $seo->infoSeo->title ?? '';
                                    $job        = $trainer->position ?? '';

                                    // Kiểm tra ảnh
                                    $defaultImage = config('image.default'); // ảnh mặc định
                                    if(!empty($seo->infoSeo->image)) {
                                        $imageSmall = \App\Helpers\Image::getUrlImageSmallByUrlImage($seo->infoSeo->image);
                                        $imageMini  = \App\Helpers\Image::getUrlImageMiniByUrlImage($seo->infoSeo->image);
                                    } else {
                                        // Ảnh mặc định
                                        $imageSmall = $defaultImage;
                                        $imageMini  = $defaultImage;
                                    }
                                @endphp

                                <div class="trainer_box" data-name="{{ Str::lower($fullName) }}" data-job="{{ Str::lower($job) }}">
                                    <div class="img_trainer">
                                        <a href="/{{ $seo->infoSeo->slug_full }}" class="img_wrapper">
                                            <img class="lazyload" src="{{ $imageMini }}?{{ time() }}" data-src="{{ $imageSmall }}?{{ time() }}" alt="{{ $fullName }}" title="{{ $fullName }}" loading="lazy" />
                                        </a>
                                        {{-- <div class="trainer_social">
                                            <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                                            <a href="#"><i class="fa-brands fa-youtube"></i></a>
                                            <a href="#"><i class="fa-brands fa-instagram"></i></a>
                                        </div> --}}
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
                <!-- Box hiện khi không có kết quả -->
                <div id="noTrainerFound" class="no-trainer-found">Hiện không có Trọng tài phù hợp</div>
            </div>
        </div>
    </div>
</section>
<!-- end trainer section -->

@push('scriptCustom')
<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function () {
        const searchInput = document.getElementById('trainerSearchInput');
        const trainerBoxes = document.querySelectorAll('.trainer_box');
        const noTrainerFound = document.getElementById('noTrainerFound');

        let debounceTimer;

        // Hàm bỏ dấu
        function removeVietnameseTones(str) {
            return str
                .normalize("NFD")
                .replace(/[\u0300-\u036f]/g, "")
                .replace(/đ/g, "d").replace(/Đ/g, "D");
        }

        function filterTrainers() {
            const keywordRaw = searchInput.value.trim().toLowerCase();
            const keyword = removeVietnameseTones(keywordRaw);
            let hasResult = false;

            trainerBoxes.forEach(box => {
                // lấy tên và job từ data
                const nameRaw = box.getAttribute('data-name') || '';
                const jobRaw  = box.getAttribute('data-job') || '';

                const name = removeVietnameseTones(nameRaw.toLowerCase());
                const job  = removeVietnameseTones(jobRaw.toLowerCase());

                if (name.includes(keyword) || job.includes(keyword)) {
                    box.style.display = '';
                    hasResult = true;
                } else {
                    box.style.display = 'none';
                }
            });

            // hiển thị/ẩn box "không có kết quả"
            if (!hasResult) {
                noTrainerFound.classList.add('show');
            } else {
                noTrainerFound.classList.remove('show');
            }

            lazyload();
        }

        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(filterTrainers, 500); // delay 0.5s
        });
    });

    function removeVietnameseTones(str) {
        return str
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "") // bỏ dấu
            .replace(/đ/g, "d").replace(/Đ/g, "D");
    }
</script>
@endpush


