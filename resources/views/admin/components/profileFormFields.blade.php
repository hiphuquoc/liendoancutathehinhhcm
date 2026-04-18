{{-- 
    Component: Profile Form Fields (Shared between Account Profile and Trainer Profile)
    Usage: @include('admin.components.profileFormFields', [
        'user' => $user,
        'item' => $item ?? null, // Trainer model (optional)
        'trainerCode' => $trainerCode ?? null, // Trainer code (optional)
        'hideAddress' => false, // Hide address field (for trainer profile)
        'formType' => 'account' // 'account' or 'trainer'
    ])
--}}
@php
    $user = $user ?? auth()->user();
    $isTrainerOrReferee = ($user->hasRole('trainer') || $user->hasRole('referee') || $user->hasRole('athlete')) && !$user->hasRole('admin');
    
    // For trainer profile, get values from trainer model
    $nameValue = old('name') ?? ($item->name ?? $user->name ?? '');
    $positionValue = old('position') ?? ($item->position ?? $user->position ?? '');
    $phoneValue = old('phone') ?? ($item->phone ?? $user->phone ?? '');
    $emailValue = old('email') ?? ($item->email ?? $user->email ?? '');
    $addressValue = old('address') ?? ($user->address ?? '');
    $descriptionValue = old('description') ?? ($item->description ?? '');
@endphp

@if(!empty($trainerCode))
    <!-- Mã HLV / VĐV - Readonly -->
    <div class="adminFormField">
        <div class="adminFormField_labelWrapper">
            <label class="adminFormField_label">
                <span>{{ ($formType ?? '') === 'athlete' ? 'Mã VĐV' : 'Mã HLV' }}</span>
            </label>
        </div>
        <div 
            class="adminPersonnelPage_card_code adminPersonnelPage_card_code--form adminPersonnelPage_card_code--readonly"
        >
            <span class="adminPersonnelPage_card_code_text">{{ $trainerCode }}</span>
        </div>
    </div>
@endif

<!-- Name -->
@if($isTrainerOrReferee && $formType === 'account')
    {{-- Trainer/Referee: readonly display with hidden input --}}
    <div class="adminFormField">
        <div class="adminFormField_labelWrapper">
            <label class="adminFormField_label">
                <span>Họ và tên</span>
            </label>
        </div>
        <input type="hidden" name="name" value="{{ $nameValue }}" />
        <div class="adminPersonnelPage_card_code adminPersonnelPage_card_code--form adminPersonnelPage_card_code--readonly-display">
            <span class="adminPersonnelPage_card_code_text">{{ $nameValue ?: 'Chưa có' }}</span>
        </div>
    </div>
@else
    @include('admin.components.formField', [
        'label' => 'Họ và tên',
        'name' => 'name',
        'type' => 'text',
        'required' => true,
        'value' => $nameValue,
        'charCount' => true,
        'maxLength' => 255
    ])
@endif

<!-- Position -->
@if($isTrainerOrReferee && $formType === 'account')
    {{-- Trainer/Referee: readonly display with hidden input --}}
    <div class="adminFormField">
        <div class="adminFormField_labelWrapper">
            <label class="adminFormField_label">
                <span>Chức vụ</span>
            </label>
        </div>
        <input type="hidden" name="position" value="{{ $positionValue }}" />
        <div class="adminPersonnelPage_card_code adminPersonnelPage_card_code--form adminPersonnelPage_card_code--readonly-display">
            <span class="adminPersonnelPage_card_code_text">{{ $positionValue ?: 'Chưa có' }}</span>
        </div>
    </div>
@else
    @include('admin.components.formField', [
        'label' => 'Chức vụ',
        'name' => 'position',
        'type' => 'text',
        'required' => false,
        'value' => $positionValue,
        'tooltip' => 'Ví dụ: Huấn luyện viên cá nhân (PT)',
        'charCount' => true,
        'maxLength' => 255
    ])
@endif

<!-- Phone -->
@include('admin.components.formField', [
    'label' => 'Số điện thoại',
    'name' => 'phone',
    'type' => 'text',
    'required' => false,
    'value' => $phoneValue,
    'tooltip' => ($formType === 'trainer' || $formType === 'athlete') ? 'Số điện thoại hiển thị trên website' : 'Số điện thoại của bạn'
])

<!-- Email -->
@include('admin.components.formField', [
    'label' => 'Email',
    'name' => 'email',
    'type' => 'email',
    'required' => true,
    'value' => $emailValue,
    'tooltip' => ($formType === 'trainer' || $formType === 'athlete') ? 'Email hiển thị trên website' : 'Email của bạn'
])

<!-- Description (HLV / VĐV profile) -->
@if($formType === 'trainer' || $formType === 'athlete')
    @include('admin.components.formField', [
        'label' => 'Giới thiệu ngắn',
        'name' => 'description',
        'type' => 'textarea',
        'required' => false,
        'value' => $descriptionValue,
        'tooltip' => $formType === 'athlete' ? 'Giới thiệu ngắn về vận động viên (đồng bộ mô tả SEO)' : 'Giới thiệu ngắn về huấn luyện viên (sẽ được đồng bộ với mô tả SEO)',
        'charCount' => true,
        'maxLength' => 2000,
        'rows' => 7
    ])
@endif

<!-- Address -->
@if(empty($hideAddress))
    @include('admin.components.formField', [
        'label' => 'Địa chỉ',
        'name' => 'address',
        'type' => 'text',
        'required' => false,
        'value' => $addressValue,
        'tooltip' => 'Địa chỉ của bạn'
    ])
@endif

