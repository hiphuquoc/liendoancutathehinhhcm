{{--
    Khu vực / Kinh nghiệm / Ngôn ngữ — hiển thị sidebar trang hồ sơ chi tiết.
    Usage: @include('admin.components.profilePersonalInfoFields', ['item' => $item])
--}}
@php
    $item = $item ?? null;
    $languageOptions = \App\Helpers\SpokenLanguage::options();
    $selectedLanguages = old('languages');
    if ($selectedLanguages === null) {
        $selectedLanguages = \App\Helpers\SpokenLanguage::parse($item->languages ?? null);
    } elseif (!is_array($selectedLanguages)) {
        $selectedLanguages = \App\Helpers\SpokenLanguage::parse($selectedLanguages);
    }
@endphp
<div class="adminFormSection">
    <div class="adminFormSection_header">
        <div class="adminFormSection_header_icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
            </svg>
        </div>
        <div class="adminFormSection_header_info">
            <h2 class="adminFormSection_title">Thông tin cá nhân (sidebar)</h2>
            <p class="adminFormSection_description">Khu vực, số năm kinh nghiệm và ngôn ngữ hiển thị trên trang hồ sơ công khai.</p>
        </div>
    </div>
    <div class="adminFormSection_body">
        <div class="adminFormGrid adminFormGrid--2cols">
            @include('admin.components.formField', [
                'label' => 'Khu vực',
                'name' => 'area',
                'type' => 'text',
                'value' => old('area') ?? ($item->area ?? ''),
                'required' => false,
                'tooltip' => 'Ví dụ: TP. Hồ Chí Minh',
                'charCount' => true,
                'maxLength' => 255
            ])
            @include('admin.components.formField', [
                'label' => 'Kinh nghiệm (năm)',
                'name' => 'years_experience',
                'type' => 'number',
                'value' => old('years_experience') ?? ($item->years_experience ?? ''),
                'required' => false,
                'tooltip' => "Số năm kinh nghiệm hiển thị dạng \"5+ Năm\""
            ])
        </div>
        <div style="margin-top: 1rem;">
            @include('admin.components.formSelect', [
                'label' => 'Ngôn ngữ',
                'name' => 'languages',
                'value' => $selectedLanguages,
                'options' => $languageOptions,
                'multiple' => true,
                'required' => false,
                'placeholder' => 'Chọn ngôn ngữ...',
                'tooltip' => 'Chọn một hoặc nhiều ngôn ngữ hiển thị trên hồ sơ công khai'
            ])
        </div>
    </div>
</div>
