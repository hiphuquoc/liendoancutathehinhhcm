@extends('layouts.admin')

@section('content')
@php
    $titlePage = 'Thêm Video mới';
    $submit = 'admin.video.createAndUpdate';
    if(!empty($type) && $type == 'edit'){
        $titlePage = 'Chỉnh sửa Video';
    }
@endphp

<form id="formAction" action="{{ route($submit) }}" method="POST" enctype="multipart/form-data" class="adminFormPage_form">
    @csrf
    <input type="hidden" id="video_info_id" name="video_info_id" value="{{ !empty($item->id) ? $item->id : 0 }}" />
    <input type="hidden" id="type" name="type" value="{{ $type }}" />

    <div class="adminFormPage">
        <div class="adminFormPage_content">
            <!-- Header -->
            @include('admin.components.pageHeader', [
                'title' => $titlePage,
                'desc' => $type == 'edit' ? 'Chỉnh sửa thông tin video' : 'Thêm video mới vào hệ thống',
                'icon' => '<path d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z"/>',
                'backUrl' => route('admin.video.list'),
                'backText' => 'Quay lại'
            ])
            
            <!-- Validation Errors Banner -->
            @include('admin.components.formValidationErrors')

            <!-- Errors -->
            @if ($errors->any())
                <div class="adminFormPage_errors">
                    <div class="adminFormPage_errors_icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                    </div>
                    <div class="adminFormPage_errors_content">
                        <h3>Có lỗi xảy ra:</h3>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Message -->
            @include('admin.components.formMessage')

            <!-- Body -->
            <div class="adminFormPage_body">
                <div class="adminFormPage_main">
                    <!-- Thông tin video -->
                    <div class="adminFormSection">
                        <div class="adminFormSection_header">
                            <div class="adminFormSection_header_icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z"/>
                                </svg>
                            </div>
                            <div class="adminFormSection_header_info">
                                <h2 class="adminFormSection_title">Thông tin video</h2>
                            </div>
                        </div>
                        <div class="adminFormSection_body">
                            <!-- Title -->
                            @include('admin.components.formField', [
                                'label' => 'Tiêu đề',
                                'name' => 'title',
                                'type' => 'text',
                                'required' => true,
                                'value' => old('title', $item->title ?? ''),
                                'charCount' => true,
                                'maxLength' => 255,
                                'placeholder' => 'Nhập tiêu đề video'
                            ])

                            <!-- Description -->
                            @include('admin.components.formField', [
                                'label' => 'Mô tả',
                                'name' => 'description',
                                'type' => 'textarea',
                                'required' => false,
                                'value' => old('description', $item->description ?? ''),
                                'rows' => 5,
                                'placeholder' => 'Nhập mô tả về video (tùy chọn)'
                            ])

                            <!-- Video File Preview (if editing) -->
                            @if(!empty($item) && !empty($item->file_cloud))
                                <div class="adminFormField">
                                    <div class="adminFormField_labelWrapper">
                                        <label class="adminFormField_label">
                                            <span>Video đã tải lên</span>
                                        </label>
                                    </div>
                                    <div class="adminFormField_inputWrapper">
                                        <div class="adminVideoPreview">
                                            <div class="adminVideoPreview_player">
                                                <video 
                                                    controls 
                                                    class="adminVideoPreview_video"
                                                    poster="{{ $item->thumbnail_url ?? '' }}"
                                                    preload="metadata"
                                                >
                                                    <source src="{{ $item->video_url }}" type="video/mp4">
                                                    <source src="{{ $item->video_url }}" type="video/webm">
                                                    Trình duyệt của bạn không hỗ trợ video HTML5.
                                                </video>
                                            </div>
                                            <div class="adminVideoPreview_info">
                                                <div class="adminVideoPreview_info_row">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="adminVideoPreview_info_icon">
                                                        <path d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z"/>
                                                    </svg>
                                                    <div class="adminVideoPreview_info_content">
                                                        <span class="adminVideoPreview_info_label">File video hiện tại</span>
                                                        <span class="adminVideoPreview_info_value">{{ basename($item->file_cloud) }}</span>
                                                    </div>
                                                </div>
                                                <div class="adminVideoPreview_actions">
                                                    <a href="{{ $item->video_url }}" target="_blank" class="adminButton adminButton--secondary adminButton--sm" download>
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                                                            <polyline points="7 10 12 15 17 10"/>
                                                            <line x1="12" y1="15" x2="12" y2="3"/>
                                                        </svg>
                                                        <span>Tải xuống</span>
                                                    </a>
                                                    <a href="{{ $item->video_url }}" target="_blank" class="adminButton adminButton--secondary adminButton--sm">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/>
                                                            <polyline points="15 3 21 3 21 9"/>
                                                            <line x1="10" y1="14" x2="21" y2="3"/>
                                                        </svg>
                                                        <span>Mở trong tab mới</span>
                                                    </a>
                                                </div>
                                                <p class="adminVideoPreview_note">
                                                    Để thay đổi file video, vui lòng chọn file mới ở bên dưới. File cũ sẽ được giữ nguyên nếu không chọn file mới.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            
                            @include('admin.components.formFileUpload', [
                                'name' => 'video_file',
                                'label' => 'File Video',
                                'required' => empty($item),
                                'accept' => '.mp4,.webm,.mov,.avi,.mkv,.flv',
                                'tooltip' => 'Chọn file video để upload lên Google Cloud Storage. Định dạng: MP4, WebM, MOV, AVI, MKV, FLV. Kích thước tối đa: 100GB.'
                            ])

                            <!-- Thumbnail URL -->
                            @include('admin.components.formField', [
                                'label' => 'URL Thumbnail',
                                'name' => 'thumbnail',
                                'type' => 'text',
                                'required' => false,
                                'value' => old('thumbnail', $item->thumbnail ?? ''),
                                'tooltip' => 'URL ảnh thumbnail hoặc đường dẫn file (tùy chọn)',
                                'placeholder' => 'https://... hoặc đường dẫn file'
                            ])

                            <!-- Thumbnail File Upload -->
                            @include('admin.components.formImageUpload', [
                                'name' => 'thumbnail_file',
                                'label' => 'Hoặc tải ảnh thumbnail lên',
                                'required' => false,
                                'currentImage' => !empty($item->thumbnail_url) ? $item->thumbnail_url : null,
                                'aspectRatio' => '16/9',
                                'tooltip' => 'Tải ảnh thumbnail lên hoặc nhập URL ở trên'
                            ])

                            <!-- Category -->
                            @include('admin.components.formField', [
                                'label' => 'Danh mục',
                                'name' => 'category',
                                'type' => 'text',
                                'required' => false,
                                'value' => old('category', $item->category ?? ''),
                                'tooltip' => 'Nhập danh mục video (ví dụ: Kỹ thuật, Lý thuyết, Thực hành...)',
                                'placeholder' => 'Kỹ thuật'
                            ])

                            <!-- Ordering -->
                            @include('admin.components.formField', [
                                'label' => 'Thứ tự sắp xếp',
                                'name' => 'ordering',
                                'type' => 'number',
                                'required' => false,
                                'value' => old('ordering', $item->ordering ?? 0),
                                'tooltip' => 'Số càng nhỏ, hiển thị càng trước (mặc định: 0)',
                                'placeholder' => '0'
                            ])

                            <!-- Status -->
                            <div class="adminFormField">
                                <div class="adminFormField_labelWrapper">
                                    <label class="adminFormField_label">
                                        <span>Trạng thái</span>
                                    </label>
                                </div>
                                <div class="adminFormField_inputWrapper">
                                    <input type="hidden" name="status" value="0" />
                                    <label class="adminToggle">
                                        <input 
                                            type="checkbox" 
                                            name="status" 
                                            value="1"
                                            {{ (old('status', $item->status ?? 1) == 1) ? 'checked' : '' }}
                                            onchange="this.previousElementSibling.value = this.checked ? '1' : '0'"
                                        />
                                        <span class="adminToggle_slider"></span>
                                        <span class="adminToggle_label">Hiển thị</span>
                                    </label>
                                    <p style="margin-top: 0.5rem; font-size: 0.875rem; color: var(--admin-gray-600);">
                                        Bật để hiển thị video trong hệ thống, tắt để ẩn video
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="adminFormPage_actions">
                <a href="{{ route('admin.video.list') }}" class="adminButton adminButton--secondary">
                    <span>Hủy</span>
                </a>
                <button type="submit" class="adminButton adminButton--primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                        <polyline points="7 3 7 8 15 8"/>
                    </svg>
                    <span>{{ $type == 'edit' ? 'Cập nhật' : 'Thêm mới' }}</span>
                </button>
            </div>
        </div>
    </div>
</form>

@endsection

