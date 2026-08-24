<!DOCTYPE html>
<html lang=vi>
<head>
    <meta charset=UTF-8>
    <meta name=viewport content=width=device-width, initial-scale=1.0>
    <title>Yêu cầu đặt lại mật khẩu</title>
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
        .link-block {
            margin: 24px 0;
            text-align: center;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            border-radius: 6px;
            color: #ffffff !important;
            background: #0174be;
        }
        .btn:hover { background: #015d9b; }
        .link-url {
            font-size: 13px;
            color: #0174be;
            word-break: break-all;
        }
        .notice-box {
            background: #fef3c7;
            border: 1px solid #fde68a;
            border-radius: 6px;
            padding: 14px 16px;
            font-size: 13px;
            color: #92400e;
            margin-top: 20px;
        }
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
    <div class=wrapper>
        <div class=card>
            <div class=card-inner>
                <div class=brand>
                    <h1>Liên Đoàn Cử Tạ – Thể Hình TP.HCM</h1>
                    <p>Yêu cầu đặt lại mật khẩu tài khoản</p>
                </div>

                <div class=greeting>
                    <p>Xin chào,</p>
                    <p>Hệ thống nhận được yêu cầu đặt lại mật khẩu cho tài khoản liên kết với địa chỉ email: <strong>{{  }}</strong>.</p>
                    <p>Vui lòng bấm vào nút bên dưới để thiết lập mật khẩu mới:</p>
                </div>

                <div class=link-block>
                    <a href={{ }} class=btn target=_blank>Đặt lại mật khẩu</a>
                </div>

                <div class=section>
                    <p style=font-size: 13px; color: #6b7280; margin-bottom: 6px;>Nếu nút trên không hoạt động, bạn có thể sao chép và dán liên kết sau vào trình duyệt:</p>
                    <a href={{ }} class=link-url target=_blank>{{  }}</a>
                </div>

                <div class=notice-box>
                    <strong>Lưu ý bảo mật:</strong>
                    <ul style=margin: 6px 0 0; padding-left: 18px;>
                        <li>Liên kết này chỉ có hiệu lực trong vòng <strong>60 phút</strong>.</li>
                        <li>Nếu bạn không thực hiện yêu cầu này, vui lòng bỏ qua email. Mật khẩu của bạn vẫn an toàn và không thay đổi.</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class=footer-text>
            <p><strong>Liên Đoàn Cử Tạ – Thể Hình Thành phố Hồ Chí Minh</strong></p>
            <p>Email gửi tự động từ hệ thống quản trị, vui lòng không trả lời thư này.</p>
            <p>© {{ date('Y') }} Liên Đoàn Cử Tạ – Thể Hình TP.HCM</p>
        </div>
    </div>
</body>
</html>