{{-- 
    Component: Admin Form Filter Content (Content boxes based on prompts)
    Usage: @include('admin.components.formFilterContent', [
        'prompts' => $prompts,
        'language' => $language,
        'item' => $item,
        'itemSeo' => $itemSeo,
        'itemSourceToCopy' => $itemSourceToCopy,
        'itemSeoSourceToCopy' => $itemSeoSourceToCopy
    ])
--}}
@foreach($prompts as $prompt)
    <!-- tiếng việt -> form viết content (đối với bản viết có nhiều box theo layout prompt viết bài) -->
    @if($language=='vi') 
        @if($prompt->reference_name=='content'&&($prompt->type=='auto_content'||$prompt->type=='auto_content_for_image'))
            @php
                $key = $prompt->ordering;
                $contentsByLanguageUse = $itemSeoSourceToCopy->contents ?? $itemSeo->contents ?? [];
                /* lấy content theo ordering */
                $xhtmlContent = '';
                if(!empty($contentsByLanguageUse)&&$contentsByLanguageUse->count()>0){
                    foreach($contentsByLanguageUse as $c){
                        if($c->ordering==$key) {
                            $xhtmlContent = $c->content;
                            break;
                        }
                    }
                }
            @endphp
            
            <div class="adminFormSection">
                <div class="adminFormSection_header">
                    <div class="adminFormSection_header_icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h7.5M16.5 7.5l-3-3m0 0l-3 3m3-3v12.75"/>
                        </svg>
                    </div>
                    <div class="adminFormSection_header_info">
                        <h2 class="adminFormSection_title">{{ !empty($prompt->name) ? $prompt->name : 'Nội dung' }}</h2>
                        @if(!empty($prompt->description))
                            <p class="adminFormSection_description">{{ $prompt->description }}</p>
                        @endif
                    </div>
                </div>
                <div class="adminFormSection_body">
                    @include('admin.components.formContent', [
                        'prompt' => $prompt,
                        'content' => $xhtmlContent, 
                        'flagCopySource' => !empty($itemSeoSourceToCopy) ? true : false,
                        'idBox' => 'content_'.$key,
                        'ordering' => $key,
                        'language' => $language,
                        'item' => $item
                    ])
                </div>
            </div>
        @endif
    @else
        <!-- tiếng khác -> form dịch -->
        @if($prompt->type=='translate_content'&&$prompt->reference_name=='content')
            @php
                $contentsByLanguageUse = $itemSeoSourceToCopy->contents ?? $itemSeo->contents ?? [];
                $contentsViUse = $itemSourceToCopy->seo->contents ?? $item->seo->contents ?? [];
            @endphp
            @if(!empty($contentsViUse))
                @foreach($contentsViUse as $content)
                    @php
                        $key = $content->ordering;
                        /* lấy content theo ordering */
                        $xhtmlContent = '';
                        foreach($contentsByLanguageUse as $c){
                            if($c->ordering==$key) {
                                $xhtmlContent = $c->content;
                                break;
                            }
                        }
                    @endphp
                    
                    <div class="adminFormSection">
                        <div class="adminFormSection_header">
                            <div class="adminFormSection_header_icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h7.5M16.5 7.5l-3-3m0 0l-3 3m3-3v12.75"/>
                                </svg>
                            </div>
                            <div class="adminFormSection_header_info">
                                <h2 class="adminFormSection_title">{{ !empty($prompt->name) ? $prompt->name : 'Nội dung' }} ({{ $key }})</h2>
                                @if(!empty($prompt->description))
                                    <p class="adminFormSection_description">{{ $prompt->description }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="adminFormSection_body">
                            @include('admin.components.formContent', [
                                'prompt' => $prompt,
                                'content' => $xhtmlContent, 
                                'flagCopySource' => !empty($itemSourceToCopy) ? true : false,
                                'idBox' => 'content_'.$key,
                                'idContent' => $content->id ?? 0,
                                'ordering' => $key,
                                'language' => $language,
                                'item' => $item
                            ])
                        </div>
                    </div>
                @endforeach
            @endif
        @endif
    @endif
@endforeach

