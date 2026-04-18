{{--
    Modal: Gửi email test thông tin tài khoản VĐV
--}}
<style>
#adminAthleteTestEmailModal.adminClearCacheModal .adminClearCacheModal_content {
    max-width: 420px;
    width: 90%;
}
#adminAthleteTestEmailModal .adminClearCacheModal_body {
    text-align: left;
}
#adminAthleteTestEmailModal .adminClearCacheModal_icon {
    margin-left: 0;
}
#adminAthleteTestEmailModal .adminClearCacheModal_field {
    margin-top: 1.25rem;
}
#adminAthleteTestEmailModal .adminClearCacheModal_label {
    display: block;
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
}
#adminAthleteTestEmailModal .adminClearCacheModal_input {
    display: block;
    width: 100%;
    box-sizing: border-box;
    padding: 0.625rem 0.875rem;
    font-size: 0.9375rem;
    line-height: 1.5;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    color: #111827;
    background: #fff;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}
#adminAthleteTestEmailModal .adminClearCacheModal_input:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
}
#adminAthleteTestEmailModal .adminClearCacheModal_input::placeholder {
    color: #9ca3af;
}
#adminAthleteTestEmailModal .adminClearCacheModal_input:disabled {
    background: #f3f4f6;
    cursor: not-allowed;
}
#adminAthleteTestEmailModal .adminClearCacheModal_error {
    display: block;
    margin-top: 0.5rem;
    font-size: 0.8125rem;
    color: #dc2626;
}
#adminAthleteTestEmailModal .adminClearCacheModal_icon--email {
    background: rgba(37, 99, 235, 0.1);
    color: #2563eb;
}
</style>

<div id="adminAthleteTestEmailModal" class="adminClearCacheModal">
    <div class="adminClearCacheModal_backdrop" onclick="closeAthleteTestEmailModal()"></div>
    <div class="adminClearCacheModal_content">
        <div class="adminClearCacheModal_header">
            <h2 class="adminClearCacheModal_title">Gửi email test</h2>
            <button type="button" class="adminClearCacheModal_close" onclick="closeAthleteTestEmailModal()" aria-label="Đóng">
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
                <p id="adminAthleteTestEmailModal_athleteLabel">Nội dung email thông tin tài khoản của VĐV sẽ được gửi đến địa chỉ bạn nhập bên dưới (gửi ngay, không qua hàng đợi).</p>
            </div>
            <div class="adminClearCacheModal_field">
                <label for="adminAthleteTestEmailModal_emailInput" class="adminClearCacheModal_label">Email nhận test</label>
                <input type="email"
                       id="adminAthleteTestEmailModal_emailInput"
                       class="adminClearCacheModal_input"
                       placeholder="vd: email@example.com"
                       autocomplete="email">
                <span id="adminAthleteTestEmailModal_error" class="adminClearCacheModal_error" style="display: none;"></span>
            </div>
        </div>

        <div class="adminClearCacheModal_footer">
            <button type="button" class="adminClearCacheModal_button adminClearCacheModal_button--secondary" onclick="closeAthleteTestEmailModal()">
                Hủy
            </button>
            <button type="button" class="adminClearCacheModal_button adminClearCacheModal_button--primary" id="adminAthleteTestEmailModal_confirmBtn" onclick="confirmSendAthleteTestEmail()">
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
