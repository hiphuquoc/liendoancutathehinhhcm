@php
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
    
    $user = auth()->user();
    $trainerCode = ($language=='vi' && !empty($item->trainer_code)) ? $item->trainer_code : null;
@endphp

@include('admin.components.profileFormFields', [
    'user' => $user,
    'item' => $item,
    'trainerCode' => $trainerCode,
    'hideAddress' => true, // Hide address in trainer profile
    'formType' => 'trainer'
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
