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

    <!-- Stats Section -->
    <div class="adminFormSection_header" style="margin-top: 2rem; margin-bottom: 1rem; border-bottom: 1px solid #eee; padding-bottom: 0.5rem;">
        <h3 class="adminFormSection_title" style="font-size: 1.1rem; color: #444;">Thống kê hoạt động</h3>
    </div>

    <div class="row" style="display: flex; flex-wrap: wrap; margin: 0 -10px;">
        <div class="col-md-4" style="flex: 0 0 33.333%; max-width: 33.333%; padding: 0 10px;">
            @include('admin.components.formField', [
                'label' => 'Số học viên',
                'name' => 'total_learner',
                'type' => 'number',
                'value' => old('total_learner') ?? ($item->total_learner ?? 0),
                'required' => false
            ])
        </div>
        <div class="col-md-4" style="flex: 0 0 33.333%; max-width: 33.333%; padding: 0 10px;">
            @include('admin.components.formField', [
                'label' => 'Giờ dạy',
                'name' => 'total_teaching_hour',
                'type' => 'number',
                'value' => old('total_teaching_hour') ?? ($item->total_teaching_hour ?? 0),
                'required' => false
            ])
        </div>
        <div class="col-md-4" style="flex: 0 0 33.333%; max-width: 33.333%; padding: 0 10px;">
            @include('admin.components.formField', [
                'label' => 'Giải thưởng',
                'name' => 'total_prize',
                'type' => 'number',
                'value' => old('total_prize') ?? ($item->total_prize ?? 0),
                'required' => false,
                'tooltip' => 'Số lượng giải thưởng đã đạt được'
            ])
        </div>
    </div>
        @endif