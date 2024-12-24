<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trainer QR Codes</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f4f4f4;
        }
        .qr-code {
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>
<body>
    <h1>Danh Sách Huấn Luyện Viên</h1>
    <table>
        <thead>
            <tr>
                <th width="60px">STT</th>
                <th>Thông tin</th>
                <th>Mã QR</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($trainers as $trainer)
                @if(!empty($trainer['name']))
                    <tr>
                        <td>
                            {{ $loop->index + 1 }}
                        </td>
                        <td style="text-align: left;">
                            <div>Họ và tên: {{ $trainer['name'] }}</div>
                            <div>Ngày tháng năm sinh: {{ $trainer['birth_day'] }}</div>
                            <div>Số CCCD: {{ $trainer['cccd'] }}</div>
                            <div>Điện thoại: {{ $trainer['phone'] }}</div>
                            <div>Địa chỉ: {{ $trainer['address'] }}</div> 
                            <div>Đường dẫn: {{ $trainer['link'] }}</div>
                        </td>
                        <td class="qr-code">{!! $trainer['qrCode'] !!}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</body>
</html>
