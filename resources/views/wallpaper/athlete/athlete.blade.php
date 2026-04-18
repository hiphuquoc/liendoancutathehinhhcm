{{-- Athlete (VĐV) list — layout/CSS shared with HLV list --}}
<section class="trainer-list-page">
    <div class="container">

        <div class="trainer-list-header">
            <div class="section-badge">
                <i class="fa-solid fa-medal"></i>
                <span>Đội ngũ vận động viên</span>
            </div>
            <h1 class="header-title">Vận Động Viên <br/> <span class="highlight">Của Chúng Tôi</span></h1>
            <p class="header-desc">
                Danh sách vận động viên được công nhận bởi Liên đoàn, với hồ sơ thành tích và thông tin minh bạch
                trên hệ thống.
            </p>
            <div class="header-stats">
                <div class="stat-badge">
                    <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
                    <div class="stat-text">
                        <div class="stat-number">100+</div>
                        <div class="stat-label">Vận động viên</div>
                    </div>
                </div>
                <div class="stat-badge">
                    <div class="stat-icon"><i class="fa-solid fa-trophy"></i></div>
                    <div class="stat-text">
                        <div class="stat-number">10+</div>
                        <div class="stat-label">Năm đồng hành</div>
                    </div>
                </div>
                <div class="stat-badge">
                    <div class="stat-icon"><i class="fa-solid fa-medal"></i></div>
                    <div class="stat-text">
                        <div class="stat-number">50+</div>
                        <div class="stat-label">Thành tích</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="trainer-search-wrapper">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input
                type="text"
                id="trainerSearchInput"
                placeholder="Tìm theo tên hoặc chuyên môn..."
                class="trainer-search-input"
                autocomplete="off"
            >
        </div>

        <div class="trainers-grid">
            @foreach($athletes as $athleteRow)
                @foreach($athleteRow->seos as $seo)
                    @if(!empty($seo->infoSeo->language) && $seo->infoSeo->language == $language)
                        @php
                            $fullName   = $athleteRow->name ?? $seo->infoSeo->title ?? '';
                            $job        = $athleteRow->position ?? '';
                            $slugFull   = $seo->infoSeo->slug_full ?? '#';

                            $defaultImage = config('image.default');
                            if(!empty($seo->infoSeo->image)) {
                                $imageSmall = \App\Helpers\Image::getUrlImageSmallByUrlImage($seo->infoSeo->image);
                                $imageMini  = \App\Helpers\Image::getUrlImageMiniByUrlImage($seo->infoSeo->image);
                            } else {
                                $imageSmall = $defaultImage;
                                $imageMini  = $defaultImage;
                            }
                        @endphp

                        <article class="trainer-card" data-name="{{ Str::lower($fullName) }}" data-job="{{ Str::lower($job) }}">
                            <a href="/{{ $slugFull }}" class="card-image" aria-label="Xem hồ sơ {{ $fullName }}">
                                <img class="lazyload"
                                     src="{{ $imageMini }}?{{ time() }}"
                                     data-src="{{ $imageSmall }}?{{ time() }}"
                                     alt="{{ $fullName }}"
                                     title="{{ $fullName }}"
                                     loading="lazy" />
                                <div class="card-overlay">
                                    <span class="overlay-btn">
                                        <i class="fa-solid fa-eye"></i> Xem hồ sơ
                                    </span>
                                </div>
                            </a>

                            <div class="card-body">
                                <h3 class="trainer-name">
                                    <a href="/{{ $slugFull }}">{{ $fullName }}</a>
                                </h3>
                                <p class="trainer-role">{{ $job }}</p>
                            </div>
                        </article>

                    @endif
                @endforeach
            @endforeach

            <div id="noTrainerFound" class="no-trainer-found">
                <i class="fa-regular fa-face-meh no-result-icon"></i>
                <p>Không tìm thấy vận động viên phù hợp.</p>
            </div>
        </div>

    </div>
</section>

@push('scriptCustom')
<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function () {
    const searchInput    = document.getElementById('trainerSearchInput');
    const trainerCards   = document.querySelectorAll('.trainer-card');
    const noTrainerFound = document.getElementById('noTrainerFound');
    let debounceTimer;

    function removeTones(str) {
        return str.normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .replace(/đ/g, "d").replace(/Đ/g, "D");
    }

    function filterTrainers() {
        const keyword = removeTones(searchInput.value.trim().toLowerCase());
        let matchCount = 0;

        trainerCards.forEach(card => {
            const name = removeTones((card.dataset.name || '').toLowerCase());
            const job  = removeTones((card.dataset.job  || '').toLowerCase());

            if (name.includes(keyword) || job.includes(keyword)) {
                card.style.display = '';
                matchCount++;
            } else {
                card.style.display = 'none';
            }
        });

        noTrainerFound.classList.toggle('show', matchCount === 0);

        if (typeof lazyload === 'function') lazyload();
    }

    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(filterTrainers, 300);
    });
});
</script>
@endpush
