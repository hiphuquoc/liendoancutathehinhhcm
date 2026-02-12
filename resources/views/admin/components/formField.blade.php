{{-- 
    Component: Admin Form Field
    Usage: 
    @include('admin.components.formField', [
        'label' => 'Tiêu đề',
        'name' => 'title',
        'type' => 'text',
        'required' => true,
        'value' => $value,
        'tooltip' => 'Giải thích...',
        'charCount' => true,
        'maxLength' => 255
    ])
--}}
@php
    $fieldId = $name ?? 'field_' . uniqid();
    $fieldType = $type ?? 'text';
    $fieldValue = $value ?? old($name);
    $isRequired = $required ?? false;
    $hasCharCount = $charCount ?? false;
    $maxLength = $maxLength ?? null;
@endphp

<div class="adminFormField {{ $isRequired ? 'adminFormField--required' : '' }} {{ isset($class) ? $class : '' }}">
    <div class="adminFormField_labelWrapper">
        <label class="adminFormField_label" for="{{ $fieldId }}">
            @if(isset($tooltip))
                <span class="adminFormField_tooltip" data-tooltip="{{ $tooltip }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                </span>
            @endif
            <span>{{ $label ?? '' }}</span>
            @if($isRequired)
                <span class="adminFormField_required">*</span>
            @endif
            @if(isset($chatgptEvent) && $chatgptEvent)
                <button type="button" class="adminFormField_chatgptReload" onclick="{{ $chatgptEvent }}" title="Tạo lại bằng ChatGPT">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/>
                        <path d="M21 3v5h-5"/>
                        <path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/>
                        <path d="M3 21v-5h5"/>
                    </svg>
                </button>
            @endif
        </label>
        @if($hasCharCount)
            <div class="adminFormField_charCount" data-field="{{ $fieldId }}">
                <span class="adminFormField_charCount_current">{{ mb_strlen($fieldValue ?? '') }}</span>
                @if($maxLength)
                    <span class="adminFormField_charCount_separator">/</span>
                    <span class="adminFormField_charCount_max">{{ $maxLength }}</span>
                @endif
            </div>
        @endif
    </div>

    @if($fieldType === 'textarea')
        <textarea 
            class="adminFormField_input adminFormField_input--textarea" 
            id="{{ $fieldId }}" 
            name="{{ $name }}" 
            @if($isRequired) required @endif
            @if($maxLength) maxlength="{{ $maxLength }}" @endif
            @if(isset($placeholder)) placeholder="{{ $placeholder }}" @endif
            @if(isset($rows)) rows="{{ $rows }}" @endif
            @if(isset($readonly) && $readonly) readonly @endif
        >{{ $fieldValue }}</textarea>
    @elseif($fieldType === 'select')
        <select 
            class="adminFormField_input adminFormField_input--select {{ isset($multiple) && $multiple ? 'adminFormField_input--multiple' : '' }}" 
            id="{{ $fieldId }}" 
            name="{{ $name }}{{ isset($multiple) && $multiple ? '[]' : '' }}"
            @if($isRequired) required @endif
            @if(isset($readonly) && $readonly) readonly @endif
            @if(isset($multiple) && $multiple) multiple @endif
            @if(isset($placeholder)) data-placeholder="{{ $placeholder }}" @endif
        >
            @if(isset($placeholder) && !isset($multiple))
                <option value="">{{ $placeholder }}</option>
            @endif
            @if(isset($options))
                @foreach($options as $optionValue => $optionLabel)
                    @php
                        $isSelected = false;
                        if(isset($multiple) && $multiple) {
                            $selectedValues = is_array($fieldValue) ? $fieldValue : (old($name) ?? []);
                            $isSelected = in_array($optionValue, $selectedValues);
                        } else {
                            $isSelected = $fieldValue == $optionValue;
                        }
                    @endphp
                    <option value="{{ $optionValue }}" {{ $isSelected ? 'selected' : '' }}>
                        {{ $optionLabel }}
                    </option>
                @endforeach
            @endif
        </select>
    @elseif($fieldType === 'checkbox')
        <div class="adminFormField_checkbox">
            <input 
                type="checkbox" 
                class="adminFormField_checkbox_input" 
                id="{{ $fieldId }}" 
                name="{{ $name }}"
                value="1"
                {{ $fieldValue ? 'checked' : '' }}
            />
            <label class="adminFormField_checkbox_label" for="{{ $fieldId }}">
                {{ $checkboxLabel ?? $label ?? '' }}
            </label>
        </div>
    @elseif($fieldType === 'file')
        <div class="adminFormField_file">
            <input 
                type="file" 
                class="adminFormField_file_input" 
                id="{{ $fieldId }}" 
                name="{{ $name }}"
                @if($isRequired) required @endif
                @if(isset($accept)) accept="{{ $accept }}" @endif
            />
            @if(isset($preview) && $preview)
                <div class="adminFormField_file_preview" id="{{ $fieldId }}_preview">
                    <!-- Preview sẽ được thêm bằng JS -->
                </div>
            @endif
        </div>
    @else
        <input 
            type="{{ $fieldType }}" 
            class="adminFormField_input" 
            id="{{ $fieldId }}" 
            name="{{ $name }}" 
            value="{{ $fieldValue }}"
            @if($isRequired) required @endif
            @if($maxLength) maxlength="{{ $maxLength }}" @endif
            @if(isset($placeholder)) placeholder="{{ $placeholder }}" @endif
            @if(isset($readonly) && $readonly) readonly @endif
            @if(isset($disabled) && $disabled) disabled @endif
            @if(isset($step)) step="{{ $step }}" @endif
            @if(isset($min)) min="{{ $min }}" @endif
            @if(isset($max)) max="{{ $max }}" @endif
            @if(isset($chatgptData)) {!! $chatgptData !!} @endif
        />
    @endif

    @if($errors->has($name))
        <div class="adminFormField_error">
            {{ $errors->first($name) }}
        </div>
    @elseif(isset($helpText))
        <div class="adminFormField_help">{{ $helpText }}</div>
    @endif
</div>

