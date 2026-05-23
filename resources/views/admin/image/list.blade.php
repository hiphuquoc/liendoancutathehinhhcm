@extends('layouts.admin')

@section('content')
<div class="adminImagePage">
    <div class="adminImagePage_content">
        <div class="companyManagementPage_section companyManagementPage_section--tracked">
            <div class="companyManagementPage_section_header companyManagementPage_section_header--image">
                <div class="companyManagementPage_section_header_left">
                    <div class="companyManagementPage_section_header_iconWrapper companyManagementPage_section_header_iconWrapper--image">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                            <circle cx="8.5" cy="8.5" r="1.5"/>
                            <polyline points="21 15 16 10 5 21"/>
                        </svg>
                    </div>
                    <div class="companyManagementPage_section_header_info">
                        <h2 class="companyManagementPage_section_title">
                            Quản lý Ảnh
                        </h2>
                        <p class="companyManagementPage_section_desc">Quản lý và tải lên ảnh trong hệ thống</p>
                    </div>
                </div>
                <div class="companyManagementPage_section_header_right">
                    <div class="adminImagePage_stats">
                        <div class="adminImagePage_stats_item">
                            <span class="adminImagePage_stats_label">Tổng số:</span>
                            <span class="adminImagePage_stats_value">{{ $list->count() ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="companyManagementPage_section_body">
                <!-- Search & Upload Bar -->
                <div class="adminImagePage_toolbar">
                    <!-- Search Form -->
                    <form id="formSearch" method="get" action="{{ route('admin.image.list') }}" class="adminImagePage_search">
                        <div class="adminImagePage_search_inputWrapper">
                            <svg class="adminImagePage_search_icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"/>
                                <path d="m21 21-4.35-4.35"/>
                            </svg>
                            <input 
                                type="text" 
                                class="adminImagePage_search_input" 
                                name="search_name" 
                                placeholder="Tìm kiếm theo tên ảnh..." 
                                value="{{ $params['search_name'] ?? '' }}"
                            />
                            <button type="submit" class="adminImagePage_search_button">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="8"/>
                                    <path d="m21 21-4.35-4.35"/>
                                </svg>
                                <span>Tìm kiếm</span>
                            </button>
            </div>
        </form>

                    <!-- Upload Form -->
                    <form id="formUpload" method="post" enctype="multipart/form-data" class="adminImagePage_upload">
                        @csrf
                        <div class="adminImagePage_upload_area" id="uploadArea">
                            <input 
                                type="file" 
                                name="image_upload[]" 
                                id="imageUploadInput" 
                                multiple 
                                accept="image/*"
                                class="adminImagePage_upload_input"
                            />
                            <div class="adminImagePage_upload_content">
                                <svg class="adminImagePage_upload_icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="17 8 12 3 7 8"/>
                                    <line x1="12" y1="3" x2="12" y2="15"/>
                                </svg>
                                <p class="adminImagePage_upload_text">
                                    <span class="adminImagePage_upload_text_primary">Kéo thả ảnh vào đây</span>
                                    <span class="adminImagePage_upload_text_secondary">hoặc click để chọn</span>
                                </p>
                                <p class="adminImagePage_upload_hint">Hỗ trợ nhiều ảnh cùng lúc</p>
    </div>
            </div>
                        <button type="submit" class="adminImagePage_upload_button" id="uploadButton" style="display:none;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="17 8 12 3 7 8"/>
                                <line x1="12" y1="3" x2="12" y2="15"/>
                            </svg>
                            <span>Tải lên</span>
                        </button>
        </form>
</div>

                <!-- Images Grid -->
                <div id="js_uploadImage_idWrite" class="adminImagePage_grid">
                    @if(!empty($list) && $list->isNotEmpty())
        @foreach($list as $infoImageCloud)
            @include('admin.image.oneRow', compact('infoImageCloud'))
        @endforeach
                    @else
                        <div class="adminImagePage_empty">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                <circle cx="8.5" cy="8.5" r="1.5"/>
                                <polyline points="21 15 16 10 5 21"/>
                            </svg>
                            <h3>Chưa có ảnh nào</h3>
                            <p>Hãy tải lên ảnh đầu tiên của bạn</p>
                        </div>
    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Change Image -->
<form id="formModal" method="post" action="{{ route('admin.image.changeImage') }}" enctype="multipart/form-data">
@csrf
    <div id="modalImage" class="adminImagePage_modal">
        <div class="adminImagePage_modal_backdrop" onclick="closeImageModal()"></div>
        <div class="adminImagePage_modal_content">
            <div class="adminImagePage_modal_header">
                <h3 class="adminImagePage_modal_title">Thay đổi ảnh</h3>
                <button type="button" class="adminImagePage_modal_close" onclick="closeImageModal()" aria-label="Đóng">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
            <div class="adminImagePage_modal_body">
                <div id="js_loadModal_message" class="adminImagePage_modal_error" style="display:none;">
                    Các trường bắt buộc không được để trống!
                </div>
                    <div id="js_loadModal_body">
                    <!-- Load Ajax -->
                </div>
            </div>
            <div class="adminImagePage_modal_footer">
                <button type="button" class="adminImagePage_modal_button adminImagePage_modal_button--secondary" onclick="closeImageModal()">
                    Đóng
                </button>
                <button id="js_loadModal_action" type="submit" class="adminImagePage_modal_button adminImagePage_modal_button--primary">
                    Xác nhận
                </button>
            </div>
        </div>
    </div>
</form>

<!-- Lightbox View Image Full -->
<div id="imageLightbox" class="adminImagePage_lightbox">
    <div class="adminImagePage_lightbox_backdrop" onclick="closeImageLightbox()"></div>
    <div class="adminImagePage_lightbox_content">
        <button class="adminImagePage_lightbox_close" onclick="closeImageLightbox()" aria-label="Đóng">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
        <div class="adminImagePage_lightbox_imageWrapper">
            <div id="lightboxLoading" class="adminImagePage_lightbox_loading">
                <div class="adminImagePage_loading_spinner"></div>
                <p>Đang tải ảnh gốc...</p>
            </div>
            <img id="lightboxImage" src="" alt="" style="display:none;" onload="hideLightboxLoading()" />
        </div>
        <div class="adminImagePage_lightbox_info">
            <h3 id="lightboxTitle" class="adminImagePage_lightbox_title"></h3>
        </div>
    </div>
</div>
    
@endsection

@pushonce('modal')
    <!-- Full Loading -->
    @include('admin.modal.fullLoading')
@endpushonce

@push('scriptCustom')
    <script type="text/javascript">
    // Upload Area Drag & Drop
    const uploadArea = document.getElementById('uploadArea');
    const uploadInput = document.getElementById('imageUploadInput');
    const uploadButton = document.getElementById('uploadButton');

    if (uploadArea && uploadInput) {
        // Click to select files
        uploadArea.addEventListener('click', () => {
            uploadInput.click();
        });

        // Drag & Drop
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('adminImagePage_upload_area--dragover');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('adminImagePage_upload_area--dragover');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('adminImagePage_upload_area--dragover');
            
            if (e.dataTransfer.files.length > 0) {
                uploadInput.files = e.dataTransfer.files;
                handleFileSelect();
            }
        });

        // File input change
        uploadInput.addEventListener('change', handleFileSelect);

        function handleFileSelect() {
            if (uploadInput.files.length > 0) {
                uploadButton.style.display = 'flex';
                uploadArea.classList.add('adminImagePage_upload_area--hasFiles');
            } else {
                uploadButton.style.display = 'none';
                uploadArea.classList.remove('adminImagePage_upload_area--hasFiles');
            }
        }
    }

    // Load Modal
        function loadModal(idImageCloud){
            $.ajax({
                url         : "{{ route('admin.image.loadModal') }}",
                type        : "get",
                dataType    : "html",
                data        : {
                    image_cloud_id : idImageCloud, 
                }
            }).done(function(data){
                $('#js_loadModal_body').html(data);
            document.getElementById('modalImage').classList.add('adminImagePage_modal--open');
            document.body.style.overflow = 'hidden';
            });
        }

    // Close Modal
    function closeImageModal(){
        document.getElementById('modalImage').classList.remove('adminImagePage_modal--open');
        document.body.style.overflow = '';
        $('#js_loadModal_body').html('');
    }

    // Change Image Submit
        $("#formModal").on('submit', function(e) {
            e.preventDefault();
        const idImageCloud = $('#image_cloud_id').val();
        const submitButton = $('#js_loadModal_action');
        const originalText = submitButton.html();
        
        submitButton.prop('disabled', true).html('<span class="spinner"></span> Đang xử lý...');
        
            $.ajax({
                url             : "{{ route('admin.image.changeImage') }}",
                type            : "POST",
                dataType        : 'json',
                data            : new FormData(this),
                contentType     : false,
                cache           : false,
                processData     : false,
                success         : function(data){
                    if(data.flag){
                        loadImageBox(idImageCloud);
                    closeImageModal();
                }
                submitButton.prop('disabled', false).html(originalText);
            },
            error           : function(){
                submitButton.prop('disabled', false).html(originalText);
                }
            });
        });

    // Load Image Box
        function loadImageBox(idImageCloud){
        const idBox = 'js_removeImage_'+idImageCloud;
        const elementBox = $('#'+idBox);
        const heightBox = elementBox.outerHeight();

            addLoading(idBox, heightBox);
            $.ajax({
                url         : "{{ route('admin.image.loadImage') }}",
                type        : "get",
                dataType    : "html",
                data        : { 
                    image_cloud_id  : idImageCloud
                }
            }).done(function(data){
                setTimeout(() => {
                    $('#'+idBox).replaceWith(data);
                }, 500);
            });
        }

    // Upload Images
        $("#formUpload").on('submit', function(e) {
            e.preventDefault();
        if (uploadInput.files.length === 0) {
            return;
        }

            openCloseFullLoading();
        const uploadButtonEl = document.getElementById('uploadButton');
        uploadButtonEl.disabled = true;
        
            $.ajax({
                url             : "{{ route('admin.image.uploadImages') }}",
                type            : "POST",
                dataType        : 'json',
                data            : new FormData(this),
                contentType     : false,
                cache           : false,
                processData     : false,
                success         : function(data){
                const elementWrite = $('#js_uploadImage_idWrite');
                const emptyState = elementWrite.find('.adminImagePage_empty');
                if (emptyState.length) {
                    emptyState.remove();
                }
                
                let contentOld = elementWrite.html();
                elementWrite.html(data.content + contentOld);
                    document.getElementById("formUpload").reset();
                uploadButton.style.display = 'none';
                uploadArea.classList.remove('adminImagePage_upload_area--hasFiles');
                openCloseFullLoading();
                uploadButtonEl.disabled = false;
            },
            error           : function(){
                openCloseFullLoading();
                uploadButtonEl.disabled = false;
                }
            });
        });

    // Remove Image
        function removeImage(id){
        if(!confirm('Bạn có chắc chắn muốn xóa ảnh này?')) {
            return;
        }
        
        const idBox = 'js_removeImage_'+id;
        const elementBox = $('#'+idBox);
        const heightBox = elementBox.outerHeight();
        
            addLoading(idBox, heightBox);
            $.ajax({
                url         : "{{ route('admin.image.removeImage') }}",
                type        : "post",
                dataType    : "html",
                data        : { 
                    '_token'        : '{{ csrf_token() }}', 
                    image_cloud_id  : id
                }
            }).done(function(data){
                setTimeout(() => {
                if(data==true) {
                    elementBox.fadeOut(300, function(){
                        $(this).remove();
                        // Check if grid is empty
                        const grid = $('#js_uploadImage_idWrite');
                        if (grid.children().length === 0) {
                            grid.html(`
                                <div class="adminImagePage_empty">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                        <circle cx="8.5" cy="8.5" r="1.5"/>
                                        <polyline points="21 15 16 10 5 21"/>
                                    </svg>
                                    <h3>Chưa có ảnh nào</h3>
                                    <p>Hãy tải lên ảnh đầu tiên của bạn</p>
                                </div>
                            `);
                        }
                    });
                }
            }, 500);
            });
        }

    // Add Loading
        function addLoading(idBox, heightBox = 300){
        const htmlLoading = `
            <div style="display:flex;align-items:center;justify-content:center;height:${heightBox}px;">
                <div class="adminImagePage_loading">
                    <div class="adminImagePage_loading_spinner"></div>
                </div>
            </div>
        `;
        $('#'+idBox).html(htmlLoading);
    }

    // Copy to Clipboard
    function copyToClipboard(idContent, buttonElement = null){
        const textarea = document.getElementById(idContent);
        if (!textarea) return;
        
        textarea.disabled = false;
        textarea.select();
        document.execCommand('copy');
        textarea.disabled = true;
        
        // Show feedback
        if (buttonElement) {
            const originalHTML = buttonElement.innerHTML;
            buttonElement.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>';
            buttonElement.style.color = '#07A35D';
            setTimeout(() => {
                buttonElement.innerHTML = originalHTML;
                buttonElement.style.color = '';
            }, 2000);
        }
    }

    // View Image Full (Lightbox)
    function viewImageFull(imageUrl, imageTitle) {
        const lightbox = document.getElementById('imageLightbox');
        const lightboxImage = document.getElementById('lightboxImage');
        const lightboxTitle = document.getElementById('lightboxTitle');
        const lightboxLoading = document.getElementById('lightboxLoading');
        
        // Reset và hiển thị loading
        lightboxImage.style.display = 'none';
        lightboxLoading.style.display = 'flex';
        lightboxTitle.textContent = imageTitle;
        
        // Mở lightbox
        lightbox.classList.add('adminImagePage_lightbox--open');
        document.body.style.overflow = 'hidden';
        
        // Tải ảnh gốc
        lightboxImage.src = imageUrl;
        lightboxImage.alt = imageTitle;
    }

    // Hide loading when image loaded
    function hideLightboxLoading() {
        const lightboxImage = document.getElementById('lightboxImage');
        const lightboxLoading = document.getElementById('lightboxLoading');
        if (lightboxImage && lightboxLoading) {
            lightboxLoading.style.display = 'none';
            lightboxImage.style.display = 'block';
        }
    }

    // Close Lightbox
    function closeImageLightbox() {
        const lightbox = document.getElementById('imageLightbox');
        const lightboxImage = document.getElementById('lightboxImage');
        const lightboxLoading = document.getElementById('lightboxLoading');
        
        lightbox.classList.remove('adminImagePage_lightbox--open');
        document.body.style.overflow = '';
        
        // Reset
        if (lightboxImage) {
            lightboxImage.src = '';
            lightboxImage.style.display = 'none';
        }
        if (lightboxLoading) {
            lightboxLoading.style.display = 'flex';
        }
    }

    // Close modal on ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeImageModal();
            closeImageLightbox();
        }
    });
    </script>
@endpush
