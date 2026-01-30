@extends('layouts.admin')

@section('content')
<div class="adminContentPage">
    <div class="adminContentPage_content">
        <div class="companyManagementPage_section companyManagementPage_section--tracked">
            <div class="companyManagementPage_section_header companyManagementPage_section_header--video">
                <div class="companyManagementPage_section_header_left">
                    <div class="companyManagementPage_section_header_iconWrapper companyManagementPage_section_header_iconWrapper--video">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z"/>
                        </svg>
                    </div>
                    <div class="companyManagementPage_section_header_info">
                        <h2 class="companyManagementPage_section_title">
                            Danh sách Video
                        </h2>
                        <p class="companyManagementPage_section_desc">Quản lý video trong hệ thống</p>
                    </div>
                </div>
                <div class="companyManagementPage_section_header_right">
                    <div class="adminContentPage_stats">
                        <div class="adminContentPage_stats_item">
                            <span class="adminContentPage_stats_label">Tổng số:</span>
                            <span class="adminContentPage_stats_value">{{ $list->total() ?? 0 }}</span>
                        </div>
                        <div class="adminContentPage_stats_item adminContentPage_stats_viewPerPage">
                            <label class="adminContentPage_stats_viewPerPage_label">Hiển thị:</label>
                            <select class="adminContentPage_stats_viewPerPage_select" onchange="settingView('viewVideoInfo', this.value);">
                                @foreach(config('setting.admin_array_number_view') as $item)
                                    <option value="{{ $item }}" {{ $viewPerPage == $item ? 'selected' : '' }}>{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>
                        <a href="{{ route('admin.video.view', ['type' => 'create']) }}" class="adminButton adminButton--primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            <span>Thêm Video</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="companyManagementPage_section_body">
                <!-- Search & Filter Bar -->
                <form id="formSearch" method="get" action="{{ route('admin.video.list') }}" class="adminContentPage_searchBar adminContentPage_searchBar--withFilter">
                    <div class="adminContentPage_searchBar_grid">
                        <!-- Search Input -->
                        <div class="adminContentPage_searchBar_inputWrapper">
                            <svg class="adminContentPage_searchBar_icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"/>
                                <path d="m21 21-4.35-4.35"/>
                            </svg>
                            <input 
                                type="text" 
                                class="adminContentPage_searchBar_input" 
                                name="search_name" 
                                id="videoSearchInput"
                                placeholder="Tìm kiếm theo tiêu đề..." 
                                value="{{ $params['search_name'] ?? '' }}"
                            />
                        </div>
                        
                        <!-- Category Filter -->
                        @if(!empty($categories))
                            <div class="adminContentPage_searchBar_filter">
                                @php
                                    $categoryOptions = [];
                                    foreach($categories as $category) {
                                        $categoryOptions[$category] = $category;
                                    }
                                @endphp
                                @include('admin.components.formSelect', [
                                    'name' => 'category',
                                    'value' => $params['category'] ?? '',
                                    'options' => $categoryOptions,
                                    'placeholder' => 'Tất cả danh mục',
                                    'class' => 'adminContentPage_searchBar_filterSelect'
                                ])
                            </div>
                        @endif
                    </div>
                </form>

                <!-- Message -->
                @include('admin.components.formMessage')

                <!-- Video List -->
                @if(!empty($list) && $list->isNotEmpty())
                    <div class="adminVideoManagement_list">
                        @foreach($list as $item)
                            <div class="adminVideoManagement_listItem" id="oneItem-{{ $item->id }}">
                                <div class="adminVideoManagement_listItem_thumbnail">
                                    @if(!empty($item->thumbnail_url))
                                        <img src="{{ $item->thumbnail_url }}" alt="{{ $item->title }}" />
                                        <div class="adminVideoManagement_listItem_overlay"></div>
                                    @else
                                        <div class="adminVideoManagement_listItem_placeholder">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    
                                    <!-- Top Left: Status Badge -->
                                    <div class="adminVideoManagement_listItem_status">
                                        @if($item->status)
                                            <span class="adminBadge adminBadge--success">Đang hiển thị</span>
                                        @else
                                            <span class="adminBadge adminBadge--secondary">Ẩn</span>
                                        @endif
                                    </div>
                                    
                                    <!-- Center: Play Button -->
                                    @if(!empty($item->file_cloud))
                                        <button 
                                            class="adminVideoManagement_listItem_playButton" 
                                            data-video-id="{{ $item->id }}"
                                            data-video-url="{{ $item->video_url }}"
                                            data-video-title="{{ $item->title }}"
                                            data-video-thumbnail="{{ $item->thumbnail_url ?? '' }}"
                                            data-video-description="{{ $item->description ?? '' }}"
                                            data-video-category="{{ $item->category ?? '' }}"
                                            data-video-status="{{ $item->status ? '1' : '0' }}"
                                            onclick="openVideoModalFromButton(this)"
                                            title="Xem video">
                                            <svg viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M8 5v14l11-7z"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                                <div class="adminVideoManagement_listItem_content">
                                    <div class="adminVideoManagement_listItem_contentInner">
                                        <h3 class="adminVideoManagement_listItem_title">{{ $item->title }}</h3>
                                    @if(!empty($item->description))
                                        <p class="adminVideoManagement_listItem_description">{{ Str::limit($item->description, 120) }}</p>
                                    @endif
                                    <div class="adminVideoManagement_listItem_footer">
                                        @if(!empty($item->category))
                                            <span class="adminVideoManagement_listItem_categoryLabel">{{ $item->category }}</span>
                                        @endif
                                        <span class="adminVideoManagement_listItem_date">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                                <line x1="16" y1="2" x2="16" y2="6"/>
                                                <line x1="8" y1="2" x2="8" y2="6"/>
                                                <line x1="3" y1="10" x2="21" y2="10"/>
                                            </svg>
                                            {{ $item->created_at->format('d/m/Y') }}
                                        </span>
                                    </div>
                                    </div>
                                    <!-- Actions (bên phải content) -->
                                    <div class="adminVideoManagement_listItem_actions">
                                        @if(!empty($item->file_cloud))
                                            <button 
                                                class="adminVideoManagement_listItem_action" 
                                                data-video-id="{{ $item->id }}"
                                                data-video-url="{{ $item->video_url }}"
                                                data-video-title="{{ $item->title }}"
                                                data-video-thumbnail="{{ $item->thumbnail_url ?? '' }}"
                                                data-video-description="{{ $item->description ?? '' }}"
                                                data-video-category="{{ $item->category ?? '' }}"
                                                data-video-status="{{ $item->status ? '1' : '0' }}"
                                                onclick="openVideoModalFromButton(this)"
                                                title="Xem video">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                    <circle cx="12" cy="12" r="3"/>
                                                </svg>
                                            </button>
                                        @endif
                                        <a href="{{ route('admin.video.view', ['id' => $item->id, 'type' => 'edit']) }}" class="adminVideoManagement_listItem_action" title="Chỉnh sửa">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                            </svg>
                                        </a>
                                        <button onclick="deleteItem({{ $item->id }})" class="adminVideoManagement_listItem_action adminVideoManagement_listItem_action--danger" title="Xóa">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if($list->hasPages())
                        <div class="adminContentPage_pagination">
                            {{ $list->appends(request()->query())->links('admin.template.paginate') }}
                        </div>
                    @endif
                @else
                    <div class="adminContentPage_empty">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z"/>
                        </svg>
                        <h3>Chưa có video nào</h3>
                        <p>Bắt đầu bằng cách thêm video mới vào hệ thống</p>
                        <a href="{{ route('admin.video.view', ['type' => 'create']) }}" class="adminButton adminButton--primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            <span>Thêm Video</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Video Preview Modal -->
<div id="videoPreviewModal" class="adminVideoModal">
    <div class="adminVideoModal_overlay" onclick="closeVideoModal()"></div>
    <div class="adminVideoModal_container">
        <button class="adminVideoModal_close" onclick="closeVideoModal()" title="Đóng">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
        
        <!-- Navigation Buttons -->
        <button id="videoModalPrev" class="adminVideoModal_nav adminVideoModal_nav--prev" onclick="navigateVideo(-1)" title="Video trước">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
        </button>
        <button id="videoModalNext" class="adminVideoModal_nav adminVideoModal_nav--next" onclick="navigateVideo(1)" title="Video tiếp theo">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
        </button>
        
        <div class="adminVideoModal_content">
            <div class="adminVideoModal_playerSection">
                <div class="adminVideoModal_player">
                    <video 
                        id="videoPreviewPlayer"
                        controls 
                        class="adminVideoModal_video"
                        preload="metadata"
                    >
                        Trình duyệt của bạn không hỗ trợ video HTML5.
                    </video>
                </div>
                
                <!-- Video Info -->
                <div class="adminVideoModal_info">
                    <div class="adminVideoModal_info_header">
                        <h1 id="videoPreviewTitle" class="adminVideoModal_info_title"></h1>
                        <div class="adminVideoModal_info_badges">
                            <span id="videoPreviewStatus" class="adminVideoModal_status"></span>
                        </div>
                    </div>
                    <div id="videoPreviewDescription" class="adminVideoModal_info_description"></div>
                    <div class="adminVideoModal_info_meta">
                        <div class="adminVideoModal_info_metaItem">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                            <span id="videoPreviewDate"></span>
                        </div>
                    </div>
                    <div class="adminVideoModal_info_actions">
                        <a id="videoPreviewEditLink" href="#" class="adminButton adminButton--primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                            <span>Chỉnh sửa video</span>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Related Videos Sidebar -->
            <div id="videoModalRelated" class="adminVideoModal_related">
                <h3 class="adminVideoModal_related_title">Danh sách video</h3>
                <div id="videoModalRelatedList" class="adminVideoModal_related_list"></div>
            </div>
        </div>
    </div>
</div>

@push('scriptCustom')
<script>
function deleteItem(id) {
    if (!confirm('Bạn có chắc chắn muốn xóa video này không?')) {
        return;
    }
    
    window.location.href = '{{ route("admin.video.delete") }}?id=' + id;
}

// Auto submit khi custom selectbox thay đổi
function setupCategorySelectAutoSubmit() {
    const categorySelectContainer = document.querySelector('.adminContentPage_searchBar_filterSelect .adminCustomSelect');
    if (!categorySelectContainer) return;
    
    const hiddenInput = categorySelectContainer.querySelector('input[type="hidden"][name="category"]');
    if (!hiddenInput) return;
    
    let lastValue = hiddenInput.value || '';
    
    function submitForm() {
        document.getElementById('formSearch').submit();
    }
    
    // Sử dụng setInterval để kiểm tra thay đổi (fallback)
    const checkInterval = setInterval(function() {
        const currentValue = hiddenInput.value || '';
        if (currentValue !== lastValue) {
            lastValue = currentValue;
            clearInterval(checkInterval);
            setTimeout(submitForm, 100);
        }
    }, 100);
    
    // Lắng nghe click trên option để submit ngay
    const optionsContainer = categorySelectContainer.querySelector('.adminCustomSelect_options');
    if (optionsContainer) {
        optionsContainer.addEventListener('click', function(e) {
            const option = e.target.closest('.adminCustomSelect_option');
            if (option) {
                setTimeout(() => {
                    const newValue = hiddenInput.value || '';
                    if (newValue !== lastValue) {
                        lastValue = newValue;
                        clearInterval(checkInterval);
                        submitForm();
                    }
                }, 150);
            }
        });
    }
    
    // MutationObserver để theo dõi thay đổi value
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                const currentValue = hiddenInput.value || '';
                if (currentValue !== lastValue) {
                    lastValue = currentValue;
                    clearInterval(checkInterval);
                    setTimeout(submitForm, 100);
                }
            }
        });
    });
    
    observer.observe(hiddenInput, {
        attributes: true,
        attributeFilter: ['value']
    });
}

// Debounce cho search input
function setupSearchAutoSubmit() {
    const searchInput = document.getElementById('videoSearchInput');
    if (!searchInput) return;
    
    let searchTimeout;
    
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            document.getElementById('formSearch').submit();
        }, 500); // 500ms debounce
    });
    
    // Submit on Enter
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            clearTimeout(searchTimeout);
            document.getElementById('formSearch').submit();
        }
    });
}

// Video Modal State
let videoModalState = {
    currentIndex: -1,
    videos: []
};

// Collect all videos from page
function collectVideos() {
    const cards = document.querySelectorAll('.adminVideoManagement_listItem');
    videoModalState.videos = [];
    
    cards.forEach((card, index) => {
        const playButton = card.querySelector('.adminVideoManagement_listItem_playButton');
        if (playButton) {
            const dateEl = card.querySelector('.adminVideoManagement_listItem_date');
            const dateText = dateEl ? dateEl.textContent.trim().replace(/\s+/g, ' ') : '';
            
            videoModalState.videos.push({
                id: playButton.getAttribute('data-video-id'),
                url: playButton.getAttribute('data-video-url'),
                title: playButton.getAttribute('data-video-title'),
                thumbnail: playButton.getAttribute('data-video-thumbnail'),
                description: playButton.getAttribute('data-video-description'),
                category: playButton.getAttribute('data-video-category'),
                status: playButton.getAttribute('data-video-status') === '1',
                date: dateText
            });
        }
    });
}

// Video Modal Functions
function openVideoModalFromButton(button) {
    collectVideos();
    
    const id = button.getAttribute('data-video-id');
    const videoUrl = button.getAttribute('data-video-url');
    const title = button.getAttribute('data-video-title');
    const thumbnail = button.getAttribute('data-video-thumbnail');
    const description = button.getAttribute('data-video-description');
    const category = button.getAttribute('data-video-category');
    const status = button.getAttribute('data-video-status') === '1';
    
    // Find current index
    videoModalState.currentIndex = videoModalState.videos.findIndex(v => v.id === id);
    
    openVideoModal(id, videoUrl, title, thumbnail, description, category, status);
    updateRelatedVideos();
    updateNavigationButtons();
}

function openVideoModal(id, videoUrl, title, thumbnail, description, category, status, date) {
    const modal = document.getElementById('videoPreviewModal');
    const player = document.getElementById('videoPreviewPlayer');
    const titleEl = document.getElementById('videoPreviewTitle');
    const statusEl = document.getElementById('videoPreviewStatus');
    const descriptionEl = document.getElementById('videoPreviewDescription');
    const dateEl = document.getElementById('videoPreviewDate');
    const editLink = document.getElementById('videoPreviewEditLink');
    
    // Set video source
    player.poster = thumbnail || '';
    player.innerHTML = '';
    const source = document.createElement('source');
    source.src = videoUrl;
    source.type = 'video/mp4';
    player.appendChild(source);
    
    // Set video info
    titleEl.textContent = title || '';
    descriptionEl.innerHTML = (description || '').replace(/\n/g, '<br>');

    // Set status
    statusEl.textContent = status ? 'Đang hiển thị' : 'Ẩn';
    statusEl.className = 'adminVideoModal_status adminBadge ' + (status ? 'adminBadge--success' : 'adminBadge--secondary');
    
    // Set date
    if (date) {
        dateEl.textContent = date;
    }
    
    // Set edit link
    editLink.href = '{{ route("admin.video.view", ["id" => ":id", "type" => "edit"]) }}'.replace(':id', id);
    
    // Show modal
    modal.classList.add('adminVideoModal--active');
    document.body.style.overflow = 'hidden';
    
    // Load and play video
    player.load();
    setTimeout(() => {
        player.play().catch(e => {
            console.log('Auto-play prevented:', e);
        });
    }, 300);
}

function navigateVideo(direction) {
    if (videoModalState.videos.length === 0) return;
    
    const newIndex = videoModalState.currentIndex + direction;
    if (newIndex < 0 || newIndex >= videoModalState.videos.length) return;
    
    const video = videoModalState.videos[newIndex];
    videoModalState.currentIndex = newIndex;
    
    openVideoModal(
        video.id,
        video.url,
        video.title,
        video.thumbnail,
        video.description,
        video.category,
        video.status,
        video.date
    );
    
    updateRelatedVideos();
    updateNavigationButtons();
}

function updateNavigationButtons() {
    const prevBtn = document.getElementById('videoModalPrev');
    const nextBtn = document.getElementById('videoModalNext');
    
    if (prevBtn) {
        prevBtn.style.display = videoModalState.currentIndex > 0 ? 'flex' : 'none';
    }
    if (nextBtn) {
        nextBtn.style.display = videoModalState.currentIndex < videoModalState.videos.length - 1 ? 'flex' : 'none';
    }
}

function updateRelatedVideos() {
    const relatedList = document.getElementById('videoModalRelatedList');
    if (!relatedList) return;
    
    relatedList.innerHTML = '';
    
    videoModalState.videos.forEach((video, index) => {
        const item = document.createElement('div');
        item.className = 'adminVideoModal_related_item' + (index === videoModalState.currentIndex ? ' adminVideoModal_related_item--active' : '');
        item.onclick = () => {
            videoModalState.currentIndex = index;
            openVideoModal(
                video.id,
                video.url,
                video.title,
                video.thumbnail,
                video.description,
                video.category,
                video.status,
                video.date
            );
            updateRelatedVideos();
            updateNavigationButtons();
        };
        
        item.innerHTML = `
            <div class="adminVideoModal_related_item_thumbnail">
                ${video.thumbnail ? `<img src="${video.thumbnail}" alt="${video.title}" />` : ''}
                <div class="adminVideoModal_related_item_play">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M8 5v14l11-7z"/>
                    </svg>
                </div>
            </div>
            <div class="adminVideoModal_related_item_content">
                <h4 class="adminVideoModal_related_item_title">${video.title}</h4>
                ${video.category ? `<span class="adminVideoModal_related_item_category">${video.category}</span>` : ''}
            </div>
        `;
        
        relatedList.appendChild(item);
    });
}

function closeVideoModal() {
    const modal = document.getElementById('videoPreviewModal');
    const player = document.getElementById('videoPreviewPlayer');
    
    // Pause and reset video
    player.pause();
    player.currentTime = 0;
    
    // Hide modal
    modal.classList.remove('adminVideoModal--active');
    document.body.style.overflow = '';
}

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    const modal = document.getElementById('videoPreviewModal');
    if (!modal || !modal.classList.contains('adminVideoModal--active')) return;
    
    if (e.key === 'Escape') {
        closeVideoModal();
    } else if (e.key === 'ArrowLeft') {
        e.preventDefault();
        navigateVideo(-1);
    } else if (e.key === 'ArrowRight') {
        e.preventDefault();
        navigateVideo(1);
    }
});

// Initialize khi DOM ready
document.addEventListener('DOMContentLoaded', function() {
    setupCategorySelectAutoSubmit();
    setupSearchAutoSubmit();
});
</script>
@endpush
@endsection

