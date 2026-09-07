{{--
    Thanh thao tác hàng loạt trên trang QR.
    @include('admin.components.qrcodeBulkBar', [
        'downloadUrl' => route('admin.trainerQrcode.downloadAll'),
        'processUrl' => route('admin.trainerQrcode.downloadAll.process'),
        'deleteUrl' => route('admin.trainerQrcode.deleteSelected'),
        'entityLabelShort' => 'HLV',
        'loadingId' => 'qrcodeLoadingOverlay',
    ])
--}}
@php
    $downloadUrl = $downloadUrl ?? '';
    $processUrl = $processUrl ?? '';
    $deleteUrl = $deleteUrl ?? '';
    $entityLabelShort = $entityLabelShort ?? 'hồ sơ';
    $loadingId = $loadingId ?? 'qrcodeLoadingOverlay';
    $canDelete = !empty($deleteUrl) && auth()->user() && auth()->user()->hasRole('admin');
@endphp

<div class="adminTrainerEmail_actions" id="qrcodeBulkBar">
    <div class="adminTrainerEmail_actions_left">
        <label class="adminCheckbox">
            <input type="checkbox" id="qrcodeSelectAll">
            <span class="adminCheckbox_label">Chọn tất cả</span>
        </label>
        <span class="adminTrainerEmail_selectedCount" id="qrcodeSelectedCount">Đã chọn: 0</span>
    </div>
    <div class="adminTrainerEmail_actions_right">
        <button type="button" id="qrcodeBulkDownloadBtn" class="adminButton adminButton--primary adminButton--sm" disabled>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
            </svg>
            <span>Tải PNG đã chọn</span>
        </button>
        @if($canDelete)
            <button type="button" id="qrcodeBulkDeleteBtn" class="adminButton adminButton--danger adminButton--sm" disabled>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
                </svg>
                <span>Xóa hồ sơ đã chọn</span>
            </button>
        @endif
    </div>
</div>

@push('scriptCustom')
<script>
(function() {
    'use strict';

    const downloadUrl = @json($downloadUrl);
    const processUrl = @json($processUrl);
    const deleteUrl = @json($canDelete ? $deleteUrl : '');
    const entityLabelShort = @json($entityLabelShort);
    const loadingId = @json($loadingId);

    const selectAll = document.getElementById('qrcodeSelectAll');
    const downloadBtn = document.getElementById('qrcodeBulkDownloadBtn');
    const deleteBtn = document.getElementById('qrcodeBulkDeleteBtn');
    const countEl = document.getElementById('qrcodeSelectedCount');
    const modal = document.getElementById('adminProfileDeleteModal');
    const nameEl = document.getElementById('adminProfileDeleteName');
    const errorEl = document.getElementById('adminProfileDeleteError');
    const confirmBtn = document.getElementById('adminProfileDeleteConfirm');
    const titleEl = document.getElementById('adminProfileDeleteTitle');

    function csrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.content || '';
    }

    function itemCheckboxes() {
        return Array.from(document.querySelectorAll('.qrcode-item-checkbox'));
    }

    function selectedCheckboxes() {
        return itemCheckboxes().filter(function(cb) { return cb.checked; });
    }

    function selectedIds() {
        return selectedCheckboxes().map(function(cb) { return cb.value; });
    }

    function updateSelectionUi() {
        const all = itemCheckboxes();
        const selected = selectedCheckboxes();
        const count = selected.length;

        if (countEl) {
            countEl.textContent = 'Đã chọn: ' + count;
        }
        if (downloadBtn) downloadBtn.disabled = count === 0;
        if (deleteBtn) deleteBtn.disabled = count === 0;
        if (selectAll) {
            selectAll.checked = all.length > 0 && count === all.length;
            selectAll.indeterminate = count > 0 && count < all.length;
        }
    }

    function appendIds(params, ids) {
        ids.forEach(function(id) {
            params.append('ids[]', id);
        });
        return params;
    }

    async function parseJsonResponse(response) {
        const payload = await response.json().catch(function() { return null; });
        if (!response.ok || !payload || payload.status === false) {
            throw new Error((payload && payload.message) || 'Không thể tải mã QR đã chọn.');
        }
        return payload;
    }

    function triggerNativeDownload(url) {
        let iframe = document.getElementById('qrcodeZipDownloadFrame');
        if (!iframe) {
            iframe = document.createElement('iframe');
            iframe.id = 'qrcodeZipDownloadFrame';
            iframe.setAttribute('aria-hidden', 'true');
            iframe.style.display = 'none';
            document.body.appendChild(iframe);
        }
        iframe.src = url;
    }

    function showModalError(message) {
        if (!errorEl) return;
        errorEl.hidden = !message;
        errorEl.textContent = message || '';
    }

    function closeModal() {
        if (confirmBtn && confirmBtn.disabled) return;
        if (modal) modal.hidden = true;
        document.body.style.overflow = '';
        showModalError('');
    }

    function openDeleteModal() {
        const selected = selectedCheckboxes();
        if (!selected.length || !modal) return;

        const names = selected.map(function(cb) {
            return cb.getAttribute('data-name') || 'hồ sơ';
        });
        const preview = names.slice(0, 8).join(', ') + (names.length > 8 ? '…' : '');

        if (titleEl) {
            titleEl.textContent = 'Xóa ' + selected.length + ' hồ sơ ' + entityLabelShort + '?';
        }
        if (nameEl) {
            nameEl.textContent = preview;
        }
        showModalError('');
        modal.hidden = false;
        document.body.style.overflow = 'hidden';
        confirmBtn?.focus();
    }

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            itemCheckboxes().forEach(function(cb) {
                cb.checked = selectAll.checked;
            });
            updateSelectionUi();
        });
    }

    document.addEventListener('change', function(e) {
        if (e.target && e.target.classList.contains('qrcode-item-checkbox')) {
            updateSelectionUi();
        }
    });

    if (downloadBtn) {
        downloadBtn.addEventListener('click', async function() {
            const ids = selectedIds();
            if (!ids.length || !downloadUrl || !processUrl) return;

            if (typeof showAdminLoading === 'function') {
                showAdminLoading(loadingId, 'Đang tạo file ZIP mã QR...', true);
            }

            let succeeded = false;
            try {
                const startResponse = await fetch(downloadUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken(),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: appendIds(new URLSearchParams({ _token: csrfToken() }), ids),
                });
                const startPayload = await parseJsonResponse(startResponse);
                const token = startPayload.token;
                const total = startPayload.total || ids.length;

                if (typeof updateAdminLoadingProgress === 'function') {
                    updateAdminLoadingProgress(loadingId, 0, '0 / ' + total + ' mã QR');
                }

                let done = false;
                let processed = 0;
                let downloadFileUrl = null;
                let guard = 0;
                const maxLoops = total + 5;

                while (!done && guard < maxLoops) {
                    guard += 1;
                    const processResponse = await fetch(processUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken(),
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        body: new URLSearchParams({
                            _token: csrfToken(),
                            token: token,
                        }),
                    });
                    const progress = await parseJsonResponse(processResponse);
                    processed = progress.processed || processed;
                    done = !!progress.done;
                    downloadFileUrl = progress.download_url || downloadFileUrl;

                    const percent = total > 0 ? Math.min(100, Math.round((processed / total) * 100)) : 100;
                    if (typeof updateAdminLoadingProgress === 'function') {
                        updateAdminLoadingProgress(loadingId, percent, processed + ' / ' + total + ' mã QR');
                    }
                }

                if (!done || !downloadFileUrl) {
                    throw new Error('Không thể hoàn tất file ZIP. Vui lòng thử lại.');
                }

                if (typeof showAdminLoading === 'function') {
                    showAdminLoading(loadingId, 'Đang tải file xuống máy...', true);
                }
                if (typeof updateAdminLoadingProgress === 'function') {
                    updateAdminLoadingProgress(loadingId, 100, 'Bắt đầu tải xuống...');
                }

                triggerNativeDownload(downloadFileUrl);
                succeeded = true;
            } catch (error) {
                alert(error.message || 'Có lỗi xảy ra khi tải mã QR.');
            } finally {
                if (typeof hideAdminLoading === 'function') {
                    if (succeeded) {
                        setTimeout(function() {
                            hideAdminLoading(loadingId);
                        }, 1200);
                    } else {
                        hideAdminLoading(loadingId);
                    }
                }
            }
        });
    }

    if (deleteBtn) {
        deleteBtn.addEventListener('click', openDeleteModal);
    }

    document.querySelectorAll('[data-profile-delete-cancel]').forEach(function(el) {
        el.addEventListener('click', closeModal);
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal && !modal.hidden) closeModal();
    });

    if (confirmBtn) {
        confirmBtn.addEventListener('click', async function() {
            const ids = selectedIds();
            if (!ids.length || !deleteUrl) return;

            const originalHtml = confirmBtn.innerHTML;
            confirmBtn.disabled = true;
            const label = confirmBtn.querySelector('span');
            if (label) label.textContent = 'Đang xóa...';
            showModalError('');

            try {
                const body = appendIds(new URLSearchParams({ _token: csrfToken() }), ids);
                const response = await fetch(deleteUrl, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken(),
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: body,
                });
                const payload = await response.json().catch(function() { return null; });
                if (!response.ok || !payload || !payload.status) {
                    throw new Error((payload && payload.message) || 'Không thể xóa hồ sơ đã chọn.');
                }

                const deletedIds = (payload.deleted_ids || []).map(String);
                deletedIds.forEach(function(id) {
                    const cb = document.querySelector('.qrcode-item-checkbox[value="' + id + '"]');
                    const item = cb ? cb.closest('.adminQrCode_listItem') : null;
                    if (item) item.remove();
                });

                const remaining = document.querySelectorAll('.adminQrCode_listItem').length;
                const statsEl = document.querySelector('.adminPersonnelPage_stats_value');
                if (statsEl) statsEl.textContent = String(remaining);

                confirmBtn.disabled = false;
                confirmBtn.innerHTML = originalHtml;
                closeModal();
                updateSelectionUi();

                if (remaining === 0) {
                    const list = document.querySelector('.adminQrCode_list');
                    const bar = document.getElementById('qrcodeBulkBar');
                    if (bar) bar.remove();
                    if (list) {
                        const empty = document.createElement('div');
                        empty.className = 'adminPersonnelPage_empty';
                        empty.innerHTML = '<p>Không còn hồ sơ nào trong danh sách này</p>';
                        list.replaceWith(empty);
                    }
                }

                if (payload.failed && payload.failed.length) {
                    alert(payload.message);
                }
            } catch (error) {
                showModalError(error.message || 'Có lỗi xảy ra khi xóa hồ sơ.');
                confirmBtn.innerHTML = originalHtml;
                confirmBtn.disabled = false;
            }
        });
    }

    updateSelectionUi();
})();
</script>
@endpush
