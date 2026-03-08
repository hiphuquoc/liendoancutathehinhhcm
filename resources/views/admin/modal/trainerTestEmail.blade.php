{{--
    Modal: Gửi email test thông tin tài khoản HLV
    Đồng bộ giao diện với modal Clear Cache
--}}
<div id="adminTrainerTestEmailModal" class="adminClearCacheModal">
    <div class="adminClearCacheModal_backdrop" onclick="closeTrainerTestEmailModal()"></div>
    <div class="adminClearCacheModal_content">
        <div class="adminClearCacheModal_header">
            <h2 class="adminClearCacheModal_title">Gửi email test</h2>
            <button type="button" class="adminClearCacheModal_close" onclick="closeTrainerTestEmailModal()" aria-label="Đóng">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 6L6 18M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="adminClearCacheModal_body">
            <div class="adminClearCacheModal_icon adminClearCacheModal_icon--email">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                </svg>
            </div>
            <div class="adminClearCacheModal_message">
                <p id="adminTrainerTestEmailModal_trainerLabel">Nội dung email thông tin tài khoản của HLV sẽ được gửi đến địa chỉ bạn nhập bên dưới (gửi ngay, không qua hàng đợi).</p>
            </div>
            <div class="adminClearCacheModal_field">
                <label for="adminTrainerTestEmailModal_emailInput" class="adminClearCacheModal_label">Email nhận test</label>
                <input type="email"
                       id="adminTrainerTestEmailModal_emailInput"
                       class="adminClearCacheModal_input"
                       placeholder="vd: email@example.com"
                       autocomplete="email">
                <span id="adminTrainerTestEmailModal_error" class="adminClearCacheModal_error" style="display: none;"></span>
            </div>
        </div>

        <div class="adminClearCacheModal_footer">
            <button type="button" class="adminClearCacheModal_button adminClearCacheModal_button--secondary" onclick="closeTrainerTestEmailModal()">
                Hủy
            </button>
            <button type="button" class="adminClearCacheModal_button adminClearCacheModal_button--primary" id="adminTrainerTestEmailModal_confirmBtn" onclick="confirmSendTrainerTestEmail()">
                <span class="adminClearCacheModal_button_text">Gửi email</span>
                <span class="adminClearCacheModal_button_loader" style="display: none;">
                    <svg class="adminClearCacheModal_button_spinner" viewBox="0 0 50 50">
                        <circle class="adminClearCacheModal_button_spinner_path" cx="25" cy="25" r="20" fill="none" stroke-width="4"></circle>
                    </svg>
                </span>
            </button>
        </div>
    </div>
</div>
