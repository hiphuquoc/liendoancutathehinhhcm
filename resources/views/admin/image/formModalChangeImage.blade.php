@if(!empty($infoImageCloud))
    @php
        $urlImageSmall = \App\Helpers\Image::getUrlImageSmallByUrlImage($infoImageCloud->file_cloud);
    @endphp
    <div class="adminImagePage_modalChangeImage">
                <input type="hidden" id="image_cloud_id" name="image_cloud_id" value="{{ $infoImageCloud->id }}" />
        
        <div class="adminImagePage_modalChangeImage_upload">
            <label class="adminImagePage_modalChangeImage_label">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="17 8 12 3 7 8"/>
                    <line x1="12" y1="3" x2="12" y2="15"/>
                </svg>
                Chọn ảnh mới
            </label>
            <input 
                class="adminImagePage_modalChangeImage_input" 
                type="file" 
                id="image_new" 
                name="image_new" 
                accept="image/*"
                onChange="readURL(this, 'js_readURL_idShow_modal')"
            />
        </div>

        <div class="adminImagePage_modalChangeImage_preview">
            <div class="adminImagePage_modalChangeImage_preview_item">
                <div class="adminImagePage_modalChangeImage_preview_label">Ảnh hiện tại</div>
                <div class="adminImagePage_modalChangeImage_preview_image">
                    <img src="{{ $urlImageSmall.'?'.time() }}" alt="Ảnh hiện tại" />
                </div>
            </div>
            
            <div class="adminImagePage_modalChangeImage_preview_arrow">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="9 18 15 12 9 6"/>
                </svg>
                    </div>
            
            <div class="adminImagePage_modalChangeImage_preview_item">
                <div class="adminImagePage_modalChangeImage_preview_label">Ảnh mới</div>
                <div class="adminImagePage_modalChangeImage_preview_image">
                    <img id="js_readURL_idShow_modal" src="{{ config('image.default') }}" alt="Ảnh mới" />
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        function readURL(input, idShow) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#'+idShow).attr('src', e.target.result);
                    $('#'+idShow).closest('.adminImagePage_modalChangeImage_preview_item').addClass('adminImagePage_modalChangeImage_preview_item--hasImage');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endif
