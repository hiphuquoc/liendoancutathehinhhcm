{{-- Referee List Section --}}
<section class="trainer-list-page">
    <div class="container">

        {{-- ═══ Header Section ═══ --}}
        <div class="trainer-list-header">
            <div class="section-badge">
                <i class="fa-solid fa-scale-balanced"></i>
                <span>Công bằng - Chính trực</span>
            </div>
            <h1 class="header-title">Trọng Tài <span>Của Chúng Tôi</span></h1>
            <p class="header-desc">
                Đội ngũ trọng tài của chúng tôi là những chuyên gia uy tín, được đào tạo bài bản 
                và giàu kinh nghiệm trong lĩnh vực Cử tạ – Thể hình. Với tinh thần công tâm, chuyên nghiệp 
                và kiến thức chuyên sâu, họ đảm bảo mọi giải đấu diễn ra công bằng, minh bạch và đúng chuẩn.
            </p>
            <div class="header-stats">
                <div class="stat-badge">
                    <div class="stat-icon"><i class="fa-solid fa-scale-balanced"></i></div>
                    <div class="stat-text">
                        <div class="stat-number">{{ count($trainers) ?? 0 }}+</div>
                        <div class="stat-label">Trọng tài</div>
                    </div>
                </div>
                <div class="stat-badge">
                    <div class="stat-icon"><i class="fa-solid fa-certificate"></i></div>
                    <div class="stat-text">
                        <div class="stat-number">100%</div>
                        <div class="stat-label">Chứng chỉ quốc gia</div>
                    </div>
                </div>
                <div class="stat-badge">
                    <div class="stat-icon"><i class="fa-solid fa-ranking-star"></i></div>
                    <div class="stat-text">
                        <div class="stat-number">50+</div>
                        <div class="stat-label">Giải đấu</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══ Search Bar ═══ --}}
        <div class="trainer-search-wrapper">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input 
                type="text" 
                id="refereeSearchInput" 
                placeholder="Tìm theo tên hoặc chuyên môn..." 
                class="trainer-search-input"
                autocomplete="off"
            >
        </div>

        {{-- ═══ Referees Grid ═══ --}}
        <div class="trainers-grid">
            @foreach($trainers as $trainer)
                @foreach($trainer->seos as $seo)
                    @if(!empty($seo->infoSeo->language) && $seo->infoSeo->language == $language)
                        @php
                            $fullName   = $trainer->name ?? $seo->infoSeo->title ?? '';
                            $job        = $trainer->position ?? '';
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
                            {{-- Image --}}
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

                            {{-- Body --}}
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

            {{-- No Result --}}
            <div id="noRefereeFound" class="no-trainer-found">
                <i class="fa-regular fa-face-meh no-result-icon"></i>
                <p>Không tìm thấy trọng tài phù hợp.</p>
            </div>
        </div>

    </div>
</section>

@push('scriptCustom')
<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function () {
    const searchInput    = document.getElementById('refereeSearchInput');
    const trainerCards   = document.querySelectorAll('.trainer-card');
    const noResult       = document.getElementById('noRefereeFound');
    let debounceTimer;

    function removeTones(str) {
        return str.normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .replace(/đ/g, "d").replace(/Đ/g, "D");
    }

    function filterItems() {
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

        noResult.classList.toggle('show', matchCount === 0);

        if (typeof lazyload === 'function') lazyload();
    }

    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(filterItems, 300);
    });
});
</script>
@endpush
