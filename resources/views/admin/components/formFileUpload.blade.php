{{-- 
    Component: Admin Form File Upload (PDF, etc.) with drag & drop
    Usage: @include('admin.components.formFileUpload', [
        'name' => 'document',
        'label' => 'Tệp tài liệu',
        'required' => true,
        'accept' => '.pdf',
        'currentFile' => $fileUrl,
        'previewType' => 'pdf'
    ])
--}}
@php
    $fieldId = $name ?? 'file_' . uniqid();
    $previewId = $fieldId . '_preview';
    $uploadAreaId = $fieldId . '_area';
    $previewType = $previewType ?? 'pdf';
@endphp

<div class="adminFormFileUpload">
    <label class="adminFormFileUpload_label" for="{{ $fieldId }}">
        @if(isset($tooltip))
            <span class="adminFormFileUpload_tooltip" data-tooltip="{{ $tooltip }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
            </span>
        @endif
        <span>{{ $label ?? 'Tệp tài liệu' }}</span>
        @if(isset($required) && $required)
            <span class="adminFormFileUpload_required">*</span>
        @endif
    </label>

    <div class="adminFormFileUpload_area" id="{{ $uploadAreaId }}">
        <input 
            type="file" 
            class="adminFormFileUpload_input" 
            id="{{ $fieldId }}" 
            name="{{ $name }}"
            @if(isset($required) && $required) required @endif
            @if(isset($accept)) accept="{{ $accept }}" @endif
            onchange="handleFileUpload(this, '{{ $previewId }}', '{{ $uploadAreaId }}', '{{ $previewType }}')"
        />
        
        <div class="adminFormFileUpload_dropzone">
            <div class="adminFormFileUpload_dropzone_content">
                <svg class="adminFormFileUpload_dropzone_icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="17 8 12 3 7 8"/>
                    <line x1="12" y1="3" x2="12" y2="15"/>
                </svg>
                <p class="adminFormFileUpload_dropzone_text">
                    <span class="adminFormFileUpload_dropzone_text_primary">Kéo thả tệp vào đây</span>
                    <span class="adminFormFileUpload_dropzone_text_secondary">hoặc click để chọn</span>
                </p>
                @if(isset($accept))
                    <p class="adminFormFileUpload_dropzone_hint">Định dạng: {{ $accept }}</p>
                @endif
            </div>
        </div>
        
        <div class="adminFormFileUpload_preview" id="{{ $previewId }}" style="display: {{ isset($currentFile) && $currentFile ? 'block' : 'none' }};">
            @if(isset($currentFile) && $currentFile)
                @if($previewType === 'pdf')
                    <embed src="{{ $currentFile }}" type="application/pdf" width="100%" height="600px" />
                @else
                    <div class="adminFormFileUpload_preview_info">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                            <polyline points="10 9 9 9 8 9"/>
                        </svg>
                        <a href="{{ $currentFile }}" target="_blank" class="adminFormFileUpload_preview_link">
                            Xem tệp hiện tại
                        </a>
                    </div>
                @endif
                <button type="button" class="adminFormFileUpload_preview_remove" onclick="removeFilePreview('{{ $previewId }}', '{{ $uploadAreaId }}', '{{ $fieldId }}')" title="Xóa tệp">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            @endif
        </div>
    </div>
</div>

@pushonce('scriptCustom')
<script>
function handleFileUpload(input, previewId, uploadAreaId, previewType) {
    const preview = document.getElementById(previewId);
    const uploadArea = document.getElementById(uploadAreaId);
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        if (previewType === 'pdf' && file.type === 'application/pdf') {
            const fileURL = URL.createObjectURL(file);
            preview.innerHTML = `
                <embed src="${fileURL}" type="application/pdf" width="100%" height="600px" />
                <button type="button" class="adminFormFileUpload_preview_remove" onclick="removeFilePreview('${previewId}', '${uploadAreaId}', '${input.id}')" title="Xóa tệp">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            `;
            preview.style.display = 'block';
            uploadArea.classList.add('adminFormFileUpload_area--hasFile');
            
            // Scroll to preview
            preview.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        } else {
            alert('Vui lòng chọn file có định dạng phù hợp');
            input.value = '';
            preview.style.display = 'none';
        }
    }
}

function removeFilePreview(previewId, uploadAreaId, inputId) {
    const preview = document.getElementById(previewId);
    const uploadArea = document.getElementById(uploadAreaId);
    const input = document.getElementById(inputId);
    
    preview.innerHTML = '';
    preview.style.display = 'none';
    uploadArea.classList.remove('adminFormFileUpload_area--hasFile');
    input.value = '';
}

// Drag & Drop for file upload
document.addEventListener('DOMContentLoaded', function() {
    const fileUploadAreas = document.querySelectorAll('.adminFormFileUpload_area');
    
    fileUploadAreas.forEach(function(uploadArea) {
        const input = uploadArea.querySelector('input[type="file"]');
        const dropzone = uploadArea.querySelector('.adminFormFileUpload_dropzone');
        
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
                uploadArea.classList.add('adminFormFileUpload_area--dragover');
            }, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, function() {
                uploadArea.classList.remove('adminFormFileUpload_area--dragover');
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

