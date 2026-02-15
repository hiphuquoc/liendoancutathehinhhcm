@extends('layouts.admin')

@section('content')
    @php
        $titlePage = 'Thêm Slider mới';
        $titleDesc = 'Tạo slider mới để hiển thị trên trang chủ';
        $submit = 'admin.slider.createAndUpdate';
        if(!empty($type) && $type == 'edit'){
            $titlePage = 'Chỉnh sửa Slider';
            $titleDesc = 'Cập nhật thông tin và hình ảnh slider';
        }
        
        $positionOptions = [
            'left' => 'Trái',
            'center' => 'Giữa',
            'right' => 'Phải'
        ];
        
        $iconOptions = [
            '' => 'Không có icon',
            'arrow-right' => 'Mũi tên phải →',
            'arrow-left' => 'Mũi tên trái ←',
            'shopping-cart' => 'Giỏ hàng 🛒',
            'heart' => 'Yêu thích ❤',
            'star' => 'Sao ⭐',
            'check' => 'Checkmark ✓',
            'plus' => 'Plus +',
            'play' => 'Play ▶',
            'search' => 'Tìm kiếm 🔍',
            'eye' => 'Xem 👁'
        ];

        // Xác định slider có text box hay không
        $hasTextContent = !empty($item->title) || !empty($item->description) || !empty($item->button_text);
    @endphp
    
    <form id="formAction" action="{{ route($submit) }}" method="POST" enctype="multipart/form-data" class="adminFormPage_form">
        @csrf
        <input type="hidden" id="id" name="id" value="{{ $item->id ?? 0 }}" />
        <input type="hidden" id="type" name="type" value="{{ $type }}" />

        <div class="adminFormPage">
            <div class="adminFormPage_content">
                {{-- Header --}}
                @include('admin.components.pageHeader', [
                    'title' => $titlePage,
                    'desc' => $titleDesc,
                    'icon' => '<rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>',
                    'backUrl' => route('admin.slider.list'),
                    'backText' => 'Quay lại danh sách'
                ])
                
                {{-- Validation Errors Banner --}}
                @include('admin.components.formValidationErrors')

                {{-- Message --}}
                @include('admin.components.formMessage')
                
                {{-- Body --}}
                <div class="adminFormPage_body">
                    {{-- Main Content Area --}}
                    <div class="adminFormPage_main">

                        {{-- Toggle Text Box --}}
                        <div class="adminFormSection">
                            <div class="adminFormSection_header">
                                <div class="adminFormSection_header_icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12 3h7a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-7m0-18H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h7m0-18v18"/>
                                    </svg>
                                </div>
                                <div class="adminFormSection_header_info">
                                    <h2 class="adminFormSection_title">Loại Slider</h2>
                                    <p class="adminFormSection_description">Chọn kiểu hiển thị cho slider</p>
                                </div>
                            </div>
                            <div class="adminFormSection_body">
                                <div class="sliderTypeSelector">
                                    <label class="sliderTypeSelector_option {{ !$hasTextContent && !empty($item->id) ? 'active' : (!empty($item->id) ? '' : '') }}" data-type="image-only">
                                        <input type="radio" name="slider_type" value="image_only" 
                                               {{ !$hasTextContent && !empty($item->id) ? 'checked' : '' }}
                                               onchange="toggleTextBoxSection(false)">
                                        <div class="sliderTypeSelector_option_icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                                <circle cx="8.5" cy="8.5" r="1.5"/>
                                                <polyline points="21 15 16 10 5 21"/>
                                            </svg>
                                        </div>
                                        <div class="sliderTypeSelector_option_info">
                                            <div class="sliderTypeSelector_option_title">Chỉ ảnh</div>
                                            <div class="sliderTypeSelector_option_desc">Slider hiển thị toàn bộ ảnh, không có text overlay</div>
                                        </div>
                                    </label>
                                    <label class="sliderTypeSelector_option {{ $hasTextContent || empty($item->id) ? 'active' : '' }}" data-type="with-text">
                                        <input type="radio" name="slider_type" value="with_text" 
                                               {{ $hasTextContent || empty($item->id) ? 'checked' : '' }}
                                               onchange="toggleTextBoxSection(true)">
                                        <div class="sliderTypeSelector_option_icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                                <line x1="7" y1="8" x2="17" y2="8"/>
                                                <line x1="7" y1="12" x2="14" y2="12"/>
                                                <rect x="7" y="15" width="6" height="3" rx="1"/>
                                            </svg>
                                        </div>
                                        <div class="sliderTypeSelector_option_info">
                                            <div class="sliderTypeSelector_option_title">Ảnh + Text Box</div>
                                            <div class="sliderTypeSelector_option_desc">Hiển thị tiêu đề, mô tả và nút CTA trên slider</div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- Text Content Section (collapsible) --}}
                        <div id="textBoxSection" class="sliderTextBoxSection {{ !$hasTextContent && !empty($item->id) ? 'sliderTextBoxSection--hidden' : '' }}">
                            {{-- Thông tin cơ bản --}}
                            <div class="adminFormSection">
                                <div class="adminFormSection_header">
                                    <div class="adminFormSection_header_icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                            <polyline points="14 2 14 8 20 8"/>
                                            <line x1="16" y1="13" x2="8" y2="13"/>
                                            <line x1="16" y1="17" x2="8" y2="17"/>
                                        </svg>
                                    </div>
                                    <div class="adminFormSection_header_info">
                                        <h2 class="adminFormSection_title">Nội dung Text Box</h2>
                                        <p class="adminFormSection_description">Tiêu đề và mô tả hiển thị trên slider</p>
                                    </div>
                                </div>
                                <div class="adminFormSection_body">
                                    @include('admin.components.formField', [
                                        'label' => 'Tiêu đề',
                                        'name' => 'title',
                                        'type' => 'text',
                                        'value' => old('title') ?? $item->title ?? null,
                                        'placeholder' => 'VD: Khám phá bộ sưu tập mới',
                                        'charCount' => true,
                                        'maxLength' => 255,
                                        'tooltip' => 'Tiêu đề hiển thị lớn trên slider'
                                    ])
                                    
                                    @include('admin.components.formField', [
                                        'label' => 'Mô tả',
                                        'name' => 'description',
                                        'type' => 'textarea',
                                        'value' => old('description') ?? $item->description ?? null,
                                        'placeholder' => 'Mô tả ngắn gọn về slider, nội dung quảng cáo...',
                                        'rows' => 3,
                                        'helpText' => 'Có thể xuống dòng để tạo nhiều đoạn. Để trống nếu không cần.'
                                    ])
                                    
                                    @include('admin.components.formSelect', [
                                        'label' => 'Vị trí nội dung',
                                        'name' => 'position',
                                        'value' => old('position') ?? $item->position ?? 'left',
                                        'options' => $positionOptions,
                                        'tooltip' => 'Vị trí hiển thị của box thông tin trên slider'
                                    ])
                                </div>
                            </div>
                            
                            {{-- Cấu hình Button --}}
                            <div class="adminFormSection">
                                <div class="adminFormSection_header">
                                    <div class="adminFormSection_header_icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <rect x="3" y="8" width="18" height="8" rx="2"/>
                                            <line x1="7" y1="12" x2="17" y2="12"/>
                                        </svg>
                                    </div>
                                    <div class="adminFormSection_header_info">
                                        <h2 class="adminFormSection_title">Nút bấm (CTA)</h2>
                                        <p class="adminFormSection_description">Tùy chọn — để trống nếu không cần nút</p>
                                    </div>
                                </div>
                                <div class="adminFormSection_body">
                                    <div class="adminFormGrid adminFormGrid--2cols">
                                        @include('admin.components.formField', [
                                            'label' => 'Text nút',
                                            'name' => 'button_text',
                                            'type' => 'text',
                                            'value' => old('button_text') ?? $item->button_text ?? null,
                                            'placeholder' => 'VD: Xem ngay, Mua ngay, Khám phá...',
                                            'helpText' => 'Để trống nếu không muốn hiển thị nút'
                                        ])
                                        
                                        @include('admin.components.formSelect', [
                                            'label' => 'Icon nút',
                                            'name' => 'button_icon',
                                            'value' => old('button_icon') ?? $item->button_icon ?? '',
                                            'options' => $iconOptions,
                                            'tooltip' => 'Chọn icon hiển thị trên button'
                                        ])
                                    </div>
                                    
                                    @include('admin.components.formField', [
                                        'label' => 'Link nút',
                                        'name' => 'button_link',
                                        'type' => 'text',
                                        'value' => old('button_link') ?? $item->button_link ?? null,
                                        'placeholder' => 'VD: /san-pham, /lien-he, https://...',
                                        'helpText' => 'URL khi click vào nút (có thể là đường dẫn tương đối hoặc tuyệt đối)'
                                    ])
                                </div>
                            </div>
                        </div>
                        
                        {{-- Cấu hình hiển thị --}}
                        <div class="adminFormSection">
                            <div class="adminFormSection_header">
                                <div class="adminFormSection_header_icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="3"/>
                                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                                    </svg>
                                </div>
                                <div class="adminFormSection_header_info">
                                    <h2 class="adminFormSection_title">Cấu hình hiển thị</h2>
                                    <p class="adminFormSection_description">Thiết lập thứ tự và trạng thái hiển thị</p>
                                </div>
                            </div>
                            <div class="adminFormSection_body">
                                <div class="adminFormGrid adminFormGrid--2cols">
                                    @include('admin.components.formField', [
                                        'label' => 'Thứ tự hiển thị',
                                        'name' => 'ordering',
                                        'type' => 'number',
                                        'value' => old('ordering') ?? $item->ordering ?? 0,
                                        'placeholder' => '0',
                                        'min' => 0,
                                        'helpText' => 'Số nhỏ hơn sẽ hiển thị trước'
                                    ])
                                    
                                    @include('admin.components.formField', [
                                        'label' => 'Trạng thái',
                                        'name' => 'flag_show',
                                        'type' => 'checkbox',
                                        'value' => (!empty($item->flag_show) || empty($item->id)) ? 1 : 0,
                                        'checkboxLabel' => 'Hiển thị slider này',
                                        'helpText' => 'Bật để slider hiển thị trên trang chủ'
                                    ])
                                </div>
                                
                                @include('admin.components.formField', [
                                    'label' => 'Ghi chú nội bộ',
                                    'name' => 'notes',
                                    'type' => 'textarea',
                                    'value' => old('notes') ?? $item->notes ?? null,
                                    'placeholder' => 'Ghi chú cho admin (không hiển thị ra ngoài)',
                                    'rows' => 2
                                ])
                            </div>
                        </div>
                    </div>

                    {{-- Sidebar --}}
                    <div class="adminFormPage_sidebar">
                        <div class="adminFormSidebar">
                            <div class="adminFormSidebar_sticky">
                                {{-- Actions --}}
                                @include('admin.components.formActions', [
                                    'backRoute' => 'admin.slider.list',
                                ])

                                {{-- Desktop Image Upload --}}
                                @include('admin.components.formImageUpload', [
                                    'name' => 'image',
                                    'label' => 'Ảnh Desktop (1920×800)',
                                    'required' => empty($item->id),
                                    'currentImage' => !empty($item->image) ? \App\Helpers\Image::getUrlImageCloud($item->image) : null,
                                    'aspectRatio' => '16/9',
                                    'tooltip' => 'Kích thước khuyến nghị: 1920x800px hoặc tỷ lệ tương tự'
                                ])
                                
                                {{-- Mobile Image Upload --}}
                                @include('admin.components.formImageUpload', [
                                    'name' => 'image_mobile',
                                    'label' => 'Ảnh Mobile (tùy chọn)',
                                    'required' => false,
                                    'currentImage' => !empty($item->image_mobile) ? \App\Helpers\Image::getUrlImageCloud($item->image_mobile) : null,
                                    'aspectRatio' => '4/3',
                                    'tooltip' => 'Kích thước khuyến nghị: 768x600px. Nếu không chọn, sẽ dùng ảnh Desktop'
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
        function toggleTextBoxSection(show) {
            const section = document.getElementById('textBoxSection');
            const options = document.querySelectorAll('.sliderTypeSelector_option');
            
            if (show) {
                section.classList.remove('sliderTextBoxSection--hidden');
                options.forEach(opt => opt.classList.remove('active'));
                document.querySelector('[data-type="with-text"]').classList.add('active');
            } else {
                section.classList.add('sliderTextBoxSection--hidden');
                // Clear text fields when hiding
                const textFields = section.querySelectorAll('input[type="text"], textarea');
                textFields.forEach(field => { field.value = ''; });
                options.forEach(opt => opt.classList.remove('active'));
                document.querySelector('[data-type="image-only"]').classList.add('active');
            }
        }
    </script>
    <style>
        /* Slider Type Selector */
        .sliderTypeSelector {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .sliderTypeSelector_option {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1.25rem;
            border-radius: 12px;
            border: 2px solid var(--adminBorder, rgba(0,0,0,0.08));
            background: var(--adminCardBg, #fff);
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }
        .sliderTypeSelector_option input[type="radio"] {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }
        .sliderTypeSelector_option:hover {
            border-color: rgba(0, 173, 239, 0.3);
            background: rgba(0, 173, 239, 0.02);
        }
        .sliderTypeSelector_option.active {
            border-color: #00adef;
            background: rgba(0, 173, 239, 0.04);
            box-shadow: 0 0 0 3px rgba(0, 173, 239, 0.1);
        }
        .sliderTypeSelector_option_icon {
            flex-shrink: 0;
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: var(--adminBgSubtle, #f1f5f9);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.25s ease;
        }
        .sliderTypeSelector_option.active .sliderTypeSelector_option_icon {
            background: rgba(0, 173, 239, 0.1);
            color: #00adef;
        }
        .sliderTypeSelector_option_icon svg {
            width: 22px;
            height: 22px;
        }
        .sliderTypeSelector_option.active .sliderTypeSelector_option_icon svg {
            stroke: #00adef;
        }
        .sliderTypeSelector_option_info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .sliderTypeSelector_option_title {
            font-size: 0.92rem;
            font-weight: 700;
            color: var(--adminTextPrimary, #1e293b);
        }
        .sliderTypeSelector_option_desc {
            font-size: 0.78rem;
            color: var(--adminTextSecondary, #64748b);
            line-height: 1.45;
        }

        /* Text Box Section - Collapsible */
        .sliderTextBoxSection {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            max-height: 2000px;
            overflow: hidden;
            opacity: 1;
        }
        .sliderTextBoxSection--hidden {
            max-height: 0;
            opacity: 0;
            gap: 0;
            pointer-events: none;
        }

        @media (max-width: 768px) {
            .sliderTypeSelector {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush
