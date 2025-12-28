{{-- 
    Component: Trainer Repeater Experience
    Usage: @include('admin.components.trainerRepeaterExperience', [
        'data' => $dataExperience, // Array or Collection
        'oldData' => old('repeater_trainer_experience')
    ])
--}}
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
        <div data-repeater-list="repeater_trainer_experience">
            @php
                $dataExperience = $oldData ?? $data ?? collect();
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
                                    $contentExp .= (is_array($c) ? $c['content'] : $c->content)."\r\n";
                                }
                            } else if(is_object($exp) && !empty($exp->contents)){
                                foreach($exp->contents as $c){
                                    $contentExp .= (is_array($c) ? $c['content'] : $c->content)."\r\n";
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

