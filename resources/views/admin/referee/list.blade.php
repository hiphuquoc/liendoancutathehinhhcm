@extends('layouts.admin')

@section('content')
<div class="adminPersonnelPage">
    <div class="adminPersonnelPage_content">
        <div class="companyManagementPage_section companyManagementPage_section--tracked">
            <div class="companyManagementPage_section_header companyManagementPage_section_header--referee">
                <div class="companyManagementPage_section_header_left">
                    <div class="companyManagementPage_section_header_iconWrapper companyManagementPage_section_header_iconWrapper--referee">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                        </svg>
                    </div>
                    <div class="companyManagementPage_section_header_info">
                        <h2 class="companyManagementPage_section_title">
                            Danh sách Trọng tài
                        </h2>
                        <p class="companyManagementPage_section_desc">Quản lý thông tin trọng tài trong hệ thống</p>
                    </div>
                </div>
                <div class="companyManagementPage_section_header_right">
                    <div class="adminPersonnelPage_stats">
                        <div class="adminPersonnelPage_stats_item">
                            <span class="adminPersonnelPage_stats_label">Tổng số:</span>
                            <span class="adminPersonnelPage_stats_value">{{ $list->total() ?? 0 }}</span>
                        </div>
                        <div class="adminPersonnelPage_stats_item adminPersonnelPage_stats_viewPerPage">
                            <label class="adminPersonnelPage_stats_viewPerPage_label">Hiển thị:</label>
                            <select class="adminPersonnelPage_stats_viewPerPage_select" onchange="settingView('viewRefereeInfo', this.value);">
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
                <form id="formSearch" method="get" action="{{ route('admin.referee.list') }}" class="adminPersonnelPage_searchBar">
                    <div class="adminPersonnelPage_searchBar_inputWrapper">
                        <svg class="adminPersonnelPage_searchBar_icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="m21 21-4.35-4.35"/>
                        </svg>
                        <input 
                            type="text" 
                            class="adminPersonnelPage_searchBar_input" 
                            name="search_name" 
                            placeholder="Tìm kiếm theo tên trọng tài..." 
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

                <!-- Cards Grid -->
                @if(!empty($list) && $list->isNotEmpty())
                    <div class="adminPersonnelPage_grid">
                        @foreach($list as $item)
                            @php
                                $slug = $item->seo->slug ?? '';
                                $canView = auth()->user()->hasRole('admin') || auth()->user()->name == $slug;
                                $urlImage = config('image.default');
                                if(!empty($item->seo->image)) {
                                    $urlImage = \App\Helpers\Image::getUrlImageSmallByUrlImage($item->seo->image);
                                }
                            @endphp
                            @if($canView)
                                <div class="adminPersonnelPage_card" id="oneItem-{{ $item->id }}">
                                    <div class="adminPersonnelPage_card_header">
                                        <div class="adminPersonnelPage_card_avatar">
                                            <img src="{{ $urlImage }}?v={{ time() }}" alt="{{ $item->seo->title ?? 'Trọng tài' }}" />
                                        </div>
                                        <div class="adminPersonnelPage_card_actions">
                                            <a href="/{{ $item->seo->slug_full ?? '#' }}" target="_blank" class="adminPersonnelPage_card_action" title="Xem trang">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                    <circle cx="12" cy="12" r="3"/>
                                                </svg>
                                            </a>
                                            <a href="{{ route('admin.referee.view', ['language' => 'vi', 'id' => $item->id]) }}" class="adminPersonnelPage_card_action" title="Chỉnh sửa">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                </svg>
                                            </a>
                                            @if(auth()->user()->hasRole('admin'))
                                                <a href="{{ route('admin.referee.view', ['id' => $item->id, 'language' => 'vi', 'type' => 'copy']) }}" class="adminPersonnelPage_card_action" title="Sao chép">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
                                                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                                                    </svg>
                                                </a>
                                                <button onclick="deleteItem({{ $item->id }})" class="adminPersonnelPage_card_action adminPersonnelPage_card_action--danger" title="Xóa">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="adminPersonnelPage_card_body">
                                        <h3 class="adminPersonnelPage_card_title">{{ $item->name ?? $item->seo->title ?? 'Chưa có tên' }}</h3>
                                        <div class="adminPersonnelPage_card_info">
                                            @if(!empty($item->email))
                                                <div class="adminPersonnelPage_card_infoItem">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                                                    </svg>
                                                    <span>{{ $item->email }}</span>
                                                </div>
                                            @endif
                                            @if(!empty($item->phone))
                                                <div class="adminPersonnelPage_card_infoItem">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/>
                                                    </svg>
                                                    <span>{{ $item->phone }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        @if(!empty($item->seo->slug_full))
                                            <div class="adminPersonnelPage_card_slug">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                                                    <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                                                </svg>
                                                <span>{{ $item->seo->slug_full }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="adminPersonnelPage_card_footer">
                                        <div class="adminPersonnelPage_card_meta">
                                            @if(!empty($item->seo->rating_aggregate_star))
                                                <div class="adminPersonnelPage_card_rating">
                                                    <svg viewBox="0 0 24 24" fill="currentColor">
                                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                    </svg>
                                                    <span>{{ $item->seo->rating_aggregate_star }} ({{ $item->seo->rating_aggregate_count }})</span>
                                                </div>
                                            @endif
                                            <div class="adminPersonnelPage_card_date">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <circle cx="12" cy="12" r="10"/>
                                                    <polyline points="12 6 12 12 16 14"/>
                                                </svg>
                                                <span>{{ date('d/m/Y', strtotime($item->seo->updated_at ?? $item->seo->created_at)) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if($list->hasPages())
                        <div class="adminPersonnelPage_pagination">
                            {{ $list->appends(request()->query())->links('admin.template.paginate') }}
                        </div>
                    @endif
                @else
                    <div class="adminPersonnelPage_empty">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                        </svg>
                        <h3>Chưa có trọng tài nào</h3>
                        <p>Hãy thêm trọng tài mới vào hệ thống</p>
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
                url         : "{{ route('admin.referee.delete') }}",
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
</script>
@endpush
