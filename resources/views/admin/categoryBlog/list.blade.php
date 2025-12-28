@extends('layouts.admin')

@section('content')
<div class="adminContentPage">
    <div class="adminContentPage_content">
        <div class="companyManagementPage_section companyManagementPage_section--tracked">
            <div class="companyManagementPage_section_header companyManagementPage_section_header--categoryBlog">
                <div class="companyManagementPage_section_header_left">
                    <div class="companyManagementPage_section_header_iconWrapper companyManagementPage_section_header_iconWrapper--categoryBlog">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 01-1.125-1.125M3.375 19.5h1.5C5.496 19.5 6 18.996 6 18.375m-3.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125h18.375m-10.5-1.5v10.5m0-10.5c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v10.5m-6 0h6"/>
                        </svg>
                    </div>
                    <div class="companyManagementPage_section_header_info">
                        <h2 class="companyManagementPage_section_title">
                            Danh sách Chuyên mục Blog
                        </h2>
                        <p class="companyManagementPage_section_desc">Quản lý các chuyên mục blog trong hệ thống</p>
                    </div>
                </div>
                <div class="companyManagementPage_section_header_right">
                    <div class="adminContentPage_stats">
                        <div class="adminContentPage_stats_item">
                            <span class="adminContentPage_stats_label">Tổng số:</span>
                            <span class="adminContentPage_stats_value">{{ $list->count() ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="companyManagementPage_section_body">
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
                                    <img src="{{ $urlImage }}?v={{ time() }}" alt="{{ $item->seo->title ?? 'Chuyên mục' }}" />
                                    <div class="adminContentPage_card_actions">
                                        <a href="/{{ $item->seo->slug_full ?? '#' }}" target="_blank" class="adminContentPage_card_action" title="Xem trang">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                <circle cx="12" cy="12" r="3"/>
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.categoryBlog.view', ['language' => 'vi', 'id' => $item->id]) }}" class="adminContentPage_card_action" title="Chỉnh sửa">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                            </svg>
                                        </a>
                                        @if(auth()->user()->hasRole('admin'))
                                            <a href="{{ route('admin.categoryBlog.view', ['id' => $item->id, 'language' => 'vi', 'type' => 'copy']) }}" class="adminContentPage_card_action" title="Sao chép">
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

                @else
                    <div class="adminContentPage_empty">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 01-1.125-1.125M3.375 19.5h1.5C5.496 19.5 6 18.996 6 18.375m-3.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125h18.375m-10.5-1.5v10.5m0-10.5c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v10.5m-6 0h6"/>
                        </svg>
                        <h3>Chưa có chuyên mục nào</h3>
                        <p>Hãy thêm chuyên mục blog mới vào hệ thống</p>
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
                    url         : "{{ route('admin.categoryBlog.delete') }}",
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
