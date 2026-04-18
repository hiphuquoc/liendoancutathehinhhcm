{{-- 
    Component: Trainer Repeater Degree
    Usage: @include('admin.components.athleteRepeaterDegree', [
        'data' => $dataDegree, // Array or Collection
        'oldData' => old('repeater_athlete_degree')
    ])
--}}
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
        <div data-repeater-list="repeater_athlete_degree">
            @php
                $dataDegree = $oldData ?? $data ?? collect();
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
                                    $contentDegree .= (is_array($c) ? $c['content'] : $c->content)."\r\n";
                                }
                            } else if(is_object($degree) && !empty($degree->contents)){
                                foreach($degree->contents as $c){
                                    $contentDegree .= (is_array($c) ? $c['content'] : $c->content)."\r\n";
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

