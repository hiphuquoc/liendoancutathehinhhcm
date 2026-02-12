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
    $thumbnailUrlSmall = null;
    if(!empty($item) && !empty($item->thumbnail)) {
        $thumbnailUrl = $item->thumbnail_url;
        // Try to get small version if available
        if(filter_var($thumbnailUrl, FILTER_VALIDATE_URL)) {
            $thumbnailUrlSmall = $thumbnailUrl;
        } else {
            $thumbnailUrlSmall = \App\Helpers\Image::getUrlImageSmallByUrlImage($item->thumbnail);
        }
    }
    
    // Video URL
    $videoUrl = null;
    if(!empty($item) && !empty($item->file_cloud)) {
        $videoUrl = $item->video_url;
    }
@endphp

<form id="formAction" action="{{ route($submit) }}" method="POST" enctype="multipart/form-data" class="adminFormPage_form">
    @csrf
    <input type="hidden" id="id" name="id" value="{{ !empty($item) && !empty($item->id) && $type != 'copy' ? $item->id : 0 }}" />
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
                                'value' => old('title') ?? (!empty($item) ? $item->title : null),
                                'tooltip' => 'Tiêu đề của video',
                                'charCount' => true,
                                'maxLength' => 255,
                                'rows' => 2
                            ])

                            @include('admin.components.formField', [
                                'label' => 'Mô tả',
                                'name' => 'description',
                                'type' => 'textarea',
                                'value' => old('description') ?? (!empty($item) ? $item->description : null),
                                'tooltip' => 'Mô tả về video',
                                'rows' => 4
                            ])

                            @include('admin.components.formField', [
                                'label' => 'Danh mục',
                                'name' => 'category',
                                'type' => 'text',
                                'value' => old('category') ?? (!empty($item) ? $item->category : null),
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
                                'value' => old('ordering') ?? (!empty($item) ? $item->ordering : 0),
                                'tooltip' => 'Thứ tự hiển thị (số càng nhỏ càng hiển thị trước)',
                                'min' => 0
                            ])

                            @php
                                $statusChecked = false;
                                if(!empty($item) && !empty($item->status) && ($item->status == 1)) {
                                    $statusChecked = true;
                                }
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
                                <div class="adminFormPage_preview" style="margin-bottom: 1.5rem;">
                                    <div class="adminFormPage_preview_label">Video hiện tại:</div>
                                    <div style="position: relative; width: 100%; max-width: 800px; margin-top: 0.75rem; background: #000; border-radius: var(--admin-radius-md); overflow: hidden;">
                                        <video controls style="width: 100%; display: block;">
                                            <source src="{{ $videoUrl }}" type="video/mp4">
                                            <source src="{{ $videoUrl }}" type="video/webm">
                                            Trình duyệt của bạn không hỗ trợ video HTML5.
                                        </video>
                                    </div>
                                    <input type="hidden" name="file_cloud_url" value="{{ $videoUrl }}" />
                                </div>
                            @endif
                            
                            @include('admin.components.formFileUpload', [
                                'name' => 'file_cloud',
                                'label' => 'Upload Video',
                                'required' => false,
                                'accept' => 'video/*',
                                'tooltip' => 'Upload file video (MP4, WebM, ...). Nếu upload file mới sẽ thay thế file hiện tại.'
                            ])
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="adminFormPage_sidebar">
                    <div class="adminFormSidebar">
                        <div class="adminFormSidebar_sticky">
                            <!-- Actions -->
                            @include('admin.components.formActions', [
                                'backRoute' => 'admin.videoAcademy.list'
                            ])

                            <!-- Thumbnail Upload -->
                            @include('admin.components.formImageUpload', [
                                'name' => 'thumbnail',
                                'label' => 'Ảnh đại diện',
                                'required' => false,
                                'currentImage' => $thumbnailUrlSmall,
                                'aspectRatio' => '16/9',
                                'tooltip' => 'Ảnh thumbnail cho video (tỷ lệ 16:9)'
                            ])
                            
                            <!-- Thumbnail URL Input -->
                            <div style="margin-top: 1.5rem;">
                                @include('admin.components.formField', [
                                    'label' => 'Hoặc nhập URL ảnh',
                                    'name' => 'thumbnail_url',
                                    'type' => 'text',
                                    'value' => old('thumbnail_url') ?? ($thumbnailUrl && filter_var($thumbnailUrl, FILTER_VALIDATE_URL) ? $thumbnailUrl : null),
                                    'tooltip' => 'Nhập URL ảnh đại diện nếu không upload file',
                                    'placeholder' => 'https://...'
                                ])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scriptCustom')
<script>
// Character count update
document.addEventListener('DOMContentLoaded', function() {
    const charCountFields = document.querySelectorAll('.adminFormField_charCount');
    
    charCountFields.forEach(function(charCount) {
        const fieldId = charCount.getAttribute('data-field');
        const field = document.getElementById(fieldId);
        const currentSpan = charCount.querySelector('.adminFormField_charCount_current');
        
        if (field && currentSpan) {
            // Update on input
            field.addEventListener('input', function() {
                const length = this.value ? this.value.length : 0;
                currentSpan.textContent = length;
            });
            
            // Initial count
            if (field.value) {
                currentSpan.textContent = field.value.length;
            }
        }
    });
    
    // Handle thumbnail URL input and remove_image logic
    const thumbnailUrlInput = document.querySelector('input[name="thumbnail_url"]');
    const form = document.getElementById('formAction');
    
    if (thumbnailUrlInput && form) {
        // When user enters a new URL, remove the remove_image flag
        thumbnailUrlInput.addEventListener('input', function() {
            const removeImageInput = form.querySelector('input[name="remove_image"]');
            if (removeImageInput && this.value.trim() !== '') {
                // User is entering a new URL, so remove the remove_image flag
                removeImageInput.remove();
            }
        });
        
        // Override removeCurrentImage function for thumbnail to also clear thumbnail_url
        const originalRemoveCurrentImage = window.removeCurrentImage;
        if (originalRemoveCurrentImage) {
            window.removeCurrentImage = function(inputId, previewId, uploadAreaId) {
                // Call original function
                originalRemoveCurrentImage(inputId, previewId, uploadAreaId);
                
                // If this is the thumbnail input, clear thumbnail_url
                if (inputId === 'thumbnail' || inputId.includes('thumbnail')) {
                    thumbnailUrlInput.value = '';
                }
            };
        }
        
        // Also handle the remove button directly
        setTimeout(function() {
            const thumbnailRemoveBtn = form.querySelector('button[onclick*="removeCurrentImage"][onclick*="thumbnail"]');
            if (thumbnailRemoveBtn) {
                const originalOnClick = thumbnailRemoveBtn.getAttribute('onclick');
                thumbnailRemoveBtn.addEventListener('click', function(e) {
                    // Clear thumbnail_url when removing thumbnail
                    thumbnailUrlInput.value = '';
                });
            }
        }, 500);
    }
});
</script>
@endpush
@endsection
