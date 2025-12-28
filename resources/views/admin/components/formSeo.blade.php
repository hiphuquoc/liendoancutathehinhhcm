{{-- 
    Component: Admin Form SEO
    Usage: @include('admin.components.formSeo', [
        'item' => $item,
        'itemSeo' => $itemSeo,
        'language' => $language,
        'prompts' => $prompts,
        'parents' => $parents
    ])
--}}
@php
    $readonly = auth()->user()->hasRole('admin') ? false : true;
    
    // ChatGPT for seo_title
    $chatgptDataAndEventTitle = [];
    foreach($prompts as $prompt){
        if($language=='vi'){
            if($prompt->reference_name=='seo_title'){
                if($prompt->type=='auto_content'||$prompt->type=='auto_content_for_image'){
                    $chatgptDataAndEventTitle = \App\Helpers\Charactor::generateChatgptDataAndEvent($item, $prompt, $language, 'seo_title');
                    break;
                }
            }
        }else {
            if($prompt->reference_name=='seo_title'&&$prompt->type=='translate_content'){
                $chatgptDataAndEventTitle = \App\Helpers\Charactor::generateChatgptDataAndEvent($item, $prompt, $language, 'seo_title');
                break;
            }
        }
    }
    
    // ChatGPT for seo_description
    $chatgptDataAndEventDescription = [];
    foreach($prompts as $prompt){
        if($language=='vi'){
            if($prompt->reference_name=='seo_description'){
                if($prompt->type=='auto_content'||$prompt->type=='auto_content_for_image'){
                    $chatgptDataAndEventDescription = \App\Helpers\Charactor::generateChatgptDataAndEvent($item, $prompt, $language, 'seo_description');
                    break;
                }
            }
        }else {
            if($prompt->reference_name=='seo_description'&&$prompt->type=='translate_content'){
                $chatgptDataAndEventDescription = \App\Helpers\Charactor::generateChatgptDataAndEvent($item, $prompt, $language, 'seo_description');
                break;
            }
        }
    }
    
    // ChatGPT for slug
    $chatgptDataAndEventSlug = [];
    foreach($prompts as $prompt){
        if($language=='vi'){
            if($prompt->reference_name=='slug'){
                if($prompt->type=='auto_content'||$prompt->type=='auto_content_for_image'){
                    $chatgptDataAndEventSlug = \App\Helpers\Charactor::generateChatgptDataAndEvent($item, $prompt, $language, 'slug');
                    break;
                }
            }
        }else {
            if($prompt->reference_name=='slug'&&$prompt->type=='translate_content'){
                $chatgptDataAndEventSlug = \App\Helpers\Charactor::generateChatgptDataAndEvent($item, $prompt, $language, 'slug');
                break;
            }
        }
    }
@endphp

<div class="adminFormSeo">
    <!-- SEO Title -->
    @include('admin.components.formField', [
        'label' => 'Tiêu đề SEO',
        'name' => 'seo_title',
        'type' => 'textarea',
        'required' => true,
        'value' => old('seo_title') ?? $itemSeo['seo_title'] ?? '',
        'tooltip' => 'Đây là Tiêu đề được hiển thị ngoài Google... Tốt nhất nên từ 55- 60 ký tự, có chứa từ khóa chính tranh top và thu hút người truy cập click',
        'charCount' => true,
        'maxLength' => 255,
        'rows' => 2,
        'chatgptEvent' => $chatgptDataAndEventTitle['eventChatgpt'] ?? null,
        'chatgptData' => $chatgptDataAndEventTitle['dataChatgpt'] ?? null,
        'readonly' => $readonly
    ])

    <!-- SEO Description -->
    @include('admin.components.formField', [
        'label' => 'Mô tả SEO',
        'name' => 'seo_description',
        'type' => 'textarea',
        'required' => true,
        'value' => old('seo_description') ?? $itemSeo['seo_description'] ?? '',
        'tooltip' => 'Đây là Mô tả được hiển thị ngoài Google... Tốt nhất nên từ 140 - 160 ký tự, có chứa từ khóa chính tranh top và mô tả được cái người dùng đang cần',
        'charCount' => true,
        'maxLength' => 500,
        'rows' => 5,
        'chatgptEvent' => $chatgptDataAndEventDescription['eventChatgpt'] ?? null,
        'chatgptData' => $chatgptDataAndEventDescription['dataChatgpt'] ?? null,
        'readonly' => $readonly
    ])

    <!-- Slug -->
    <div class="adminFormField adminFormField--required">
        <div class="adminFormField_labelWrapper">
            <label class="adminFormField_label" for="slug">
                <span class="adminFormField_tooltip" data-tooltip="Đây là URL để người dùng truy cập... viết liền không dấu và ngăn cách nhau bởi dấu gạch (-)... nên chứa từ khóa SEO chính và ngắn gọn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                </span>
                <span>Đường dẫn tĩnh</span>
                <span class="adminFormField_required">*</span>
                @if(!empty($chatgptDataAndEventSlug['eventChatgpt']))
                    <button type="button" class="adminFormField_chatgptReload" onclick="{{ $chatgptDataAndEventSlug['eventChatgpt'] }}" title="Tạo lại bằng ChatGPT">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/>
                            <path d="M21 3v5h-5"/>
                            <path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/>
                            <path d="M3 21v-5h5"/>
                        </svg>
                    </button>
                @endif
                <button type="button" class="adminFormField_chatgptReload" onclick="convertStrToSlug('slug', $('#title'), '{{ $itemSeo->type ?? $item->seo->type ?? null }}', '{{ $language }}', {{ $item->seo->parent ?? 0 }});" title="Tạo từ tiêu đề">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2v20M2 12h20"/>
                    </svg>
                </button>
            </label>
        </div>
        <input 
            type="text" 
            id="slug" 
            class="adminFormField_input" 
            name="slug" 
            value="{{ old('slug') ?? $itemSeo['slug'] ?? '' }}"
            @if($readonly) readonly @endif
            required
            @if(!empty($chatgptDataAndEventSlug['dataChatgpt'])) {!! $chatgptDataAndEventSlug['dataChatgpt'] !!} @endif
        />
        @if($errors->has('slug'))
            <div class="adminFormField_error">{{ $errors->first('slug') }}</div>
        @endif
    </div>

    <!-- Parent (if available) -->
    @if(!empty($parents) && $parents->isNotEmpty())
        @php
            $parentOptions = [];
            $selectedParent = old('parent') ?? $itemSeo->parent ?? 0;
            foreach($parents as $page) {
                $seoChoose = null;
                foreach($page->seos as $seo) {
                    if(!empty($seo->infoSeo->language)) {
                        if($language == $seo->infoSeo->language) {
                            $seoChoose = $seo->infoSeo;
                            break;
                        }
                    }
                }
                if(!empty($seoChoose)) {
                    $parentOptions[$seoChoose->id] = $page->seo->title;
                }
            }
        @endphp
        @include('admin.components.formSelect', [
            'label' => 'Trang cha',
            'name' => 'parent',
            'value' => $selectedParent,
            'options' => $parentOptions,
            'placeholder' => '- Lựa chọn -',
            'tooltip' => 'Là trang cha chứa trang hiện tại... URL cũng sẽ được hiển thị theo cấp cha - con',
            'readonly' => $readonly
        ])
    @endif

    <!-- Rating Aggregate -->
    <div class="adminFormSeo_rating">
        <div class="adminFormSeo_rating_item">
            @include('admin.components.formField', [
                'label' => 'Lượt đánh giá',
                'name' => 'rating_aggregate_count',
                'type' => 'number',
                'required' => true,
                'value' => old('rating_aggregate_count') ?? $itemSeo['rating_aggregate_count'] ?? $item->seo['rating_aggregate_count'] ?? rand(1000,10000),
                'tooltip' => 'Đây là Số lượt đánh giá này được hiển thị trên trang website và ngoài Google để thể hiện sự uy tín (tự nhập tùy thích)',
                'readonly' => $readonly
            ])
        </div>
        <div class="adminFormSeo_rating_item">
            @include('admin.components.formField', [
                'label' => 'Điểm đánh giá',
                'name' => 'rating_aggregate_star',
                'type' => 'text',
                'required' => true,
                'value' => old('rating_aggregate_star') ?? $itemSeo['rating_aggregate_star'] ?? $item->seo['rating_aggregate_star'] ?? '4.'.rand(6,8),
                'tooltip' => 'Đây là Điểm đánh giá tương ứng này được hiển thị trên trang website và ngoài Google để thể hiện sự uy tín (tự nhập tùy thích)',
                'readonly' => $readonly
            ])
        </div>
    </div>
</div>

@push('scriptCustom')
<script>
function convertStrToSlug(idWrite, inputData, type, language, idParentVI) {
    const string = $(inputData).val();
    $.ajax({
        url: '{{ route("admin.helper.convertStrToSlug") }}',
        type: 'get',
        dataType: 'html',
        data: { 
            string,
            type,
            language,
            id_parent_vi: idParentVI,
        }
    }).done(function(data){
        $('#'+idWrite).val(data);
    });
}
</script>
@endpush

