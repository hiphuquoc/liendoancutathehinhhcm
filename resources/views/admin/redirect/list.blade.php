@extends('layouts.admin')

@section('content')
<div class="adminContentPage">
    <div class="adminContentPage_content">
        <div class="companyManagementPage_section companyManagementPage_section--tracked">
            <div class="companyManagementPage_section_header companyManagementPage_section_header--redirect">
                <div class="companyManagementPage_section_header_left">
                    <div class="companyManagementPage_section_header_iconWrapper companyManagementPage_section_header_iconWrapper--redirect">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/>
                        </svg>
                    </div>
                    <div class="companyManagementPage_section_header_info">
                        <h2 class="companyManagementPage_section_title">
                            Redirect 301
                        </h2>
                        <p class="companyManagementPage_section_desc">Quản lý chuyển hướng URL 301 trong hệ thống</p>
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
                            <select class="adminContentPage_stats_viewPerPage_select" onchange="settingView('viewRedirectInfo', this.value);">
                                @foreach(config('setting.admin_array_number_view') as $item)
                                    <option value="{{ $item }}" {{ $viewPerPage == $item ? 'selected' : '' }}>{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="companyManagementPage_section_body">
                <!-- Add New Redirect Form -->
                <form method="get" action="{{ route('admin.redirect.create') }}" class="adminRedirect_addForm">
                    <div class="adminRedirect_addForm_row">
                        <div class="adminRedirect_addForm_field">
                            <label class="adminRedirect_addForm_label">Đường dẫn cũ</label>
                            <input 
                                type="text" 
                                class="adminRedirect_addForm_input" 
                                name="old_url" 
                                placeholder="Ví dụ: /trang-cu" 
                                value="{{ request()->get('old_url') ?? '' }}"
                                required
                            />
                        </div>
                        <div class="adminRedirect_addForm_field">
                            <label class="adminRedirect_addForm_label">Đường dẫn mới</label>
                            <input 
                                type="text" 
                                class="adminRedirect_addForm_input" 
                                name="new_url" 
                                placeholder="Ví dụ: /trang-moi" 
                                value="{{ request()->get('new_url') ?? '' }}"
                                required
                            />
                        </div>
                        <div class="adminRedirect_addForm_actions">
                            <button type="submit" class="adminButton adminButton--primary">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="5" x2="12" y2="19"/>
                                    <line x1="5" y1="12" x2="19" y2="12"/>
                                </svg>
                                <span>Thêm Redirect</span>
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Message -->
                @include('admin.components.formMessage')

                <!-- Redirect Table -->
                @if(!empty($list) && $list->isNotEmpty())
                    <div class="adminRedirect_tableWrapper">
                        <table class="adminRedirect_table">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">STT</th>
                                    <th>Đường dẫn cũ</th>
                                    <th>Đường dẫn mới</th>
                                    <th style="width: 100px;">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($list as $item)
                                    @include('admin.redirect.oneRow', ['no' => ($list->currentPage() - 1) * $list->perPage() + $loop->index + 1, 'item' => $item])
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($list->hasPages())
                        <div class="adminContentPage_pagination">
                            {{ $list->links('admin.template.paginate') }}
                        </div>
                    @endif
                @else
                    <div class="adminContentPage_empty">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/>
                        </svg>
                        <h3>Chưa có redirect nào</h3>
                        <p>Bắt đầu bằng cách thêm redirect mới vào hệ thống</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scriptCustom')
<script>
function deleteItem(id) {
    if (!confirm('Bạn có chắc chắn muốn xóa redirect này không?')) {
        return;
    }
    
    $.ajax({
        url: "{{ route('admin.redirect.delete') }}",
        type: "GET",
        dataType: "html",
        data: { id: id }
    }).done(function(data) {
        if(data == true || data == '1') {
            $('#oneItem-' + id).fadeOut(300, function() {
                $(this).remove();
                // Reload page if table is empty
                if($('.adminRedirect_table tbody tr').length === 0) {
                    location.reload();
                }
            });
        } else {
            alert('Có lỗi xảy ra khi xóa redirect');
        }
    }).fail(function() {
        alert('Có lỗi xảy ra khi xóa redirect');
    });
}

</script>
@endpush
@endsection
