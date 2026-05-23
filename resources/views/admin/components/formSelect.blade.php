{{-- 
    Component: Admin Form Select (Custom - Single or Multiple)
    Usage: 
    @include('admin.components.formSelect', [
        'label' => 'Categories',
        'name' => 'categories',
        'required' => false,
        'value' => $selectedValue, // single: value, multiple: array
        'options' => ['1' => 'Option 1', '2' => 'Option 2'],
        'multiple' => true,
        'placeholder' => 'Chọn...',
        'tooltip' => 'Giải thích...'
    ])
--}}
@php
    $fieldId = $name ?? 'select_' . uniqid();
    $isRequired = $required ?? false;
    $isMultiple = $multiple ?? false;
    $fieldValue = $value ?? old($name);
    $placeholder = $placeholder ?? ($isMultiple ? 'Chọn...' : '- Lựa chọn -');
    $selectName = $isMultiple ? $name . '[]' : $name;
    
    // Handle value for multiple select
    if($isMultiple) {
        $selectedValues = is_array($fieldValue) ? $fieldValue : [];
        if(empty($selectedValues) && old($name)) {
            $selectedValues = old($name);
        }
    } else {
        $selectedValue = $fieldValue ?? old($name);
    }
@endphp

<div class="adminFormSelect {{ $isRequired ? 'adminFormSelect--required' : '' }} {{ isset($class) ? $class : '' }}">
    <div class="adminFormSelect_labelWrapper">
        <label class="adminFormSelect_label" for="{{ $fieldId }}">
            @if(isset($tooltip))
                <span class="adminFormSelect_tooltip" data-tooltip="{{ $tooltip }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                </span>
            @endif
            <span>{{ $label ?? '' }}</span>
            @if($isRequired)
                <span class="adminFormSelect_required">*</span>
            @endif
        </label>
    </div>
    
    <div class="adminCustomSelect {{ $isMultiple ? 'adminCustomSelect--multiple' : 'adminCustomSelect--single' }}" data-field-name="{{ $name }}" data-multiple="{{ $isMultiple ? 'true' : 'false' }}" data-required="{{ $isRequired ? 'true' : 'false' }}" id="{{ $fieldId }}_container">
        <input type="hidden" name="{{ $selectName }}" id="{{ $fieldId }}_input" value="{{ $isMultiple ? json_encode($selectedValues ?? []) : ($selectedValue ?? '') }}" @if($isRequired) required @endif>
        <div class="adminCustomSelect_display">
            @if($isMultiple)
                <div class="adminCustomSelect_tags" id="{{ $fieldId }}_tags"></div>
                <input type="text" class="adminCustomSelect_search" id="{{ $fieldId }}_search" placeholder="{{ $placeholder }}" autocomplete="off" {{ isset($readonly) && $readonly ? 'disabled' : '' }}>
            @else
                <div class="adminCustomSelect_selected" id="{{ $fieldId }}_selected">
                    <span class="adminCustomSelect_selected_text">{{ $placeholder }}</span>
                </div>
                <input type="text" class="adminCustomSelect_search" id="{{ $fieldId }}_search" placeholder="{{ $placeholder }}" autocomplete="off" style="display: none;" {{ isset($readonly) && $readonly ? 'disabled' : '' }}>
            @endif
            <button type="button" class="adminCustomSelect_dropdown" aria-label="Mở danh sách" {{ isset($readonly) && $readonly ? 'disabled' : '' }}>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M6 9l6 6 6-6"/>
                </svg>
            </button>
        </div>
        <div class="adminCustomSelect_options" id="{{ $fieldId }}_options">
            @if(isset($options))
                @foreach($options as $optionValue => $optionLabel)
                    <div class="adminCustomSelect_option" data-value="{{ $optionValue }}" data-label="{{ $optionLabel }}">
                        {{ $optionLabel }}
                    </div>
                @endforeach
            @endif
        </div>
    </div>
    
    @if($errors->has($name))
        <div class="adminFormSelect_error">
            {{ $errors->first($name) }}
        </div>
    @endif
    
    @if(isset($helpText))
        <div class="adminFormSelect_help">{{ $helpText }}</div>
    @endif
</div>

@push('scriptCustom')
<script>
(function() {
    function initAdminCustomSelect(container) {
        const fieldName = container.dataset.fieldName;
        const isMultiple = container.dataset.multiple === 'true';
        const isRequired = container.dataset.required === 'true';
        const hiddenInput = container.querySelector('input[type="hidden"]');
        const display = container.querySelector('.adminCustomSelect_display');
        const searchInput = container.querySelector('.adminCustomSelect_search');
        const optionsContainer = container.querySelector('.adminCustomSelect_options');
        const dropdownBtn = container.querySelector('.adminCustomSelect_dropdown');
        const selectedContainer = !isMultiple ? container.querySelector('.adminCustomSelect_selected') : null;
        
        let selectedValues = [];
        let selectedValue = '';
        
        // Initialize values
        if (isMultiple) {
            const tagsContainer = container.querySelector('.adminCustomSelect_tags');
            try {
                const oldValue = hiddenInput.value;
                if (oldValue) {
                    const parsed = JSON.parse(oldValue);
                    selectedValues = Array.isArray(parsed) ? parsed.map(v => String(v)) : [];
                    selectedValues = [...new Set(selectedValues)];
                }
            } catch(e) {
                selectedValues = [];
            }
        } else {
            const selectedContainer = container.querySelector('.adminCustomSelect_selected');
            try {
                selectedValue = hiddenInput.value || '';
            } catch(e) {
                selectedValue = '';
            }
        }
        
        // Render tags (for multiple)
        function renderTags() {
            if (!isMultiple) return;
            
            const tagsContainer = container.querySelector('.adminCustomSelect_tags');
            selectedValues = [...new Set(selectedValues)];
            
            tagsContainer.innerHTML = '';
            selectedValues.forEach(value => {
                const option = optionsContainer.querySelector(`[data-value="${value}"]`);
                if (option) {
                    const tag = document.createElement('div');
                    tag.className = 'adminCustomSelect_tag';
                    tag.innerHTML = `
                        <span>${option.dataset.label}</span>
                        <button type="button" class="adminCustomSelect_tag_remove" data-value="${value}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M18 6L6 18M6 6l12 12"/>
                            </svg>
                        </button>
                    `;
                    tagsContainer.appendChild(tag);
                }
            });
            
            // Update placeholder
            if (selectedValues.length > 0) {
                searchInput.placeholder = '';
            } else {
                const originalPlaceholder = searchInput.getAttribute('data-placeholder') || searchInput.placeholder || 'Chọn...';
                searchInput.placeholder = originalPlaceholder;
            }
            
            // Update option selected state
            optionsContainer.querySelectorAll('.adminCustomSelect_option').forEach(option => {
                const value = option.dataset.value;
                if (selectedValues.includes(value)) {
                    option.classList.add('adminCustomSelect_option--selected');
                } else {
                    option.classList.remove('adminCustomSelect_option--selected');
                }
            });
            
            // Update hidden input
            hiddenInput.value = JSON.stringify(selectedValues);
            
            // Update required validation for hidden input
            if (isRequired && hiddenInput) {
                if (selectedValues.length === 0) {
                    hiddenInput.setCustomValidity('Vui lòng chọn ít nhất một mục');
                } else {
                    hiddenInput.setCustomValidity('');
                }
            }
            
            // Create array inputs for form submission
            const existingInputs = container.parentElement.querySelectorAll(`input[name="${fieldName}[]"]`);
            existingInputs.forEach(input => input.remove());
            
            selectedValues.forEach(value => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `${fieldName}[]`;
                input.value = value;
                container.parentElement.appendChild(input);
            });
        }
        
        // Render selected (for single)
        function renderSelected() {
            if (isMultiple || !selectedContainer) return;
            
            const selectedText = selectedContainer.querySelector('.adminCustomSelect_selected_text');
            
            if (selectedValue) {
                const option = optionsContainer.querySelector(`[data-value="${selectedValue}"]`);
                if (option) {
                    selectedText.textContent = option.dataset.label;
                    selectedContainer.style.display = 'flex';
                    searchInput.style.display = 'none';
                } else {
                    selectedText.textContent = searchInput.placeholder;
                    selectedContainer.style.display = 'flex';
                    searchInput.style.display = 'none';
                }
            } else {
                selectedText.textContent = searchInput.placeholder;
                selectedContainer.style.display = 'flex';
                searchInput.style.display = 'none';
            }
            
            // Update option selected state
            optionsContainer.querySelectorAll('.adminCustomSelect_option').forEach(option => {
                const value = option.dataset.value;
                if (value === selectedValue) {
                    option.classList.add('adminCustomSelect_option--selected');
                } else {
                    option.classList.remove('adminCustomSelect_option--selected');
                }
            });
            
            // Update hidden input
            hiddenInput.value = selectedValue;
            
            // Update required validation for hidden input
            if (isRequired && hiddenInput) {
                if (!selectedValue || selectedValue === '') {
                    hiddenInput.setCustomValidity('Vui lòng chọn một mục');
                } else {
                    hiddenInput.setCustomValidity('');
                }
            }
        }
        
        // Toggle dropdown
        function toggleDropdown() {
            container.classList.toggle('adminCustomSelect--open');
            if (container.classList.contains('adminCustomSelect--open')) {
                if (isMultiple) {
                    setTimeout(() => searchInput?.focus(), 0);
                } else {
                    if (selectedContainer) selectedContainer.style.display = 'none';
                    searchInput.style.display = 'block';
                    setTimeout(() => searchInput?.focus(), 0);
                }
            } else {
                if (!isMultiple) {
                    searchInput.style.display = 'none';
                    if (selectedContainer) selectedContainer.style.display = 'flex';
                }
            }
        }
        
        function closeDropdown() {
            container.classList.remove('adminCustomSelect--open');
            if (!isMultiple) {
                searchInput.style.display = 'none';
                if (selectedContainer) selectedContainer.style.display = 'flex';
            }
        }
        
        // Filter options
        function filterOptions(searchTerm) {
            const options = optionsContainer.querySelectorAll('.adminCustomSelect_option');
            options.forEach(option => {
                const label = option.dataset.label.toLowerCase();
                const matches = label.includes(searchTerm.toLowerCase());
                option.style.display = matches ? 'block' : 'none';
            });
        }
        
        // Select option
        function selectOption(value) {
            if (isMultiple) {
                selectedValues = [...new Set(selectedValues)];
                if (selectedValues.includes(value)) {
                    removeTag(value);
                } else {
                    selectedValues.push(value);
                    selectedValues = [...new Set(selectedValues)];
                    renderTags();
                }
                searchInput.value = '';
                filterOptions('');
            } else {
                selectedValue = value;
                renderSelected();
                closeDropdown();
            }
        }
        
        // Remove tag (for multiple)
        function removeTag(value) {
            selectedValues = selectedValues.filter(v => v != value);
            renderTags();
        }
        
        // Event listeners
        dropdownBtn?.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (!this.disabled) {
                toggleDropdown();
            }
        });
        
        display?.addEventListener('click', function(e) {
            if (e.target === searchInput || e.target.closest('.adminCustomSelect_search')) return;
            if (e.target.closest('.adminCustomSelect_tag_remove')) return;
            if (e.target.closest('.adminCustomSelect_dropdown')) return;
            if (!dropdownBtn.disabled) {
                toggleDropdown();
            }
        });
        
        searchInput.addEventListener('click', function(e) {
            e.stopPropagation();
            if (!container.classList.contains('adminCustomSelect--open')) {
                container.classList.add('adminCustomSelect--open');
            }
        });
        
        searchInput.addEventListener('input', function() {
            filterOptions(this.value);
        });
        
        searchInput.addEventListener('focus', function(e) {
            if (!container.classList.contains('adminCustomSelect--open')) {
                container.classList.add('adminCustomSelect--open');
            }
        });
        
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDropdown();
            }
        });
        
        optionsContainer.addEventListener('click', function(e) {
            const option = e.target.closest('.adminCustomSelect_option');
            if (option) {
                selectOption(option.dataset.value);
            }
        });
        
        if (isMultiple) {
            const tagsContainer = container.querySelector('.adminCustomSelect_tags');
            tagsContainer.addEventListener('click', function(e) {
                const removeBtn = e.target.closest('.adminCustomSelect_tag_remove');
                if (removeBtn) {
                    removeTag(removeBtn.dataset.value);
                }
            });
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!container.contains(e.target)) {
                closeDropdown();
            }
        });
        
        // Save original placeholder
        if (searchInput && !searchInput.getAttribute('data-placeholder')) {
            searchInput.setAttribute('data-placeholder', searchInput.placeholder || '');
        }
        
        // Initialize
        if (isMultiple) {
            renderTags();
        } else {
            renderSelected();
        }
        
        // Set initial validation state
        if (isRequired && hiddenInput) {
            if (isMultiple) {
                if (selectedValues.length === 0) {
                    hiddenInput.setCustomValidity('Vui lòng chọn ít nhất một mục');
                } else {
                    hiddenInput.setCustomValidity('');
                }
            } else {
                if (!selectedValue || selectedValue === '') {
                    hiddenInput.setCustomValidity('Vui lòng chọn một mục');
                } else {
                    hiddenInput.setCustomValidity('');
                }
            }
        }
    }
    
    // Initialize all custom selects when DOM is ready
    function initAllAdminCustomSelects() {
        const containers = document.querySelectorAll('.adminCustomSelect:not([data-initialized])');
        containers.forEach(container => {
            container.setAttribute('data-initialized', 'true');
            initAdminCustomSelect(container);
        });
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAllAdminCustomSelects);
    } else {
        initAllAdminCustomSelects();
    }
    
    // Re-initialize after AJAX content load
    if (typeof $ !== 'undefined') {
        $(document).on('ajaxComplete', function() {
            setTimeout(initAllAdminCustomSelects, 100);
        });
    }
})();
</script>
@endpush
