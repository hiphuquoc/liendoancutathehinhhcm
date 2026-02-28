@php
    $readonly = auth()->user()->hasRole('admin') ? false : true;
    
    // ChatGPT for title
    $chatgptDataAndEvent = [];
    if(isset($prompts) && $prompts) {
        foreach($prompts as $prompt){
            if($language=='vi'){
                if($prompt->reference_name=='title'&&$prompt->type=='auto_content'){
                    $chatgptDataAndEvent = \App\Helpers\Charactor::generateChatgptDataAndEvent($item, $prompt, $language, 'title');
                    break;
                }
            }else {
                if($prompt->reference_name=='title'&&$prompt->type=='translate_content'){
                    $chatgptDataAndEvent = \App\Helpers\Charactor::generateChatgptDataAndEvent($item, $prompt, $language, 'title');
                    break;
                }
            }
        }
    }
            @endphp

@include('admin.components.formField', [
    'label' => 'Họ và tên',
    'name' => 'name',
    'type' => 'text',
    'required' => true,
    'value' => old('name') ?? $item->name ?? ($itemSeo->title ?? null),
    'readonly' => $readonly,
    'charCount' => true,
    'maxLength' => 255,
    'chatgptEvent' => $chatgptDataAndEvent['eventChatgpt'] ?? null,
    'chatgptData' => $chatgptDataAndEvent['dataChatgpt'] ?? null
])

@include('admin.components.formField', [
    'label' => 'Chức vụ',
    'name' => 'position',
    'type' => 'text',
    'required' => false,
    'value' => old('position') ?? $item->position ?? null,
    'tooltip' => 'Ví dụ: Trọng tài quốc tế',
    'readonly' => $readonly,
    'charCount' => true,
    'maxLength' => 255
])

        @if($language=='vi')
    @include('admin.components.formField', [
        'label' => 'Số điện thoại',
        'name' => 'phone',
        'type' => 'text',
        'required' => true,
        'value' => old('phone') ?? $item->phone ?? null,
        'tooltip' => 'Đây là Số điện thoại của Trọng tài hiển thị trên website'
    ])
    
    @include('admin.components.formField', [
        'label' => 'Email',
        'name' => 'email',
        'type' => 'text',
        'required' => true,
        'value' => old('email') ?? $item->email ?? null,
        'tooltip' => 'Đây là Email của Trọng tài hiển thị trên website'
    ])
    
    @php
        // Lấy description từ seo.description (referee_info không có cột description)
        $descriptionValue = old('description');
        if (is_null($descriptionValue)) {
            // Nếu có itemSeo, lấy từ seo.description
            if (!empty($itemSeo->description)) {
                $descriptionValue = $itemSeo->description;
            } elseif (!empty($item->seo->description)) {
                $descriptionValue = $item->seo->description;
            } elseif (empty($item->id)) {
                // Nếu tạo mới và chưa có giá trị, đặt giá trị mặc định
                $descriptionValue = 'Viết giới thiệu ngắn về bạn!';
            } else {
                $descriptionValue = '';
            }
        }
    @endphp
    
    @include('admin.components.formField', [
        'label' => 'Giới thiệu ngắn',
        'name' => 'description',
        'type' => 'textarea',
        'required' => false,
        'value' => $descriptionValue,
        'tooltip' => 'Giới thiệu ngắn về trọng tài (sẽ được đồng bộ với mô tả SEO)',
        'charCount' => true,
        'maxLength' => 2000,
        'rows' => 7
    ])
        @endif