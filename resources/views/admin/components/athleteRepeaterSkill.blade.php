{{-- 
    Component: Trainer Repeater Skill
    Usage: @include('admin.components.athleteRepeaterSkill', [
        'data' => $dataSkills, // Array or Collection
        'oldData' => old('repeater_athlete_skill')
    ])
--}}
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
        <div data-repeater-list="repeater_athlete_skill">
            @php
                $dataSkills = $oldData ?? $data ?? collect();
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

