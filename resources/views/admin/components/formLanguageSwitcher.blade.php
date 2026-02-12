{{-- 
    Component: Admin Form Language Switcher
    Usage: @include('admin.components.formLanguageSwitcher', [
        'item' => $item,
        'language' => $language,
        'routeName' => 'admin.document.view'
    ])
--}}
<div class="adminFormLanguageSwitcher">
    <div class="adminFormLanguageSwitcher_label">Ngôn ngữ:</div>
    <div class="adminFormLanguageSwitcher_list">
        @foreach(config('language') as $lang)
            @php
                $selected = null;
                if(!empty($language) && $language == $lang['key']) $selected = 'adminFormLanguageSwitcher_item--active';
                
                $disabled = false;
                $languageLink = route($routeName, [
                    "language" => $lang['key'], 
                    "id" => $item->id ?? 0
                ]);
                
                if(!empty($item->seos)){
                    foreach($item->seos as $s){
                        if(!empty($s->infoSeo->language) && $s->infoSeo->language == $lang['key']){
                            $disabled = false;
                            break;
                        }
                    }
                }
            @endphp
            <a 
                href="{{ $languageLink }}" 
                class="adminFormLanguageSwitcher_item {{ $selected }} {{ $disabled ? 'adminFormLanguageSwitcher_item--disabled' : '' }}"
                title="{{ $lang['name_by_language'] }}"
            >
                <span class="adminFormLanguageSwitcher_item_code">{{ strtoupper($lang['key']) }}</span>
                <span class="adminFormLanguageSwitcher_item_name">{{ $lang['name_by_language'] }}</span>
            </a>
        @endforeach
    </div>
</div>

