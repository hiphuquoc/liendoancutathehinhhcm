{{-- 
    Component: Admin Form Image Upload (with drag & drop)
    Usage: @include('admin.components.formImageUpload', [
        'name' => 'image',
        'label' => 'Ảnh đại diện',
        'required' => false,
        'currentImage' => $imageUrl,
        'aspectRatio' => '800/533'
    ])
--}}
@php
    $fieldId = $name ?? 'image_' . uniqid();
    $previewId = $fieldId . '_preview';
    $uploadAreaId = $fieldId . '_area';
    $aspectRatio = $aspectRatio ?? '800/533';
    $hasCurrentImage = isset($currentImage) && $currentImage;
@endphp

<div class="adminFormImageUpload">
    <label class="adminFormImageUpload_label" for="{{ $fieldId }}">
        @if(isset($tooltip))
            <span class="adminFormImageUpload_tooltip" data-tooltip="{{ $tooltip }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
            </span>
        @endif
        <span>{{ $label ?? 'Ảnh đại diện' }}</span>
        @if(isset($required) && $required)
            <span class="adminFormImageUpload_required">*</span>
        @endif
    </label>

    <div class="adminFormImageUpload_area {{ $hasCurrentImage ? 'adminFormImageUpload_area--hasImage' : '' }}" id="{{ $uploadAreaId }}">
        <input 
            type="file" 
            class="adminFormImageUpload_input" 
            id="{{ $fieldId }}" 
            name="{{ $name }}"
            accept="image/*"
            @if(isset($required) && $required) required @endif
            onchange="handleImageUpload(this, '{{ $previewId }}', '{{ $uploadAreaId }}')"
        />
        
        @if($hasCurrentImage)
            <!-- Display current image with action buttons -->
            <div class="adminFormImageUpload_current" id="{{ $previewId }}_current">
                <div class="adminFormImageUpload_current_image" style="aspect-ratio: {{ $aspectRatio }};">
                    <img src="{{ $currentImage }}?{{ time() }}" alt="Current image" />
                    <div class="adminFormImageUpload_current_overlay">
                        <button type="button" class="adminFormImageUpload_current_action adminFormImageUpload_current_action--change" onclick="showImageUploadInput('{{ $fieldId }}', '{{ $previewId }}', '{{ $uploadAreaId }}')" title="Thay đổi ảnh">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                            <span>Thay đổi</span>
                        </button>
                        <button type="button" class="adminFormImageUpload_current_action adminFormImageUpload_current_action--remove" onclick="removeCurrentImage('{{ $fieldId }}', '{{ $previewId }}', '{{ $uploadAreaId }}')" title="Xóa ảnh">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18"/>
                                <line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                            <span>Xóa</span>
                        </button>
                    </div>
                </div>
                @if(isset($imageInfo))
                    <div class="adminFormImageUpload_info">
                        <span>{{ $imageInfo['extension'] ?? '' }}</span>
                        <span>{{ $imageInfo['width'] ?? '' }} × {{ $imageInfo['height'] ?? '' }} px</span>
                        <span>{{ $imageInfo['size'] ?? '' }} KB</span>
                    </div>
                @endif
            </div>
        @endif
        
        <!-- Upload dropzone (shown when no image or when changing) -->
        <div class="adminFormImageUpload_dropzone" id="{{ $previewId }}_dropzone" style="{{ $hasCurrentImage ? 'display: none;' : '' }}">
            <div class="adminFormImageUpload_dropzone_content">
                <svg class="adminFormImageUpload_dropzone_icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="17 8 12 3 7 8"/>
                    <line x1="12" y1="3" x2="12" y2="15"/>
                </svg>
                <p class="adminFormImageUpload_dropzone_text">
                    <span class="adminFormImageUpload_dropzone_text_primary">Kéo thả ảnh vào đây</span>
                    <span class="adminFormImageUpload_dropzone_text_secondary">hoặc click để chọn</span>
                </p>
            </div>
        </div>
        
        <!-- Preview for new upload (hidden initially) -->
        <div class="adminFormImageUpload_preview" id="{{ $previewId }}" style="aspect-ratio: {{ $aspectRatio }}; display: none;">
            <img src="" alt="Preview" />
            <button type="button" class="adminFormImageUpload_preview_remove" onclick="removeImagePreview('{{ $previewId }}', '{{ $uploadAreaId }}', '{{ $fieldId }}')" title="Xóa ảnh">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
    </div>
</div>

@pushonce('scriptCustom')
<script>
function handleImageUpload(input, previewId, uploadAreaId) {
    const preview = document.getElementById(previewId);
    const uploadArea = document.getElementById(uploadAreaId);
    const dropzone = document.getElementById(previewId + '_dropzone');
    const currentImage = document.getElementById(previewId + '_current');
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Validate image
        if (!file.type.startsWith('image/')) {
            alert('Vui lòng chọn file ảnh');
            input.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            // Hide dropzone and current image if exists
            if (dropzone) dropzone.style.display = 'none';
            if (currentImage) currentImage.style.display = 'none';
            
            // Show preview
            preview.querySelector('img').src = e.target.result;
            preview.style.display = 'block';
            uploadArea.classList.add('adminFormImageUpload_area--hasImage');
        };
        reader.readAsDataURL(file);
    }
}

function showImageUploadInput(inputId, previewId, uploadAreaId) {
    const input = document.getElementById(inputId);
    const dropzone = document.getElementById(previewId + '_dropzone');
    const currentImage = document.getElementById(previewId + '_current');
    const uploadArea = document.getElementById(uploadAreaId);
    
    // Hide current image
    if (currentImage) currentImage.style.display = 'none';
    
    // Show dropzone (input file stays hidden, only dropzone is visible)
    if (dropzone) dropzone.style.display = 'block';
    uploadArea.classList.remove('adminFormImageUpload_area--hasImage');
    
    // Trigger file input click (input is hidden but functional)
    setTimeout(() => {
        input.click();
    }, 100);
}

function removeCurrentImage(inputId, previewId, uploadAreaId) {
    const input = document.getElementById(inputId);
    const dropzone = document.getElementById(previewId + '_dropzone');
    const currentImage = document.getElementById(previewId + '_current');
    const uploadArea = document.getElementById(uploadAreaId);
    
    // Remove current image display
    if (currentImage) currentImage.style.display = 'none';
    
    // Show dropzone (input file stays hidden, only dropzone is visible)
    input.value = '';
    input.removeAttribute('required'); // Remove required if exists
    if (dropzone) dropzone.style.display = 'block';
    uploadArea.classList.remove('adminFormImageUpload_area--hasImage');
    
    // Add hidden input to indicate image should be removed
    const form = input.closest('form');
    if (form) {
        // Remove existing remove_image input if any
        const existingRemove = form.querySelector('input[name="remove_image"]');
        if (existingRemove) existingRemove.remove();
        
        // Add hidden input to signal image removal
        const removeInput = document.createElement('input');
        removeInput.type = 'hidden';
        removeInput.name = 'remove_image';
        removeInput.value = '1';
        form.appendChild(removeInput);
    }
}

function removeImagePreview(previewId, uploadAreaId, inputId) {
    const preview = document.getElementById(previewId);
    const uploadArea = document.getElementById(uploadAreaId);
    const input = document.getElementById(inputId);
    const dropzone = document.getElementById(previewId + '_dropzone');
    const currentImage = document.getElementById(previewId + '_current');
    
    preview.innerHTML = '<img src="" alt="Preview" /><button type="button" class="adminFormImageUpload_preview_remove" onclick="removeImagePreview(\'' + previewId + '\', \'' + uploadAreaId + '\', \'' + inputId + '\')" title="Xóa ảnh"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>';
    preview.style.display = 'none';
    uploadArea.classList.remove('adminFormImageUpload_area--hasImage');
    input.value = '';
    
    // Show dropzone again
    if (dropzone) dropzone.style.display = 'block';
    
    // If there was a current image, show it again
    if (currentImage) {
        currentImage.style.display = 'block';
        uploadArea.classList.add('adminFormImageUpload_area--hasImage');
    }
}

// Drag & Drop
document.addEventListener('DOMContentLoaded', function() {
    const uploadAreas = document.querySelectorAll('.adminFormImageUpload_area');
    
    uploadAreas.forEach(function(uploadArea) {
        const input = uploadArea.querySelector('input[type="file"]');
        const dropzone = uploadArea.querySelector('.adminFormImageUpload_dropzone');
        
        if (!input || !dropzone) return;
        
        // Prevent default drag behaviors
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, preventDefaults, false);
            document.body.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        // Highlight drop zone when item is dragged over it
        ['dragenter', 'dragover'].forEach(eventName => {
            uploadArea.addEventListener(eventName, function() {
                if (dropzone && dropzone.style.display !== 'none') {
                    uploadArea.classList.add('adminFormImageUpload_area--dragover');
                }
            }, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, function() {
                uploadArea.classList.remove('adminFormImageUpload_area--dragover');
            }, false);
        });
        
        // Handle dropped files
        uploadArea.addEventListener('drop', function(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            
            if (files.length > 0) {
                input.files = files;
                const event = new Event('change', { bubbles: true });
                input.dispatchEvent(event);
            }
        }, false);
        
        // Click to select
        dropzone.addEventListener('click', function() {
            input.click();
        });
    });
});
</script>
@endpushonce
