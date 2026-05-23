{{-- 
    Component: Admin Form File Upload (with drag & drop)
    Usage: @include('admin.components.formFileUpload', [
        'name' => 'excel_file',
        'label' => 'File Excel',
        'required' => true,
        'accept' => '.xlsx,.xls',
        'tooltip' => 'Hướng dẫn...'
    ])
--}}
@php
    $fieldId = $name ?? 'file_' . uniqid();
    $uploadAreaId = $fieldId . '_area';
    $fileInfoId = $fieldId . '_info';
    $accept = $accept ?? '*';
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
        <span>{{ $label ?? 'File' }}</span>
        @if(isset($required) && $required)
            <span class="adminFormImageUpload_required">*</span>
        @endif
    </label>

    <div class="adminFormImageUpload_area" id="{{ $uploadAreaId }}">
        <input 
            type="file" 
            class="adminFormImageUpload_input" 
            id="{{ $fieldId }}" 
            name="{{ $name }}"
            accept="{{ $accept }}"
            @if(isset($required) && $required) required @endif
            onchange="handleFileUpload(this, '{{ $fileInfoId }}', '{{ $uploadAreaId }}')"
        />
        
        <!-- File Info Display (shown when file is selected) -->
        <div class="adminFormImageUpload_fileInfo" id="{{ $fileInfoId }}" style="display: none;">
            <div class="adminFormImageUpload_fileInfo_content">
                <svg class="adminFormImageUpload_fileInfo_icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                </svg>
                <div class="adminFormImageUpload_fileInfo_text">
                    <span class="adminFormImageUpload_fileInfo_name" id="{{ $fileInfoId }}_name"></span>
                    <span class="adminFormImageUpload_fileInfo_size" id="{{ $fileInfoId }}_size"></span>
                </div>
                <button type="button" class="adminFormImageUpload_fileInfo_remove" onclick="removeFileUpload('{{ $fieldId }}', '{{ $fileInfoId }}', '{{ $uploadAreaId }}')" title="Xóa file">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Upload dropzone (shown when no file) -->
        <div class="adminFormImageUpload_dropzone" id="{{ $fileInfoId }}_dropzone">
            <div class="adminFormImageUpload_dropzone_content">
                <svg class="adminFormImageUpload_dropzone_icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="17 8 12 3 7 8"/>
                    <line x1="12" y1="3" x2="12" y2="15"/>
                </svg>
                <p class="adminFormImageUpload_dropzone_text">
                    <span class="adminFormImageUpload_dropzone_text_primary">Kéo thả file vào đây</span>
                    <span class="adminFormImageUpload_dropzone_text_secondary">hoặc click để chọn</span>
                </p>
            </div>
        </div>
    </div>
</div>

@pushonce('scriptCustom')
<script>
function handleFileUpload(input, fileInfoId, uploadAreaId) {
    const fileInfo = document.getElementById(fileInfoId);
    const dropzone = document.getElementById(fileInfoId + '_dropzone');
    const fileName = document.getElementById(fileInfoId + '_name');
    const fileSize = document.getElementById(fileInfoId + '_size');
    const uploadArea = document.getElementById(uploadAreaId);
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const fileSizeMB = (file.size / 1024 / 1024).toFixed(2);
        
        // Update file info
        fileName.textContent = file.name;
        fileSize.textContent = fileSizeMB + ' MB';
        
        // Show file info, hide dropzone
        fileInfo.style.display = 'block';
        dropzone.style.display = 'none';
        uploadArea.classList.add('adminFormImageUpload_area--hasFile');
    }
}

function removeFileUpload(inputId, fileInfoId, uploadAreaId) {
    const input = document.getElementById(inputId);
    const fileInfo = document.getElementById(fileInfoId);
    const dropzone = document.getElementById(fileInfoId + '_dropzone');
    const uploadArea = document.getElementById(uploadAreaId);
    
    // Clear input
    input.value = '';
    
    // Hide file info, show dropzone
    fileInfo.style.display = 'none';
    dropzone.style.display = 'block';
    uploadArea.classList.remove('adminFormImageUpload_area--hasFile');
}

// Drag & Drop for file upload
document.addEventListener('DOMContentLoaded', function() {
    const fileUploadAreas = document.querySelectorAll('.adminFormImageUpload_area input[type="file"][accept*=".xlsx"], .adminFormImageUpload_area input[type="file"][accept*=".xls"]');
    
    fileUploadAreas.forEach(function(input) {
        const uploadArea = input.closest('.adminFormImageUpload_area');
        const dropzone = uploadArea.querySelector('.adminFormImageUpload_dropzone');
        
        if (!dropzone) return;
        
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
