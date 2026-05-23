@extends('layouts.admin')

@section('content')
<div class="adminContentPage">
    <div class="adminContentPage_content">
        <div class="companyManagementPage_section companyManagementPage_section--tracked">
            <div class="companyManagementPage_section_header companyManagementPage_section_header--blog">
                <div class="companyManagementPage_section_header_left">
                    <div class="companyManagementPage_section_header_iconWrapper companyManagementPage_section_header_iconWrapper--blog">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h7.5M16.5 7.5l-3-3m0 0l-3 3m3-3v12.75"/>
                        </svg>
                    </div>
                    <div class="companyManagementPage_section_header_info">
                        <h2 class="companyManagementPage_section_title">
                            Danh sách Bài viết Blog
                        </h2>
                        <p class="companyManagementPage_section_desc">Quản lý các bài viết blog trong hệ thống</p>
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
                            <select class="adminContentPage_stats_viewPerPage_select" onchange="settingView('viewBlogInfo', this.value);">
                                @foreach(config('setting.admin_array_number_view') as $item)
                                    <option value="{{ $item }}" {{ $viewPerPage == $item ? 'selected' : '' }}>{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="companyManagementPage_section_body">
                <!-- Search & Filter Bar -->
                <form id="formSearch" method="get" action="{{ route('admin.blog.list') }}" class="adminContentPage_searchBar adminContentPage_searchBar--withFilter">
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
                                id="blogSearchInput"
                                placeholder="Tìm kiếm theo tên bài viết..." 
                                value="{{ $params['search_name'] ?? '' }}"
                            />
                        </div>
                        
                        <!-- Category Filter -->
                        @if(!empty($categories) && $categories->isNotEmpty())
                            <div class="adminContentPage_searchBar_filter">
                                @php
                                    $categoryOptions = [0 => 'Tất cả chuyên mục'];
                                    foreach($categories as $category) {
                                        $categoryOptions[$category->id] = $category->seo->title ?? '';
                                    }
                                @endphp
                                @include('admin.components.formSelect', [
                                    'name' => 'search_category',
                                    'value' => $params['search_category'] ?? 0,
                                    'options' => $categoryOptions,
                                    'placeholder' => 'Tất cả chuyên mục',
                                    'class' => 'adminContentPage_searchBar_filterSelect'
                                ])
                            </div>
                        @endif
                    </div>
                </form>

                <!-- Cards Grid -->
                @if(!empty($list) && $list->isNotEmpty())
                    <div class="adminContentPage_grid">
                        @foreach($list as $item)
            @php
                                $urlImage = config('image.default');
                                if(!empty($item->seo->image)) {
                                    $urlImage = \App\Helpers\Image::getUrlImageSmallByUrlImage($item->seo->image);
                                }
            @endphp
                            <div class="adminContentPage_card" id="oneItem-{{ $item->id }}">
                                <div class="adminContentPage_card_imageWrapper">
                                    <img src="{{ $urlImage }}?v={{ time() }}" alt="{{ $item->seo->title ?? 'Bài viết' }}" />
                                    <div class="adminContentPage_card_actions">
                                        <a href="/{{ $item->seo->slug_full ?? '#' }}" target="_blank" class="adminContentPage_card_action" title="Xem trang">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                <circle cx="12" cy="12" r="3"/>
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.blog.view', ['language' => 'vi', 'id' => $item->id]) }}" class="adminContentPage_card_action" title="Chỉnh sửa">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                            </svg>
                                        </a>
                                        @if(auth()->user()->hasRole('admin'))
                                            <a href="{{ route('admin.blog.view', ['id' => $item->id, 'language' => 'vi', 'type' => 'copy']) }}" class="adminContentPage_card_action" title="Sao chép">
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
                                </div>
                                <div class="adminContentPage_card_body">
                                    <h3 class="adminContentPage_card_title">{{ $item->seo->title ?? 'Chưa có tiêu đề' }}</h3>
                                    @if(!empty($item->seo->seo_description))
                                        <p class="adminContentPage_card_description">{{ Str::limit($item->seo->seo_description, 100) }}</p>
                                    @endif
                                    @if(!empty($item->seo->slug_full))
                                        <div class="adminContentPage_card_slug">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                                                <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                                            </svg>
                                            <span>{{ $item->seo->slug_full }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="adminContentPage_card_footer">
                                    <div class="adminContentPage_card_meta">
                                        @if(!empty($item->seo->rating_aggregate_star))
                                            <div class="adminContentPage_card_rating">
                                                <svg viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                </svg>
                                                <span>{{ $item->seo->rating_aggregate_star }} ({{ $item->seo->rating_aggregate_count }})</span>
                                            </div>
                                        @endif
                                        <div class="adminContentPage_card_date">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="10"/>
                                                <polyline points="12 6 12 12 16 14"/>
                                            </svg>
                                            <span>{{ date('d/m/Y', strtotime($item->seo->updated_at ?? $item->seo->created_at)) }}</span>
                                        </div>
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
                            <path d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h7.5M16.5 7.5l-3-3m0 0l-3 3m3-3v12.75"/>
                        </svg>
                        <h3>Chưa có bài viết nào</h3>
                        <p>Hãy thêm bài viết mới vào hệ thống</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
    
@endsection

@push('scriptCustom')
    <script type="text/javascript">
        function deleteItem(id){
            if(confirm('{{ config("admin.alert.confirmRemove") }}')) {
                $.ajax({
                    url         : "{{ route('admin.blog.delete') }}",
                    type        : "get",
                    dataType    : "html",
                    data        : { id : id }
                }).done(function(data){
                    if(data==true) {
                    $('#oneItem-'+id).fadeOut(300, function(){
                        $(this).remove();
                    });
                    }
                });
            }
        }
    
    // Auto submit khi custom selectbox thay đổi
    function setupCategorySelectAutoSubmit() {
        const categorySelectContainer = document.querySelector('.adminContentPage_searchBar_filterSelect .adminCustomSelect');
        if (!categorySelectContainer) return;
        
        const hiddenInput = categorySelectContainer.querySelector('input[type="hidden"][name="search_category"]');
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
        const searchInput = document.getElementById('blogSearchInput');
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

    // Initialize khi DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        setupCategorySelectAutoSubmit();
        setupSearchAutoSubmit();
    });
    </script>
@endpush
