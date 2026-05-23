@extends('layouts.admin')

@section('content')
    @php
    $titlePage = 'Thêm Huấn luyện viên mới';
    $submit = 'admin.trainer.createAndUpdate';
    if(!empty($type) && $type == 'edit'){
        $titlePage = 'Chỉnh sửa Huấn luyện viên';
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
    <input type="hidden" id="trainer_info_id" name="trainer_info_id" value="{{ !empty($item->id) && $type != 'copy' ? $item->id : 0 }}" />
    <input type="hidden" id="language" name="language" value="{{ $language ?? 'vi' }}" />
    <input type="hidden" id="type" name="type" value="{{ $type }}" />

    <div class="adminFormPage">
        <div class="adminFormPage_content">
            <!-- Header -->
            @include('admin.components.pageHeader', [
                'title' => $titlePage,
                'desc' => $type == 'edit' ? 'Chỉnh sửa thông tin huấn luyện viên' : 'Tạo huấn luyện viên mới',
                'icon' => '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>',
                'backUrl' => route('admin.trainer.list'),
                'backText' => 'Quay lại'
            ])
            
            <!-- Language Switcher -->
            <div class="adminFormPage_languageSwitcher">
                @include('admin.components.formLanguageSwitcher', [
                    'item' => $item,
                    'language' => $language,
                    'routeName' => 'admin.trainer.view'
                ])
            </div>
            
            <!-- Validation Errors Banner -->
            @include('admin.components.formValidationErrors')

            <!-- Errors - Now handled by formValidationErrors component -->

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
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                            </div>
                            <div class="adminFormSection_header_info">
                                <h2 class="adminFormSection_title">Thông tin trang</h2>
                            </div>
                        </div>
                        <div class="adminFormSection_body">
                            @include('admin.trainer.formPage', [
                                'item' => !empty($itemSourceToCopy) ? $itemSourceToCopy : $item,
                                'itemSeo' => !empty($itemSeoSourceToCopy) ? $itemSeoSourceToCopy : $itemSeo,
                                'flagCopySource' => !empty($itemSeoSourceToCopy) ? true : false,
                                'language' => $language,
                                'prompts' => $prompts ?? collect()
                            ])
                        </div>
                    </div>

                    <!-- Thống kê hoạt động -->
                    <div class="adminFormSection">
                        <div class="adminFormSection_header">
                            <div class="adminFormSection_header_icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 20V10"/>
                                    <path d="M18 20V4"/>
                                    <path d="M6 20v-4"/>
                                </svg>
                            </div>
                            <div class="adminFormSection_header_info">
                                <h2 class="adminFormSection_title">Thống kê hoạt động</h2>
                                <p class="adminFormSection_description">Số học viên, giờ dạy, giải thưởng hiển thị trên trang profile.</p>
                            </div>
                        </div>
                        <div class="adminFormSection_body">
                            <div class="adminFormGrid adminFormGrid--3cols">
                                @include('admin.components.formField', [
                                    'label' => 'Số học viên',
                                    'name' => 'total_learner',
                                    'type' => 'number',
                                    'value' => old('total_learner') ?? ($item->total_learner ?? 0),
                                    'required' => false
                                ])
                                @include('admin.components.formField', [
                                    'label' => 'Giờ dạy',
                                    'name' => 'total_teaching_hour',
                                    'type' => 'number',
                                    'value' => old('total_teaching_hour') ?? ($item->total_teaching_hour ?? 0),
                                    'required' => false
                                ])
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
                                'item' => !empty($itemSourceToCopy) ? $itemSourceToCopy : $item,
                                'itemSeo' => !empty($itemSeoSourceToCopy) ? $itemSeoSourceToCopy : $itemSeo,
                                'language' => $language,
                                'prompts' => $prompts ?? collect(),
                                'parents' => $parents ?? collect()
                            ])
                        </div>
                    </div>

                    <!-- Thành tích -->
                    @include('admin.components.trainerRepeaterAchievement', [
                        'data' => $item->achievements ?? collect(),
                        'oldData' => old('repeater_trainer_achievement')
                    ])
                    
                    <!-- Kỹ năng -->
                    @include('admin.components.trainerRepeaterSkill', [
                        'data' => $item->skills ?? collect(),
                        'oldData' => old('repeater_trainer_skill')
                    ])

                    <!-- Kinh nghiệm -->
                    @include('admin.components.trainerRepeaterExperience', [
                        'data' => $item->experiences ?? collect(),
                        'oldData' => old('repeater_trainer_experience')
                    ])

                    <!-- Bằng cấp -->
                    @include('admin.components.trainerRepeaterDegree', [
                        'data' => $item->degrees ?? collect(),
                        'oldData' => old('repeater_trainer_degree')
                    ])

                    <!-- Hình ảnh hoạt động -->
                    <div class="adminFormSection">
                        <div class="adminFormSection_header">
                            <div class="adminFormSection_header_icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                    <circle cx="8.5" cy="8.5" r="1.5"/>
                                    <polyline points="21 15 16 10 5 21"/>
                                </svg>
                            </div>
                            <div class="adminFormSection_header_info">
                                <h2 class="adminFormSection_title">Hình ảnh hoạt động</h2>
                                <p class="adminFormSection_description">Ảnh hiển thị tại trang chi tiết profile. Upload lên Google Cloud.</p>
                            </div>
                        </div>
                        <div class="adminFormSection_body">
                            @include('admin.components.activityImagesUpload', [
                                'activityImages' => (!empty($item->id) && ($type ?? '') != 'copy') ? ($item->activityImages ?? collect()) : collect(),
                                'ownerType' => 'trainer_info',
                                'ownerId' => $item->id ?? 0
                            ])
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="adminFormPage_sidebar">
                    <div class="adminFormSidebar">
                        <div class="adminFormSidebar_sticky">
                            <!-- Actions -->
                            @include('admin.components.formActions', [
                                'backRoute' => 'admin.trainer.list',
                                'viewUrl' => $viewUrl,
                                'showIndexGoogle' => true
                            ])

                            <!-- Image Upload -->
                            @include('admin.components.formImageUpload', [
                                'name' => 'image',
                                'label' => 'Ảnh đại diện 600×800px',
                                'required' => false,
                                'currentImage' => $imageUrlSmall,
                                'aspectRatio' => '600/800',
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

@push('scriptCustom')
    <script type="text/javascript">
    // Load repeater plugin dynamically if not already loaded
    function loadRepeaterPlugin(callback) {
        if (typeof $.fn.repeater !== 'undefined') {
            callback();
            return;
        }
        
        // Try to load from CDN as fallback
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/jquery.repeater@1.2.1/jquery.repeater.min.js';
        script.onload = function() {
            callback();
        };
        script.onerror = function() {
            // Try local file
            const localScript = document.createElement('script');
            localScript.src = '{{ asset("sources/admin/app-assets/vendors/js/forms/repeater/jquery.repeater.min.js") }}';
            localScript.onload = function() {
                callback();
            };
            localScript.onerror = function() {
                // Silent fail - plugin might already be loaded
            };
            document.head.appendChild(localScript);
        };
        document.head.appendChild(script);
    }
    
    // Initialize repeater
    function initRepeaters() {
        // Check if repeater plugin is loaded
        if (typeof $.fn.repeater === 'undefined') {
            return;
        }
        
        // Initialize each repeater section
        $('.adminFormSection--repeater').each(function() {
            const $section = $(this);
            const $repeaterList = $section.find('[data-repeater-list]');
            const $createButton = $section.find('.adminFormSection_header_action');
            const $hiddenCreateButton = $section.find('[data-repeater-create]').not('.adminFormSection_header_action');
            
            if ($repeaterList.length && $hiddenCreateButton.length) {
                // Initialize repeater on the section body (parent of repeater-list)
                // The plugin will find the data-repeater-create button in the same container
                $section.find('.adminFormSection_body').repeater({
                    initEmpty: false,
            show: function () {
                        $(this).slideDown(300);
                        // Initialize sortable for new item
                        initSortable($repeaterList);
                        // Update ordering after adding new item
                        updateRepeaterOrdering($repeaterList);
            },
            hide: function (deleteElement) {
                        $(this).slideUp(300, deleteElement);
                        // Update ordering after removing item
                        updateRepeaterOrdering($repeaterList);
                    },
                    ready: function (setIndexes) {
                        setIndexes();
                        // Initialize sortable
                        initSortable($repeaterList);
                        // Update ordering
                        updateRepeaterOrdering($repeaterList);
                    }
                });
                
                // Handle click on header button - trigger the hidden button
                $createButton.off('click.repeater').on('click.repeater', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $hiddenCreateButton.trigger('click');
                });
            }
        });
    }
    
    // Initialize sortable for repeater list
    function initSortable($repeaterList) {
        if (typeof $.fn.sortable === 'undefined') {
            return;
        }
        
        $repeaterList.sortable({
            handle: '.adminFormRepeater_item_drag',
            items: '[data-repeater-item]',
            cursor: 'move',
            opacity: 0.7,
            tolerance: 'pointer',
            placeholder: 'adminFormRepeater_item adminFormRepeater_item--placeholder',
            start: function(e, ui) {
                ui.placeholder.height(ui.item.height());
            },
            stop: function(e, ui) {
                // Update ordering after sort
                updateRepeaterOrdering($repeaterList);
            }
        });
    }
    
    // Update ordering values based on current position
    function updateRepeaterOrdering($repeaterList) {
        $repeaterList.find('[data-repeater-item]').each(function(index) {
            const $item = $(this);
            const $orderingInput = $item.find('.adminFormRepeater_item_ordering');
            if ($orderingInput.length) {
                $orderingInput.val(index);
            } else {
                // Create ordering input if it doesn't exist
                const $content = $item.find('.adminFormRepeater_item_content');
                if ($content.length) {
                    $content.prepend('<input type="hidden" name="ordering" value="' + index + '" class="adminFormRepeater_item_ordering" />');
                }
            }
        });
    }
    
    // Wait for DOM and plugin to be ready
    $(document).ready(function() {
        loadRepeaterPlugin(function() {
            // Small delay to ensure plugin is fully initialized
            setTimeout(initRepeaters, 50);
        });
    });

    // Copy trainer code function
    function copyTrainerCode(code, element) {
        // element can be button or the entire code box
        var codeBox = element.classList.contains('adminPersonnelPage_card_code') ? element : element.closest('.adminPersonnelPage_card_code');
        if (!codeBox) codeBox = element;
        
        // Copy to clipboard
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(code).then(function() {
                // Show success state
                codeBox.classList.add('adminPersonnelPage_card_code--copied');
                codeBox.setAttribute('data-tooltip', 'Đã sao chép!');
                codeBox.setAttribute('title', 'Đã sao chép!');
                
                // Reset after 2 seconds
                setTimeout(function() {
                    codeBox.classList.remove('adminPersonnelPage_card_code--copied');
                    codeBox.setAttribute('data-tooltip', 'Nhấp để sao chép mã HLV');
                    codeBox.setAttribute('title', 'Nhấp để sao chép mã HLV');
                }, 2000);
            }).catch(function(err) {
                console.error('Failed to copy:', err);
                alert('Không thể sao chép mã số. Vui lòng thử lại hoặc sao chép thủ công.');
            });
        } else {
            // Fallback for older browsers
            var textArea = document.createElement("textarea");
            textArea.value = code;
            textArea.style.position = "fixed";
            textArea.style.left = "-999999px";
            textArea.style.top = "-999999px";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                document.execCommand('copy');
                codeBox.classList.add('adminPersonnelPage_card_code--copied');
                codeBox.setAttribute('data-tooltip', 'Đã sao chép!');
                codeBox.setAttribute('title', 'Đã sao chép!');
                setTimeout(function() {
                    codeBox.classList.remove('adminPersonnelPage_card_code--copied');
                    codeBox.setAttribute('data-tooltip', 'Nhấp để sao chép mã HLV');
                    codeBox.setAttribute('title', 'Nhấp để sao chép mã HLV');
                }, 2000);
            } catch (err) {
                console.error('Failed to copy:', err);
                alert('Không thể sao chép mã số. Vui lòng thử lại hoặc sao chép thủ công.');
            }
            document.body.removeChild(textArea);
        }
    }
    </script>
@endpush
