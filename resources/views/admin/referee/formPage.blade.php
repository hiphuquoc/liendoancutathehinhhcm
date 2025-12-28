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
        @endif