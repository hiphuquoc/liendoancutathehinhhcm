<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin tài khoản HLV</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #2196F3;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #2196F3;
            margin: 0;
            font-size: 24px;
        }
        .content {
            margin-bottom: 30px;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
        }
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #2196F3;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .info-box h3 {
            margin-top: 0;
            color: #2196F3;
            font-size: 18px;
        }
        .info-item {
            margin: 15px 0;
            padding: 10px;
            background-color: #ffffff;
            border-radius: 4px;
        }
        .info-label {
            font-weight: bold;
            color: #555;
            display: inline-block;
            width: 120px;
        }
        .info-value {
            color: #2196F3;
            font-weight: 600;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #2196F3;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
            font-weight: 600;
            text-align: center;
        }
        .button:hover {
            background-color: #1976D2;
        }
        .button-secondary {
            background-color: #4CAF50;
        }
        .button-secondary:hover {
            background-color: #45a049;
        }
        .notice {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .notice strong {
            color: #856404;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            color: #777;
            font-size: 14px;
        }
        .steps {
            margin: 20px 0;
        }
        .step {
            margin: 15px 0;
            padding-left: 30px;
            position: relative;
        }
        .step-number {
            position: absolute;
            left: 0;
            top: 0;
            background-color: #2196F3;
            color: #ffffff;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>Liên Đoàn Cử Tạ - Thể Hình HCM</h1>
            <p>Thông tin tài khoản Huấn luyện viên</p>
        </div>

        <div class="content">
            <div class="greeting">
                <p>Xin chào <strong>{{ $trainerName }}</strong>,</p>
                <p>Chào mừng bạn đến với hệ thống quản lý HLV của Liên Đoàn Cử Tạ - Thể Hình HCM. Dưới đây là thông tin tài khoản đăng nhập của bạn:</p>
            </div>

            <div class="info-box">
                <h3>📋 Thông tin tài khoản</h3>
                
                <div class="info-item">
                    <span class="info-label">Họ và tên:</span>
                    <span class="info-value">{{ $trainerName }}</span>
                </div>

                @if(!empty($trainerCode))
                <div class="info-item">
                    <span class="info-label">Mã HLV:</span>
                    <span class="info-value">{{ $trainerCode }}</span>
                </div>
                @endif

                <div class="info-item">
                    <span class="info-label">Email đăng nhập:</span>
                    <span class="info-value">{{ $email }}</span>
                </div>

                <div class="info-item">
                    <span class="info-label">Tên đăng nhập:</span>
                    <span class="info-value">{{ $username }}</span>
                </div>

                <div class="info-item">
                    <span class="info-label">Mật khẩu mặc định:</span>
                    <span class="info-value">{{ $username }}</span>
                    <p style="margin: 5px 0 0 0; font-size: 13px; color: #666; font-style: italic;">
                        ⚠️ Vui lòng đổi mật khẩu ngay sau lần đăng nhập đầu tiên để bảo mật tài khoản
                    </p>
                </div>
            </div>

            <div class="notice">
                <strong>🔐 Hướng dẫn đăng nhập:</strong>
                <div class="steps">
                    <div class="step">
                        <span class="step-number">1</span>
                        <p>Truy cập vào đường link đăng nhập: <a href="{{ $loginUrl }}" style="color: #2196F3;">{{ $loginUrl }}</a></p>
                    </div>
                    <div class="step">
                        <span class="step-number">2</span>
                        <p>Nhập <strong>Tên đăng nhập</strong> hoặc <strong>Email</strong>: <strong>{{ $username }}</strong></p>
                    </div>
                    <div class="step">
                        <span class="step-number">3</span>
                        <p>Nhập <strong>Mật khẩu</strong>: <strong>{{ $username }}</strong> (mật khẩu mặc định)</p>
                    </div>
                    <div class="step">
                        <span class="step-number">4</span>
                        <p>Nhấn nút <strong>Đăng nhập</strong></p>
                    </div>
                </div>
            </div>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $loginUrl }}" class="button">🔑 Đăng nhập ngay</a>
            </div>

            <div class="info-box" style="background-color: #e3f2fd; border-left-color: #2196F3;">
                <h3>✨ Các bước tiếp theo sau khi đăng nhập</h3>
                <div style="margin-top: 15px;">
                    <p><strong>1. Đổi mật khẩu:</strong></p>
                    <p style="margin-left: 20px; color: #555;">
                        Vào <strong>Tài khoản</strong> → <strong>Đổi mật khẩu</strong> để đặt mật khẩu mới an toàn hơn.
                    </p>
                    
                    <p style="margin-top: 20px;"><strong>2. Cập nhật thông tin cá nhân:</strong></p>
                    <p style="margin-left: 20px; color: #555;">
                        Vào <strong>Tài khoản</strong> → <strong>Hồ sơ HLV</strong> để cập nhật đầy đủ thông tin, ảnh đại diện, và các thông tin khác.
                    </p>
                    
                    <p style="margin-top: 20px;"><strong>3. Hoàn thiện hồ sơ:</strong></p>
                    <p style="margin-left: 20px; color: #555;">
                        Cập nhật các thông tin như: thành tích, kỹ năng, kinh nghiệm, bằng cấp để hồ sơ của bạn được hiển thị đầy đủ trên website.
                    </p>
                </div>

                <div style="text-align: center; margin-top: 25px;">
                    <a href="{{ $profileEditUrl }}" class="button button-secondary">📝 Cập nhật hồ sơ ngay</a>
                    <a href="{{ $profileUrl }}" class="button" style="background-color: #9C27B0;">👤 Xem hồ sơ công khai</a>
                </div>
            </div>

            <div class="notice">
                <strong>💡 Lưu ý:</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Vui lòng giữ bí mật thông tin tài khoản của bạn</li>
                    <li>Nếu bạn quên mật khẩu, sử dụng tính năng "Quên mật khẩu" trên trang đăng nhập</li>
                    <li>Nếu có bất kỳ thắc mắc nào, vui lòng liên hệ với ban quản trị</li>
                </ul>
            </div>
        </div>

        <div class="footer">
            <p><strong>Liên Đoàn Cử Tạ - Thể Hình Thành phố Hồ Chí Minh</strong></p>
            <p>Email này được gửi tự động, vui lòng không trả lời email này.</p>
            <p style="font-size: 12px; color: #999; margin-top: 15px;">
                © {{ date('Y') }} Liên Đoàn Cử Tạ - Thể Hình HCM. Tất cả quyền được bảo lưu.
            </p>
        </div>
    </div>
</body>
</html>

