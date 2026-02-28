{{--
  Hình ảnh hoạt động – Thêm / Xóa / Kéo thả sắp xếp ngay lập tức (AJAX).
  Truyền: $activityImages (collection), $ownerType ('trainer_info'|'referee_info'), $ownerId (int).
--}}
@php
    $activityImages = $activityImages ?? collect();
    $ownerType = $ownerType ?? 'trainer_info';
    $ownerId = (int) ($ownerId ?? 0);
    $uploadUrl = route('admin.profileActivityImage.upload');
    $deleteUrl = route('admin.profileActivityImage.delete');
    $reorderUrl = route('admin.profileActivityImage.reorder');
    $csrf = csrf_token();
    $containerId = 'profileActivityImages-' . $ownerType . '-' . $ownerId;
$canUpload = $ownerId > 0;
@endphp

<div id="{{ $containerId }}" class="profileActivityImages {{ !$canUpload ? 'profileActivityImages--noOwner' : '' }}" data-owner-type="{{ $ownerType }}" data-owner-id="{{ $ownerId }}" data-upload-url="{{ $uploadUrl }}" data-delete-url="{{ $deleteUrl }}" data-reorder-url="{{ $reorderUrl }}" data-csrf="{{ $csrf }}">
    <div class="profileActivityImages_list js-profileActivityImages-sortable">
        @foreach($activityImages as $img)
            @php
                $thumbUrl = !empty($img->image) ? \App\Helpers\Image::getUrlImageSmallByUrlImage($img->image) : $img->image_url;
            @endphp
            <div class="profileActivityImages_card" data-id="{{ $img->id }}">
                <div class="profileActivityImages_card_handle" title="Kéo để sắp xếp">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="5" r="1"/><circle cx="9" cy="12" r="1"/><circle cx="9" cy="19" r="1"/><circle cx="15" cy="5" r="1"/><circle cx="15" cy="12" r="1"/><circle cx="15" cy="19" r="1"/></svg>
                </div>
                <div class="profileActivityImages_card_thumb">
                    <img src="{{ $thumbUrl }}" alt="" loading="lazy" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22%3E%3Crect fill=%22%23eee%22 width=%22100%22 height=%22100%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 fill=%22%23999%22 text-anchor=%22middle%22 dy=%22.3em%22%3E?%3C/text%3E%3C/svg%3E'">
                </div>
                <button type="button" class="profileActivityImages_card_delete js-profileActivityImages-delete" title="Xóa ảnh">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    <span>Xóa</span>
                </button>
            </div>
        @endforeach
        <div class="profileActivityImages_card profileActivityImages_card--add js-profileActivityImages-add">
            <input type="file" class="profileActivityImages_add_input js-profileActivityImages-file" accept="image/jpeg,image/png,image/webp,image/jpg" multiple @if(!$canUpload) disabled @endif>
            <div class="profileActivityImages_add_zone js-profileActivityImages-dropzone" @if(!$canUpload) data-disabled="1" @endif>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
                @if($canUpload)
                    <span class="profileActivityImages_add_text">Thêm ảnh</span>
                    <span class="profileActivityImages_add_hint">Kéo thả hoặc nhấn để chọn</span>
                @else
                    <span class="profileActivityImages_add_text profileActivityImages_add_text--muted">Lưu hồ sơ trước khi thêm ảnh</span>
                @endif
            </div>
        </div>
    </div>
    <div class="profileActivityImages_feedback js-profileActivityImages-feedback" aria-live="polite"></div>

    {{-- Modal xác nhận xóa ảnh (giao diện giống modal xóa cache HTML) --}}
    <div class="adminClearCacheModal js-profileActivityImages-deleteModal" role="dialog" aria-labelledby="adminDeleteActivityImageModal_title" aria-modal="true">
        <div class="adminClearCacheModal_backdrop js-profileActivityImages-deleteModal-backdrop"></div>
        <div class="adminClearCacheModal_content">
            <div class="adminClearCacheModal_header">
                <h2 id="adminDeleteActivityImageModal_title" class="adminClearCacheModal_title">Xác nhận xóa ảnh</h2>
                <button type="button" class="adminClearCacheModal_close js-profileActivityImages-deleteModal-close" aria-label="Đóng">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 6L6 18M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="adminClearCacheModal_body">
                <div class="adminClearCacheModal_icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                    </svg>
                </div>
                <div class="adminClearCacheModal_message">
                    <p>Ảnh này sẽ bị xóa vĩnh viễn khỏi hình ảnh hoạt động. Bạn có chắc chắn muốn xóa?</p>
                </div>
            </div>
            <div class="adminClearCacheModal_footer">
                <button type="button" class="adminClearCacheModal_button adminClearCacheModal_button--secondary js-profileActivityImages-deleteModal-cancel">
                    Hủy
                </button>
                <button type="button" class="adminClearCacheModal_button adminClearCacheModal_button--primary js-profileActivityImages-deleteModal-confirm">
                    <span class="adminClearCacheModal_button_text">Xác nhận</span>
                    <span class="adminClearCacheModal_button_loader" style="display: none;">
                        <svg class="adminClearCacheModal_button_spinner" viewBox="0 0 50 50">
                            <circle class="adminClearCacheModal_button_spinner_path" cx="25" cy="25" r="20" fill="none" stroke-width="4"></circle>
                        </svg>
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

@pushonce('scriptCustom')
<script>
(function() {
    function initProfileActivityImages() {
        document.querySelectorAll('.profileActivityImages').forEach(function(container) {
            if (container.dataset.initialized === '1') return;
            container.dataset.initialized = '1';
            var uploadUrl = container.dataset.uploadUrl;
            var deleteUrl = container.dataset.deleteUrl;
            var reorderUrl = container.dataset.reorderUrl;
            var ownerType = container.dataset.ownerType;
            var ownerId = container.dataset.ownerId;
            var csrf = container.dataset.csrf || document.querySelector('meta[name="csrf-token"]')?.content || '';
            var list = container.querySelector('.js-profileActivityImages-sortable');
            var feedback = container.querySelector('.js-profileActivityImages-feedback');
            var addCard = container.querySelector('.profileActivityImages_card--add');
            var fileInput = container.querySelector('.js-profileActivityImages-file');
            var dropzone = container.querySelector('.js-profileActivityImages-dropzone');

            function showFeedback(msg, type) {
                if (!feedback) return;
                feedback.textContent = msg || '';
                feedback.className = 'profileActivityImages_feedback js-profileActivityImages-feedback profileActivityImages_feedback--' + (type || 'info');
                if (msg) setTimeout(function() { feedback.textContent = ''; feedback.className = 'profileActivityImages_feedback js-profileActivityImages-feedback'; }, 4000);
            }

            function addCardToDOM(item) {
                var card = document.createElement('div');
                card.className = 'profileActivityImages_card';
                card.dataset.id = item.id;
                card.innerHTML = '<div class="profileActivityImages_card_handle" title="Kéo để sắp xếp"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="5" r="1"/><circle cx="9" cy="12" r="1"/><circle cx="9" cy="19" r="1"/><circle cx="15" cy="5" r="1"/><circle cx="15" cy="12" r="1"/><circle cx="15" cy="19" r="1"/></svg></div><div class="profileActivityImages_card_thumb"><img src="' + (item.thumb_url || item.image_url) + '" alt="" loading="lazy"></div><button type="button" class="profileActivityImages_card_delete js-profileActivityImages-delete" title="Xóa ảnh"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg><span>Xóa</span></button>';
                list.insertBefore(card, addCard);
                bindDelete(card);
            }

            var deleteModal = container.querySelector('.js-profileActivityImages-deleteModal');
            var confirmBtn = deleteModal ? deleteModal.querySelector('.js-profileActivityImages-deleteModal-confirm') : null;
            var confirmBtnText = confirmBtn ? confirmBtn.querySelector('.adminClearCacheModal_button_text') : null;
            var confirmBtnLoader = confirmBtn ? confirmBtn.querySelector('.adminClearCacheModal_button_loader') : null;

            function openDeleteModal(card) {
                if (!deleteModal) return;
                var id = card.dataset.id;
                deleteModal.dataset.pendingId = id;
                deleteModal.classList.add('adminClearCacheModal--open');
                document.body.style.overflow = 'hidden';
            }
            function closeDeleteModal() {
                if (!deleteModal) return;
                deleteModal.classList.remove('adminClearCacheModal--open');
                document.body.style.overflow = '';
                deleteModal.dataset.pendingId = '';
                if (confirmBtn) confirmBtn.disabled = false;
                if (confirmBtnText) confirmBtnText.style.display = '';
                if (confirmBtnLoader) confirmBtnLoader.style.display = 'none';
            }
            function confirmDeleteInModal() {
                var id = deleteModal.dataset.pendingId;
                if (!id) return;
                if (confirmBtn) confirmBtn.disabled = true;
                if (confirmBtnText) confirmBtnText.style.display = 'none';
                if (confirmBtnLoader) confirmBtnLoader.style.display = 'inline-flex';
                var fd = new FormData();
                fd.append('_token', csrf);
                fd.append('id', id);
                fetch(deleteUrl, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                    .then(function(r) { return r.json(); })
                    .then(function(res) {
                        closeDeleteModal();
                        if (res.success) {
                            var card = container.querySelector('.profileActivityImages_card[data-id="' + id + '"]');
                            if (card) card.remove();
                            showFeedback('Đã xóa ảnh.', 'success');
                        } else {
                            showFeedback(res.message || 'Không thể xóa.', 'error');
                        }
                    })
                    .catch(function() {
                        closeDeleteModal();
                        showFeedback('Lỗi kết nối.', 'error');
                    });
            }

            if (deleteModal) {
                var backdrop = deleteModal.querySelector('.js-profileActivityImages-deleteModal-backdrop');
                var closeBtn = deleteModal.querySelector('.js-profileActivityImages-deleteModal-close');
                var cancelBtn = deleteModal.querySelector('.js-profileActivityImages-deleteModal-cancel');
                if (backdrop) backdrop.addEventListener('click', closeDeleteModal);
                if (closeBtn) closeBtn.addEventListener('click', closeDeleteModal);
                if (cancelBtn) cancelBtn.addEventListener('click', closeDeleteModal);
                if (confirmBtn) confirmBtn.addEventListener('click', confirmDeleteInModal);
            }

            function bindDelete(card) {
                var btn = card && card.querySelector('.js-profileActivityImages-delete');
                if (!btn) return;
                btn.addEventListener('click', function() {
                    openDeleteModal(card);
                });
            }
            container.querySelectorAll('.profileActivityImages_card:not(.profileActivityImages_card--add) .js-profileActivityImages-delete').forEach(function(btn) { bindDelete(btn.closest('.profileActivityImages_card')); });

            function uploadFiles(files) {
                if (!files || !files.length) return;
                if (parseInt(ownerId, 10) === 0) {
                    showFeedback('Lưu hồ sơ trước khi thêm ảnh.', 'info');
                    return;
                }
                var fd = new FormData();
                fd.append('_token', csrf);
                fd.append('owner_type', ownerType);
                fd.append('owner_id', ownerId);
                for (var i = 0; i < files.length; i++) fd.append('image[]', files[i]);
                showFeedback('Đang tải lên...', 'info');
                fetch(uploadUrl, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                    .then(function(r) { return r.json(); })
                    .then(function(res) {
                        if (res.success && res.items && res.items.length) {
                            res.items.forEach(addCardToDOM);
                            showFeedback(res.message || 'Đã thêm ảnh.', 'success');
                        } else showFeedback(res.message || 'Không thêm được ảnh.', 'error');
                    })
                    .catch(function() { showFeedback('Lỗi kết nối.', 'error'); });
            }

            if (fileInput) {
                fileInput.addEventListener('change', function() {
                    uploadFiles(this.files);
                    this.value = '';
                });
            }
            if (dropzone) {
                dropzone.addEventListener('click', function() { fileInput && fileInput.click(); });
                dropzone.addEventListener('dragover', function(e) { e.preventDefault(); dropzone.classList.add('profileActivityImages_add_zone--dragover'); });
                dropzone.addEventListener('dragleave', function() { dropzone.classList.remove('profileActivityImages_add_zone--dragover'); });
                dropzone.addEventListener('drop', function(e) {
                    e.preventDefault();
                    dropzone.classList.remove('profileActivityImages_add_zone--dragover');
                    var files = e.dataTransfer && e.dataTransfer.files;
                    if (files && files.length) uploadFiles(files);
                });
            }

            if (typeof $ !== 'undefined' && $.fn && $.fn.sortable) {
                $(list).sortable({
                    items: '.profileActivityImages_card:not(.profileActivityImages_card--add)',
                    handle: '.profileActivityImages_card_handle',
                    placeholder: 'profileActivityImages_card profileActivityImages_card--placeholder',
                    tolerance: 'pointer',
                    update: function() {
                        var ids = [];
                        list.querySelectorAll('.profileActivityImages_card:not(.profileActivityImages_card--add)').forEach(function(c) { if (c.dataset.id) ids.push(c.dataset.id); });
                        if (!ids.length) return;
                        var fd = new FormData();
                        fd.append('_token', csrf);
                        fd.append('owner_type', ownerType);
                        fd.append('owner_id', ownerId);
                        ids.forEach(function(id, i) { fd.append('order[' + i + ']', id); });
                        fetch(reorderUrl, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                            .then(function(r) { return r.json(); })
                            .then(function(res) { if (res.success) showFeedback('Đã cập nhật thứ tự.', 'success'); });
                    }
                });
            }
        });
        document.addEventListener('keydown', function(e) {
            if (e.key !== 'Escape') return;
            var open = document.querySelector('.js-profileActivityImages-deleteModal.adminClearCacheModal--open');
            if (open) {
                open.classList.remove('adminClearCacheModal--open');
                document.body.style.overflow = '';
                open.dataset.pendingId = '';
            }
        });
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initProfileActivityImages);
    else initProfileActivityImages();
})();
</script>
@endpushonce
