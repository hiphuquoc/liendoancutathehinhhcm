# Kiểm tra Google Cloud Storage khi upload được local nhưng lỗi trên tên miền chính (production)

Khi **local upload được** nhưng **production (tên miền chính) báo lỗi** (vd: 401 Invalid Credentials), thường do cấu hình hoặc credentials trên server / Cloud, không phải do code. Làm lần lượt các bước dưới.

---

## Phần 1: Kiểm tra trên Google Cloud Console

### Bước 1 – Xác định project và bucket

1. Vào [Google Cloud Console](https://console.cloud.google.com/).
2. Chọn đúng **Project** (góc trên bên trái) – project chứa bucket bạn dùng (vd: bucket `liendoancutathehinhhcm`).
3. Vào **Cloud Storage** → **Buckets**.
4. Mở bucket dùng cho upload (vd: `liendoancutathehinhhcm`).
5. Ghi lại:
   - **Tên bucket**
   - **Project ID** (trong thông tin bucket hoặc trên thanh project).

---

### Bước 2 – Kiểm tra Service Account và key

1. Vào **IAM & Admin** → **Service Accounts** (cùng project với bucket).
2. Tìm Service Account mà app đang dùng:
   - Trên local: xem trong file JSON key (trường `client_email`), ví dụ: `xxx@project-id.iam.gserviceaccount.com`.
   - Trên production: xem trong file JSON key bạn đã đặt trên server (cũng là `client_email`).
3. Kiểm tra:
   - Service Account đó **vẫn tồn tại**, không bị xóa.
   - Nếu dùng key cũ (đã xoay/xóa): tạo key mới cho cùng Service Account đó:
     - Mở Service Account → tab **Keys** → **Add key** → **Create new key** → **JSON** → tải file.
     - Đặt file JSON mới lên **server production** và cập nhật `GOOGLE_CLOUD_KEY_FILE` trong `.env` trên server trỏ đúng đường dẫn file mới.

---

### Bước 3 – Cấp quyền cho Service Account lên bucket

1. Vào **Cloud Storage** → **Buckets** → chọn bucket (vd: `liendoancutathehinhhcm`).
2. Mở tab **Permissions** (hoặc **Permissions** trong chi tiết bucket).
3. Bấm **Grant access** (hoặc **Add principal**).
4. Trong **New principals**, nhập đúng **email** Service Account (vd: `xxx@project-id.iam.gserviceaccount.com`).
5. Trong **Role**, chọn một trong:
   - **Storage Object Admin** (đủ để tạo/sửa/xóa object trong bucket), hoặc
   - **Storage Admin** (nếu cần quản lý cả bucket).
6. Bấm **Save**.

Nếu bucket thuộc **project khác** với project chứa Service Account:

- Vẫn vào đúng **bucket** đó → **Permissions** → **Grant access**.
- Thêm chính Service Account (email đúng) với role **Storage Object Admin** (hoặc **Storage Admin**).
- GCS cho phép SA của project A được cấp quyền trên bucket của project B.

---

### Bước 4 – (Tùy chọn) Kiểm tra IAM ở cấp project

1. Vào **IAM & Admin** → **IAM** (cùng project chứa bucket).
2. Tìm Service Account (email như trên).
3. Đảm bảo có ít nhất một role liên quan Storage, ví dụ:
   - **Storage Object Admin**, hoặc
   - **Storage Admin**.

Thường chỉ cần cấp quyền ở **Bước 3 (trên bucket)** là đủ; cấp thêm ở project giúp tránh lỗi quyền.

---

## Phần 2: Kiểm tra trên server (tên miền chính / production)

### Bước 5 – File key JSON có tồn tại và đúng đường dẫn

Trên **máy server production** (SSH hoặc terminal):

```bash
# Thay bằng đường dẫn bạn đã set trong .env (GOOGLE_CLOUD_KEY_FILE)
cat /đường/dẫn/đến/file-key.json
```

- Nếu báo "No such file or directory" → file chưa có trên server hoặc đường dẫn trong `.env` sai.
- Sửa: upload đúng file JSON key lên server, sửa `GOOGLE_CLOUD_KEY_FILE` trong `.env` trên server cho đúng **đường dẫn tuyệt đối** (vd: `/www/wwwroot/tenmien.com.vn/storage/app/gcs-key.json`).

Kiểm tra nhanh nội dung key (chỉ xem, không gửi cho ai):

```bash
php -r "
\$p = '/đường/dẫn/đến/file-key.json';
if (!is_file(\$p)) { echo 'FILE_NOT_FOUND'; exit; }
\$j = json_decode(file_get_contents(\$p), true);
echo 'client_email: ' . (\$j['client_email'] ?? 'missing') . PHP_EOL;
echo 'project_id: ' . (\$j['project_id'] ?? 'missing') . PHP_EOL;
"
```

Phải in ra đúng `client_email` và `project_id` giống với Service Account bạn đã cấp quyền ở Bước 3.

---

### Bước 6 – Biến môi trường trên server

Trên server, đảm bảo **ứng dụng đọc đúng .env** (restart PHP-FPM / queue nếu cần):

- `GOOGLE_CLOUD_KEY_FILE` = đường dẫn tuyệt đối tới file JSON (vd: `/www/.../storage/app/gcs-key.json`).
- `GOOGLE_CLOUD_STORAGE_BUCKET` = tên bucket (vd: `liendoancutathehinhhcm`).

Có thể kiểm tra trong Laravel (tạm thời, chỉ trên môi trường an toàn):

```bash
cd /đường/dẫn/root/của/app
php artisan tinker
>>> config('filesystems.disks.gcs.key_file_path')
>>> config('filesystems.disks.gcs.bucket')
>>> exit
```

Nếu trả về `null` hoặc rỗng → `.env` chưa có hoặc chưa được load; sửa `.env` và clear config: `php artisan config:clear`.

---

### Bước 7 – Quyền đọc file key (Linux)

User chạy PHP (vd: `www-data`, `nginx`) phải đọc được file key:

```bash
ls -la /đường/dẫn/đến/file-key.json
```

Nếu cần:

```bash
chmod 640 /đường/dẫn/đến/file-key.json
chown www-data:www-data /đường/dẫn/đến/file-key.json
```

(Thay `www-data` bằng user chạy PHP trên server của bạn.)

---

## Tóm tắt nhanh

| Nơi kiểm tra | Việc cần làm |
|--------------|--------------|
| **Cloud Console – Service Account** | SA còn tồn tại; nếu đổi key thì dùng file key mới trên server. |
| **Cloud Console – Bucket** | Cấp **Storage Object Admin** (hoặc **Storage Admin**) cho đúng email SA. |
| **Server – File key** | File JSON tồn tại, đường dẫn trong `GOOGLE_CLOUD_KEY_FILE` đúng. |
| **Server – .env** | `GOOGLE_CLOUD_KEY_FILE` và `GOOGLE_CLOUD_STORAGE_BUCKET` đúng; sau khi sửa chạy `php artisan config:clear`. |
| **Server – Quyền file** | User PHP có quyền đọc file key. |

Sau khi chỉnh xong Cloud (Bước 1–4) và server (Bước 5–7), thử upload lại trên tên miền chính. Nếu vẫn lỗi, gửi **nguyên văn thông báo lỗi** (hoặc dòng log liên quan) để soi tiếp.

---

## Phần 3: Khi **mọi dự án** đều lỗi upload lên cloud trên **hosting thật**, còn **local đều OK**

Nếu bạn đã kiểm tra Cloud + file key + .env trên server và **đúng hết**, nhưng **tất cả dự án** dùng upload GCS đều **chỉ lỗi trên hosting / tên miền thật**, còn **local chạy tốt** → nhiều khả năng do **môi trường hosting** (SSL, firewall, PHP restrictions). Làm lần lượt bên dưới.

### Bước 8 – Chạy lệnh chẩn đoán trên hosting

Trên **server hosting** (SSH hoặc terminal), vào thư mục gốc của **một** dự án Laravel có dùng GCS và chạy:

```bash
cd /đường/dẫn/root/của/dự/án
php artisan gcs:check-env
```

Lệnh này kiểm tra: file key có tồn tại và đọc được không, config GCS, extension PHP (openssl, curl), và có kết nối được tới Google HTTPS không. Ghi lại phần nào **FAIL** hoặc **Lỗi** trong kết quả in ra.

---

### Bước 9 – SSL / CA certificate (hay gặp trên hosting)

Trên nhiều **shared hosting**, PHP không có đủ CA bundle để xác thực HTTPS của Google → kết nối tới `oauth2.googleapis.com` hoặc `storage.googleapis.com` **thất bại** (có thể báo 401 Invalid Credentials vì request không hoàn tất đúng).

**Cách kiểm tra nhanh (trên hosting):**

```bash
php -r "
\$ctx = stream_context_create(['ssl' => ['verify_peer' => true]]);
\$r = @file_get_contents('https://www.googleapis.com/oauth2/v1/certs', false, \$ctx);
echo \$r === false ? 'FAIL: Không kết nối được HTTPS tới Google' : 'OK: Kết nối được';
"
```

- Nếu in **FAIL** → cần cấu hình CA certificate cho PHP trên hosting.

**Cách xử lý (tùy loại hosting):**

1. **Shared hosting (cPanel, DirectAdmin, …)**  
   - Trong **PHP Settings** / **Select PHP Version** / **Extensions**, bật **openssl**, **curl**.  
   - Nếu có mục **php.ini** hoặc **.user.ini**: thêm hoặc sửa (đường dẫn phải đúng với hosting):
     ```ini
     curl.cainfo = "/path/to/cacert.pem"
     openssl.cafile = "/path/to/cacert.pem"
     ```
     File `cacert.pem` có thể tải từ: https://curl.se/ca/cacert.pem — upload lên server (vd: `storage/app/cacert.pem`) và dùng đường dẫn tuyệt đối.  
   - Hỏi nhà cung cấp hosting: *“Làm sao để PHP verify SSL khi gọi HTTPS ra ngoài (Google APIs)?”* — họ có thể chỉ đường dẫn `cacert.pem` sẵn có hoặc hướng dẫn riêng.

2. **VPS / server tự quản lý**  
   - Cài CA bundle (vd: `ca-certificates`) và trong `php.ini`:
     ```ini
     curl.cainfo = "/etc/ssl/certs/ca-certificates.crt"
     openssl.cafile = "/etc/ssl/certs/ca-certificates.crt"
     ```
   - Restart PHP-FPM / web server, rồi chạy lại lệnh kiểm tra ở trên.

Sau khi chỉnh xong, chạy lại `php artisan gcs:check-env` và thử upload lại.

---

### Bước 10 – Firewall / chặn outbound (hosting)

Một số hosting **chặn outbound** hoặc chỉ cho phép một số domain/port. Khi đó PHP không gọi được tới `*.googleapis.com` → upload GCS lỗi (local không bị vì mạng của bạn không bị chặn).

**Cách kiểm tra (trên hosting):**

```bash
php -r "
\$ch = curl_init('https://storage.googleapis.com');
curl_setopt(\$ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt(\$ch, CURLOPT_NOBODY, true);
curl_setopt(\$ch, CURLOPT_TIMEOUT, 10);
\$ok = curl_exec(\$ch);
\$err = curl_error(\$ch);
curl_close(\$ch);
echo \$ok !== false && \$err === '' ? 'OK' : 'FAIL: ' . (\$err ?: 'Timeout hoặc bị chặn');
"
```

- Nếu **FAIL** → liên hệ nhà cung cấp hosting: *“Có cho phép outbound HTTPS (port 443) tới domain `googleapis.com` / `google.com` không?”*. Nếu họ chặn thì cần mở (whitelist) cho ứng dụng web.

---

### Bước 11 – open_basedir / disable_functions (PHP trên hosting)

Hosting có thể bật **open_basedir** hoặc **disable_functions**, khiến PHP **không đọc được file key** (dù đường dẫn trong .env đúng) hoặc không dùng được `curl`/`file_get_contents` ra ngoài.

**Cách kiểm tra:**

- Trong kết quả `php artisan gcs:check-env`, xem mục **File key** có báo đọc được không.  
- Hoặc chạy trên hosting:
  ```bash
  php -r "echo ini_get('open_basedir') ?: '(empty)';"
  php -r "echo ini_get('disable_functions') ?: '(none)';"
  ```
  Nếu `open_basedir` có giá trị → đường dẫn file key và đường dẫn chứa CA (nếu dùng) phải **nằm trong** các path được phép.  
  Nếu `disable_functions` có `curl`, `file_get_contents`, `openssl_*` → cần nhờ hosting bỏ chặn cho các function đó (ít nhất cho PHP chạy web app).

---

### Bước 12 – User chạy PHP trên hosting

Trên hosting thật, PHP (vd: PHP-FPM) có thể chạy bằng user **khác** (vd: `nobody`, `apache`, user FTP) chứ không phải user bạn SSH. User đó phải **đọc được** file key.

- Đường dẫn file key: không nên đặt trong thư mục chỉ user SSH mới đọc được.  
- Quyền: `chmod 640` và owner/group sao cho **user chạy PHP** đọc được (hoặc nhờ hosting chỉnh).  
- Chạy `php artisan gcs:check-env` **bằng user/process giống với khi chạy web** (vd: qua `php-fpm` hoặc cron dùng cùng user) để đảm bảo kết quả giống lúc upload thật.

---

### Tóm tắt Phần 3 (local OK, mọi dự án lỗi trên hosting)

| Nguyên nhân thường gặp | Việc cần làm |
|------------------------|---------------|
| **SSL/CA**             | Cấu hình `curl.cainfo` / `openssl.cafile` trên hosting; kiểm tra kết nối HTTPS tới Google. |
| **Firewall / outbound**| Xác nhận hosting không chặn HTTPS tới `googleapis.com`; nhờ mở nếu bị chặn. |
| **open_basedir**       | Đảm bảo đường dẫn file key (và CA nếu dùng) nằm trong path được phép. |
| **disable_functions**  | Đảm bảo không disable `curl`, `file_get_contents`, `openssl_*` cho PHP chạy web. |
| **User PHP**          | User chạy PHP (web) phải đọc được file key; chạy `gcs:check-env` trong cùng môi trường đó. |

Chạy `php artisan gcs:check-env` trên **hosting thật** và sửa theo từng mục FAIL; sau đó thử upload lại. Nếu vẫn lỗi, gửi **kết quả in ra của lệnh** (có thể che bớt đường dẫn nhạy cảm) để soi tiếp.
