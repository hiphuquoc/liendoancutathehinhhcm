{{-- 
    Component: Trainer Repeater Achievement
    Usage: @include('admin.components.trainerRepeaterAchievement', [
        'data' => $dataAchievements, // Array or Collection
        'oldData' => old('repeater_trainer_achievement')
    ])
--}}
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
        <div data-repeater-list="repeater_trainer_achievement">
            @php
                $dataAchievements = $oldData ?? $data ?? collect();
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

