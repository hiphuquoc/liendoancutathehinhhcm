@if(!empty($infoImageCloud))
    @php
        $urlImageSmall = \App\Helpers\Image::getUrlImageSmallByUrlImage($infoImageCloud->file_cloud);
        $urlImageLarge = \App\Helpers\Image::getUrlImageLargeByUrlImage($infoImageCloud->file_cloud);
        $urlImageOriginal = \App\Helpers\Image::getUrlImageCloud($infoImageCloud->file_cloud);
        $idContent = 'js_copyToClipboard_content_'.rand(0, 1000000);
    @endphp
    <div id="js_removeImage_{{ $infoImageCloud->id }}" class="adminImagePage_card">
        <div class="adminImagePage_card_imageWrapper">
            <img src="{{ $urlImageSmall }}?{{ time() }}" alt="{{ $infoImageCloud->file_name ?? 'Ảnh' }}" loading="lazy" />
            <div class="adminImagePage_card_overlay">
                <div class="adminImagePage_card_actions">
                    <button 
                        class="adminImagePage_card_action" 
                        title="Xem ảnh full"
                        onclick="viewImageFull('{{ $urlImageOriginal }}', '{{ $infoImageCloud->file_name ?? 'Ảnh' }}')"
                    >
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                    <button 
                        class="adminImagePage_card_action" 
                        title="Thay đổi ảnh"
                        onclick="loadModal({{ $infoImageCloud->id }})"
                    >
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                    </button>
                    <button 
                        class="adminImagePage_card_action adminImagePage_card_action--danger" 
                        title="Xóa ảnh"
                        onclick="removeImage({{ $infoImageCloud->id }})"
                    >
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <div class="adminImagePage_card_body">
            <div class="adminImagePage_card_url">
                <textarea 
                    id="{{ $idContent }}" 
                    class="adminImagePage_card_url_input"
                    readonly
                >{{ $urlImageLarge }}</textarea>
                <button 
                    class="adminImagePage_card_url_copy"
                    onclick="copyToClipboard('{{ $idContent }}', this)"
                    title="Copy URL"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                    </svg>
                </button>
            </div>
            <div class="adminImagePage_card_info">
                <div class="adminImagePage_card_info_item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                        <circle cx="8.5" cy="8.5" r="1.5"/>
                        <polyline points="21 15 16 10 5 21"/>
                    </svg>
                    <span>{{ $infoImageCloud->file_name ?? '-' }}</span>
                </div>
                <div class="adminImagePage_card_info_item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                        <path d="M9 9h6v6H9z"/>
                    </svg>
                    <span>{{ $infoImageCloud->width ?? '-' }} × {{ $infoImageCloud->height ?? '-' }}</span>
                </div>
                <div class="adminImagePage_card_info_item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                        <line x1="12" y1="22.08" x2="12" y2="12"/>
                    </svg>
                    <span>{{ round($infoImageCloud->file_size/1024) }} KB</span>
                </div>
            </div>
        </div>
    </div>
@endif
