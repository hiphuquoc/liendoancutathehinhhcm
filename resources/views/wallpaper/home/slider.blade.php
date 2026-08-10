@php
    // Lấy sliders từ database, nếu không có thì dùng dữ liệu mặc định
    $dataSlider = !empty($sliders) && $sliders->isNotEmpty() 
        ? $sliders->map(function($slider) {
            return [
                'id' => $slider->id,
                'image' => !empty($slider->image) 
                    ? \App\Helpers\Image::getUrlImageCloud($slider->image) . '?v=' . (!empty($slider->updated_at) ? strtotime($slider->updated_at) : time())
                    : config('image.default'),
                'image_mobile' => !empty($slider->image_mobile) 
                    ? \App\Helpers\Image::getUrlImageCloud($slider->image_mobile) . '?v=' . (!empty($slider->updated_at) ? strtotime($slider->updated_at) : time())
                    : (!empty($slider->image) 
                        ? \App\Helpers\Image::getUrlImageCloud($slider->image) . '?v=' . (!empty($slider->updated_at) ? strtotime($slider->updated_at) : time())
                        : config('image.default')),
                'title' => $slider->title ?? null,
                'description' => $slider->description ?? null,
                'position' => $slider->position ?? 'left',
                'button_text' => $slider->button_text ?? null,
                'button_icon' => $slider->button_icon ?? null,
                'button_link' => $slider->button_link ?? null,
            ];
        })->toArray()
        : [
            [
                'image' => \App\Helpers\Image::getUrlImageCloud('storage/images/background-slider-1.webp'),
                'image_mobile' => \App\Helpers\Image::getUrlImageCloud('storage/images/background-slider-1.webp'),
                'title' => null,
                'description' => null,
                'position' => 'left',
                'button_text' => null,
                'button_icon' => null,
                'button_link' => null,
            ],
        ];
@endphp
<!-- START: Home Slider -->
<div class="homepageSlider" id="homepageSlider">
    <div id="js_lazyloadSliderDesktop_box" class="swiper sliderHome">
        <div class="swiper-wrapper">
            @foreach($dataSlider as $index => $slider)
                @php
                    $hasTitle = !empty($slider['title']);
                    $hasDesc = !empty($slider['description']);
                    $hasButton = !empty($slider['button_text']) && !empty($slider['button_link']);
                    $hasContent = $hasTitle || $hasDesc || $hasButton;
                    
                    // Xác định class cho content inner
                    $contentClasses = 'sliderHome_item_content_inner';
                    if ($hasTitle && !$hasDesc && !$hasButton) $contentClasses .= ' only-title';
                    elseif (!$hasTitle && $hasDesc && !$hasButton) $contentClasses .= ' only-description';
                    elseif (!$hasTitle && !$hasDesc && $hasButton) $contentClasses .= ' only-button';
                    elseif ($hasTitle && $hasDesc && !$hasButton) $contentClasses .= ' title-desc';
                    elseif ($hasTitle && !$hasDesc && $hasButton) $contentClasses .= ' title-button';
                    elseif (!$hasTitle && $hasDesc && $hasButton) $contentClasses .= ' desc-button';
                    else $contentClasses .= ' full';
                @endphp
                <div class="swiper-slide sliderHome_item {{ !$hasContent ? 'sliderHome_item--imageOnly' : '' }}" 
                     style="background-image: url({{ $slider['image'] ?? '' }});"
                     data-mobile-bg="{{ $slider['image_mobile'] ?? $slider['image'] ?? '' }}">
                    
                    @if($hasContent)
                        <div class="containerSlider containerSlider--{{ $slider['position'] ?? 'left' }}">
                            <div class="sliderHome_item_content sliderHome_item_content--{{ $slider['position'] ?? 'left' }}">
                                <div class="{{ $contentClasses }}">
                                    @if($hasTitle)
                                        <div class="sliderHome_item_content_title">
                                            {!! nl2br(e($slider['title'])) !!}
                                        </div>
                                    @endif
                                    @if($hasDesc)
                                        <div class="sliderHome_item_content_description">
                                            {!! nl2br(e($slider['description'])) !!}
                                        </div>
                                    @endif
                                    @if($hasButton)
                                        <div class="sliderHome_item_content_actions">
                                            <a href="{{ $slider['button_link'] }}" class="btn-brand-filled">
                                                @if(!empty($slider['button_icon']))
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="sliderHome_item_content_button_icon">
                                                        @if($slider['button_icon'] == 'arrow-right')
                                                            <path d="M5 12h14M12 5l7 7-7 7"/>
                                                        @elseif($slider['button_icon'] == 'arrow-left')
                                                            <path d="M19 12H5M12 19l-7-7 7-7"/>
                                                        @elseif($slider['button_icon'] == 'shopping-cart')
                                                            <path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                        @elseif($slider['button_icon'] == 'heart')
                                                            <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/>
                                                        @elseif($slider['button_icon'] == 'star')
                                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                        @elseif($slider['button_icon'] == 'check')
                                                            <path d="M20 6L9 17l-5-5"/>
                                                        @elseif($slider['button_icon'] == 'plus')
                                                            <path d="M12 5v14m7-7H5"/>
                                                        @elseif($slider['button_icon'] == 'play')
                                                            <path d="M8 5v14l11-7z"/>
                                                        @elseif($slider['button_icon'] == 'search')
                                                            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                                                        @elseif($slider['button_icon'] == 'eye')
                                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                                                        @endif
                                                    </svg>
                                                @endif
                                                <span>{{ $slider['button_text'] }}</span>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Pagination dots --}}
        @if(count($dataSlider) > 1)
            <div class="swiper-pagination"></div>

            {{-- Navigation arrows --}}
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        @endif
    </div>

    {{-- Scroll indicator --}}
    <a href="#id-neo" id="id-neo" class="sliderHome_scrollIndicator">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 5v14M19 12l-7 7-7-7"/>
        </svg>
    </a>
</div>
<!-- END: Home Slider -->

@push('headCustom')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
@endpush

@push('scriptCustom')
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function () {
            const slideCount = document.querySelectorAll('.sliderHome .swiper-slide').length;
            
            // Init Swiper
            const swiper = new Swiper('.sliderHome', {
                loop: slideCount > 1,
                autoplay: slideCount > 1 ? {
                    delay: 5000,
                    disableOnInteraction: false,
                    pauseOnMouseEnter: true,
                } : false,
                effect: 'fade',
                fadeEffect: {
                    crossFade: true
                },
                speed: 1000,
                pagination: slideCount > 1 ? {
                    el: '.swiper-pagination',
                    clickable: true,
                } : false,
                navigation: slideCount > 1 ? {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                } : false,
                breakpoints: {
                    0: {
                        allowTouchMove: slideCount > 1,
                    },
                    768: {
                        allowTouchMove: slideCount > 1,
                    }
                },
                on: {
                    slideChangeTransitionStart: function() {
                        // Animation cho content khi slide thay đổi
                        const activeSlide = this.slides[this.activeIndex];
                        const content = activeSlide?.querySelector('.sliderHome_item_content_inner');
                        if (content) {
                            content.style.animation = 'none';
                            content.offsetHeight; // trigger reflow
                            content.style.animation = 'sliderContentIn 0.8s cubic-bezier(0.22, 1, 0.36, 1) forwards';
                        }
                    }
                }
            });
            
            // Responsive background images
            function updateSliderBackgrounds() {
                const slides = document.querySelectorAll('.sliderHome_item');
                const isMobile = window.innerWidth <= 768;
                slides.forEach(slide => {
                    const mobileBg = slide.getAttribute('data-mobile-bg');
                    const currentBg = slide.getAttribute('data-original-bg');
                    
                    if (!currentBg) {
                        // Lưu original bg lần đầu
                        const style = slide.style.backgroundImage;
                        slide.setAttribute('data-original-bg', style);
                    }
                    
                    if (isMobile && mobileBg) {
                        slide.style.backgroundImage = `url(${mobileBg})`;
                    } else {
                        const originalBg = slide.getAttribute('data-original-bg');
                        if (originalBg) {
                            slide.style.backgroundImage = originalBg;
                        }
                    }
                });
            }
            
            updateSliderBackgrounds();
            let resizeTimeout;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(updateSliderBackgrounds, 150);
            });
        });
    </script>
@endpush