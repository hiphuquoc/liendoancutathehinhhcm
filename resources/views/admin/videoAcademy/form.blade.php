@extends('layouts.admin')

@section('content')
@php
    $titlePage = 'Thêm Video mới';
    $submit = 'admin.videoAcademy.createAndUpdate';
    if(!empty($type) && $type == 'edit'){
        $titlePage = 'Chỉnh sửa Video';
    }
    
    // Thumbnail URL
    $thumbnailUrl = null;
    if(!empty($item->thumbnail)) {
        $thumbnailUrl = $item->thumbnail_url;
    }
    
    // Video URL
    $videoUrl = null;
    if(!empty($item->file_cloud)) {
        $videoUrl = $item->video_url;
    }
@endphp

<form id="formAction" action="{{ route($submit) }}" method="POST" enctype="multipart/form-data" class="adminFormPage_form">
    @csrf
    <input type="hidden" id="id" name="id" value="{{ !empty($item->id) && $type != 'copy' ? $item->id : 0 }}" />
    <input type="hidden" id="type" name="type" value="{{ $type }}" />

    <div class="adminFormPage">
        <div class="adminFormPage_content">
            <!-- Header -->
            @include('admin.components.pageHeader', [
                'title' => $titlePage,
                'desc' => $type == 'edit' ? 'Chỉnh sửa video' : 'Tạo video mới',
                'icon' => '<path d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z"/>',
                'backUrl' => route('admin.videoAcademy.list'),
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
                    <!-- Thông tin Video -->
                    <div class="adminFormSection">
                        <div class="adminFormSection_header">
                            <div class="adminFormSection_header_icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z"/>
                                </svg>
                            </div>
                            <div class="adminFormSection_header_info">
                                <h2 class="adminFormSection_title">Thông tin Video</h2>
                            </div>
                        </div>
                        <div class="adminFormSection_body">
                            @include('admin.components.formField', [
                                'label' => 'Tiêu đề Video',
                                'name' => 'title',
                                'type' => 'textarea',
                                'required' => true,
                                'value' => old('title') ?? $item->title ?? null,
                                'tooltip' => 'Tiêu đề của video',
                                'charCount' => true,
                                'maxLength' => 255,
                                'rows' => 2
                            ])

                            @include('admin.components.formField', [
                                'label' => 'Mô tả',
                                'name' => 'description',
                                'type' => 'textarea',
                                'value' => old('description') ?? $item->description ?? null,
                                'tooltip' => 'Mô tả về video',
                                'rows' => 4
                            ])

                            @include('admin.components.formField', [
                                'label' => 'Danh mục',
                                'name' => 'category',
                                'type' => 'text',
                                'value' => old('category') ?? $item->category ?? null,
                                'tooltip' => 'Danh mục của video',
                                'placeholder' => 'Nhập tên danh mục...'
                            ])
                            @if(!empty($categories))
                                <div style="margin-top: 8px; font-size: 12px; color: #666;">
                                    <strong>Danh mục hiện có:</strong> {{ implode(', ', $categories) }}
                                </div>
                            @endif

                            @include('admin.components.formField', [
                                'label' => 'Thứ tự',
                                'name' => 'ordering',
                                'type' => 'number',
                                'value' => old('ordering') ?? $item->ordering ?? 0,
                                'tooltip' => 'Thứ tự hiển thị (số càng nhỏ càng hiển thị trước)',
                                'min' => 0
                            ])

                            @php
                                $statusChecked = !empty($item->status) && ($item->status == 1);
                                if(old('status') !== null) {
                                    $statusChecked = old('status') === 'on';
                                }
                            @endphp
                            @include('admin.components.formField', [
                                'label' => 'Trạng thái',
                                'name' => 'status',
                                'type' => 'checkbox',
                                'value' => $statusChecked,
                                'checkboxLabel' => 'Cho phép hiển thị'
                            ])
                        </div>
                    </div>

                    <!-- File Video -->
                    <div class="adminFormSection">
                        <div class="adminFormSection_header">
                            <div class="adminFormSection_header_icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                    <line x1="12" y1="18" x2="12" y2="12"/>
                                    <line x1="9" y1="15" x2="15" y2="15"/>
                                </svg>
                            </div>
                            <div class="adminFormSection_header_info">
                                <h2 class="adminFormSection_title">File Video</h2>
                            </div>
                        </div>
                        <div class="adminFormSection_body">
                            @if(!empty($videoUrl))
                                <div class="adminFormPage_preview" style="margin-bottom: 16px;">
                                    <div class="adminFormPage_preview_label">Video hiện tại:</div>
                                    <video controls style="max-width: 100%; max-height: 400px; border-radius: 8px;">
                                        <source src="{{ $videoUrl }}" type="video/mp4">
                                        Trình duyệt của bạn không hỗ trợ video HTML5.
                                    </video>
                                    <input type="hidden" name="file_cloud_url" value="{{ $videoUrl }}" />
                                </div>
                            @endif
                            
                            @include('admin.components.formField', [
                                'label' => 'Upload Video',
                                'name' => 'file_cloud',
                                'type' => 'file',
                                'value' => null,
                                'tooltip' => 'Upload file video (MP4, WebM, ...)',
                                'accept' => 'video/*'
                            ])
                        </div>
                    </div>

                    <!-- Thumbnail -->
                    <div class="adminFormSection">
                        <div class="adminFormSection_header">
                            <div class="adminFormSection_header_icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                    <circle cx="8.5" cy="8.5" r="1.5"/>
                                    <polyline points="21 15 16 10 5 21"/>
                                </svg>
                            </div>
                            <div class="adminFormSection_header_info">
                                <h2 class="adminFormSection_title">Ảnh đại diện</h2>
                            </div>
                        </div>
                        <div class="adminFormSection_body">
                            @if(!empty($thumbnailUrl))
                                <div class="adminFormPage_preview" style="margin-bottom: 16px;">
                                    <div class="adminFormPage_preview_label">Ảnh đại diện hiện tại:</div>
                                    <img src="{{ $thumbnailUrl }}?v={{ time() }}" alt="Thumbnail" style="max-width: 300px; border-radius: 8px;" />
                                    <input type="hidden" name="thumbnail_url" value="{{ $thumbnailUrl }}" />
                                </div>
                            @endif
                            
                            @include('admin.components.formField', [
                                'label' => 'Upload Ảnh đại diện',
                                'name' => 'thumbnail',
                                'type' => 'file',
                                'value' => null,
                                'tooltip' => 'Upload ảnh thumbnail cho video',
                                'accept' => 'image/*'
                            ])
                            
                            @include('admin.components.formField', [
                                'label' => 'Hoặc nhập URL ảnh',
                                'name' => 'thumbnail_url',
                                'type' => 'text',
                                'value' => old('thumbnail_url') ?? ($thumbnailUrl && !filter_var($thumbnailUrl, FILTER_VALIDATE_URL) ? null : $thumbnailUrl),
                                'tooltip' => 'Nhập URL ảnh đại diện nếu không upload file'
                            ])
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="adminFormPage_footer">
                <div class="adminFormPage_footer_actions">
                    <a href="{{ route('admin.videoAcademy.list') }}" class="adminButton adminButton--secondary">
                        <span>Hủy</span>
                    </a>
                    <button type="submit" class="adminButton adminButton--primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                            <polyline points="17 21 17 13 7 13 7 21"/>
                            <polyline points="7 3 7 8 15 8"/>
                        </svg>
                        <span>{{ $type == 'edit' ? 'Cập nhật' : 'Tạo mới' }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection


@endpush
