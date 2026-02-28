@extends('layouts.admin')

@section('content')
@php
    $user = auth()->user();
    
    // Image URL
    $imageUrl = null;
    $imageUrlSmall = null;
    $imageInfo = null;
    if(!empty($referee->seo->image)) {
        $imageUrl = \App\Helpers\Image::getUrlImageCloud($referee->seo->image);
        $imageUrlSmall = \App\Helpers\Image::getUrlImageSmallByUrlImage($referee->seo->image);
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
    $viewUrl = !empty($referee->seo->slug_full) ? '/' . $referee->seo->slug_full : null;
@endphp

<form id="formAction" action="{{ route('admin.account.updateRefereeProfile') }}" method="POST" enctype="multipart/form-data" class="adminFormPage_form">
    @csrf
    
    <div class="adminFormPage">
        <div class="adminFormPage_content">
            <!-- Header -->
            @include('admin.components.pageHeader', [
                'title' => 'Hồ sơ Trọng tài',
                'desc' => 'Chỉnh sửa thông tin hồ sơ trọng tài',
                'icon' => '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>',
                'backUrl' => route('admin.account.profile'),
                'backText' => 'Quay lại'
            ])
            
            <!-- Validation Errors Banner -->
            @include('admin.components.formValidationErrors')
            
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
                            @php
                                $phoneValue = old('phone') ?? ($referee->phone ?? '');
                                // referee_info không có cột description, lấy từ seo.description
                                $descriptionValue = old('description') ?? ($referee->seo->description ?? '');
                            @endphp
                            
                            {{-- Name (readonly display) --}}
                            <div class="adminFormField">
                                <div class="adminFormField_labelWrapper">
                                    <label class="adminFormField_label">
                                        <span>Họ và tên</span>
                                    </label>
                                </div>
                                <div class="adminPersonnelPage_card_code adminPersonnelPage_card_code--form adminPersonnelPage_card_code--readonly-display">
                                    <span class="adminPersonnelPage_card_code_text">{{ $referee->name ?? 'Chưa có' }}</span>
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
                                    <span class="adminPersonnelPage_card_code_text">{{ $referee->position ?? 'Chưa có' }}</span>
                                </div>
                            </div>
                            
                            {{-- Email (editable) --}}
                            @php
                                $emailValue = old('email') ?? ($referee->email ?? '');
                            @endphp
                            @include('admin.components.formField', [
                                'label' => 'Email',
                                'name' => 'email',
                                'type' => 'email',
                                'required' => false,
                                'value' => $emailValue,
                                'tooltip' => 'Đây là Email của Trọng tài hiển thị trên website'
                            ])
                            
                            {{-- Phone (editable) --}}
                            @include('admin.components.formField', [
                                'label' => 'Số điện thoại',
                                'name' => 'phone',
                                'type' => 'text',
                                'required' => false,
                                'value' => $phoneValue,
                                'tooltip' => 'Đây là Số điện thoại của Trọng tài hiển thị trên website'
                            ])
                            
                            {{-- Description (editable) --}}
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
                        </div>
                    </div>

                    <!-- Thành tích -->
                    <div class="adminFormSection adminFormSection--repeater repeater" data-repeater-container>
                        <div class="adminFormSection_header">
                            <div class="adminFormSection_header_icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                            </div>
                            <div class="adminFormSection_header_info">
                                <h2 class="adminFormSection_title">Thành tích</h2>
                            </div>
                            <button type="button" class="adminFormSection_header_action" data-repeater-create>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="12" y1="8" x2="12" y2="16"/>
                                    <line x1="8" y1="12" x2="16" y2="12"/>
                                </svg>
                                <span>Thêm</span>
                            </button>
                        </div>
                        <div class="adminFormSection_body">
                            <div data-repeater-list="repeater_referee_achievement">
                                @php
                                    $dataAchievements = old('repeater_referee_achievement', $referee->achievements ?? collect());
                                    if ($dataAchievements instanceof \Illuminate\Support\Collection) {
                                        $dataAchievements = $dataAchievements->isNotEmpty() ? $dataAchievements->toArray() : [null];
                                    } elseif (is_array($dataAchievements)) {
                                        $dataAchievements = !empty($dataAchievements) ? $dataAchievements : [null];
                                    } else {
                                        $dataAchievements = [null];
                                    }
                                @endphp
                                @foreach($dataAchievements as $index => $achi)
                                    <div class="adminFormRepeater_item" data-repeater-item>
                                        <div class="adminFormRepeater_item_drag">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="9" cy="5" r="1"/>
                                                <circle cx="9" cy="12" r="1"/>
                                                <circle cx="9" cy="19" r="1"/>
                                                <circle cx="15" cy="5" r="1"/>
                                                <circle cx="15" cy="12" r="1"/>
                                                <circle cx="15" cy="19" r="1"/>
                                            </svg>
                                        </div>
                                        <div class="adminFormRepeater_item_content">
                                            <input type="hidden" name="ordering" value="{{ is_array($achi) ? ($achi['ordering'] ?? $index) : ($achi->ordering ?? $index) }}" class="adminFormRepeater_item_ordering" />
                                            @include('admin.components.formField', [
                                                'label' => 'Thành tích',
                                                'name' => 'content',
                                                'type' => 'text',
                                                'required' => true,
                                                'value' => is_array($achi) ? ($achi['content'] ?? '') : ($achi->content ?? ''),
                                                'placeholder' => 'Nhập thành tích...'
                                            ])
                                        </div>
                                        <button type="button" class="adminFormRepeater_item_delete" data-repeater-delete>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <line x1="18" y1="6" x2="6" y2="18"/>
                                                <line x1="6" y1="6" x2="18" y2="18"/>
                                            </svg>
                                            <span>Xóa</span>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                            <!-- Hidden button for repeater plugin to find -->
                            <button type="button" data-repeater-create style="display:none;"></button>
                        </div>
                    </div>
                    
                    <!-- Kỹ năng -->
                    <div class="adminFormSection adminFormSection--repeater repeater" data-repeater-container>
                        <div class="adminFormSection_header">
                            <div class="adminFormSection_header_icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                                    <path d="M2 17l10 5 10-5"/>
                                    <path d="M2 12l10 5 10-5"/>
                                </svg>
                            </div>
                            <div class="adminFormSection_header_info">
                                <h2 class="adminFormSection_title">Kỹ năng</h2>
                            </div>
                            <button type="button" class="adminFormSection_header_action" data-repeater-create>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="12" y1="8" x2="12" y2="16"/>
                                    <line x1="8" y1="12" x2="16" y2="12"/>
                                </svg>
                                <span>Thêm</span>
                            </button>
                        </div>
                        <div class="adminFormSection_body">
                            <div data-repeater-list="repeater_referee_skill">
                                @php
                                    $dataSkills = old('repeater_referee_skill', $referee->skills ?? collect());
                                    if ($dataSkills instanceof \Illuminate\Support\Collection) {
                                        $dataSkills = $dataSkills->isNotEmpty() ? $dataSkills->toArray() : [null];
                                    } elseif (is_array($dataSkills)) {
                                        $dataSkills = !empty($dataSkills) ? $dataSkills : [null];
                                    } else {
                                        $dataSkills = [null];
                                    }
                                @endphp
                                @foreach($dataSkills as $index => $skill)
                                    <div class="adminFormRepeater_item" data-repeater-item>
                                        <div class="adminFormRepeater_item_drag">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="9" cy="5" r="1"/>
                                                <circle cx="9" cy="12" r="1"/>
                                                <circle cx="9" cy="19" r="1"/>
                                                <circle cx="15" cy="5" r="1"/>
                                                <circle cx="15" cy="12" r="1"/>
                                                <circle cx="15" cy="19" r="1"/>
                                            </svg>
                                        </div>
                                        <div class="adminFormRepeater_item_content adminFormRepeater_item_content--grid">
                                            <input type="hidden" name="ordering" value="{{ is_array($skill) ? ($skill['ordering'] ?? $index) : ($skill->ordering ?? $index) }}" class="adminFormRepeater_item_ordering" />
                                            @include('admin.components.formField', [
                                                'label' => 'Kỹ năng',
                                                'name' => 'skill',
                                                'type' => 'text',
                                                'required' => true,
                                                'value' => is_array($skill) ? ($skill['skill'] ?? '') : ($skill->skill ?? ''),
                                                'placeholder' => 'Nhập kỹ năng...',
                                                'class' => 'adminFormRepeater_item_field'
                                            ])
                                            @include('admin.components.formField', [
                                                'label' => 'Phần trăm',
                                                'name' => 'percent',
                                                'type' => 'number',
                                                'required' => true,
                                                'value' => is_array($skill) ? ($skill['percent'] ?? '') : ($skill->percent ?? ''),
                                                'placeholder' => '%',
                                                'min' => 0,
                                                'max' => 100,
                                                'class' => 'adminFormRepeater_item_field adminFormRepeater_item_field--small'
                                            ])
                                        </div>
                                        <button type="button" class="adminFormRepeater_item_delete" data-repeater-delete>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <line x1="18" y1="6" x2="6" y2="18"/>
                                                <line x1="6" y1="6" x2="18" y2="18"/>
                                            </svg>
                                            <span>Xóa</span>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                            <!-- Hidden button for repeater plugin to find -->
                            <button type="button" data-repeater-create style="display:none;"></button>
                        </div>
                    </div>

                    <!-- Kinh nghiệm -->
                    <div class="adminFormSection adminFormSection--repeater repeater" data-repeater-container>
                        <div class="adminFormSection_header">
                            <div class="adminFormSection_header_icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                    <line x1="16" y1="13" x2="8" y2="13"/>
                                    <line x1="16" y1="17" x2="8" y2="17"/>
                                    <polyline points="10 9 9 9 8 9"/>
                                </svg>
                            </div>
                            <div class="adminFormSection_header_info">
                                <h2 class="adminFormSection_title">Kinh nghiệm</h2>
                            </div>
                            <button type="button" class="adminFormSection_header_action" data-repeater-create>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="12" y1="8" x2="12" y2="16"/>
                                    <line x1="8" y1="12" x2="16" y2="12"/>
                                </svg>
                                <span>Thêm</span>
                            </button>
                        </div>
                        <div class="adminFormSection_body">
                            <div data-repeater-list="repeater_referee_experience">
                                @php
                                    $dataExperience = old('repeater_referee_experience', $referee->experiences ?? collect());
                                    if ($dataExperience instanceof \Illuminate\Support\Collection) {
                                        $dataExperience = $dataExperience->isNotEmpty() ? $dataExperience : [null];
                                    } elseif (is_array($dataExperience)) {
                                        $dataExperience = !empty($dataExperience) ? $dataExperience : [null];
                                    } else {
                                        $dataExperience = [null];
                                    }
                                @endphp
                                @foreach($dataExperience as $index => $exp)
                                    <div class="adminFormRepeater_item adminFormRepeater_item--block" data-repeater-item>
                                        <div class="adminFormRepeater_item_drag">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="9" cy="5" r="1"/>
                                                <circle cx="9" cy="12" r="1"/>
                                                <circle cx="9" cy="19" r="1"/>
                                                <circle cx="15" cy="5" r="1"/>
                                                <circle cx="15" cy="12" r="1"/>
                                                <circle cx="15" cy="19" r="1"/>
                                            </svg>
                                        </div>
                                        <div class="adminFormRepeater_item_content">
                                            <input type="hidden" name="ordering" value="{{ is_array($exp) ? ($exp['ordering'] ?? $index) : ($exp->ordering ?? $index) }}" class="adminFormRepeater_item_ordering" />
                                            @include('admin.components.formField', [
                                                'label' => 'Chức vụ',
                                                'name' => 'title',
                                                'type' => 'text',
                                                'required' => true,
                                                'value' => is_array($exp) ? ($exp['title'] ?? '') : ($exp->title ?? '')
                                            ])
                                            @include('admin.components.formField', [
                                                'label' => 'Đơn vị',
                                                'name' => 'company',
                                                'type' => 'text',
                                                'required' => true,
                                                'value' => is_array($exp) ? ($exp['company'] ?? '') : ($exp->company ?? '')
                                            ])
                                            @php
                                                $contentExp = '';
                                                if(!empty($exp['content'])){
                                                    $contentExp = $exp['content'];
                                                }else if(!empty($exp['contents'])){
                                                    foreach($exp['contents'] as $c){
                                                        $contentExp .= $c['content']."\r\n";
                                                    }
                                                }
                                            @endphp
                                            @include('admin.components.formField', [
                                                'label' => 'Kỹ năng (mỗi dòng 1 kỹ năng)',
                                                'name' => 'content',
                                                'type' => 'textarea',
                                                'required' => true,
                                                'value' => $contentExp,
                                                'rows' => 5
                                            ])
                                        </div>
                                        <button type="button" class="adminFormRepeater_item_delete" data-repeater-delete>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <line x1="18" y1="6" x2="6" y2="18"/>
                                                <line x1="6" y1="6" x2="18" y2="18"/>
                                            </svg>
                                            <span>Xóa</span>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                            <!-- Hidden button for repeater plugin to find -->
                            <button type="button" data-repeater-create style="display:none;"></button>
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
                                    'value' => old('total_learner') ?? ($referee->total_learner ?? 0),
                                    'required' => false
                                ])
                                @include('admin.components.formField', [
                                    'label' => 'Giờ dạy',
                                    'name' => 'total_teaching_hour',
                                    'type' => 'number',
                                    'value' => old('total_teaching_hour') ?? ($referee->total_teaching_hour ?? 0),
                                    'required' => false
                                ])
                                @include('admin.components.formField', [
                                    'label' => 'Giải thưởng',
                                    'name' => 'total_prize',
                                    'type' => 'number',
                                    'value' => old('total_prize') ?? ($referee->total_prize ?? 0),
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
                                'activityImages' => $referee->activityImages ?? collect(),
                                'ownerType' => 'referee_info',
                                'ownerId' => $referee->id
                            ])
                        </div>
                    </div>

                    <!-- Bằng cấp -->
                    <div class="adminFormSection adminFormSection--repeater repeater" data-repeater-container>
                        <div class="adminFormSection_header">
                            <div class="adminFormSection_header_icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                                    <path d="M6 12v5c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2v-5"/>
                                </svg>
                            </div>
                            <div class="adminFormSection_header_info">
                                <h2 class="adminFormSection_title">Bằng cấp</h2>
                            </div>
                            <button type="button" class="adminFormSection_header_action" data-repeater-create>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="12" y1="8" x2="12" y2="16"/>
                                    <line x1="8" y1="12" x2="16" y2="12"/>
                                </svg>
                                <span>Thêm</span>
                            </button>
                        </div>
                        <div class="adminFormSection_body">
                            <div data-repeater-list="repeater_referee_degree">
                                @php
                                    $dataDegree = old('repeater_referee_degree', $referee->degrees ?? collect());
                                    if ($dataDegree instanceof \Illuminate\Support\Collection) {
                                        $dataDegree = $dataDegree->isNotEmpty() ? $dataDegree : [null];
                                    } elseif (is_array($dataDegree)) {
                                        $dataDegree = !empty($dataDegree) ? $dataDegree : [null];
                                    } else {
                                        $dataDegree = [null];
                                    }
                                @endphp
                                @foreach($dataDegree as $index => $degree)
                                    <div class="adminFormRepeater_item adminFormRepeater_item--block" data-repeater-item>
                                        <div class="adminFormRepeater_item_drag">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="9" cy="5" r="1"/>
                                                <circle cx="9" cy="12" r="1"/>
                                                <circle cx="9" cy="19" r="1"/>
                                                <circle cx="15" cy="5" r="1"/>
                                                <circle cx="15" cy="12" r="1"/>
                                                <circle cx="15" cy="19" r="1"/>
                                            </svg>
                                        </div>
                                        <div class="adminFormRepeater_item_content">
                                            <input type="hidden" name="ordering" value="{{ is_array($degree) ? ($degree['ordering'] ?? $index) : ($degree->ordering ?? $index) }}" class="adminFormRepeater_item_ordering" />
                                            @include('admin.components.formField', [
                                                'label' => 'Tiêu đề',
                                                'name' => 'title',
                                                'type' => 'text',
                                                'required' => true,
                                                'value' => is_array($degree) ? ($degree['title'] ?? '') : ($degree->title ?? '')
                                            ])
                                            @include('admin.components.formField', [
                                                'label' => 'Trường học',
                                                'name' => 'school',
                                                'type' => 'text',
                                                'required' => true,
                                                'value' => is_array($degree) ? ($degree['school'] ?? '') : ($degree->school ?? '')
                                            ])
                                            @php
                                                $contentDegree = '';
                                                if(!empty($degree['content'])){
                                                    $contentDegree = $degree['content'];
                                                }else if(!empty($degree['contents'])){
                                                    foreach($degree['contents'] as $c){
                                                        $contentDegree .= $c['content']."\r\n";
                                                    }
                                                }
                                            @endphp
                                            @include('admin.components.formField', [
                                                'label' => 'Kỹ năng (mỗi dòng 1 kỹ năng)',
                                                'name' => 'content',
                                                'type' => 'textarea',
                                                'required' => true,
                                                'value' => $contentDegree,
                                                'rows' => 5
                                            ])
                                        </div>
                                        <button type="button" class="adminFormRepeater_item_delete" data-repeater-delete>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <line x1="18" y1="6" x2="6" y2="18"/>
                                                <line x1="6" y1="6" x2="18" y2="18"/>
                                            </svg>
                                            <span>Xóa</span>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                            <!-- Hidden button for repeater plugin to find -->
                            <button type="button" data-repeater-create style="display:none;"></button>
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

