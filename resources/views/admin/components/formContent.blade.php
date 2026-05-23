{{-- 
    Component: Admin Form Content (Rich Text Editor)
    Usage: @include('admin.components.formContent', [
        'prompt' => $prompt,
        'content' => $content,
        'idBox' => 'content_1',
        'ordering' => 1,
        'idContent' => 0,
        'flagCopySource' => false,
        'language' => 'vi',
        'item' => $item
    ])
--}}
@php
    $chatgptDataAndEvent = [];
    if(!empty($prompt)){
        if($language=='vi'){
            if($prompt->reference_name=='content'){
                if($prompt->type=='auto_content'||$prompt->type=='auto_content_for_image'){
                    $chatgptDataAndEvent = \App\Helpers\Charactor::generateChatgptDataAndEvent($item, $prompt, $language, $idBox, $idContent ?? 0);
                }
            }
        }else {
            if($prompt->reference_name=='content'&&$prompt->type=='translate_content'){
                $chatgptDataAndEvent = \App\Helpers\Charactor::generateChatgptDataAndEvent($item, $prompt, $language, $idBox, $idContent ?? 0);
            }
        }
    }
    $contentValue = old('content') ?? $content ?? '';
    $contentValue = is_array($contentValue) ? implode('', $contentValue) : $contentValue;
    $contentLabel = !empty($prompt->name) ? $prompt->name : 'Nội dung';
@endphp

<div class="adminFormContent">
    <div class="adminFormContent_labelWrapper">
        <label class="adminFormContent_label" for="{{ $idBox }}">
            <span>{{ $contentLabel }}</span>
            <span class="adminFormContent_required">*</span>
            @if(!empty($chatgptDataAndEvent['eventChatgpt']))
                <button type="button" class="adminFormContent_chatgptReload" onclick="{{ $chatgptDataAndEvent['eventChatgpt'] }}" title="Tạo lại bằng ChatGPT">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/>
                        <path d="M21 3v5h-5"/>
                        <path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/>
                        <path d="M3 21v-5h5"/>
                    </svg>
                </button>
            @endif
        </label>
    </div>
    
    <div class="adminFormContent_editorWrapper tiptap-editor-wrapper {{ !empty($flagCopySource) && $flagCopySource == true ? 'adminFormContent_editorWrapper--copied' : '' }}">
        <textarea 
            class="adminFormContent_editor tiptap-textarea" 
            id="{{ $idBox }}" 
            name="content[{{ $ordering }}]" 
            rows="30"
            @if(!empty($chatgptDataAndEvent['dataChatgpt'])) {!! $chatgptDataAndEvent['dataChatgpt'] !!} @endif
        >{!! $contentValue !!}</textarea>
    </div>
</div>

