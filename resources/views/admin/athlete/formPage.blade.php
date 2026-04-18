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
    $athleteCode = ($language=='vi' && !empty($item->athlete_code)) ? $item->athlete_code : null;
@endphp

@include('admin.components.profileFormFields', [
    'user' => $user,
    'item' => $item,
    'trainerCode' => $athleteCode,
    'hideAddress' => true,
    'formType' => 'athlete'
])
