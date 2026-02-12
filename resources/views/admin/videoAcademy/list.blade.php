@extends('layouts.admin')

@section('content')
<div class="adminContentPage">
    <div class="adminContentPage_content">
        <div class="companyManagementPage_section companyManagementPage_section--tracked">
            <div class="companyManagementPage_section_header companyManagementPage_section_header--document">
                <div class="companyManagementPage_section_header_left">
                    <div class="companyManagementPage_section_header_iconWrapper companyManagementPage_section_header_iconWrapper--document">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z"/>
                        </svg>
                    </div>
                    <div class="companyManagementPage_section_header_info">
                        <h2 class="companyManagementPage_section_title">
                            Quản lý Video Academy
                        </h2>
                        <p class="companyManagementPage_section_desc">Quản lý video trong hệ thống Video Academy</p>
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
                            <select class="adminContentPage_stats_viewPerPage_select" onchange="settingView('viewVideoAcademy', this.value);">
                                @foreach(config('setting.admin_array_number_view') as $item)
                                    <option value="{{ $item }}" {{ $viewPerPage == $item ? 'selected' : '' }}>{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <a href="{{ route('admin.videoAcademy.view', ['type' => 'create']) }}" class="adminButton adminButton--primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 5v14M5 12h14"/>
                        </svg>
                        <span>Thêm video mới</span>
                    </a>
                </div>
            </div>
            <div class="companyManagementPage_section_body">
                <!-- Search Bar -->
                <form id="formSearch" method="get" action="{{ route('admin.videoAcademy.list') }}" class="adminContentPage_searchBar">
                    <div class="adminContentPage_searchBar_inputWrapper">
                        <svg class="adminContentPage_searchBar_icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="m21 21-4.35-4.35"/>
                        </svg>
                        <input 
                            type="text" 
                            class="adminContentPage_searchBar_input" 
                            name="search_name" 
                            placeholder="Tìm kiếm theo tên video..." 
                            value="{{ $params['search_name'] ?? '' }}"
                        />
                        @if(!empty($categories))
                            <select name="category" class="adminContentPage_searchBar_select">
                                <option value="">Tất cả danh mục</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category }}" {{ (isset($params['category']) && $params['category'] == $category) ? 'selected' : '' }}>{{ $category }}</option>
                                @endforeach
                            </select>
                        @endif
                        <select name="status" class="adminContentPage_searchBar_select">
                            <option value="">Tất cả trạng thái</option>
                            <option value="1" {{ (isset($params['status']) && $params['status'] == '1') ? 'selected' : '' }}>Đang hoạt động</option>
                            <option value="0" {{ (isset($params['status']) && $params['status'] === '0') ? 'selected' : '' }}>Tạm ẩn</option>
                        </select>
                        <button type="submit" class="adminContentPage_searchBar_button">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"/>
                                <path d="m21 21-4.35-4.35"/>
                            </svg>
                            <span>Tìm kiếm</span>
                        </button>
                    </div>
                </form>

                <!-- Cards Grid -->
                @if(!empty($list) && $list->isNotEmpty())
                    <div class="adminContentPage_grid">
                        @foreach($list as $item)
                            <div class="adminContentPage_card videoAcademyCard" id="oneItem-{{ $item->id }}">
                                <div class="adminContentPage_card_imageWrapper">
                                    @if(!empty($item->thumbnail_url))
                                        <img src="{{ $item->thumbnail_url }}?v={{ time() }}" alt="{{ $item->title }}" />
                                    @else
                                        <div class="videoAcademyCard_placeholder">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="adminContentPage_card_actions videoAcademyCard_actions">
                                        <a href="{{ route('admin.videoAcademy.show', ['id' => $item->id]) }}" target="_blank" class="adminContentPage_card_action" title="Xem video">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                <circle cx="12" cy="12" r="3"/>
                                            </svg>
                                        </a>
                                        @if(auth()->user()->hasRole('admin'))
                                            <a href="{{ route('admin.videoAcademy.view', ['id' => $item->id, 'type' => 'edit']) }}" class="adminContentPage_card_action" title="Chỉnh sửa">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                </svg>
                                            </a>
                                            <a href="{{ route('admin.videoAcademy.view', ['id' => $item->id, 'type' => 'copy']) }}" class="adminContentPage_card_action" title="Sao chép">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
                                                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                                                </svg>
                                            </a>
                                            <button onclick="deleteItem({{ $item->id }})" class="adminContentPage_card_action adminContentPage_card_action--danger" title="Xóa">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                    @if($item->status == 0)
                                        <div class="adminContentPage_card_badge videoAcademyCard_badge">
                                            Tạm ẩn
                                        </div>
                                    @endif
                                </div>
                                <div class="adminContentPage_card_body">
                                    <h3 class="adminContentPage_card_title">{{ $item->title ?? 'Chưa có tiêu đề' }}</h3>
                                    @if(!empty($item->description))
                                        <p class="adminContentPage_card_description">{{ Str::limit($item->description, 100) }}</p>
                                    @endif
                                    @if(!empty($item->category))
                                        <div class="adminContentPage_card_slug">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                            </svg>
                                            <span>{{ $item->category }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="adminContentPage_card_footer">
                                    <div class="adminContentPage_card_meta">
                                        <div class="adminContentPage_card_date">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="10"/>
                                                <polyline points="12 6 12 12 16 14"/>
                                            </svg>
                                            <span>{{ $item->created_at->format('d/m/Y') }}</span>
                                        </div>
                                        @if($item->ordering > 0)
                                            <div class="adminContentPage_card_date">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M3 3h18M7 8h10M7 13h10M7 18h10"/>
                                                </svg>
                                                <span>Thứ tự: {{ $item->ordering }}</span>
                                            </div>
                                        @endif
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
                        <p>Hãy thêm video mới vào hệ thống</p>
                        <a href="{{ route('admin.videoAcademy.view', ['type' => 'create']) }}" class="adminButton adminButton--primary" style="margin-top: 16px;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 5v14M5 12h14"/>
                            </svg>
                            <span>Thêm video mới</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
    
@endsection

@push('styleCustom')
<style>
/* Video Academy Card Styles */
.videoAcademyCard {
    position: relative;
}

.videoAcademyCard_placeholder {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.videoAcademyCard_placeholder svg {
    width: 60px;
    height: 60px;
    color: #999;
}

.videoAcademyCard_badge {
    position: absolute;
    top: 8px;
    left: 8px;
    background: #f59e0b;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    z-index: 1;
}

/* Actions Container */
.videoAcademyCard_actions {
    position: absolute;
    top: 12px;
    right: 12px;
    display: flex;
    gap: 8px;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.2s ease, visibility 0.2s ease;
    z-index: 20;
}

/* Show actions on card hover */
.videoAcademyCard:hover .videoAcademyCard_actions {
    opacity: 1;
    visibility: visible;
}

/* Action Button Styles */
.videoAcademyCard_actions .adminContentPage_card_action {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.95);
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    color: #374151;
    text-decoration: none;
    transition: all 0.2s ease;
    cursor: pointer;
    backdrop-filter: blur(8px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.videoAcademyCard_actions .adminContentPage_card_action svg {
    width: 18px;
    height: 18px;
}

.videoAcademyCard_actions .adminContentPage_card_action:hover {
    background: #3b82f6;
    border-color: #3b82f6;
    color: #fff;
    transform: scale(1.1);
    box-shadow: 0 4px 8px rgba(59, 130, 246, 0.3);
}

.videoAcademyCard_actions .adminContentPage_card_action--danger:hover {
    background: #ef4444;
    border-color: #ef4444;
    box-shadow: 0 4px 8px rgba(239, 68, 68, 0.3);
}

/* Mobile: Always show actions */
@media (max-width: 768px) {
    .videoAcademyCard_actions {
        opacity: 1;
        visibility: visible;
        top: 8px;
        right: 8px;
        gap: 6px;
    }
    
    .videoAcademyCard_actions .adminContentPage_card_action {
        width: 32px;
        height: 32px;
    }
    
    .videoAcademyCard_actions .adminContentPage_card_action svg {
        width: 16px;
        height: 16px;
    }
}
</style>
@endpush

@push('scriptCustom')
    <script type="text/javascript">
        function deleteItem(id){
            if(confirm('{{ config("admin.alert.confirmRemove") }}')) {
                $.ajax({
                    url         : "{{ route('admin.videoAcademy.delete') }}",
                    type        : "get",
                    dataType    : "html",
                    data        : { id : id }
                }).done(function(data){
                    if(data==true) {
                        $('#oneItem-'+id).fadeOut(300, function(){
                            $(this).remove();
                        });
                    } else {
                        alert('Có lỗi xảy ra khi xóa video');
                    }
                });
            }
        }

        // Ensure hover actions work
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.videoAcademyCard');
            
            cards.forEach(function(card) {
                const actions = card.querySelector('.videoAcademyCard_actions');
                if (!actions) return;
                
                // Mouse enter
                card.addEventListener('mouseenter', function() {
                    actions.style.opacity = '1';
                    actions.style.visibility = 'visible';
                });
                
                // Mouse leave
                card.addEventListener('mouseleave', function() {
                    actions.style.opacity = '0';
                    actions.style.visibility = 'hidden';
                });
            });
        });
    </script>
@endpush
