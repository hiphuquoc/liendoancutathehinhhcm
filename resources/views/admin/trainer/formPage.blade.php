@extends('layouts.admin')
@section('content')
    @php
        $titlePage      = 'Thêm Bài Viết mới';
        $submit         = 'admin.trainer.createAndUpdate';
        if(!empty($type)&&$type=='edit'){
            $titlePage  = 'Chỉnh sửa Bài Viết';
        }
    @endphp
    <!-- Start: backgroun để chặn thao tác khi đang dịch content ngầm -->
    @include('admin.category.lock')
    <!-- End: backgroun để chặn thao tác khi đang dịch content ngầm -->
    <form id="formAction" class="needs-validation invalid" action="{{ route($submit) }}" method="POST" novalidate enctype="multipart/form-data">
    @csrf
    <input type="hidden" id="seo_id" name="seo_id" value="{{ $itemSeo->id ?? 0 }}" />
    <input type="hidden" id="seo_id_vi" name="seo_id_vi" value="{{ !empty($item->seo->id)&&$type!='copy' ? $item->seo->id : 0 }}" />
    <input type="hidden" id="trainer_info_id" name="trainer_info_id" value="{{ !empty($item->id)&&$type!='copy' ? $item->id : 0 }}" />
    <input type="hidden" id="language" name="language" value="{{ $language ?? 'vi' }}" />
    <input type="hidden" id="type" name="type" value="{{ $type }}" />
        <div class="pageAdminWithRightSidebar withRightSidebar">
            <div class="pageAdminWithRightSidebar_header" style="z-index:1000;position:relative;">
                <div style="width:100%;margin-bottom:10px;">{{ $titlePage }}</div>
                @include('admin.template.languageBox', [
                    'item' => $item,
                    'language' => $language,
                    'routeName' => 'admin.trainer.view',
                ])
            </div>
            
            <!-- Error -->
            @if ($errors->any())
                <ul class="errorList">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
            <!-- MESSAGE -->
            @include('admin.template.messageAction')
            
            <div class="pageAdminWithRightSidebar_main">
                <!-- START:: Main content -->
                <div class="pageAdminWithRightSidebar_main_content">
                    <div class="pageAdminWithRightSidebar_main_content_item">
                        <div class="card">
                            <div class="card-header border-bottom">
                                <h4 class="card-title">Thông tin trang</h4>
                            </div>
                            <div class="card-body">

                                @include('admin.trainer.formPage', [
                                    'item'              => !empty($itemSourceToCopy) ? $itemSourceToCopy : $item,
                                    'itemSeo'           => !empty($itemSeoSourceToCopy) ? $itemSeoSourceToCopy : $itemSeo,
                                    'flagCopySource'    => !empty($itemSeoSourceToCopy) ? true : false,
                                ])

                            </div>
                        </div>
                    </div>
                    <div class="pageAdminWithRightSidebar_main_content_item">
                        <div class="card">
                            <div class="card-header border-bottom">
                                <h4 class="card-title">Thông tin SEO</h4>
                            </div>
                            <div class="card-body">

                                @include('admin.form.formSeo', [
                                    'item'              => !empty($itemSourceToCopy) ? $itemSourceToCopy : $item,
                                    'itemSeo'           => !empty($itemSeoSourceToCopy) ? $itemSeoSourceToCopy : $itemSeo,
                                    'flagCopySource'    => !empty($itemSeoSourceToCopy) ? true : false,
                                    'idSeoSource'       => $itemSeoSourceToCopy->id ?? 0
                                ])
                                
                            </div>
                        </div>
                    </div>
                    <!-- thành tích -->
                    <div class="pageAdminWithRightSidebar_main_content_item repeater">
                        <div class="card">
                            <div class="card-header border-bottom">
                                <h4 class="card-title">
                                    Thành tích
                                    <i class="fa-regular fa-circle-plus" data-repeater-create></i>
                                </h4>
                            </div>
                            <div class="card-body" data-repeater-list="repeater_trainer_achievement">
                                @php
                                    $dataAchievements = old('repeater_trainer_achievement') ?? $item->achievements ?? [];
                                    if (empty($dataAchievements)||!$dataAchievements->isNotEmpty()) {
                                        $dataAchievements[] = null; // Thêm phần tử trống nếu mảng rỗng -> để ít nhát in ra được 1 lần
                                    }
                                @endphp
                            
                                @foreach($dataAchievements as $achi)
                                    <!-- item -->
                                    <div class="flexBox" data-repeater-item>
                                        <div class="flexBox_item">
                                            <input 
                                                type="text" 
                                                class="form-control" 
                                                name="content" 
                                                placeholder="Nhập thành tích..." 
                                                value="{{ is_array($achi) ? ($achi['content'] ?? '') : ($achi->content ?? '') }}" 
                                                required>
                                        </div>
                                        <div class="flexBox_item" style="display: flex; flex: 0 0 30px; justify-content: space-between;">
                                            <div class="icon-wrapper iconAction">
                                                <a href="#" class="actionDelete" data-repeater-delete>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-square">
                                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                                        <line x1="9" y1="9" x2="15" y2="15"></line>
                                                        <line x1="15" y1="9" x2="9" y2="15"></line>
                                                    </svg>
                                                    <div>Xóa</div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                        </div>
                    </div>
                    
                   <!-- kỹ năng -->
                    <div class="pageAdminWithRightSidebar_main_content_item repeater">
                        <div class="card">
                            <div class="card-header border-bottom">
                                <h4 class="card-title">
                                    Kỹ năng
                                    <i class="fa-regular fa-circle-plus" data-repeater-create></i>
                                </h4>
                            </div>
                            <div class="card-body" data-repeater-list="repeater_trainer_skill">
                                @php
                                    $dataSkills = old('repeater_trainer_skill') ?? $item->skills ?? [];
                                    if (empty($dataSkills)||!$dataSkills->isNotEmpty()) {
                                        $dataSkills[] = null; // Thêm phần tử trống nếu mảng rỗng -> để ít nhát in ra được 1 lần
                                    }
                                @endphp
                                @foreach($dataSkills as $skill)
                                    <!-- item -->
                                    <div class="flexBox" data-repeater-item>
                                        <div class="flexBox_item">
                                            <input type="text" class="form-control" name="skill" placeholder="Nhập kỹ năng..." value="{{ is_array($skill) ? ($skill['skill'] ?? '') : ($skill->skill ?? '') }}" required>
                                        </div>
                                        <div class="flexBox_item" style="width:100px;">
                                            <input type="number" class="form-control" name="percent" placeholder="%" value="{{ is_array($skill) ? ($skill['percent'] ?? '') : ($skill->percent ?? '') }}" required>
                                        </div>
                                        <div class="flexBox_item" style="display: flex; flex: 0 0 30px; justify-content: space-between;">
                                            <div class="icon-wrapper iconAction">
                                                <a href="#" class="actionDelete" data-repeater-delete>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-square">
                                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                                        <line x1="9" y1="9" x2="15" y2="15"></line>
                                                        <line x1="15" y1="9" x2="9" y2="15"></line>
                                                    </svg>
                                                    <div>Xóa</div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- kinh nghiệm -->
                    <div class="pageAdminWithRightSidebar_main_content_item repeater">
                        <div class="card" data-repeater-list="repeater_trainer_experience">
                            <div class="card-header border-bottom">
                                <h4 class="card-title">
                                    Kinh nghiệm
                                    <i class="fa-regular fa-circle-plus" data-repeater-create></i>
                                </h4>
                            </div>
                            @php
                                $dataExperience = old('repeater_trainer_achievement') ?? $item->experiences ?? [];
                                if (empty($dataExperience)||!$dataExperience->isNotEmpty()) {
                                    $dataExperience[] = null; // Thêm phần tử trống nếu mảng rỗng -> để ít nhát in ra được 1 lần
                                }
                            @endphp
                            @foreach($dataExperience as $exp)
                                <div class="card-body" data-repeater-item>
                                    <!-- item -->
                                    <div class="flexBox">
                                        <div class="flexBox_item">
                                            <label class="form-label inputRequired">Chức vụ</label>
                                            <input type="text" class="form-control" name="title" value="{{ is_array($exp) ? ($exp['title'] ?? '') : ($exp->title ?? '') }}" required>
                                            <div class="invalid-feedback">{{ config('admin.massage_validate.not_empty') }}</div>
                                        </div>
                                    </div>
                                    <!-- item -->
                                    <div class="flexBox">
                                        <div class="flexBox_item">
                                            <label class="form-label inputRequired">Đơn vị</label>
                                            <input type="text" class="form-control" name="company" value="{{ is_array($exp) ? ($exp['company'] ?? '') : ($exp->company ?? '') }}" required>
                                            <div class="invalid-feedback">{{ config('admin.massage_validate.not_empty') }}</div>
                                        </div>
                                    </div>
                                    <!-- item -->
                                    @php
                                        if(is_array($exp)) {
                                            $contentExp     = $exp['content'];
                                        }else {
                                            $contentExp     = '';
                                            if(!empty($exp->contents)){
                                                foreach($exp->contents as $c){
                                                    $contentExp .= $c->content."\r\n";
                                                }
                                            }
                                        }
                                    @endphp
                                    <div class="flexBox">
                                        <div class="flexBox_item">
                                            <label class="form-label inputRequired">Kỹ năng (mỗi dòng 1 kỹ năng)</label>
                                            <textarea class="form-control" name="content" rows="5" required>{{ $contentExp }}</textarea>
                                            <div class="invalid-feedback">{{ config('admin.massage_validate.not_empty') }}</div>
                                        </div>
                                    </div>
                                    <div class="flexBox">
                                        <div class="flexBox_item">
                                            <button type="button" class="btn btn-danger waves-effect waves-float waves-light" style="float:right;" data-repeater-delete>Xóa</button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>

                    <!-- bằng cấp -->
                    <div class="pageAdminWithRightSidebar_main_content_item repeater">
                        <div class="card" data-repeater-list="repeater_trainer_degree">
                            <div class="card-header border-bottom">
                                <h4 class="card-title">
                                    Bằng cấp
                                    <i class="fa-regular fa-circle-plus" data-repeater-create></i>
                                </h4>
                            </div>
                            @php
                                $dataDegree = old('repeater_trainer_degree') ?? $item->degrees ?? [];
                                if (empty($dataDegree)||!$dataDegree->isNotEmpty()) {
                                    $dataDegree[] = null; // Thêm phần tử trống nếu mảng rỗng -> để ít nhát in ra được 1 lần
                                }
                            @endphp
                            @foreach($dataDegree as $degree)
                                <div class="card-body" data-repeater-item>
                                    <!-- item -->
                                    <div class="flexBox">
                                        <div class="flexBox_item">
                                            <label class="form-label inputRequired">Tiêu đề</label>
                                            <input type="text" class="form-control" name="title" value="{{ is_array($degree) ? ($degree['title'] ?? '') : ($degree->title ?? '') }}" required>
                                            <div class="invalid-feedback">{{ config('admin.massage_validate.not_empty') }}</div>
                                        </div>
                                    </div>
                                    <!-- item -->
                                    <div class="flexBox">
                                        <div class="flexBox_item">
                                            <label class="form-label inputRequired">Trường học</label>
                                            <input type="text" class="form-control" name="school" value="{{ is_array($degree) ? ($degree['school'] ?? '') : ($degree->school ?? '') }}" required>
                                            <div class="invalid-feedback">{{ config('admin.massage_validate.not_empty') }}</div>
                                        </div>
                                    </div>
                                    <!-- item -->
                                    @php
                                        if(is_array($degree)) {
                                            $contentDegree  = $degree['content'];
                                        }else {
                                            $contentDegree  = '';
                                            if(!empty($degree->contents)){
                                                foreach($degree->contents as $c){
                                                    $contentDegree .= $c->content."\r\n";
                                                }
                                            }
                                        }
                                    @endphp
                                    <div class="flexBox">
                                        <div class="flexBox_item">
                                            <label class="form-label inputRequired">Kỹ năng (mỗi dòng 1 kỹ năng)</label>
                                            <textarea class="form-control" name="content" rows="5" required>{{ $contentDegree }}</textarea>
                                            <div class="invalid-feedback">{{ config('admin.massage_validate.not_empty') }}</div>
                                        </div>
                                    </div>
                                    <div class="flexBox">
                                        <div class="flexBox_item">
                                            <button type="button" class="btn btn-danger waves-effect waves-float waves-light" style="float:right;" data-repeater-delete>Xóa</button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
                <!-- END:: Main content -->

                <!-- START:: Sidebar content -->
                <div class="pageAdminWithRightSidebar_main_rightSidebar">
                    <!-- action -->
                    @include('admin.trainer.buttonAction', [
                        'routeBack' => 'admin.trainer.list',
                    ])
                    <!-- action support -->
                    <div class="customScrollBar-y">
                        <!-- Form Upload -->
                        <div class="pageAdminWithRightSidebar_main_rightSidebar_item">
                            @include('admin.form.formAvatarTrainer')
                        </div>
                        {{-- <!-- thêm kinh nghiệm -->
                        <div class="pageAdminWithRightSidebar_main_rightSidebar_item">
                            <button class="btn btn-icon btn-primary waves-effect waves-float waves-light" type="button" aria-label="Thêm" style="width:100%;" data-repeater-create data-target-repeater="repeater-sp-version">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus me-25"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                <span>Thêm phiên bản SP</span>
                            </button>
                        </div> --}}
                    </div>
                </div>
                <!-- END:: Sidebar content -->
            </div>
        </div>
    </form>
@endsection
@push('scriptCustom')
    <script type="text/javascript">
        $('.repeater').repeater({
            initEmpty: false, // Nếu muốn danh sách trống khi khởi tạo, đặt thành true
            show: function () {
                $(this).slideDown(); // Hiệu ứng khi thêm mới
            },
            hide: function (deleteElement) {
                // if (confirm('Bạn có chắc chắn muốn xóa?')) {
                    $(this).slideUp(deleteElement); // Hiệu ứng khi xóa
                // }
            }
        });
    </script>
@endpush