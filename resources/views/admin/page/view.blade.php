@extends('layouts.admin')

@section('content')
@php
    $titlePage = 'Thêm Trang mới';
    $submit = 'admin.page.createAndUpdate';
    if(!empty($type) && $type == 'edit'){
        $titlePage = 'Chỉnh sửa Trang';
    }
    
    // Image URL
    $imageUrl = null;
    $imageUrlSmall = null;
    $imageInfo = null;
    if(!empty($item->seo->image)) {
        $imageUrl = \App\Helpers\Image::getUrlImageCloud($item->seo->image);
        $imageUrlSmall = \App\Helpers\Image::getUrlImageSmallByUrlImage($item->seo->image);
        try {
            $response = Http::get($imageUrl);
            if($response->ok() && $type != 'copy') {
                $size = getimagesize($imageUrl);
                $extension = pathinfo($imageUrl, PATHINFO_EXTENSION);
                $fileSize = $response->header('content-length');
                $imageInfo = [
                    'extension' => $extension,
                    'width' => $size[0],
                    'height' => $size[1],
                    'size' => round($fileSize / 1024, 0)
                ];
            }
        } catch (\Exception $e) {
            // Ignore
        }
    }
    
    // View URL
    $viewUrl = !empty($itemSeo->slug_full) ? '/' . $itemSeo->slug_full : null;
    
    // Page types for select
    $pageTypeOptions = [];
    if(!empty($pageTypes)) {
        foreach($pageTypes as $pageType) {
            $pageTypeOptions[$pageType->id] = $pageType->name;
        }
    }
    
    // Ensure variables exist
    $itemSourceToCopy = $itemSourceToCopy ?? null;
    $itemSeoSourceToCopy = $itemSeoSourceToCopy ?? null;
@endphp

<!-- Start: background để chặn thao tác khi đang dịch content ngầm -->
@include('admin.category.lock')
<!-- End: background để chặn thao tác khi đang dịch content ngầm -->

<form id="formAction" action="{{ route($submit) }}" method="POST" enctype="multipart/form-data" class="adminFormPage_form">
    @csrf
    <input type="hidden" id="seo_id" name="seo_id" value="{{ $itemSeo->id ?? 0 }}" />
    <input type="hidden" id="seo_id_vi" name="seo_id_vi" value="{{ !empty($item->seo->id) && $type != 'copy' ? $item->seo->id : 0 }}" />
    <input type="hidden" id="page_info_id" name="page_info_id" value="{{ !empty($item->id) && $type != 'copy' ? $item->id : 0 }}" />
    <input type="hidden" id="language" name="language" value="{{ $language ?? 'vi' }}" />
    <input type="hidden" id="type" name="type" value="{{ $type }}" />

    <div class="adminFormPage">
        <div class="adminFormPage_content">
            <!-- Header -->
            @include('admin.components.pageHeader', [
                'title' => $titlePage,
                'desc' => $type == 'edit' ? 'Chỉnh sửa trang' : 'Tạo trang mới',
                'icon' => '<path d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>',
                'backUrl' => route('admin.page.list'),
                'backText' => 'Quay lại'
            ])
            
            <!-- Language Switcher -->
            <div class="adminFormPage_languageSwitcher">
                @include('admin.components.formLanguageSwitcher', [
                    'item' => $item,
                    'language' => $language,
                    'routeName' => 'admin.page.view'
                ])
            </div>
            
            <!-- Validation Errors Banner -->
            @include('admin.components.formValidationErrors')

            <!-- Errors -->
            @if ($errors->any())
                <div class="adminFormPage_errors">
                    <div class="adminFormPage_errors_icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                    </div>
                    <div class="adminFormPage_errors_content">
                        <h3>Có lỗi xảy ra:</h3>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Message -->
            @include('admin.components.formMessage')

            <!-- Body -->
            <div class="adminFormPage_body">
                <div class="adminFormPage_main">
                    <!-- Thông tin trang -->
                    <div class="adminFormSection">
                        <div class="adminFormSection_header">
                            <div class="adminFormSection_header_icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                                </svg>
                            </div>
                            <div class="adminFormSection_header_info">
                                <h2 class="adminFormSection_title">Thông tin trang</h2>
                            </div>
                        </div>
                        <div class="adminFormSection_body">
                            @php
                                $chatgptDataAndEvent = [];
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
                            @endphp
                            
                            @include('admin.components.formField', [
                                'label' => 'Tiêu đề Trang',
                                'name' => 'title',
                                'type' => 'textarea',
                                'required' => true,
                                'value' => old('title') ?? $itemSeo->title ?? null,
                                'tooltip' => 'Đây là Tiêu đề được hiển thị trên website',
                                'charCount' => true,
                                'maxLength' => 255,
                                'rows' => 2,
                                'chatgptEvent' => $chatgptDataAndEvent['eventChatgpt'] ?? null,
                                'chatgptData' => $chatgptDataAndEvent['dataChatgpt'] ?? null
                            ])

                            @if($language == 'vi')
                                @include('admin.components.formField', [
                                    'label' => 'Thứ tự',
                                    'name' => 'ordering',
                                    'type' => 'number',
                                    'value' => old('ordering') ?? $itemSeo->ordering ?? ($item->seo->ordering ?? ''),
                                    'tooltip' => 'Nhập vào một số để thể hiện độ ưu tiên khi hiển thị cùng các Category khác (Số càng nhỏ càng ưu tiên cao - Để trống tức là không ưu tiên)',
                                    'min' => 0
                                ])

                                @include('admin.components.formSelect', [
                                    'label' => 'Phân loại',
                                    'name' => 'type_id',
                                    'required' => true,
                                    'value' => old('type_id') ?? $item->type_id ?? null,
                                    'options' => $pageTypeOptions,
                                    'placeholder' => '- Lựa chọn -',
                                    'tooltip' => 'Chọn phân loại cho trang'
                                ])

                                @php
                                    $showSidebarChecked = !empty($item->show_sidebar) && ($item->show_sidebar == 1);
                                @endphp
                                @include('admin.components.formField', [
                                    'label' => 'Cho phép hiển thị trong sidebar',
                                    'name' => 'show_sidebar',
                                    'type' => 'checkbox',
                                    'value' => $showSidebarChecked,
                                    'checkboxLabel' => 'Cho phép hiển thị trong sidebar'
                                ])
                            @endif
                        </div>
                    </div>

                    <!-- Thông tin SEO -->
                    <div class="adminFormSection">
                        <div class="adminFormSection_header">
                            <div class="adminFormSection_header_icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                                    <path d="M2 12h20"/>
                                </svg>
                            </div>
                            <div class="adminFormSection_header_info">
                                <h2 class="adminFormSection_title">Thông tin SEO</h2>
                            </div>
                        </div>
                        <div class="adminFormSection_body">
                            @include('admin.components.formSeo', [
                                'item' => $item,
                                'itemSeo' => $itemSeo,
                                'language' => $language,
                                'prompts' => $prompts,
                                'parents' => $parents ?? collect()
                            ])
                        </div>
                    </div>

                    <!-- Nội dung -->
                    @include('admin.components.formFilterContent', [
                        'prompts' => $prompts,
                        'language' => $language,
                        'item' => $item,
                        'itemSeo' => $itemSeo,
                        'itemSourceToCopy' => $itemSourceToCopy ?? null,
                        'itemSeoSourceToCopy' => $itemSeoSourceToCopy ?? null
                    ])
                </div>

                <!-- Sidebar -->
                <div class="adminFormPage_sidebar">
                    <div class="adminFormSidebar">
                        <div class="adminFormSidebar_sticky">
                            <!-- Actions -->
                            @include('admin.components.formActions', [
                                'backRoute' => 'admin.page.list',
                                'viewUrl' => $viewUrl,
                                'showIndexGoogle' => true
                            ])

                            <!-- Image Upload -->
                            @include('admin.components.formImageUpload', [
                                'name' => 'image',
                                'label' => 'Ảnh đại diện 800×533px',
                                'required' => false,
                                'currentImage' => $imageUrlSmall,
                                'aspectRatio' => '800/533',
                                'imageInfo' => $imageInfo,
                                'tooltip' => 'Đây là Ảnh đại diện dùng làm Ảnh đại diện trên website, Ảnh đại diện ngoài Google, Ảnh đại diện khi Share link'
                            ])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection

{{-- Modal removed - feature not in use --}}

@push('scriptCustom')
<script>
// Character count update
document.addEventListener('DOMContentLoaded', function() {
    const charCountFields = document.querySelectorAll('.adminFormField_charCount');
    
    charCountFields.forEach(function(charCount) {
        const fieldId = charCount.getAttribute('data-field');
        const field = document.getElementById(fieldId);
        const currentSpan = charCount.querySelector('.adminFormField_charCount_current');
        
        if (field && currentSpan) {
            // Update on input
            field.addEventListener('input', function() {
                const length = this.value ? this.value.length : 0;
                currentSpan.textContent = length;
            });
            
            // Initial count
            if (field.value) {
                currentSpan.textContent = field.value.length;
            }
        }
    });
    
    // Repeater for content boxes
    if (typeof $ !== 'undefined' && $.fn.repeater) {
        $('.repeater').repeater();
    }
});
</script>
@endpush
