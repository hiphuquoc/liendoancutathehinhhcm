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
