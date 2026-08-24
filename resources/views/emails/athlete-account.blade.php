<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin tài khoản HLV</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            font-size: 15px;
            line-height: 1.5;
            color: #1f2937;
            background-color: #f3f4f6;
        }
        .wrapper {
            max-width: 560px;
            margin: 0 auto;
            padding: 24px 16px;
        }
        .card {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }
        .card-inner { padding: 28px 24px; }
        .brand {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 24px;
        }
        .brand h1 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            color: #111827;
        }
        .brand p {
            margin: 4px 0 0;
            font-size: 13px;
            color: #6b7280;
        }
        .greeting {
            margin-bottom: 20px;
        }
        .greeting p {
            margin: 0 0 8px;
        }
        .greeting strong { color: #111827; }
        .section {
            margin-bottom: 22px;
        }
        .section-title {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin: 0 0 12px;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        .info-table tr {
            border-bottom: 1px solid #f3f4f6;
        }
        .info-table tr:last-child { border-bottom: none; }
        .info-table td {
            padding: 10px 0;
            vertical-align: top;
        }
        .info-table .label {
            width: 140px;
            color: #6b7280;
        }
        .info-table .value {
            font-weight: 500;
            color: #111827;
        }
        .note-inline {
            font-size: 13px;
            color: #6b7280;
            margin-top: 6px;
        }
        .steps-list {
            margin: 0;
            padding-left: 20px;
        }
        .steps-list li {
            margin-bottom: 10px;
        }
        .steps-list li:last-child { margin-bottom: 0; }
        .link-block {
            margin: 22px 0;
            text-align: center;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            border-radius: 6px;
            color: #fff !important;
            background: #2563eb;
        }
        .btn:hover { background: #1d4ed8; }
        .link-url {
            font-size: 13px;
            color: #2563eb;
            word-break: break-all;
        }
        .tips {
            background: #f9fafb;
            border-radius: 6px;
            padding: 16px;
            font-size: 14px;
            color: #4b5563;
        }
        .tips .section-title { margin-bottom: 10px; }
        .tips ol {
            margin: 0;
            padding-left: 18px;
        }
        .tips li { margin-bottom: 6px; }
        .footer-text {
            text-align: center;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 12px;
            color: #9ca3af;
        }
        .footer-text p { margin: 4px 0; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <div class="card-inner">
                <div class="brand">
                    <h1>Liên Đoàn Cử Tạ – Thể Hình HCM</h1>
                    <p>Thông tin tài khoản Huấn luyện viên</p>
                </div>

                <div class="greeting">
                    <p>Xin chào <strong>{{ $trainerName }}</strong>,</p>
                    <p>Bạn được cấp tài khoản đăng nhập hệ thống quản lý HLV. Dưới đây là thông tin đăng nhập và hướng dẫn sử dụng.</p>
                </div>

                <div class="section">
                    <p class="section-title">Thông tin đăng nhập</p>
                    <table class="info-table">
                        <tr>
                            <td class="label">Họ và tên</td>
                            <td class="value">{{ $trainerName }}</td>
                        </tr>
                        @if(!empty($trainerCode))
                        <tr>
                            <td class="label">Mã HLV</td>
                            <td class="value">{{ $trainerCode }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="label">Email</td>
                            <td class="value">{{ $email }}</td>
                        </tr>
                        <tr>
                            <td class="label">Tên đăng nhập</td>
                            <td class="value">{{ $username }}</td>
                        </tr>
                        <tr>
                            <td class="label">Mật khẩu đăng nhập</td>
                            <td class="value"><span style="font-family: monospace; font-size: 15px; font-weight: 700; color: #0174be; background: #e8f4fc; padding: 3px 8px; border-radius: 4px;">{{ $password ?? $username }}</span></td>
                        </tr>
                    </table>
                    <p class="note-inline" style="color: #059669; font-weight: 500;">✓ Mật khẩu trên là mật khẩu đăng nhập chính xác hiện tại của bạn. Vui lòng đổi lại mật khẩu sau khi đăng nhập để bảo mật.</p>
                </div>

                <div class="section">
                    <p class="section-title">Cách đăng nhập</p>
                    <ol class="steps-list">
                        <li>Mở link đăng nhập: <a href="{{ $loginUrl }}" class="link-url">{{ $loginUrl }}</a></li>
                        <li>Nhập <strong>Tên đăng nhập</strong> hoặc <strong>Email</strong>: <strong>{{ $username }}</strong></li>
                        <li>Nhập <strong>Mật khẩu</strong>: <strong>{{ $password ?? $username }}</strong></li>
                        <li>Bấm <strong>Đăng nhập</strong></li>
                    </ol>
                    <div class="link-block">
                        <a href="{{ $loginUrl }}" class="btn">Đăng nhập</a>
                    </div>
                </div>

                <div class="section tips">
                    <p class="section-title">Sau khi đăng nhập nên làm</p>
                    <ol>
                        <li><strong>Đổi mật khẩu:</strong> Vào Tài khoản → Đổi mật khẩu.</li>
                        <li><strong>Cập nhật hồ sơ:</strong> Vào Tài khoản → Hồ sơ HLV để cập nhật thông tin, ảnh đại diện.</li>
                        <li><strong>Hoàn thiện hồ sơ:</strong> Bổ sung thành tích, kỹ năng, kinh nghiệm, bằng cấp để hồ sơ hiển thị đầy đủ trên website.</li>
                    </ol>
                    <div class="link-block">
                        <a href="{{ $profileEditUrl }}" class="btn">Cập nhật hồ sơ</a>
                        <a href="{{ $profileUrl }}" class="btn" style="background:#6b7280; margin-left:8px;">Xem hồ sơ công khai</a>
                    </div>
                </div>

                <div class="section">
                    <p class="section-title">Lưu ý</p>
                    <ul class="steps-list" style="list-style:disc;">
                        <li>Giữ bí mật thông tin tài khoản.</li>
                        <li>Quên mật khẩu: dùng chức năng "Quên mật khẩu" trên trang đăng nhập.</li>
                        <li>Liên hệ ban quản trị nếu cần hỗ trợ.</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="footer-text">
            <p><strong>Liên Đoàn Cử Tạ – Thể Hình Thành phố Hồ Chí Minh</strong></p>
            <p>Email gửi tự động, vui lòng không trả lời.</p>
            <p>© {{ date('Y') }} Liên Đoàn Cử Tạ – Thể Hình HCM</p>
        </div>
    </div>
</body>
</html>
