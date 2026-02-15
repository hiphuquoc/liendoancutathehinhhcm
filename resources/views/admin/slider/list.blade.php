@extends('layouts.admin')

@section('content')
<div class="adminPersonnelPage">
    <div class="adminPersonnelPage_content">
        <div class="companyManagementPage_section companyManagementPage_section--tracked">
            {{-- ===== Header Section ===== --}}
            <div class="companyManagementPage_section_header companyManagementPage_section_header--trainer">
                <div class="companyManagementPage_section_header_left">
                    <div class="companyManagementPage_section_header_iconWrapper companyManagementPage_section_header_iconWrapper--trainer">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                            <line x1="8" y1="21" x2="16" y2="21"/>
                            <line x1="12" y1="17" x2="12" y2="21"/>
                        </svg>
                    </div>
                    <div class="companyManagementPage_section_header_info">
                        <h2 class="companyManagementPage_section_title">
                            Quản lý Slider
                        </h2>
                        <p class="companyManagementPage_section_desc">Quản lý các slider hiển thị trên trang chủ website</p>
                    </div>
                </div>
                <div class="companyManagementPage_section_header_right">
                    <div class="adminPersonnelPage_stats">
                        <div class="adminPersonnelPage_stats_item">
                            <span class="adminPersonnelPage_stats_label">Tổng số:</span>
                            <span class="adminPersonnelPage_stats_value">{{ $statistics['total'] ?? 0 }}</span>
                        </div>
                        <div class="adminPersonnelPage_stats_item">
                            <span class="adminPersonnelPage_stats_label">Hiển thị:</span>
                            <span class="adminPersonnelPage_stats_value" style="color: #22c55e;">{{ $statistics['active'] ?? 0 }}</span>
                        </div>
                        <div class="adminPersonnelPage_stats_item">
                            <span class="adminPersonnelPage_stats_label">Ẩn:</span>
                            <span class="adminPersonnelPage_stats_value" style="color: #94a3b8;">{{ $statistics['hidden'] ?? 0 }}</span>
                        </div>
                        <div class="adminPersonnelPage_stats_item adminPersonnelPage_stats_viewPerPage">
                            <label class="adminPersonnelPage_stats_viewPerPage_label">Hiển thị:</label>
                            <select class="adminPersonnelPage_stats_viewPerPage_select" onchange="settingView('viewSlider', this.value);">
                                @foreach(config('setting.admin_array_number_view') as $item)
                                    <option value="{{ $item }}" {{ $viewPerPage == $item ? 'selected' : '' }}>{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <a href="{{ route('admin.slider.view') }}" class="adminPageHeader_action" style="margin-top: 0.75rem;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        <span>Thêm Slider</span>
                    </a>
                </div>
            </div>

            <div class="companyManagementPage_section_body">
                {{-- ===== Search Bar ===== --}}
                <form id="formSearch" method="get" action="{{ route('admin.slider.list') }}" class="adminPersonnelPage_searchBar">
                    <div class="adminPersonnelPage_searchBar_inputWrapper">
                        <svg class="adminPersonnelPage_searchBar_icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="m21 21-4.35-4.35"/>
                        </svg>
                        <input 
                            type="text" 
                            class="adminPersonnelPage_searchBar_input" 
                            name="search_name" 
                            placeholder="Tìm kiếm theo tiêu đề, mô tả..." 
                            value="{{ $params['search_name'] ?? '' }}"
                        />
                        <button type="submit" class="adminPersonnelPage_searchBar_button">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"/>
                                <path d="m21 21-4.35-4.35"/>
                            </svg>
                            <span>Tìm kiếm</span>
                        </button>
                    </div>
                </form>

                {{-- ===== Slider Cards Grid ===== --}}
                @if(!empty($list) && $list->isNotEmpty())
                    <div class="sliderAdminList">
                        @foreach($list as $item)
                            @php
                                $imageUrl = config('image.default');
                                if(!empty($item->image)) {
                                    $imageUrl = \App\Helpers\Image::getUrlImageSmallByUrlImage($item->image);
                                }
                            @endphp
                            <div class="sliderAdminCard" id="sliderCard-{{ $item->id }}">
                                {{-- Preview Image --}}
                                <div class="sliderAdminCard_preview">
                                    @if(!empty($item->image))
                                        <img src="{{ $imageUrl }}?v={{ time() }}" 
                                             alt="{{ $item->title ?? 'Slider' }}" 
                                             class="sliderAdminCard_image"
                                             loading="lazy">
                                    @else
                                        <div class="sliderAdminCard_placeholder">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                                <circle cx="8.5" cy="8.5" r="1.5"/>
                                                <polyline points="21 15 16 10 5 21"/>
                                            </svg>
                                            <span>Chưa có ảnh</span>
                                        </div>
                                    @endif

                                    {{-- Status Badge --}}
                                    <div class="sliderAdminCard_statusBadge {{ !empty($item->flag_show) && $item->flag_show == 1 ? 'sliderAdminCard_statusBadge--active' : 'sliderAdminCard_statusBadge--hidden' }}">
                                        @if(!empty($item->flag_show) && $item->flag_show == 1)
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                <polyline points="20 6 9 17 4 12"/>
                                            </svg>
                                            Hiển thị
                                        @else
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                <line x1="18" y1="6" x2="6" y2="18"/>
                                                <line x1="6" y1="6" x2="18" y2="18"/>
                                            </svg>
                                            Ẩn
                                        @endif
                                    </div>

                                    {{-- Order Badge --}}
                                    <div class="sliderAdminCard_orderBadge">
                                        <span>#{{ $item->ordering ?? 0 }}</span>
                                    </div>
                                </div>

                                {{-- Card Content --}}
                                <div class="sliderAdminCard_content">
                                    <div class="sliderAdminCard_info">
                                        <h3 class="sliderAdminCard_title">{{ $item->title ?? 'Chưa có tiêu đề' }}</h3>

                                        @if(!empty($item->description))
                                            <p class="sliderAdminCard_description">{{ Str::limit($item->description, 80) }}</p>
                                        @endif

                                        @if(!empty($item->button_text) && !empty($item->button_link))
                                            <div class="sliderAdminCard_button">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                                                    <polyline points="15 3 21 3 21 9"/>
                                                    <line x1="10" y1="14" x2="21" y2="3"/>
                                                </svg>
                                                <span class="sliderAdminCard_buttonText">{{ $item->button_text }}</span>
                                                <span class="sliderAdminCard_buttonLink">{{ Str::limit($item->button_link, 30) }}</span>
                                            </div>
                                        @endif

                                        <div class="sliderAdminCard_meta">
                                            <div class="sliderAdminCard_metaItem">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                                    <line x1="16" y1="2" x2="16" y2="6"/>
                                                    <line x1="8" y1="2" x2="8" y2="6"/>
                                                    <line x1="3" y1="10" x2="21" y2="10"/>
                                                </svg>
                                                <span>{{ $item->created_at ? $item->created_at->format('d/m/Y') : '--' }}</span>
                                            </div>
                                            @if(!empty($item->position))
                                                <div class="sliderAdminCard_metaItem">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <line x1="4" y1="21" x2="4" y2="14"/>
                                                        <line x1="4" y1="10" x2="4" y2="3"/>
                                                        <line x1="12" y1="21" x2="12" y2="12"/>
                                                        <line x1="12" y1="8" x2="12" y2="3"/>
                                                        <line x1="20" y1="21" x2="20" y2="16"/>
                                                        <line x1="20" y1="12" x2="20" y2="3"/>
                                                    </svg>
                                                    <span>{{ ucfirst($item->position) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Actions --}}
                                    <div class="sliderAdminCard_actions">
                                        <a href="{{ route('admin.slider.view', ['id' => $item->id]) }}" 
                                           class="sliderAdminCard_actionBtn sliderAdminCard_actionBtn--edit" 
                                           title="Chỉnh sửa">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                            </svg>
                                            <span>Sửa</span>
                                        </a>
                                        <button type="button" 
                                                class="sliderAdminCard_actionBtn sliderAdminCard_actionBtn--delete" 
                                                onclick="deleteSlider({{ $item->id }})" 
                                                title="Xóa">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <polyline points="3 6 5 6 21 6"/>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                            </svg>
                                            <span>Xóa</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    @if($list->hasPages())
                        <div class="adminPersonnelPage_pagination">
                            {{ $list->appends(request()->query())->links('admin.template.paginate') }}
                        </div>
                    @endif
                @else
                    {{-- Empty State --}}
                    <div class="adminPersonnelPage_empty">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                            <line x1="8" y1="21" x2="16" y2="21"/>
                            <line x1="12" y1="17" x2="12" y2="21"/>
                        </svg>
                        <h3>Chưa có slider nào</h3>
                        <p>Hãy thêm slider mới để hiển thị trên trang chủ</p>
                        <a href="{{ route('admin.slider.view') }}" class="adminPageHeader_action" style="margin-top: 1rem;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 4.5v15m7.5-7.5h-15"/>
                            </svg>
                            <span>Thêm slider đầu tiên</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('scriptCustom')
    <script type="text/javascript">
        function deleteSlider(id) {
            showDeleteConfirm(id);
        }

        function showDeleteConfirm(id) {
            const modalHtml = `
                <div id="deleteModal" class="delete-modal">
                    <div class="delete-modal-overlay" onclick="closeDeleteModal()"></div>
                    <div class="delete-modal-content">
                        <div class="delete-modal-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 6h18"></path>
                                <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                            </svg>
                        </div>
                        <h3>Xác nhận xóa</h3>
                        <p>Bạn có chắc chắn muốn xóa slider này? Hành động này không thể hoàn tác.</p>
                        <div class="delete-modal-actions">
                            <button type="button" class="delete-modal-btn delete-modal-btn--cancel" onclick="closeDeleteModal()">Hủy</button>
                            <button type="button" class="delete-modal-btn delete-modal-btn--danger" onclick="confirmDelete(${id})">
                                <span class="btn-text">Xóa</span>
                                <span class="btn-loading" style="display:none;">Đang xóa...</span>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', modalHtml);
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteModal');
            if (modal) modal.remove();
        }

        function confirmDelete(id) {
            const btn = document.querySelector('.delete-modal .delete-modal-btn--danger');
            if (!btn) return;

            btn.disabled = true;
            btn.querySelector('.btn-text').style.display = 'none';
            btn.querySelector('.btn-loading').style.display = 'inline';

            $.ajax({
                url: "{{ route('admin.slider.delete') }}",
                type: "get",
                dataType: "html",
                data: { id: id }
            }).done(function(data) {
                closeDeleteModal();
                if(data == true || data === 'true' || data == 1) {
                    const card = document.getElementById('sliderCard-' + id);
                    if (card) {
                        card.style.transition = 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.95)';
                        setTimeout(() => {
                            card.remove();
                            if(document.querySelectorAll('.sliderAdminCard').length === 0) {
                                location.reload();
                            }
                        }, 400);
                    }
                } else {
                    alert('Có lỗi xảy ra khi xóa!');
                }
            }).fail(function() {
                closeDeleteModal();
                alert('Có lỗi xảy ra khi xóa!');
            });
        }
    </script>

    <style>
    /* ===== Slider Admin List ===== */
    .sliderAdminList {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 1.25rem;
        margin-top: 1rem;
    }

    .sliderAdminCard {
        background: var(--adminCardBg, #fff);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04);
        border: 1px solid var(--adminBorder, rgba(0,0,0,0.06));
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .sliderAdminCard:hover {
        box-shadow: 0 10px 25px rgba(0,0,0,0.1), 0 4px 10px rgba(0,0,0,0.06);
        transform: translateY(-2px);
    }

    /* Preview */
    .sliderAdminCard_preview {
        position: relative;
        width: 100%;
        aspect-ratio: 16/9;
        overflow: hidden;
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    }
    .sliderAdminCard_image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }
    .sliderAdminCard:hover .sliderAdminCard_image {
        transform: scale(1.03);
    }

    .sliderAdminCard_placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        gap: 0.5rem;
        color: #94a3b8;
    }
    .sliderAdminCard_placeholder svg {
        width: 48px;
        height: 48px;
        opacity: 0.4;
    }
    .sliderAdminCard_placeholder span {
        font-size: 0.8rem;
        opacity: 0.6;
    }

    /* Status Badge */
    .sliderAdminCard_statusBadge {
        position: absolute;
        top: 10px;
        left: 10px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 0.02em;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }
    .sliderAdminCard_statusBadge svg {
        width: 12px;
        height: 12px;
    }
    .sliderAdminCard_statusBadge--active {
        background: rgba(34, 197, 94, 0.15);
        color: #16a34a;
        border: 1px solid rgba(34, 197, 94, 0.2);
    }
    .sliderAdminCard_statusBadge--hidden {
        background: rgba(148, 163, 184, 0.15);
        color: #64748b;
        border: 1px solid rgba(148, 163, 184, 0.2);
    }

    /* Order Badge */
    .sliderAdminCard_orderBadge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(0,0,0,0.5);
        color: #fff;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 700;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }

    /* Content */
    .sliderAdminCard_content {
        padding: 1rem 1.25rem 1.25rem;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    .sliderAdminCard_info {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
    }
    .sliderAdminCard_title {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--adminTextPrimary, #1e293b);
        margin: 0;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .sliderAdminCard_description {
        font-size: 0.8rem;
        color: var(--adminTextSecondary, #64748b);
        margin: 0;
        line-height: 1.5;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Button */
    .sliderAdminCard_button {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        background: var(--adminBgSubtle, #f1f5f9);
        border-radius: 8px;
        margin-top: 4px;
    }
    .sliderAdminCard_button svg {
        width: 14px;
        height: 14px;
        color: var(--adminTextSecondary, #64748b);
        flex-shrink: 0;
    }
    .sliderAdminCard_buttonText {
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--adminTextPrimary, #1e293b);
    }
    .sliderAdminCard_buttonLink {
        font-size: 0.72rem;
        color: var(--adminTextSecondary, #94a3b8);
        margin-left: auto;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* Meta */
    .sliderAdminCard_meta {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-top: 4px;
    }
    .sliderAdminCard_metaItem {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 0.72rem;
        color: var(--adminTextSecondary, #94a3b8);
    }
    .sliderAdminCard_metaItem svg {
        width: 13px;
        height: 13px;
        flex-shrink: 0;
    }

    /* Actions */
    .sliderAdminCard_actions {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding-top: 0.75rem;
        border-top: 1px solid var(--adminBorder, rgba(0,0,0,0.06));
    }
    .sliderAdminCard_actionBtn {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 0.78rem;
        font-weight: 600;
        cursor: pointer;
        border: 1px solid transparent;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .sliderAdminCard_actionBtn svg {
        width: 15px;
        height: 15px;
    }
    .sliderAdminCard_actionBtn--edit {
        background: rgba(59, 130, 246, 0.08);
        color: #3b82f6;
        border-color: rgba(59, 130, 246, 0.15);
    }
    .sliderAdminCard_actionBtn--edit:hover {
        background: rgba(59, 130, 246, 0.15);
        color: #2563eb;
    }
    .sliderAdminCard_actionBtn--delete {
        background: rgba(239, 68, 68, 0.08);
        color: #ef4444;
        border-color: rgba(239, 68, 68, 0.15);
    }
    .sliderAdminCard_actionBtn--delete:hover {
        background: rgba(239, 68, 68, 0.15);
        color: #dc2626;
    }

    /* ===== Delete Modal ===== */
    .delete-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .delete-modal-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        backdrop-filter: blur(4px);
    }
    .delete-modal-content {
        position: relative;
        background: #fff;
        border-radius: 16px;
        padding: 2rem;
        max-width: 400px;
        width: 90%;
        text-align: center;
        box-shadow: 0 25px 50px rgba(0,0,0,0.15);
        animation: modalFadeIn 0.3s ease;
    }
    @keyframes modalFadeIn {
        from { opacity: 0; transform: scale(0.95) translateY(10px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
    .delete-modal-icon {
        margin-bottom: 1rem;
        color: #ef4444;
    }
    .delete-modal-icon svg {
        width: 48px;
        height: 48px;
    }
    .delete-modal-content h3 {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0 0 0.5rem;
    }
    .delete-modal-content p {
        font-size: 0.85rem;
        color: #64748b;
        margin: 0 0 1.5rem;
        line-height: 1.5;
    }
    .delete-modal-actions {
        display: flex;
        gap: 0.75rem;
        justify-content: center;
    }
    .delete-modal-btn {
        padding: 8px 24px;
        border-radius: 10px;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        border: none;
        transition: all 0.2s ease;
    }
    .delete-modal-btn--cancel {
        background: #f1f5f9;
        color: #64748b;
    }
    .delete-modal-btn--cancel:hover {
        background: #e2e8f0;
    }
    .delete-modal-btn--danger {
        background: #ef4444;
        color: #fff;
    }
    .delete-modal-btn--danger:hover {
        background: #dc2626;
    }
    .delete-modal-btn--danger:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    /* ===== Responsive ===== */
    @media (max-width: 768px) {
        .sliderAdminList {
            grid-template-columns: 1fr;
        }
        .sliderAdminCard_content {
            padding: 0.75rem 1rem 1rem;
        }
        .sliderAdminCard_actions {
            flex-wrap: wrap;
        }
    }
    </style>
@endpush
