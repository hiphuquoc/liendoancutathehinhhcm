@extends('layouts.admin')

@section('content')
@php
    $titlePage = 'Thêm Bài Viết mới';
    $submit = 'admin.blog.createAndUpdate';
    if(!empty($type) && $type == 'edit'){
        $titlePage = 'Chỉnh sửa Bài Viết';
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
    
    // Categories for select (multiple)
    $categoryOptions = [];
    if(!empty($parents)) {
        foreach($parents as $c) {
            if(!empty($c->seo) && $c->seo->level > 1) {
                $categoryOptions[$c->id] = $c->seo->title;
            }
        }
    }
    
    // Selected categories
    $selectedCategories = [];
    if(!empty($item->categories) && $item->categories->isNotEmpty()) {
        foreach($item->categories as $cCategory) {
            $selectedCategories[] = $cCategory->infoCategory->id;
        }
    }
    $selectedCategories = old('categories', $selectedCategories);
    
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
    <input type="hidden" id="blog_info_id" name="blog_info_id" value="{{ !empty($item->id) && $type != 'copy' ? $item->id : 0 }}" />
    <input type="hidden" id="language" name="language" value="{{ $language ?? 'vi' }}" />
    <input type="hidden" id="type" name="type" value="{{ $type }}" />

    <div class="adminFormPage">
        <div class="adminFormPage_content">
            <!-- Header -->
            @include('admin.components.pageHeader', [
                'title' => $titlePage,
                'desc' => $type == 'edit' ? 'Chỉnh sửa bài viết blog' : 'Tạo bài viết blog mới',
                'icon' => '<path d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h7.5M16.5 7.5l-3-3m0 0l-3 3m3-3v12.75"/>',
                'backUrl' => route('admin.blog.list'),
                'backText' => 'Quay lại'
            ])
            
            <!-- Language Switcher -->
            <div class="adminFormPage_languageSwitcher">
                @include('admin.components.formLanguageSwitcher', [
                    'item' => $item,
                    'language' => $language,
                    'routeName' => 'admin.blog.view'
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
                                    <path d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h7.5M16.5 7.5l-3-3m0 0l-3 3m3-3v12.75"/>
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
                                <!-- Categories (Multiple Select) -->
                                @php
                                    $categoryOptions = [];
                                    if(!empty($parents)) {
                                        foreach($parents as $c) {
                                            if(!empty($c->seo) && $c->seo->level > 1) {
                                                $categoryOptions[$c->id] = $c->seo->title;
                                            }
                                        }
                                    }
                                @endphp
                                @include('admin.components.formSelect', [
                                    'label' => 'Categories cha',
                                    'name' => 'categories',
                                    'value' => $selectedCategories,
                                    'options' => $categoryOptions,
                                    'multiple' => true,
                                    'placeholder' => 'Chọn categories...',
                                    'tooltip' => 'Danh sách các Category mà Blog này thuộc'
                                ])

                                @include('admin.components.formField', [
                                    'label' => 'Thứ tự',
                                    'name' => 'ordering',
                                    'type' => 'number',
                                    'value' => old('ordering') ?? $itemSeo->ordering ?? ($itemSourceToCopy->seo->ordering ?? ''),
                                    'tooltip' => 'Nhập vào một số để thể hiện độ ưu tiên khi hiển thị cùng các Category khác (Số càng nhỏ càng ưu tiên cao - Để trống tức là không ưu tiên)',
                                    'min' => 0
                                ])

                                @php
                                    $statusChecked = false;
                                    if(empty($item)){
                                        $statusChecked = !empty($itemSourceToCopy->status) && ($itemSourceToCopy->status == 1);
                                    } else {
                                        $statusChecked = !empty($item->status) && ($item->status == 1);
                                    }
                                @endphp
                                @include('admin.components.formField', [
                                    'label' => 'Cho phép hiển thị',
                                    'name' => 'status',
                                    'type' => 'checkbox',
                                    'value' => $statusChecked,
                                    'checkboxLabel' => 'Cho phép hiển thị'
                                ])

                                @php
                                    $outstandingChecked = false;
                                    if(empty($item)){
                                        $outstandingChecked = !empty($itemSourceToCopy->outstanding) && ($itemSourceToCopy->outstanding == 1);
                                    } else {
                                        $outstandingChecked = !empty($item->outstanding) && ($item->outstanding == 1);
                                    }
                                @endphp
                                @include('admin.components.formField', [
                                    'label' => 'Bài viết nổi bật',
                                    'name' => 'outstanding',
                                    'type' => 'checkbox',
                                    'value' => $outstandingChecked,
                                    'checkboxLabel' => 'Bài viết nổi bật'
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
                        'itemSourceToCopy' => $itemSourceToCopy,
                        'itemSeoSourceToCopy' => $itemSeoSourceToCopy
                    ])
                </div>

                <!-- Sidebar -->
                <div class="adminFormPage_sidebar">
                    <div class="adminFormSidebar">
                        <div class="adminFormSidebar_sticky">
                            <!-- Actions -->
                            @include('admin.components.formActions', [
                                'backRoute' => 'admin.blog.list',
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

{{-- Modals removed - features not in use --}}

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
    
    // Initialize Select2 for multiple select
    if (typeof $ !== 'undefined' && $.fn.select2) {
        $('#categories').select2({
            placeholder: 'Chọn categories...',
            allowClear: true,
            width: '100%'
        });
    }
});
</script>
@endpush
