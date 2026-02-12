<div class="formBox">
    <div class="formBox_full">
        <div class="formBox_full_item">
            <!-- One Column -->
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
                $content = old('content') ?? $content ?? '';
            @endphp
            <div>
                <label class="form-label inputRequired" for="content">Nội dung</label>
                @if(!empty($chatgptDataAndEvent['eventChatgpt']))
                    <i class="fa-solid fa-arrow-rotate-left reloadContentIcon" onclick="{{ $chatgptDataAndEvent['eventChatgpt'] ?? null }}"></i>
                @endif
            </div>
            <div class="{{ !empty($flagCopySource)&&$flagCopySource==true ? 'boxInputSuccess' : '' }} tiptap-editor-wrapper">
                <textarea class="form-control tiptap-textarea" id="{{ $idBox }}" name="content[{{ $ordering }}]" rows="30" {{ $chatgptDataAndEvent['dataChatgpt'] ?? null }}>{!! is_array($content) ? implode('', $content) : $content !!}</textarea>
            </div>
        </div>
        {{-- <div class="formBox_full_item">
            <textarea class="form-control" id="en_content"  name="en_content" rows="20">{{ old('en_content') ?? $enContent ?? '' }}</textarea>
        </div> --}}
    </div>
</div>