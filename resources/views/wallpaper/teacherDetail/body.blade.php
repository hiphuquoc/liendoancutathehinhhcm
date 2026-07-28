<div class="trainer-profile-container">
    <div class="container">
        <div class="trainer-profile-grid">
            <!-- Sidebar -->
            <div class="profile-sidebar">
                <!-- Profile Card -->
                <div class="sidebar-card profile-card">
                    <div class="profile-image-wrapper">
                        @php
                            $imageUrl = $item->seo->image ?? $item->image;
                            if (!empty($imageUrl) && !str_starts_with($imageUrl, 'http')) {
                                $avatar = 'https://liendoancutathehinhhcm.storage.googleapis.com/' . $imageUrl;
                            } else {
                                $avatar = $imageUrl ?? 'https://via.placeholder.com/300x400';
                            }
                        @endphp
                        <img src="{{ $avatar }}" alt="{{ $item->name ?? 'Trainer' }}" class="profile-image">
                        <div class="status-badge"><i class="fa-solid fa-circle-check"></i> Verified Coach</div>
                    </div>
                    <div class="profile-info">
                        <h1 class="profile-name">{{ $item->name ?? 'Huấn Luyện Viên' }}</h1>
                        <p class="profile-title">{{ $item->position ?? 'Chuyên gia thể hình & cử tạ' }}</p>
                        
                        <div class="profile-rating">
                            @php
                                $rating = $item->seo->rating_aggregate_star ?? 5;
                                $ratingCount = $item->seo->rating_aggregate_count ?? 1;
                                $fullStars = floor($rating);
                                $halfStar = ($rating - $fullStars) >= 0.5;
                            @endphp
                            @for($i=1; $i<=5; $i++)
                                @if($i <= $fullStars)
                                    <i class="fa-solid fa-star text-warning"></i>
                                @elseif($halfStar && $i == $fullStars + 1)
                                    <i class="fa-solid fa-star-half-stroke text-warning"></i>
                                @else
                                    <i class="fa-regular fa-star text-muted"></i>
                                @endif
                            @endfor
                            <span>({{ $rating }}/5)</span>
                        </div>

                        <div class="profile-actions">
                            <a href="#contact-section" class="btn btn-primary btn-block">Liên hệ ngay</a>
                        </div>
                    </div>
                </div>

                <!-- Personal Info -->
                <div class="sidebar-card info-card">
                    <h3 class="card-title">Thông tin cá nhân</h3>
                    <ul class="info-list">
                        <li>
                            <div class="icon-wrap"><i class="fa-solid fa-location-dot"></i></div>
                            <div class="info-content">
                                <span class="label">Khu vực</span>
                                <span class="value">{{ $item->area ?: 'TP. Hồ Chí Minh' }}</span>
                            </div>
                        </li>
                        <li>
                            <div class="icon-wrap"><i class="fa-solid fa-briefcase"></i></div>
                            <div class="info-content">
                                <span class="label">Kinh nghiệm</span>
                                <span class="value">
                                    @if(!empty($item->years_experience))
                                        {{ $item->years_experience }}+ Năm
                                    @else
                                        5+ Năm
                                    @endif
                                </span>
                            </div>
                        </li>
                        <li>
                            <div class="icon-wrap"><i class="fa-solid fa-language"></i></div>
                            <div class="info-content">
                                <span class="label">Ngôn ngữ</span>
                                <span class="value">{{ \App\Helpers\SpokenLanguage::display($item->languages ?? null) }}</span>
                            </div>
                        </li>
                        @if(!empty($item->email))
                            <li>
                                <div class="icon-wrap"><i class="fa-solid fa-envelope"></i></div>
                                <div class="info-content">
                                    <span class="label">Email</span>
                                    <span class="value text-break">{{ $item->email }}</span>
                                </div>
                            </li>
                        @endif
                        @if(!empty($item->phone))
                            <li>
                                <div class="icon-wrap"><i class="fa-solid fa-phone"></i></div>
                                <div class="info-content">
                                    <span class="label">Điện thoại</span>
                                    <span class="value">{{ $item->phone }}</span>
                                </div>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="profile-content">
                <!-- About Section -->
                <div class="content-box about-section">
                    <h2 class="section-title"><i class="fa-solid fa-user"></i> Giới thiệu</h2>
                    <div class="content-text">
                        @if(!empty($item->seo->description))
                            {!! $item->seo->description !!}
                        @else
                            <p>Là một huấn luyện viên chuyên nghiệp với hơn 5 năm kinh nghiệm trong lĩnh vực thể hình và cử tạ. Tôi chuyên giúp các học viên đạt được mục tiêu về sức khỏe, vóc dáng và sức mạnh thông qua các phương pháp tập luyện khoa học và chế độ dinh dưỡng hợp lý.</p>
                            <p>Phương châm huấn luyện: "Kỷ luật là sức mạnh". Tôi cam kết đồng hành cùng bạn trên mọi chặng đường thay đổi bản thân.</p>
                        @endif
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="stats-grid">
                    @php
                        $statLearner = isset($item->total_learner) && (int) $item->total_learner > 0 ? (int) $item->total_learner . '+' : '--';
                        $statHours   = isset($item->total_teaching_hour) && (int) $item->total_teaching_hour > 0 ? (int) $item->total_teaching_hour . '+' : '--';
                        $statPrize   = isset($item->total_prize) && (int) $item->total_prize > 0 ? (int) $item->total_prize . '+' : '--';
                    @endphp
                    <div class="stat-card">
                        <div class="icon-box"><i class="fa-solid fa-users"></i></div>
                        <div class="stat-info">
                            <span class="number">{{ $statLearner }}</span>
                            <span class="label">Học viên</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="icon-box"><i class="fa-solid fa-clock"></i></div>
                        <div class="stat-info">
                            <span class="number">{{ $statHours }}</span>
                            <span class="label">Giờ dạy</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="icon-box"><i class="fa-solid fa-trophy"></i></div>
                        <div class="stat-info">
                            <span class="number">{{ $statPrize }}</span>
                            <span class="label">Giải thưởng</span>
                        </div>
                    </div>
                </div>

                <!-- Achievements Section -->
                <div class="content-box achievement-section">
                    <h2 class="section-title"><i class="fa-solid fa-medal"></i> Thành tích nổi bật</h2>
                    <ul class="achievement-list">
                        @if($item->achievements->isNotEmpty())
                            @foreach($item->achievements as $achievement)
                                <li class="achievement-item">
                                    <div class="achievement-icon"><i class="fa-solid fa-star"></i></div>
                                    <div class="achievement-content">
                                        {!! $achievement->content !!}
                                    </div>
                                </li>
                            @endforeach
                        @else
                            <p class="text-muted">Đang cập nhật thành tích...</p>
                        @endif
                    </ul>
                </div>

                <!-- Skills Section (Moved from Sidebar) -->
                <div class="content-box skills-section-main">
                    <h2 class="section-title"><i class="fa-solid fa-bolt"></i> Kỹ năng chuyên môn</h2>
                    <div class="skills-grid">
                        @if($item->skills->isNotEmpty())
                            @foreach($item->skills as $skill)
                                <div class="skill-item-main">
                                    <div class="skill-header">
                                        <span class="skill-name">{{ $skill->skill }}</span>
                                        <span class="skill-percent">{{ $skill->percent }}%</span>
                                    </div>
                                    <div class="progress-bar-main">
                                        <div class="fill" style="width: {{ $skill->percent }}%" data-percent="{{ $skill->percent }}"></div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <!-- Fallback -->
                            <div class="skill-item-main">
                                <div class="skill-header">
                                    <span class="skill-name">Personal Training</span>
                                    <span class="skill-percent">95%</span>
                                </div>
                                <div class="progress-bar-main"><div class="fill" style="width: 95%" data-percent="95"></div></div>
                            </div>
                            <div class="skill-item-main">
                                <div class="skill-header">
                                    <span class="skill-name">Nutrition Planning</span>
                                    <span class="skill-percent">90%</span>
                                </div>
                                <div class="progress-bar-main"><div class="fill" style="width: 90%" data-percent="90"></div></div>
                            </div>
                            <div class="skill-item-main">
                                <div class="skill-header">
                                    <span class="skill-name">Weightlifting</span>
                                    <span class="skill-percent">85%</span>
                                </div>
                                <div class="progress-bar-main"><div class="fill" style="width: 85%" data-percent="85"></div></div>
                            </div>
                            <div class="skill-item-main">
                                <div class="skill-header">
                                    <span class="skill-name">Cardio & Endurance</span>
                                    <span class="skill-percent">80%</span>
                                </div>
                                <div class="progress-bar-main"><div class="fill" style="width: 80%" data-percent="80"></div></div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Degrees & Certifications Section -->
                <div class="content-box degree-section">
                    <h2 class="section-title"><i class="fa-solid fa-graduation-cap"></i> Bằng cấp & Chứng chỉ</h2>
                    <div class="degree-list">
                        @if($item->degrees->isNotEmpty())
                            @foreach($item->degrees as $degree)
                                <div class="degree-item">
                                    <div class="degree-icon"><i class="fa-solid fa-certificate"></i></div>
                                    <div class="degree-info">
                                        <h3 class="degree-title">{{ $degree->title }}</h3>
                                        <p class="degree-school">{{ $degree->school }}</p>
                                        @if($degree->contents->isNotEmpty())
                                            <div class="degree-details">
                                                @foreach($degree->contents as $content)
                                                    <div class="detail-line">{!! $content->content !!}</div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted">Đang cập nhật bằng cấp...</p>
                        @endif
                    </div>
                </div>

                <!-- Experience Section -->
                <div class="content-box experience-section">
                    <h2 class="section-title"><i class="fa-solid fa-briefcase"></i> Kinh nghiệm làm việc</h2>
                    <div class="timeline-content">
                        @if($item->experiences->isNotEmpty())
                            @foreach($item->experiences as $exp)
                                <div class="timeline-item">
                                    <div class="timeline-header">
                                        <h3 class="timeline-title">{{ $exp->title }}</h3>
                                        @if(!empty($exp->company))
                                            <span class="timeline-company"><i class="fa-solid fa-building"></i> {{ $exp->company }}</span>
                                        @endif
                                    </div>
                                    @if($exp->contents->isNotEmpty())
                                        <ul class="timeline-details">
                                            @foreach($exp->contents as $content)
                                                <li>{!! $content->content !!}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted">Đang cập nhật kinh nghiệm...</p>
                        @endif
                    </div>
                </div>
                
                <!-- SEO Content (if extra info exists) -->
                @if($item->seo && $item->seo->contents->isNotEmpty())
                    <div class="content-box seo-section">
                        <h2 class="section-title"><i class="fa-solid fa-file-invoice"></i> Thông tin thêm</h2>
                        <div class="article-content">
                            @foreach($item->seo->contents as $content)
                                <div class="seo-content-block">
                                    {!! $content->content !!}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif


                <!-- Gallery Section -->
                <div class="content-box gallery-section">
                    <h2 class="section-title"><i class="fa-solid fa-images"></i> Hình ảnh hoạt động</h2>
                    @if(!empty($item->activityImages) && $item->activityImages->isNotEmpty())
                        <div class="gallery-grid">
                            @foreach($item->activityImages as $actImg)
                                @php
                                    $imgUrl = $actImg->image_url;
                                    $thumbUrl = !empty($actImg->image) ? \App\Helpers\Image::getUrlImageSmallByUrlImage($actImg->image) : $imgUrl;
                                @endphp
                                <a href="{{ $imgUrl }}" class="glightbox gallery-item" data-gallery="profile-gallery" title="Hình ảnh hoạt động">
                                    <img src="{{ $thumbUrl }}" alt="Hình ảnh hoạt động" loading="lazy">
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">Chưa có hình ảnh hoạt động.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
