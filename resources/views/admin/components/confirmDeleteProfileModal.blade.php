{{--
    Modal xác nhận xóa hàng loạt hồ sơ trên trang QR.
--}}
@php
    $entityLabelShort = $entityLabelShort ?? 'hồ sơ';
@endphp

<div id="adminProfileDeleteModal" class="adminConfirmModal" hidden>
    <div class="adminConfirmModal_overlay" data-profile-delete-cancel></div>
    <div class="adminConfirmModal_dialog" role="dialog" aria-modal="true" aria-labelledby="adminProfileDeleteTitle">
        <div class="adminConfirmModal_icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
        </div>
        <h3 id="adminProfileDeleteTitle" class="adminConfirmModal_title">Xóa hồ sơ {{ $entityLabelShort }} đã chọn?</h3>
        <p class="adminConfirmModal_lead">
            Bạn sắp xóa vĩnh viễn các hồ sơ:
            <strong id="adminProfileDeleteName">—</strong>
        </p>
        <ul class="adminConfirmModal_list">
            <li>Trang hồ sơ, mã QR và toàn bộ nội dung SEO</li>
            <li>Ảnh đại diện, ảnh hoạt động, thành tích, kỹ năng, kinh nghiệm, bằng cấp</li>
            <li>Tài khoản đăng nhập — trừ khi người này còn chức vụ khác trong hệ thống</li>
        </ul>
        <p class="adminConfirmModal_warning">Thao tác này không thể khôi phục.</p>
        <p id="adminProfileDeleteError" class="adminConfirmModal_error" hidden></p>
        <div class="adminConfirmModal_actions">
            <button type="button" class="adminButton adminButton--secondary adminButton--sm" data-profile-delete-cancel>Hủy</button>
            <button type="button" class="adminButton adminButton--danger adminButton--sm" id="adminProfileDeleteConfirm">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
                </svg>
                <span>Xóa vĩnh viễn</span>
            </button>
        </div>
    </div>
</div>
