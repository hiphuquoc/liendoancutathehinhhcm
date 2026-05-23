{{-- 
    Component: Admin Form Validation Errors Display
    Usage: @include('admin.components.formValidationErrors')
    
    This component displays a prominent error banner at the top of the form
    showing all fields that need to be filled, with links to scroll to each field.
--}}
<div id="adminFormValidationErrors" class="adminFormValidationErrors" style="display: none;">
    <div class="adminFormValidationErrors_container">
        <div class="adminFormValidationErrors_icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
        </div>
        <div class="adminFormValidationErrors_content">
            <h3 class="adminFormValidationErrors_title">Vui lòng điền đầy đủ các thông tin sau:</h3>
            <ul class="adminFormValidationErrors_list" id="adminFormValidationErrors_list">
                <!-- Errors will be populated by JavaScript -->
            </ul>
        </div>
        <button type="button" class="adminFormValidationErrors_close" id="adminFormValidationErrors_close">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
    </div>
</div>

@push('scriptCustom')
<script>
(function() {
    // Field label mapping - maps field names to Vietnamese labels
    const fieldLabelMap = {
        'title': 'Họ và tên & Chức vụ',
        'seo_title': 'Tiêu đề SEO',
        'seo_description': 'Mô tả SEO',
        'slug': 'Đường dẫn tĩnh',
        'image': 'Ảnh đại diện',
        'type_id': 'Phân loại',
        'parent': 'Trang cha',
        'categories': 'Chuyên mục',
        'content': 'Nội dung',
        'ordering': 'Thứ tự',
        'document': 'Tệp tài liệu',
        'name': 'Họ và tên',
        'email': 'Email',
        'address': 'Địa chỉ',
        'phone': 'Số điện thoại',
        'current_password': 'Mật khẩu hiện tại',
        'new_password': 'Mật khẩu mới',
        'new_password_confirmation': 'Xác nhận mật khẩu mới',
        'repeater_trainer_achievement': 'Thành tích',
        'repeater_trainer_skill': 'Kỹ năng',
        'repeater_trainer_experience': 'Kinh nghiệm',
        'repeater_trainer_degree': 'Bằng cấp'
    };
    
    // Get field label
    function getFieldLabel(fieldName) {
        // Try to get from map
        if (fieldLabelMap[fieldName]) {
            return fieldLabelMap[fieldName];
        }
        
        // Try to get from label element
        const field = document.querySelector(`[name="${fieldName}"], [name="${fieldName}[]"]`);
        if (field) {
            const label = document.querySelector(`label[for="${field.id}"]`);
            if (label) {
                const labelText = label.textContent.trim();
                // Remove asterisk only, keep Vietnamese characters
                return labelText.replace(/\*/g, '').trim();
            }
            
            // Try to get from parent formField
            const formField = field.closest('.adminFormField, .adminFormSelect, .adminFormImageUpload, .adminFormFileUpload, .adminFormContent');
            if (formField) {
                const fieldLabel = formField.querySelector('.adminFormField_label, .adminFormSelect_label, .adminFormImageUpload_label, .adminFormFileUpload_label, .adminFormContent_label');
                if (fieldLabel) {
                    const labelText = fieldLabel.textContent.trim();
                    // Remove asterisk only, keep Vietnamese characters
                    // Also remove tooltip SVG if present
                    const labelClone = fieldLabel.cloneNode(true);
                    const tooltips = labelClone.querySelectorAll('.adminFormField_tooltip, .adminFormSelect_tooltip, .adminFormImageUpload_tooltip, .adminFormFileUpload_tooltip');
                    tooltips.forEach(tooltip => tooltip.remove());
                    return labelClone.textContent.replace(/\*/g, '').trim();
                }
            }
        }
        
        // Fallback to field name
        return fieldName.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    }
    
    // Highlight invalid fields
    function highlightInvalidFields(invalidFields) {
        // Remove previous highlights
        document.querySelectorAll('.adminFormField--invalid, .adminFormSelect--invalid, .adminFormImageUpload--invalid, .adminFormFileUpload--invalid, .adminFormContent--invalid').forEach(function(el) {
            el.classList.remove('adminFormField--invalid', 'adminFormSelect--invalid', 'adminFormImageUpload--invalid', 'adminFormFileUpload--invalid', 'adminFormContent--invalid');
        });
        
        invalidFields.forEach(function(field) {
            // Find the parent form field component
            const formField = field.closest('.adminFormField, .adminFormSelect, .adminFormImageUpload, .adminFormFileUpload, .adminFormContent');
            if (formField) {
                if (formField.classList.contains('adminFormField')) {
                    formField.classList.add('adminFormField--invalid');
                } else if (formField.classList.contains('adminFormSelect')) {
                    formField.classList.add('adminFormSelect--invalid');
                } else if (formField.classList.contains('adminFormImageUpload')) {
                    formField.classList.add('adminFormImageUpload--invalid');
                } else if (formField.classList.contains('adminFormFileUpload')) {
                    formField.classList.add('adminFormFileUpload--invalid');
                } else if (formField.classList.contains('adminFormContent')) {
                    formField.classList.add('adminFormContent--invalid');
                }
            }
        });
    }
    
    // Show validation errors
    function showValidationErrors(invalidFields) {
        const errorBanner = document.getElementById('adminFormValidationErrors');
        const errorList = document.getElementById('adminFormValidationErrors_list');
        
        if (!errorBanner || !errorList) return;
        
        // Clear previous errors
        errorList.innerHTML = '';
        
        // Group errors by field
        const errorsByField = {};
        invalidFields.forEach(function(field) {
            const fieldName = field.name || field.id;
            if (!errorsByField[fieldName]) {
                errorsByField[fieldName] = [];
            }
            errorsByField[fieldName].push({
                field: field,
                message: field.validationMessage || 'Trường này là bắt buộc'
            });
        });
        
        // Group errors by repeater section
        const repeaterErrors = {
            'repeater_trainer_achievement': [],
            'repeater_trainer_skill': [],
            'repeater_trainer_experience': [],
            'repeater_trainer_degree': []
        };
        const otherErrors = [];
        
        Object.keys(errorsByField).forEach(function(fieldName) {
            const fieldErrors = errorsByField[fieldName];
            const field = fieldErrors[0].field;
            
            // Check if this field belongs to a repeater
            let isRepeaterField = false;
            for (const repeaterKey in repeaterErrors) {
                if (fieldName.startsWith(repeaterKey + '[')) {
                    repeaterErrors[repeaterKey].push({
                        field: field,
                        fieldName: fieldName,
                        message: fieldErrors[0].message
                    });
                    isRepeaterField = true;
                    break;
                }
            }
            
            if (!isRepeaterField) {
                otherErrors.push({
                    field: field,
                    fieldName: fieldName,
                    fieldErrors: fieldErrors
                });
            }
        });
        
        // Display repeater errors with custom messages
        const repeaterMessages = {
            'repeater_trainer_achievement': 'Vui lòng nhập ít nhất một thành tích hợp lệ (có nội dung thành tích).',
            'repeater_trainer_skill': 'Vui lòng nhập ít nhất một kỹ năng hợp lệ (có tên kỹ năng và phần trăm).',
            'repeater_trainer_experience': 'Vui lòng nhập ít nhất một kinh nghiệm hợp lệ (có chức vụ, đơn vị và kỹ năng).',
            'repeater_trainer_degree': 'Vui lòng nhập ít nhất một bằng cấp hợp lệ (có tiêu đề, trường học và kỹ năng).'
        };
        
        for (const repeaterKey in repeaterErrors) {
            if (repeaterErrors[repeaterKey].length > 0) {
                const li = document.createElement('li');
                li.className = 'adminFormValidationErrors_item';
                li.textContent = repeaterMessages[repeaterKey];
                errorList.appendChild(li);
                
                // Highlight the section
                const section = document.querySelector(`[data-repeater-list="${repeaterKey}"]`)?.closest('.adminFormSection');
                if (section) {
                    section.classList.add('adminFormSection--invalid');
                }
            }
        }
        
        // Display other field errors
        otherErrors.forEach(function(errorData) {
            const field = errorData.field;
            const fieldName = errorData.fieldName;
            const fieldErrors = errorData.fieldErrors;
            const label = getFieldLabel(fieldName);
            
            const li = document.createElement('li');
            li.className = 'adminFormValidationErrors_item';
            
            const validationMessage = fieldErrors[0].message;
            const displayText = validationMessage && 
                                validationMessage !== 'Trường này là bắt buộc' && 
                                validationMessage !== 'Please fill out this field.' &&
                                validationMessage !== 'Vui lòng điền vào trường này.' &&
                                validationMessage.length > 20
                ? validationMessage 
                : (label || fieldName);
            
            const link = document.createElement('a');
            link.href = '#';
            link.className = 'adminFormValidationErrors_link';
            link.textContent = displayText;
            link.onclick = function(e) {
                e.preventDefault();
                const formField = field.closest('.adminFormField, .adminFormSelect, .adminFormImageUpload, .adminFormFileUpload, .adminFormContent, .adminFormSection');
                if (formField) {
                    formField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    setTimeout(function() {
                        if (field.focus) field.focus();
                    }, 300);
                }
            };
            
            li.appendChild(link);
            errorList.appendChild(li);
        });
        
        // Show banner
        errorBanner.style.display = 'block';
        
        // Scroll to banner
        errorBanner.scrollIntoView({ behavior: 'smooth', block: 'start' });
        
        // Highlight invalid fields
        highlightInvalidFields(invalidFields);
    }
    
    // Hide validation errors
    function hideValidationErrors() {
        const errorBanner = document.getElementById('adminFormValidationErrors');
        if (errorBanner) {
            errorBanner.style.display = 'none';
        }
        
        // Remove highlights
        document.querySelectorAll('.adminFormField--invalid, .adminFormSelect--invalid, .adminFormImageUpload--invalid, .adminFormFileUpload--invalid, .adminFormContent--invalid').forEach(function(el) {
            el.classList.remove('adminFormField--invalid', 'adminFormSelect--invalid', 'adminFormImageUpload--invalid', 'adminFormFileUpload--invalid', 'adminFormContent--invalid');
        });
    }
    
    // Show server-side validation errors if any
    function showServerValidationErrors() {
        @if ($errors->any())
            const serverErrors = @json($errors->all());
            if (serverErrors && serverErrors.length > 0) {
                const errorBanner = document.getElementById('adminFormValidationErrors');
                const errorList = document.getElementById('adminFormValidationErrors_list');
                const errorTitle = document.querySelector('.adminFormValidationErrors_title');
                
                if (errorBanner && errorList) {
                    errorList.innerHTML = '';
                    
                    // Change title
                    if (errorTitle) {
                        errorTitle.textContent = 'Có lỗi xảy ra:';
                    }
                    
                    // Add each error message as a list item
                    serverErrors.forEach(function(errorMessage) {
                        const li = document.createElement('li');
                        li.className = 'adminFormValidationErrors_item';
                        li.textContent = errorMessage;
                        errorList.appendChild(li);
                    });
                    
                    // Show banner
                    errorBanner.style.display = 'block';
                    errorBanner.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    
                    // Highlight related sections for repeater fields
                    serverErrors.forEach(function(errorMessage) {
                        // Check if error is for repeater fields
                        if (errorMessage.includes('thành tích')) {
                            const section = document.querySelector('[data-repeater-list="repeater_trainer_achievement"]')?.closest('.adminFormSection');
                            if (section) section.classList.add('adminFormSection--invalid');
                        } else if (errorMessage.includes('kỹ năng')) {
                            const section = document.querySelector('[data-repeater-list="repeater_trainer_skill"]')?.closest('.adminFormSection');
                            if (section) section.classList.add('adminFormSection--invalid');
                        } else if (errorMessage.includes('kinh nghiệm')) {
                            const section = document.querySelector('[data-repeater-list="repeater_trainer_experience"]')?.closest('.adminFormSection');
                            if (section) section.classList.add('adminFormSection--invalid');
                        } else if (errorMessage.includes('bằng cấp')) {
                            const section = document.querySelector('[data-repeater-list="repeater_trainer_degree"]')?.closest('.adminFormSection');
                            if (section) section.classList.add('adminFormSection--invalid');
                        }
                    });
                    
                    // Highlight other fields
                    @foreach($errors->keys() as $key)
                        @if(!str_starts_with($key, 'repeater_'))
                            const field{{ $loop->index }} = document.querySelector('[name="{{ $key }}"], [name="{{ $key }}[]"]');
                            if (field{{ $loop->index }}) {
                                const formField{{ $loop->index }} = field{{ $loop->index }}.closest('.adminFormField, .adminFormSelect, .adminFormImageUpload, .adminFormFileUpload, .adminFormContent');
                                if (formField{{ $loop->index }}) {
                                    if (formField{{ $loop->index }}.classList.contains('adminFormField')) {
                                        formField{{ $loop->index }}.classList.add('adminFormField--invalid');
                                    } else if (formField{{ $loop->index }}.classList.contains('adminFormSelect')) {
                                        formField{{ $loop->index }}.classList.add('adminFormSelect--invalid');
                                    } else if (formField{{ $loop->index }}.classList.contains('adminFormImageUpload')) {
                                        formField{{ $loop->index }}.classList.add('adminFormImageUpload--invalid');
                                    } else if (formField{{ $loop->index }}.classList.contains('adminFormFileUpload')) {
                                        formField{{ $loop->index }}.classList.add('adminFormFileUpload--invalid');
                                    } else if (formField{{ $loop->index }}.classList.contains('adminFormContent')) {
                                        formField{{ $loop->index }}.classList.add('adminFormContent--invalid');
                                    }
                                }
                            }
                        @endif
                    @endforeach
                }
            }
        @endif
    }
    
    // Initialize when DOM is ready
    function initValidationErrors() {
        const form = document.getElementById('formAction') || document.getElementById('formProfile');
        if (!form) {
            return;
        }
        
        // Show server-side errors on page load
        showServerValidationErrors();
        
        // Handle form submit
        form.addEventListener('submit', function(e) {
            // Check validation
            if (form.checkValidity && !form.checkValidity()) {
                e.preventDefault();
                
                // Get all invalid fields (including hidden inputs)
                const invalidFields = Array.from(form.querySelectorAll(':invalid'));
                
                // Also check custom selects with required hidden inputs
                const customSelects = form.querySelectorAll('.adminCustomSelect[data-required="true"]');
                customSelects.forEach(function(select) {
                    const hiddenInput = select.querySelector('input[type="hidden"]');
                    if (hiddenInput && hiddenInput.hasAttribute('required')) {
                        const isMultiple = select.dataset.multiple === 'true';
                        let isEmpty = false;
                        
                        if (isMultiple) {
                            try {
                                const value = JSON.parse(hiddenInput.value || '[]');
                                isEmpty = !Array.isArray(value) || value.length === 0;
                            } catch(e) {
                                isEmpty = true;
                            }
                        } else {
                            isEmpty = !hiddenInput.value || hiddenInput.value === '';
                        }
                        
                        if (isEmpty) {
                            if (!invalidFields.includes(hiddenInput)) {
                                invalidFields.push(hiddenInput);
                            }
                            // Set custom validity
                            hiddenInput.setCustomValidity(isMultiple ? 'Vui lòng chọn ít nhất một mục' : 'Vui lòng chọn một mục');
                        }
                    }
                });
                
                if (invalidFields.length > 0) {
                    showValidationErrors(invalidFields);
                    
                    // Scroll to first invalid field
                    const firstInvalid = invalidFields[0];
                    const formField = firstInvalid.closest('.adminFormField, .adminFormSelect, .adminFormImageUpload, .adminFormFileUpload, .adminFormContent, .adminFormSection');
                    if (formField) {
                        setTimeout(function() {
                            formField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            // Try to focus on the actual input if possible
                            const focusableInput = formField.querySelector('input:not([type="hidden"]), textarea, select');
                            if (focusableInput && focusableInput.focus) {
                                focusableInput.focus();
                            } else if (firstInvalid.focus && firstInvalid.type !== 'hidden') {
                                firstInvalid.focus();
                            }
                        }, 500);
                    }
                }
            } else {
                hideValidationErrors();
            }
        });
        
        // Handle close button
        const closeBtn = document.getElementById('adminFormValidationErrors_close');
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                hideValidationErrors();
            });
        }
        
        // Clear errors when fields are changed
        form.addEventListener('input', function(e) {
            const field = e.target;
            if (field.checkValidity && field.checkValidity()) {
                // Remove highlight from this field
                const formField = field.closest('.adminFormField, .adminFormSelect, .adminFormImageUpload, .adminFormFileUpload, .adminFormContent');
                if (formField) {
                    formField.classList.remove('adminFormField--invalid', 'adminFormSelect--invalid', 'adminFormImageUpload--invalid', 'adminFormFileUpload--invalid', 'adminFormContent--invalid');
                }
                
                // Check if all fields are valid
                const invalidFields = Array.from(form.querySelectorAll(':invalid'));
                if (invalidFields.length === 0) {
                    hideValidationErrors();
                }
            }
        });
        
        // Also handle change events for selects and file inputs
        form.addEventListener('change', function(e) {
            const field = e.target;
            if (field.checkValidity && field.checkValidity()) {
                const formField = field.closest('.adminFormField, .adminFormSelect, .adminFormImageUpload, .adminFormFileUpload, .adminFormContent');
                if (formField) {
                    formField.classList.remove('adminFormField--invalid', 'adminFormSelect--invalid', 'adminFormImageUpload--invalid', 'adminFormFileUpload--invalid', 'adminFormContent--invalid');
                }
                
                const invalidFields = Array.from(form.querySelectorAll(':invalid'));
                if (invalidFields.length === 0) {
                    hideValidationErrors();
                }
            }
        });
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initValidationErrors);
    } else {
        initValidationErrors();
    }
    
    // Export functions for use in formActions
    window.adminFormValidation = {
        show: showValidationErrors,
        hide: hideValidationErrors,
        highlight: highlightInvalidFields
    };
})();
</script>
@endpush

