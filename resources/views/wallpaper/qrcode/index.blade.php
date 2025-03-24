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
        .qr-code img {
            max-width: 100px;
            max-height: 100px;
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
                        <td>{{ $loop->index + 1 }}</td>
                        <td style="text-align: left;">
                            <div>Họ và tên: {{ $trainer['name'] }}</div>
                            <div>Ngày tháng năm sinh: {{ $trainer['birth_day'] }}</div>
                            <div>Số CCCD: {{ $trainer['cccd'] }}</div>
                            <div>Điện thoại: {{ $trainer['phone'] }}</div>
                            <div>Địa chỉ: {{ $trainer['address'] }}</div>
                            <div>Đường dẫn: <a href="{{ $trainer['link'] }}" target="_blank">{{ $trainer['link'] }}</a></div>
                        </td>
                        <td class="qr-code">
                            <img src="{{ $trainer['qrCode'] }}" alt="QR Code" class="qr-img" />
                            <button class="download-btn" data-name="{{ $trainer['name'] }}">Tải xuống PNG</button>
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".download-btn").forEach(button => {
                button.addEventListener("click", function() {
                    let row = this.closest("td");
                    let img = row.querySelector(".qr-img");
                    let trainerName = this.getAttribute("data-name").replace(/\s+/g, "_");
        
                    fetch(img.src)
                        .then(res => res.text()) // Lấy nội dung SVG
                        .then(svgText => {
                            let canvas = document.createElement("canvas");
                            let ctx = canvas.getContext("2d");
                            let imgElement = new Image();
        
                            let svgBlob = new Blob([svgText], { type: "image/svg+xml;charset=utf-8" });
                            let url = URL.createObjectURL(svgBlob);
        
                            imgElement.onload = function() {
                                canvas.width = 300; // Kích thước mong muốn
                                canvas.height = 300;
                                ctx.drawImage(imgElement, 0, 0, 300, 300);
        
                                // Tạo file PNG
                                let pngUrl = canvas.toDataURL("image/png");
        
                                // Tạo thẻ <a> để tải xuống
                                let downloadLink = document.createElement("a");
                                downloadLink.href = pngUrl;
                                downloadLink.download = `QR_${trainerName}.png`;
                                document.body.appendChild(downloadLink);
                                downloadLink.click();
                                document.body.removeChild(downloadLink);
                                URL.revokeObjectURL(url);
                            };
                            imgElement.src = url;
                        });
                });
            });
        });
    </script>
</body>
</html>
