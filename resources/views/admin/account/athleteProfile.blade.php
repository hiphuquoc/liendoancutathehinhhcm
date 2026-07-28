@extends('layouts.admin')

@section('content')
@php
    $user = auth()->user();
    $athleteCode = $athleteCode ?? null;
    
    // Image URL
    $imageUrl = null;
    $imageUrlSmall = null;
    $imageInfo = null;
    if(!empty($athlete->seo->image)) {
        $imageUrl = \App\Helpers\Image::getUrlImageCloud($athlete->seo->image);
        $imageUrlSmall = \App\Helpers\Image::getUrlImageSmallByUrlImage($athlete->seo->image);
        try {
            $response = \Illuminate\Support\Facades\Http::get($imageUrl);
            if($response->ok()) {
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
    $viewUrl = !empty($athlete->seo->slug_full) ? '/' . $athlete->seo->slug_full : null;
@endphp

<form id="formAction" action="{{ route('admin.account.updateAthleteProfile') }}" method="POST" enctype="multipart/form-data" class="adminFormPage_form">
    @csrf
    
    <div class="adminFormPage">
        <div class="adminFormPage_content">
            <!-- Header -->
            @include('admin.components.pageHeader', [
                'title' => 'Hồ sơ VĐV',
                'desc' => 'Chỉnh sửa thông tin hồ sơ huấn luyện viên',
                'icon' => '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>',
                'backUrl' => route('admin.account.profile'),
                'backText' => 'Quay lại'
            ])
            
            <!-- Validation Errors Banner -->
            @include('admin.components.formValidationErrors')
            
            <!-- Message -->
            @if(session('message'))
                @php
                    $sessionMessage = session('message');
                @endphp
                <div class="adminFormPage_message adminFormPage_message--{{ $sessionMessage['type'] ?? 'info' }}">
                    <div class="adminFormPage_message_icon">
                        @if(($sessionMessage['type'] ?? '') === 'success')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                        @else
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                        @endif
                    </div>
                    <div class="adminFormPage_message_content">
                        {!! $sessionMessage['message'] ?? '' !!}
                    </div>
                </div>
                @php
                    session()->forget('message');
                @endphp
            @endif

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
                            @php
                                $phoneValue = old('phone') ?? ($athlete->phone ?? '');
                                $descriptionValue = old('description') ?? ($athlete->description ?? '');
                            @endphp
                            
                            {{-- Trainer Code (readonly) --}}
                            @if(!empty($athleteCode))
                                <div class="adminFormField">
                                    <div class="adminFormField_labelWrapper">
                                        <label class="adminFormField_label">
                                            <span>Mã VĐV</span>
                                        </label>
                                    </div>
                                    <div 
                                        class="adminPersonnelPage_card_code adminPersonnelPage_card_code--form adminPersonnelPage_card_code--readonly"
                                    >
                                        <span class="adminPersonnelPage_card_code_text">{{ $athleteCode }}</span>
                                    </div>
                                </div>
                            @endif
                            
                            {{-- Name (readonly display) --}}
                            <div class="adminFormField">
                                <div class="adminFormField_labelWrapper">
                                    <label class="adminFormField_label">
                                        <span>Họ và tên</span>
                                    </label>
                                </div>
                                <div class="adminPersonnelPage_card_code adminPersonnelPage_card_code--form adminPersonnelPage_card_code--readonly-display">
                                    <span class="adminPersonnelPage_card_code_text">{{ $athlete->name ?? 'Chưa có' }}</span>
                                </div>
                            </div>
                            
                            {{-- Position (readonly display) --}}
                            <div class="adminFormField">
                                <div class="adminFormField_labelWrapper">
                                    <label class="adminFormField_label">
                                        <span>Chức vụ</span>
                                    </label>
                                </div>
                                <div class="adminPersonnelPage_card_code adminPersonnelPage_card_code--form adminPersonnelPage_card_code--readonly-display">
                                    <span class="adminPersonnelPage_card_code_text">{{ $athlete->position ?? 'Chưa có' }}</span>
                                </div>
                            </div>
                            
                            {{-- Email (editable) --}}
                            @php
                                $emailValue = old('email') ?? ($athlete->email ?? '');
                            @endphp
                            @include('admin.components.formField', [
                                'label' => 'Email',
                                'name' => 'email',
                                'type' => 'email',
                                'required' => false,
                                'value' => $emailValue,
                                'tooltip' => 'Đây là Email của Huấn luyện viên hiển thị trên website'
                            ])
                            
                            {{-- Phone (editable) --}}
                            @include('admin.components.formField', [
                                'label' => 'Số điện thoại',
                                'name' => 'phone',
                                'type' => 'text',
                                'required' => false,
                                'value' => $phoneValue,
                                'tooltip' => 'Đây là Số điện thoại của Huấn luyện viên hiển thị trên website'
                            ])
                            
                            {{-- Description (editable) --}}
                            @include('admin.components.formField', [
                                'label' => 'Giới thiệu ngắn',
                                'name' => 'description',
                                'type' => 'textarea',
                                'required' => false,
                                'value' => $descriptionValue,
                                'tooltip' => 'Giới thiệu ngắn về huấn luyện viên (sẽ được đồng bộ với mô tả SEO)',
                                'charCount' => true,
                                'maxLength' => 2000,
                                'rows' => 7
                            ])
                        </div>
                    </div>

                    <!-- Thành tích -->
                    @include('admin.components.athleteRepeaterAchievement', [
                        'data' => $athlete->achievements ?? collect(),
                        'oldData' => old('repeater_athlete_achievement')
                    ])

                    <!-- Kỹ năng -->
                    @include('admin.components.athleteRepeaterSkill', [
                        'data' => $athlete->skills ?? collect(),
                        'oldData' => old('repeater_athlete_skill')
                    ])

                    <!-- Kinh nghiệm -->
                    @include('admin.components.athleteRepeaterExperience', [
                        'data' => $athlete->experiences ?? collect(),
                        'oldData' => old('repeater_athlete_experience')
                    ])

                    <!-- Bằng cấp -->
                    @include('admin.components.athleteRepeaterDegree', [
                        'data' => $athlete->degrees ?? collect(),
                        'oldData' => old('repeater_athlete_degree')
                    ])

                    <!-- Thông tin cá nhân (sidebar) -->
                    @include('admin.components.profilePersonalInfoFields', [
                        'item' => $athlete
                    ])

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
                                    'value' => old('total_learner') ?? ($athlete->total_learner ?? 0),
                                    'required' => false
                                ])
                                @include('admin.components.formField', [
                                    'label' => 'Giờ dạy',
                                    'name' => 'total_teaching_hour',
                                    'type' => 'number',
                                    'value' => old('total_teaching_hour') ?? ($athlete->total_teaching_hour ?? 0),
                                    'required' => false
                                ])
                                @include('admin.components.formField', [
                                    'label' => 'Giải thưởng',
                                    'name' => 'total_prize',
                                    'type' => 'number',
                                    'value' => old('total_prize') ?? ($athlete->total_prize ?? 0),
                                    'required' => false,
                                    'tooltip' => 'Số lượng giải thưởng đã đạt được'
                                ])
                            </div>
                        </div>
                    </div>

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
                                'activityImages' => $athlete->activityImages ?? collect(),
                                'ownerType' => 'athlete_info',
                                'ownerId' => $athlete->id
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
                                'backUrl' => route('admin.account.profile'),
                                'viewUrl' => $viewUrl,
                                'showIndexGoogle' => false
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
</script>
@endpush
